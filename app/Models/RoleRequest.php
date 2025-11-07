<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requested_role',
        'requested_bagian',
        'deskripsi',
        'dokumen_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'admin_notes',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user who made the request
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the admin who reviewed the request (UTAMA)
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the admin who approved (ALIAS untuk compatibility)
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Alias untuk approver (backward compatibility)
     */
    public function approver()
    {
        return $this->approvedBy();
    }

    /**
     * Scope: Only pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Only approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Only rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if request is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if request is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if request is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Get status badge color (PENTING!)
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300',
            'approved' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300',
            'rejected' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300',
            default => 'bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-300',
        };
    }

    /**
     * Get role badge color classes (PENTING!)
     */
    public function getRoleBadgeClass()
    {
        return match($this->requested_role) {
            'supervisi' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300',
            'perizinan' => 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300',
            'karyawan' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300',
            default => 'bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-300',
        };
    }

    /**
     * Get bagian badge color classes (PENTING!)
     */
    public function getBagianBadgeClass()
    {
        return match($this->requested_bagian) {
            'PKJ' => 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300',
            'PGB' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300',
            default => 'bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-300',
        };
    }

    /**
     * Get formatted role label (PENTING!)
     */
    public function getRoleLabel()
    {
        return match($this->requested_role) {
            'supervisi' => 'Supervisi',
            'perizinan' => 'PKJ (Perizinan)',
            'karyawan' => 'PGB (Karyawan)',
            default => ucfirst($this->requested_role)
        };
    }

    /**
     * Get status color attribute (PENTING!)
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Format created date (helper method)
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M Y H:i');
    }

    /**
     * Get days since request was made
     */
    public function getDaysOldAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Check if request is urgent (more than 3 days old and still pending)
     */
    public function isUrgent()
    {
        return $this->isPending() && $this->days_old > 3;
    }
}