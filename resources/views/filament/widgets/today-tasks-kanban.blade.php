<x-filament-widgets::widget>
    <x-filament::section>
        <div class="px-2 pt-2 sm:px-4 sm:pt-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $heading }}
                </h2>
                
                <div class="flex flex-wrap items-center gap-4">
                    <div class="inline-flex rounded-lg p-1 bg-slate-100 dark:bg-gray-800">
                        @foreach (['today' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $mode => $label)
                            <button
                                type="button"
                                wire:click="setFilter('{{ $mode }}')"
                                class="rounded-md px-3 py-1.5 text-xs font-semibold transition-colors"
                                style="{{ $filter === $mode ? 'background:#fff;color:#4338ca;box-shadow:0 1px 2px rgba(0,0,0,.08);' : 'color:#64748b;background:transparent;' }}"
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <x-filament::icon icon="heroicon-o-calendar" class="w-5 h-5" />
                        {{ now()->format('M d, Y') }}
                    </div>
                </div>
            </div>

        @php
            $columnStyles = [
                'pending' => [
                    'col_bg'      => 'background:#FEFCE8;',
                    'header_bg'   => 'background:#FEF08A;',
                    'header_text' => 'color:#713F12;',
                    'badge_bg'    => 'background:#FACC15; color:#713F12;',
                    'avatar_bg'   => 'background:#FEF08A; color:#713F12;',
                ],
                'in_progress' => [
                    'col_bg'      => 'background:#EFF6FF;',
                    'header_bg'   => 'background:#BFDBFE;',
                    'header_text' => 'color:#1E3A8A;',
                    'badge_bg'    => 'background:#3B82F6; color:#fff;',
                    'avatar_bg'   => 'background:#BFDBFE; color:#1E3A8A;',
                ],
                'blockage' => [
                    'col_bg'      => 'background:#FFF1F2;',
                    'header_bg'   => 'background:#FECDD3;',
                    'header_text' => 'color:#881337;',
                    'badge_bg'    => 'background:#F43F5E; color:#fff;',
                    'avatar_bg'   => 'background:#FECDD3; color:#881337;',
                ],
                'completed' => [
                    'col_bg'      => 'background:#F0FDF4;',
                    'header_bg'   => 'background:#BBF7D0;',
                    'header_text' => 'color:#14532D;',
                    'badge_bg'    => 'background:#22C55E; color:#fff;',
                    'avatar_bg'   => 'background:#BBF7D0; color:#14532D;',
                ],
            ];
        @endphp

        <div class="flex flex-col md:flex-row gap-4 overflow-x-auto pb-4">
            @foreach($kanbanColumns as $key => $column)
                @php $s = $columnStyles[$key] ?? $columnStyles['pending']; @endphp

                <div class="flex-1 min-w-[260px] rounded-xl p-4 flex flex-col max-h-[600px]"
                     style="{{ $s['col_bg'] }}">

                    {{-- Column Header --}}
                    <div class="flex items-center justify-between mb-4 pb-2 rounded-lg px-3 py-2"
                         style="{{ $s['header_bg'] }}">
                        <div class="flex items-center gap-2">
                            <x-filament::icon
                                icon="{{ $column['icon'] }}"
                                class="w-5 h-5"
                                style="{{ $s['header_text'] }}" />
                            <h3 class="font-bold text-sm" style="{{ $s['header_text'] }}">
                                {{ $column['title'] }}
                            </h3>
                        </div>
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full"
                              style="{{ $s['badge_bg'] }}">
                            {{ $column['tasks']->count() }}
                        </span>
                    </div>

                    {{-- Tasks List --}}
                    <div class="flex-1 overflow-y-auto space-y-3 pr-1 custom-scrollbar">
                        @forelse($column['tasks'] as $task)
                            <div class="bg-white dark:bg-gray-900 rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow">

                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-xs font-medium text-gray-400">#{{ $task->id }}</span>
                                    @if($task->due_date)
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded"
                                              style="{{ $task->due_date < now() && $task->status !== 'completed' ? 'background:#FEE2E2;color:#B91C1C;' : 'background:#F1F5F9;color:#64748B;' }}">
                                            {{ $task->due_date->format('m/d/Y') }}
                                        </span>
                                    @endif
                                </div>

                                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2 leading-tight">
                                    {{ $task->title }}
                                </h4>

                                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold overflow-hidden shrink-0"
                                             style="{{ $s['avatar_bg'] }}">
                                            @if($task->assignee && $task->assignee->avatar)
                                                <img src="{{ asset('storage/' . $task->assignee->avatar) }}" class="w-full h-full object-cover">
                                            @elseif($task->assignee)
                                                {{ substr($task->assignee->name, 0, 1) }}
                                            @else
                                                ?
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500 truncate max-w-[100px]">
                                            {{ $task->assignee->name ?? 'Unassigned' }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-1 text-gray-400">
                                        <x-filament::icon icon="heroicon-o-chat-bubble-left" class="w-3.5 h-3.5" />
                                        <span class="text-[10px]">0</span>
                                    </div>
                                </div>

                            </div>
                        @empty
                            <div class="text-center py-8">
                                <span class="text-sm text-gray-400">No tasks here</span>
                            </div>
                        @endforelse
                    </div>

                </div>
            @endforeach
        </div>

        <style>
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
            .dark .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #475569; }
        </style>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>