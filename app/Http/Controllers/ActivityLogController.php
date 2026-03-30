<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return Inertia::render('ActivityLogs', [
            'logs' => $logs
        ]);
    }

    public function download()
    {
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->get();
        
        $filename = "activity_logs_" . date('Y-m-d_H-i-s') . ".csv";
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Header row
        fputcsv($handle, ['ID', 'User', 'Action', 'Description', 'Module', 'IP Address', 'Timestamp']);

        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->id,
                $log->user->name ?? 'Unknown',
                $log->action,
                $log->description,
                $log->module,
                $log->ip_address,
                $log->created_at,
            ]);
        }

        fclose($handle);
        exit();
    }
}
