<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RoleRequestController as AdminRoleRequestController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\RoleRequestController;
use App\Http\Controllers\Api\KeepAliveController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

// ============================================================================
// PUBLIC ROUTES (No authentication required)
// ============================================================================

Route::get('/', function () {
    return view('welcome');
});

// Language Switcher - Available for both guest and authenticated users
Route::get('/set-locale/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    
    return redirect()->back();
})->name('set-locale');

// ============================================================================
// AUTHENTICATED ROUTES
// ============================================================================

Route::middleware('auth')->group(function () {
    
    // ========================================
    // SESSION KEEP-ALIVE ROUTES
    // ========================================
    Route::post('/api/keep-alive', [KeepAliveController::class, 'ping'])
        ->name('api.keep-alive');
    Route::get('/api/session/status', [KeepAliveController::class, 'status'])
        ->name('api.session-status');

    Route::post('/session/force-logout', function() {
        Auth::logout();
        Session::flush();
        Session::regenerate();
        
        return response()->json([
            'status' => 'logged_out',
            'redirect' => route('login'),
            'message' => 'Session timeout'
        ]);
    })->name('session.force-logout');
    
    // ========================================
    // DASHBOARD
    // ========================================
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    // ========================================
    // EXPORT ROUTES - MUST BE ABOVE RESOURCE ROUTES!
    // ========================================
    
    // Projects Export (Supervisi & PKJ)
    // Route::get('/projects/export-preview', [ProjectController::class, 'exportPreview'])
    //     ->name('projects.export-preview');
    // Route::get('/projects/export-pdf', [ProjectController::class, 'exportPdf'])
    //     ->name('projects.export-pdf');

    // Single Project Export (dari detail page)
    Route::get('/projects/{project:uuid}/export-preview', [ProjectController::class, 'singleProjectExportPreview'])
        ->name('projects.single-export-preview');
    Route::get('/projects/{project:uuid}/export-pdf', [ProjectController::class, 'singleProjectExportPdf'])
        ->name('projects.single-export-pdf');
    
    // Activities Export (Supervisi only)
    Route::middleware('supervisi')->group(function () {
        Route::get('/activities/export-preview', [ActivityController::class, 'exportPreview'])
            ->name('activities.export-preview');
        Route::get('/activities/export-pdf', [ActivityController::class, 'exportPdf'])
            ->name('activities.export-pdf');
    });
    
    // ========================================
    // PROJECTS CRUD
    // ========================================
    Route::resource('projects', ProjectController::class);
    
    // ========================================
    // ACTIVITIES ROUTES
    // ========================================
    
    // My Activities Page
    Route::get('/activities/my-activities', [ActivityController::class, 'myActivities'])
        ->name('activities.my-activities');
    
    // My Activities Export (Supervisi & PKJ only)
    Route::get('/activities/my-activities-export-preview', [ActivityController::class, 'myActivitiesExportPreview'])
        ->name('activities.my-activities-export-preview');
    Route::get('/activities/my-activities-export-pdf', [ActivityController::class, 'myActivitiesExportPdf'])
        ->name('activities.my-activities-export-pdf');
    
    // Timeline
    Route::get('/activities/timeline', [ActivityController::class, 'timeline'])
        ->name('activities.timeline');
    
    // Activities CRUD
    Route::resource('activities', ActivityController::class);

    // Activities JSON API
    Route::get('/activities/{activity}/json', function(App\Models\Activity $activity) {
        // Authorization check
        $user = auth()->user();
        if ($user->role !== 'supervisi') {
            // Karyawan/Perizinan: cek apakah bisa lihat (PGB bisa lihat PKJ, vice versa)
            $allowedBagian = [$user->bagian];
            if ($user->bagian === 'PGB') {
                $allowedBagian[] = 'PKJ'; // PGB bisa lihat PKJ
            } elseif ($user->bagian === 'PKJ') {
                $allowedBagian[] = 'PGB'; // PKJ bisa lihat PGB
            }
            
            $activityOwner = $activity->user;
            if (!in_array($activityOwner->bagian, $allowedBagian)) {
                abort(403, 'Unauthorized access');
            }
        }
        
        $activity->load(['project.pemilikProject', 'project.picProyek', 'user']);
        
        return response()->json([
            'id' => $activity->id,
            'uuid' => $activity->uuid,
            'nama_aktivitas' => $activity->nama_aktivitas,
            'jenis_kegiatan' => $activity->jenis_kegiatan,
            'status' => $activity->status,
            'tanggal_mulai' => $activity->tanggal_mulai->format('d F Y'),
            'tanggal_selesai' => $activity->tanggal_selesai ? $activity->tanggal_selesai->format('d F Y') : null,
            'deskripsi' => $activity->deskripsi,
            'lampiran' => $activity->lampiran ? basename($activity->lampiran) : null,
            'lampiran_url' => $activity->lampiran ? asset('storage/' . $activity->lampiran) : null,
            'lampiran_link' => $activity->lampiran_link,
            'project' => [
                'nama' => $activity->project->nama_project,
                'pemilik' => $activity->project->pemilikProject->nama_divisi,
                'pic' => $activity->project->picProyek->name,
            ],
            'user' => [
                'name' => $activity->user->name,
                'email' => $activity->user->email,
                'bagian' => $activity->user->bagian,
            ],
            'can_edit' => $user->role !== 'supervisi' 
                       && $user->id === $activity->user_id 
                       && $user->bagian === $activity->user->bagian,
        ]);
    })->name('activities.json');
    
    // ========================================
    // EMPLOYEES (Supervisi only)
    // ========================================
    Route::middleware('supervisi')->group(function () {
        Route::get('/employees', [EmployeeController::class, 'index'])
            ->name('employees.index');
        
        Route::get('/employees/export-all-pdf', [EmployeeController::class, 'exportAllPdf'])
            ->name('employees.export.all-pdf');

        Route::get('/employees/{user}/export-preview', [EmployeeController::class, 'exportPreview'])
            ->name('employees.export-preview');

        Route::get('/employees/{user}/export-pdf', [EmployeeController::class, 'exportPdf'])
            ->name('employees.export-pdf');

        Route::get('/employees/{user:uuid}', [EmployeeController::class, 'show'])
            ->name('employees.show');
    });

    // ========================================
    // ROLE REQUEST ROUTES (Guest User)
    // ========================================
    Route::get('/role-requests/create', [RoleRequestController::class, 'create'])
        ->name('role-requests.create');
    Route::post('/role-requests', [RoleRequestController::class, 'store'])
        ->name('role-requests.store');
    Route::delete('/role-requests/{roleRequest}', [RoleRequestController::class, 'cancel'])
        ->name('role-requests.cancel');

    // ========================================
    // ADMIN ROUTES
    // ========================================
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        
        // Admin Dashboard
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])
            ->name('dashboard');

        // User Management
        Route::get('/users', [UserManagementController::class, 'index'])
            ->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])
            ->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])
            ->name('users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
            ->name('users.edit');
        Route::patch('/users/{user}', [UserManagementController::class, 'update'])
            ->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])
            ->name('users.destroy');
        Route::get('/users/{user}/reset-password', [UserManagementController::class, 'showResetPasswordForm'])
            ->name('users.reset-password');
        Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])
            ->name('users.reset-password.update');
        
        // Role Request Management
        Route::get('/role-requests', [AdminRoleRequestController::class, 'index'])
            ->name('role-requests.index');
        Route::get('/role-requests/{roleRequest}', [AdminRoleRequestController::class, 'show'])
            ->name('role-requests.show');
        Route::post('/role-requests/{roleRequest}/approve', [AdminRoleRequestController::class, 'approve'])
            ->name('role-requests.approve');
        Route::post('/role-requests/{roleRequest}/reject', [AdminRoleRequestController::class, 'reject'])
            ->name('role-requests.reject');
        Route::post('/role-requests/bulk-approve', [AdminRoleRequestController::class, 'bulkApprove'])
            ->name('role-requests.bulk-approve');
        Route::delete('/role-requests/{roleRequest}', [AdminRoleRequestController::class, 'destroy'])
            ->name('role-requests.destroy');

        // Activity Logs Management
        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index');
            Route::get('/{activityLog}', [ActivityLogController::class, 'show'])->name('show');
            Route::get('/export/pdf', [ActivityLogController::class, 'exportPdf'])->name('export-pdf');
            Route::post('/clear-old', [ActivityLogController::class, 'clearOldLogs'])->name('clear-old');
        });
    });
    
    // ========================================
    // API ENDPOINTS
    // ========================================
    Route::get('/api/projects-per-user', [DashboardController::class, 'projectsPerUser']);
    Route::get('/api/projects-per-division', [DashboardController::class, 'projectsPerDivision']);
    Route::get('/api/activities-status-distribution', [DashboardController::class, 'activitiesStatusDistribution']);
    Route::get('/api/activities-per-month', [DashboardController::class, 'activitiesPerMonth']);
    Route::get('/api/employee-activities', [DashboardController::class, 'employeeActivities']);
    Route::get('/api/project-urgency-distribution', [DashboardController::class, 'projectUrgencyDistribution']);
    Route::get('/api/employees/{employee}/chart-data', [EmployeeController::class, 'performanceData']);
    
    // ========================================
    // PROFILE MANAGEMENT
    // ========================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ========================================
    // LOGOUT
    // ========================================
    Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// ============================================================================
// AUTH ROUTES (Login, Register, Password Reset, etc.)
// ============================================================================
require __DIR__.'/auth.php';