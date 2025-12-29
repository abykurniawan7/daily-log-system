<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'role',
        'bagian',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return $this->where($field, $value)->firstOrFail();
        }
        
        $result = $this->where('uuid', $value)->first();
        
        if (!$result && is_numeric($value)) {
            $result = $this->where('id', $value)->first();
        }
        
        if (!$result) {
            abort(404);
        }
        
        return $result;
    }

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'pic_proyek_id');
    }

    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'user_id');
    }

    public function projectsAsPic()
    {
        return $this->hasMany(Project::class, 'pic_proyek_id');
    }

    public function roleRequests()
    {
        return $this->hasMany(RoleRequest::class);
    }

    public function assignedProjects()
    {
        return $this->belongsToMany(Project::class, 'project_user')
                    ->withPivot('role_type')
                    ->withTimestamps();
    }

    // ============================================
    // ROLE CHECK HELPERS (Existing)
    // ============================================

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSupervisi()
    {
        return $this->role === 'supervisi';
    }

    public function isKaryawan()
    {
        return $this->role === 'karyawan';
    }

    public function isPerizinan()
    {
        return $this->role === 'perizinan';
    }

    public function isGuest()
    {
        return $this->role === 'guest';
    }

    public function isKabagPGB()
    {
        return $this->role === 'kabag_pgb';
    }

    // ============================================
    // ✅ NEW: PKJ HELPER FUNCTIONS
    // ============================================

    /**
     * Check if user is PKJ (Kabag PKJ or Staff PKJ)
     * Staff PKJ punya fungsi sama dengan Kabag PKJ
     * 
     * @return bool
     */
    public function isPKJ(): bool
    {
        return $this->role === 'perizinan' || 
               ($this->role === 'karyawan' && $this->bagian === 'PKJ');
    }

    /**
     * Check if user is Kabag PKJ (role perizinan)
     * 
     * @return bool
     */
    public function isKabagPKJ(): bool
    {
        return $this->role === 'perizinan';
    }

    /**
     * Check if user is Staff PKJ (karyawan with bagian PKJ)
     * 
     * @return bool
     */
    public function isStaffPKJ(): bool
    {
        return $this->role === 'karyawan' && $this->bagian === 'PKJ';
    }

    /**
     * Check if user can create projects
     * Supervisi, Kabag PGB, Kabag PKJ, dan Staff PKJ bisa buat project
     * 
     * @return bool
     */
    public function canCreateProject(): bool
    {
        return in_array($this->role, ['supervisi', 'kabag_pgb', 'perizinan']) ||
               ($this->role === 'karyawan' && $this->bagian === 'PKJ');
    }

    /**
     * Check if user can access employee menu
     * Supervisi, Kabag PGB, Kabag PKJ, dan Staff PKJ bisa akses
     * 
     * @return bool
     */
    public function canAccessEmployees(): bool
    {
        return in_array($this->role, ['supervisi', 'kabag_pgb', 'perizinan']) ||
               ($this->role === 'karyawan' && $this->bagian === 'PKJ');
    }

    /**
     * Check if user can export data
     * 
     * @return bool
     */
    public function canExport(): bool
    {
        return in_array($this->role, ['supervisi', 'kabag_pgb', 'perizinan']) ||
               ($this->role === 'karyawan' && $this->bagian === 'PKJ');
    }

    // ============================================
    // OTHER HELPERS
    // ============================================

    public function pendingRoleRequest()
    {
        return $this->roleRequests()->where('status', 'pending')->first();
    }

    public function hasPendingRoleRequest()
    {
        return $this->roleRequests()->where('status', 'pending')->exists();
    }

    public function latestRoleRequest()
    {
        return $this->roleRequests()->latest()->first();
    }

    public function isPicOf($projectId)
    {
        return $this->assignedProjects()
                    ->where('project_id', $projectId)
                    ->exists();
    }
}