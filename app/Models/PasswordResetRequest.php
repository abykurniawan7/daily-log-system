<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // 'email', // DIHAPUS - ambil dari relasi user
        'reason',
        'status',
        'processed_at',
        'processed_by',
        'admin_response',
        'new_password',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    /**
     * Relationship ke User (yang request)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship ke Admin (yang memproses)
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Accessor untuk mendapatkan email dari user
     * Usage: $passwordResetRequest->email
     */
    public function getEmailAttribute()
    {
        return $this->user?->email;
    }
}