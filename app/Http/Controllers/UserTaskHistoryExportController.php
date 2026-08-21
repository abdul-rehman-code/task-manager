<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserTaskHistoryExportController extends Controller
{
    public function pdf(Request $request): View
    {
        $authUser = $request->user();

        abort_unless(
            $authUser && ($authUser->isAdmin() || $authUser->isManager() || $authUser->isEmployee()),
            403
        );

        $period = $request->string('period')->toString() ?: 'today';
        $userId = $request->integer('user') ?: null;
        $tz = 'Asia/Karachi';

        $canFilterUsers = $authUser->isAdmin() || $authUser->isManager();

        if (! $canFilterUsers) {
            $userId = $authUser->id;
        }

        $query = Task::query()
            ->with('assignee')
            ->where('status', 'completed');

        if ($userId) {
            $query->where('assigned_to', $userId);
        }

        $query = match ($period) {
            'weekly' => $query->whereBetween('updated_at', [
                now($tz)->startOfWeek()->timezone('UTC'),
                now($tz)->endOfWeek()->timezone('UTC'),
            ]),
            'monthly' => $query->whereBetween('updated_at', [
                now($tz)->startOfMonth()->timezone('UTC'),
                now($tz)->endOfMonth()->timezone('UTC'),
            ]),
            'overall' => $query,
            default => $query->whereBetween('updated_at', [
                now($tz)->startOfDay()->timezone('UTC'),
                now($tz)->endOfDay()->timezone('UTC'),
            ]),
        };

        $periodLabel = match ($period) {
            'weekly' => 'This week',
            'monthly' => 'This month',
            'overall' => 'Overall',
            default => 'Daily',
        };

        $user = $userId ? User::query()->find($userId) : null;

        return view('filament.pages.user-task-history-pdf', [
            'user' => $user,
            'tasks' => $query->latest('updated_at')->get(),
            'periodLabel' => $periodLabel,
        ]);
    }
}
