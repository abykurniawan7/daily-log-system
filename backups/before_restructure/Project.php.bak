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

    // ✅ METHOD 1: getRouteKeyName (untuk URL generation)
    public function getRouteKeyName()
    {
        return 'uuid';
    }
    
    // ✅ METHOD 2: resolveRouteBinding (untuk resolve dari URL)
    public function resolveRouteBinding($value, $field = null)
    {
        // Jika field di-specify, gunakan field tersebut
        if ($field) {
            return $this->where($field, $value)->firstOrFail();
        }
        
        // Coba resolve by UUID dulu
        $result = $this->where('uuid', $value)->first();
        
        // Fallback ke ID jika UUID tidak ketemu (backward compatibility)
        if (!$result && is_numeric($value)) {
            $result = $this->where('id', $value)->first();
        }
        
        if (!$result) {
            abort(404);
        }
        
        return $result;
    }

    // Relationships
    public function pemilikProject()
    {
        return $this->belongsTo(Division::class, 'pemilik_project_id');
    }

    public function picProyek()
    {
        return $this->belongsTo(User::class, 'pic_proyek_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'project_uuid', 'uuid');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}