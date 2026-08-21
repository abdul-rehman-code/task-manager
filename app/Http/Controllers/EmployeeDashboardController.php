<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Task Statistics
        $totalTasks = Task::where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('user_id', $user->id);
            })->count();
            
        $inProgressTasks = Task::where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('user_id', $user->id);
            })->where('status', 'in_progress')->count();
            
        $completedTasks = Task::where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('user_id', $user->id);
            })->where('status', 'completed')->count();
            
        $overdueTasks = Task::where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('user_id', $user->id);
            })
            ->where('status', '!=', 'completed')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->count();

        $pendingTasks = Task::where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('user_id', $user->id);
            })->where('status', 'pending')->count();

        // My Tasks (Paginated & Filtered)
        $statusFilter = request()->query('status');
        
        $tasksQuery = Task::where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('user_id', $user->id);
            });
            
        if ($statusFilter && in_array($statusFilter, ['pending', 'in_progress', 'blockage', 'completed'])) {
            if ($statusFilter === 'blockage') {
                $tasksQuery->where('is_blocked', true);
            } elseif ($statusFilter === 'pending') {
                $tasksQuery->where('status', 'pending')->where('is_blocked', false);
            } elseif ($statusFilter === 'in_progress') {
                $tasksQuery->where('status', 'in_progress')->where('is_blocked', false);
            } else {
                $tasksQuery->where('status', $statusFilter);
            }
        }
            
        $tasks = $tasksQuery->orderByRaw("CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC")
            ->paginate(6)
            ->withQueryString();

        // Upcoming Deadlines (Next 5 pending tasks)
        $upcomingDeadlines = Task::where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('user_id', $user->id);
            })
            ->where('status', '!=', 'completed')
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now())
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        // Recent Activity (Real data from user_activities)
        $recentActivity = \App\Models\UserActivity::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // Pass active timer
        $activeTimer = \App\Models\TimeLog::where('user_id', $user->id)
            ->whereNull('end_time')
            ->first();

        return view('employee.dashboard', compact(
            'user', 
            'totalTasks', 
            'pendingTasks',
            'inProgressTasks', 
            'completedTasks', 
            'overdueTasks', 
            'tasks', 
            'upcomingDeadlines', 
            'recentActivity',
            'activeTimer'
        ));
    }
    public function allTasks()
    {
        $user = Auth::user();
        $tasks = Task::where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('user_id', $user->id);
            })
            ->where('status', '!=', 'completed')
            ->orderByRaw("CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, due_date ASC")
            ->paginate(15);
            
        return view('employee.tasks', compact('user', 'tasks'));
    }

    public function history()
    {
        $user = Auth::user();
        $tasks = Task::where(function($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('user_id', $user->id);
            })
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
            
        return view('employee.history', compact('user', 'tasks'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('employee.profile', compact('user'));
    }

    public function settings()
    {
        $user = Auth::user();
        return view('employee.settings', compact('user'));
    }
}
