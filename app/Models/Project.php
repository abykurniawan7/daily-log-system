<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'tanggal_inisiasi',
        'target_implementasi',
        'nama_project',
        'urgensi',
        'sifat_project',
        'deskripsi',
        'pemilik_project_id',
        'pic_proyek_id',
        'pengawas_id', // ✅ NEW: Pengawas Project (Kadiv)
        'status',
        'user_id',
    ];

    protected $casts = [
        'tanggal_inisiasi' => 'date',
    ];

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

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function pemilikProject()
    {
        return $this->belongsTo(Division::class, 'pemilik_project_id');
    }

    public function picProyek()
    {
        return $this->belongsTo(User::class, 'pic_proyek_id');
    }

    /**
     * ✅ NEW: Pengawas Project (Kadiv/Supervisor)
     * Auto-assigned untuk setiap project yang dibuat (bukan oleh Kadiv)
     */
    public function pengawas()
    {
        return $this->belongsTo(User::class, 'pengawas_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'project_uuid', 'uuid');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Multi-PIC Relationships
     */
    public function pics()
    {
        return $this->belongsToMany(User::class, 'project_user')
                    ->withPivot('role_type')
                    ->withTimestamps();
    }

    public function primaryPic()
    {
        return $this->pics()->first();
    }

    public function syncPics(array $userIds)
    {
        $this->pics()->sync($userIds);
    }

    public function addPic($userId)
    {
        if (!$this->pics()->where('user_id', $userId)->exists()) {
            $this->pics()->attach($userId, ['role_type' => 'pic']);
        }
    }

    public function removePic($userId)
    {
        $this->pics()->detach($userId);
    }

    public function hasPic($userId)
    {
        return $this->pics()->where('user_id', $userId)->exists();
    }

    /**
     * ✅ NEW: Helper - Check if project has Pengawas
     */
    public function hasPengawas()
    {
        return !is_null($this->pengawas_id);
    }

    /**
     * ✅ NEW: Helper - Check if user is the Pengawas
     */
    public function isPengawas($userId)
    {
        return $this->pengawas_id === $userId;
    }
}