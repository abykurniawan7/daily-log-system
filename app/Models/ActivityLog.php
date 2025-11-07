<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relationship ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that was logged (polymorphic)
     */
    public function loggable()
    {
        return $this->morphTo('model');
    }

    /**
     * Scope untuk filter by action
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope untuk filter by model type
     */
    public function scopeModelType($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope untuk filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate = null)
    {
        if ($endDate) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        return $query->whereDate('created_at', '>=', $startDate);
    }

    /**
     * Get action badge color
     */
    public function getActionColorAttribute()
    {
        return match($this->action) {
            'created' => 'bg-green-100 text-green-800',
            'updated' => 'bg-blue-100 text-blue-800',
            'deleted' => 'bg-red-100 text-red-800',
            'login' => 'bg-purple-100 text-purple-800',
            'logout' => 'bg-gray-100 text-gray-800',
            'approved' => 'bg-emerald-100 text-emerald-800',
            'rejected' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get action label
     */
    public function getActionLabelAttribute()
    {
        return match($this->action) {
            'created' => 'Menambah',
            'updated' => 'Mengubah',
            'deleted' => 'Menghapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'approved' => 'Menyetujui',
            'rejected' => 'Menolak',
            'reset_password' => 'Reset Password',
            default => ucfirst($this->action)
        };
    }

    /**
     * Get model name in Indonesian
     */
    public function getModelNameAttribute()
    {
        if (!$this->model_type) {
            return null;
        }

        return match($this->model_type) {
            'App\Models\Activity' => 'Aktivitas',
            'App\Models\Project' => 'Project',
            'App\Models\User' => 'User',
            'App\Models\RoleRequest' => 'Role Request',
            default => class_basename($this->model_type)
        };
    }
}