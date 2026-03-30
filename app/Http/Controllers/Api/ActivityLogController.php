<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'description' => 'nullable|string',
            'module' => 'nullable|string',
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => $request->action,
            'description' => $request->description,
            'module' => $request->module,
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['message' => 'Activity logged successfully']);
    }
}
