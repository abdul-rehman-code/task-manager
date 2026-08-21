<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Reports{{ $user ? ' — ' . $user->name : '' }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #1f2937; margin: 24px; background: #fff; }
        h1 { font-size: 20px; margin: 0 0 4px; color: #111827; }
        .meta { color: #6b7280; margin-bottom: 24px; font-size: 12px; }
        
        /* User Section Card */
        .user-card {
            border: 1px solid #d1fae5;
            border-radius: 12px;
            margin-bottom: 20px;
            overflow: hidden;
            background: #fff;
        }
        .user-header {
            background: #ecfdf5; /* Light green background for user name */
            padding: 12px 16px;
            border-bottom: 1px solid #a7f3d0;
            font-size: 14px;
            font-weight: bold;
            color: #065f46; /* Darker green text for the user name */
            display: flex;
            justify-content: space-between;
        }
        .user-meta-count {
            font-size: 11px;
            font-weight: normal;
            color: #047857;
        }

        /* Task Table Inside Card */
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { padding: 10px 16px; text-align: left; vertical-align: top; border-bottom: 1px solid #f3f4f6; }
        th { background: #f9fafb; color: #374151; font-weight: 600; text-transform: uppercase; font-size: 10px; letter-spacing: 0.05em; }
        tr:last-child td { border-bottom: none; }
        
        .badge { 
            background: #d1fae5;
            color: #065f46;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 999px;
            display: inline-block;
        }
        .date-text { color: #9ca3af; font-size: 11px; margin-top: 2px; }
        .task-title { font-weight: 600; color: #1f2937; }
        .task-desc { color: #6b7280; font-size: 11px; margin-top: 2px; }

        .no-data { text-align: center; padding: 32px; color: #9ca3af; font-size: 13px; border: 1px solid #e5e7eb; border-radius: 12px; }

        @media print {
            .no-print { display: none; }
            body { margin: 0; }
            .user-card { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <p class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #059669; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Print / Save as PDF</button>
    </p>

    <h1>{{ $user ? $user->name . ' — ' : '' }}Employee Reports</h1>
    <p class="meta">
        @if ($user)
            {{ $user->email }} &bull;
        @else
            All users &bull;
        @endif
        Period: {{ $periodLabel }} &bull;
        Total Tasks: {{ $tasks->count() }}
    </p>

    @php
        $groupedTasks = $tasks->groupBy(function($task) {
            return $task->assignee?->name ?? 'Unassigned';
        });
    @endphp

    @forelse ($groupedTasks as $userName => $userTasks)
        <div class="user-card">
            <div class="user-header">
                <span>{{ $userName }}</span>
                <span class="user-meta-count">{{ $userTasks->count() }} {{ $userTasks->count() === 1 ? 'Task' : 'Tasks' }}</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 35%;">Task Name</th>
                        <th style="width: 40%;">Details</th>
                        <th style="width: 25%;">Status / Completed At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($userTasks as $task)
                        <tr>
                            <td>
                                <div class="task-title">{{ $task->title }}</div>
                            </td>
                            <td>
                                <div class="task-desc">{{ trim(strip_tags((string) $task->description)) ?: '—' }}</div>
                            </td>
                            <td>
                                <span class="badge">Completed</span>
                                <div class="date-text">{{ $task->updated_at?->timezone('Asia/Karachi')->format('M d, Y h:i A') }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="no-data">
            No completed tasks for this period.
        </div>
    @endforelse

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>