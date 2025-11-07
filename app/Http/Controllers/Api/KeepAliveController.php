<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class KeepAliveController extends Controller
{
    public function ping(Request $request)
    {
        if (auth()->check()) {
            Session::put('last_activity_time', time());
            
            return response()->json([
                'success' => true,
                'message' => 'Session extended',
                'timestamp' => now()->toDateTimeString(),
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Not authenticated'
        ], 401);
    }
    
    public function status(Request $request)
    {
        if (auth()->check()) {
            $lastActivity = Session::get('last_activity_time', time());
            $timeout = config('session.lifetime') * 60;
            $elapsed = time() - $lastActivity;
            $remaining = max(0, $timeout - $elapsed);
            
            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id' => auth()->id(),
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role,
                ],
                'remaining_seconds' => $remaining,
            ]);
        }
        
        return response()->json(['authenticated' => false], 401);
    }
}