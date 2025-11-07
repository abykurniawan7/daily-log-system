<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Models\Project;
use App\Models\Activity;
use App\Models\User; // ✅ TAMBAHKAN

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function boot(): void
    {
        $this->configureRateLimiting();

        // Explicit Route Model Binding by UUID
        Route::bind('project', function ($value) {
            return Project::where('uuid', $value)
                ->orWhere('id', $value) // Fallback untuk backward compatibility
                ->firstOrFail();
        });
        
        Route::bind('activity', function ($value) {
            return Activity::where('uuid', $value)
                ->orWhere('id', $value)
                ->firstOrFail();
        });
        
        // ✅ TAMBAHKAN: Binding for employee (User model)
        Route::bind('employee', function ($value) {
            return User::where('uuid', $value)
                ->orWhere('id', $value) // Fallback
                ->whereIn('role', ['perizinan', 'karyawan'])
                ->firstOrFail();
        });
        
        // ✅ TAMBAHKAN: Binding untuk id (generic)
        Route::bind('id', function ($value) {
            // Untuk route employee yang pakai {id}
            return User::where('uuid', $value)
                ->orWhere('id', $value)
                ->whereIn('role', ['perizinan', 'karyawan'])
                ->firstOrFail();
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}