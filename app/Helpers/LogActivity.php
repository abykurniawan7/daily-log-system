<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogActivity
{
    /**
     * Create activity log
     * 
     * @param string $action (created, updated, deleted, etc)
     * @param string $description (human readable)
     * @param mixed $model (Eloquent model instance, optional)
     * @param array $properties (additional data, optional)
     * @return ActivityLog
     */
    public static function log(
        string $action, 
        string $description, 
        $model = null, 
        array $properties = []
    ) {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log created action
     */
    public static function created($model, string $description = null)
    {
        $desc = $description ?? "Menambah " . class_basename($model) . ": " . self::getModelName($model);
        
        return self::log('created', $desc, $model, [
            'attributes' => $model->getAttributes()
        ]);
    }

    /**
     * Log updated action
     */
    public static function updated($model, array $oldValues = [], string $description = null)
    {
        $desc = $description ?? "Mengubah " . class_basename($model) . ": " . self::getModelName($model);
        
        return self::log('updated', $desc, $model, [
            'old' => $oldValues,
            'new' => $model->getAttributes()
        ]);
    }

    /**
     * Log deleted action
     */
    public static function deleted($model, string $description = null)
    {
        $desc = $description ?? "Menghapus " . class_basename($model) . ": " . self::getModelName($model);
        
        return self::log('deleted', $desc, $model, [
            'attributes' => $model->getAttributes()
        ]);
    }

    /**
     * Log authentication (login/logout)
     */
    public static function auth(string $action)
    {
        $descriptions = [
            'login' => 'Login ke sistem',
            'logout' => 'Logout dari sistem',
            'failed_login' => 'Gagal login (password salah)',
        ];

        return self::log($action, $descriptions[$action] ?? $action);
    }

    /**
     * Get model display name
     */
    private static function getModelName($model): string
    {
        if (method_exists($model, 'getLogName')) {
            return $model->getLogName();
        }

        // Fallback ke attribute umum
        if (isset($model->nama_aktivitas)) {
            return $model->nama_aktivitas;
        }
        if (isset($model->nama_project)) {
            return $model->nama_project;
        }
        if (isset($model->name)) {
            return $model->name;
        }
        
        return "ID: " . $model->id;
    }
}