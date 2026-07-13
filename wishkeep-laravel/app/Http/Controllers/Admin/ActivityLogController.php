<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('admin.activity-log', ['logs' => $logs]);
    }
}
