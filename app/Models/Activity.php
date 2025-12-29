<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', // ✅ TAMBAHKAN
        'project_id', // ✅ Tetap ada untuk compatibility
        'project_uuid', // ✅ TAMBAHKAN
        'placeholder_project_name',
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'nama_aktivitas',
        'jenis_kegiatan',
        'status',
        'deskripsi',
        'lampiran',
        'lampiran_link',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // ✅ AUTO-GENERATE UUID
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            
            // ✅ Auto-set project_uuid dari project_id jika ada
            if (!empty($model->project_id) && empty($model->project_uuid)) {
                $project = Project::find($model->project_id);
                if ($project) {
                    $model->project_uuid = $project->uuid;
                }
            }
        });
    }

    // ✅ ROUTE KEY pakai UUID
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_uuid', 'uuid'); // ✅ UBAH: pakai UUID
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}