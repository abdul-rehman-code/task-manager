<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Task;

class TodayTasksKanbanWidget extends Widget
{
    protected static string $view = 'filament.widgets.today-tasks-kanban';
    
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public string $filter = 'today';

    public function setFilter(string $filter)
    {
        $this->filter = $filter;
    }

    protected function getViewData(): array
    {
        $query = Task::with('assignee');

        if ($this->filter === 'today') {
            $query->where(function($q) {
                $q->whereDate('created_at', today())
                  ->orWhereDate('due_date', today());
            });
            $heading = "Today's Tasks";
        } elseif ($this->filter === 'weekly') {
            $query->where(function($q) {
                $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                  ->orWhereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()]);
            });
            $heading = "This Week's Tasks";
        } elseif ($this->filter === 'monthly') {
            $query->where(function($q) {
                $q->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                  ->orWhereBetween('due_date', [now()->startOfMonth(), now()->endOfMonth()]);
            });
            $heading = "This Month's Tasks";
        }

        $tasks = $query->get();

        return [
            'heading' => $heading,
            'kanbanColumns' => [
                'pending' => [
                    'title' => 'Pending',
                    'color_bg' => 'bg-slate-50',
                    'color_border' => 'border-slate-300',
                    'color_text' => 'text-slate-700',
                    'icon' => 'heroicon-o-clock',
                    'tasks' => $tasks->where('status', 'pending')->where('is_blocked', false),
                ],
                'in_progress' => [
                    'title' => 'In Progress',
                    'color_bg' => 'bg-blue-50',
                    'color_border' => 'border-blue-400',
                    'color_text' => 'text-blue-700',
                    'icon' => 'heroicon-o-play-circle',
                    'tasks' => $tasks->where('status', 'in_progress')->where('is_blocked', false),
                ],
                'blockage' => [
                    'title' => 'Blocked / Issues',
                    'color_bg' => 'bg-rose-50',
                    'color_border' => 'border-rose-400',
                    'color_text' => 'text-rose-700',
                    'icon' => 'heroicon-o-exclamation-triangle',
                    'tasks' => $tasks->where('is_blocked', true),
                ],
                'completed' => [
                    'title' => 'Completed',
                    'color_bg' => 'bg-emerald-50',
                    'color_border' => 'border-emerald-400',
                    'color_text' => 'text-emerald-700',
                    'icon' => 'heroicon-o-check-circle',
                    'tasks' => $tasks->where('status', 'completed'),
                ],
            ]
        ];
    }
}
