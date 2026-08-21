<x-filament-widgets::widget>
    <div 
        x-data="{ isFullscreen: false, zoomLevel: 100 }"
        class="rounded-xl bg-white transition-all flex flex-col" 
        :class="isFullscreen ? 'fixed inset-4 z-50 shadow-2xl h-[calc(100vh-2rem)]' : ''"
        style="border: 1px solid #e2e8f0;"
    >
        <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between" style="border-bottom: 1px solid #e2e8f0;">
            <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                    {{ $heading }}
                </h2>
                
                <div class="flex items-center gap-1 ml-2 border rounded-md p-1 bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                    <button type="button" @click="zoomLevel = Math.max(50, zoomLevel - 10)" class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded text-gray-600 dark:text-gray-300">
                        <x-heroicon-m-minus class="w-3 h-3"/>
                    </button>
                    <span class="text-[10px] font-semibold text-gray-600 dark:text-gray-300 w-7 text-center" x-text="zoomLevel + '%'"></span>
                    <button type="button" @click="zoomLevel = Math.min(150, zoomLevel + 10)" class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded text-gray-600 dark:text-gray-300">
                        <x-heroicon-m-plus class="w-3 h-3"/>
                    </button>
                </div>
                
                <button type="button" @click="isFullscreen = !isFullscreen" class="p-1.5 border rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 shadow-sm ml-1">
                    <x-heroicon-m-arrows-pointing-out class="w-4 h-4" x-show="!isFullscreen" />
                    <x-heroicon-m-x-mark class="w-4 h-4" x-show="isFullscreen" style="display: none;" />
                </button>
            </div>

            <div class="tsm-controls">
                <div class="inline-flex rounded-lg p-1" style="background: #f1f5f9;">
                    @foreach (['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $mode => $label)
                        <button
                            type="button"
                            wire:click="setViewMode('{{ $mode }}')"
                            class="rounded-md px-3 py-1.5 text-xs font-semibold"
                            style="{{ $viewMode === $mode ? 'background:#fff;color:#4338ca;box-shadow:0 1px 2px rgba(0,0,0,.08);' : 'color:#64748b;background:transparent;' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <label class="tsm-field">
                    <span>User Filter</span>
                    @php
                        $selectedEmployeeName = $employees->firstWhere('id', $selectedUserId)?->name ?? 'All Users';
                    @endphp
                    <div class="tsm-user-filter" x-data="{ open: false }">
                        <button type="button" class="tsm-filter-button" @click="open = !open" @click.away="open = false">
                            <span class="truncate">{{ $selectedEmployeeName }}</span>
                            <x-heroicon-m-chevron-down class="h-4 w-4 shrink-0" />
                        </button>
                        <div x-cloak x-show="open" x-transition class="tsm-user-options">
                            <button type="button" wire:click="setSelectedUser(null)" @click="open = false">All Users</button>
                        @foreach($employees as $emp)
                            <button type="button" wire:click="setSelectedUser({{ $emp->id }})" @click="open = false">{{ $emp->name }}</button>
                        @endforeach
                        </div>
                    </div>
                </label>

                <label class="tsm-field">
                    <span>Date</span>
                    <input
                        type="date"
                        max="{{ $maxDate }}"
                        value="{{ $selectedDate }}"
                        wire:change="setDate($event.target.value)"
                    >
                </label>
            </div>
        </div>

        <div
            class="tsm-scroll"
            :style="isFullscreen ? 'max-height: none; flex: 1;' : ''"
            wire:key="schedule-grid-{{ $viewMode }}-{{ $selectedDate }}-{{ $selectedUserId ?? 'all' }}"
        >
            @php
                $empCount = max(1, $columns->count());
            @endphp

            <div
                class="tsm-grid"
                :style="`zoom: ${zoomLevel/100}; --emp-count: {{ $empCount }}; min-width: {{ 108 + ($empCount * 156) }}px;`"
            >
                <div class="tsm-cell tsm-corner">{{ $cornerLabel }}</div>

                @forelse ($columns as $column)
                    <div class="tsm-cell tsm-colhead">{{ $column->name }}</div>
                @empty
                    <div class="tsm-cell tsm-colhead" style="color:#94a3b8;">No columns found</div>
                @endforelse

                @foreach ($rows as $row)
                    <div class="tsm-cell tsm-rowhead">{{ $row['label'] }}</div>

                    @forelse ($columns as $column)
                        @php $items = $row['cells'][$column->id] ?? []; @endphp
                        <div class="tsm-cell tsm-data" x-data="{ hasActive: false }" :style="hasActive ? 'z-index: 50;' : ''">
                            @foreach ($items as $item)
                                @if ($item['is_break'])
                                    <div style="padding:4px; text-align:center; font-size:11px; font-style:italic; color:#94a3b8;">
                                        {{ $item['title'] }}
                                    </div>
                                @else
                                    <div
                                        x-data="{ showTooltip: false, isClicked: false }"
                                        @mouseenter="showTooltip = true; hasActive = true"
                                        @mouseleave="if(!isClicked) { showTooltip = false; hasActive = false; }"
                                        @click="isClicked = !isClicked; showTooltip = true; hasActive = true"
                                        @click.away="isClicked = false; showTooltip = false; hasActive = false"
                                        @if(\App\Filament\Pages\UserTaskHistory::canAccess())
                                            onclick="window.location.href='{{ \App\Filament\Pages\UserTaskHistory::getUrl() }}?user={{ $item['userId'] }}&period=daily'"
                                        @endif
                                        class="relative cursor-pointer"
                                        style="background: {{ $item['tone']['bg'] }}; color: {{ $item['tone']['text'] }}; border: 1px solid {{ $item['tone']['border'] }}; border-radius: 6px; padding: 6px 8px; font-size: 11px; font-weight: 600; line-height: 1.3;"
                                    >
                                        {{ $item['title'] }}

                                        @if($item['is_blocked'] && !empty($item['description']))
                                            <div class="mt-1.5 p-1.5 rounded bg-white/50 border border-rose-200 text-rose-800 text-[10px] font-normal leading-tight">
                                                {{ Str::limit($item['description'], 60) }}
                                            </div>
                                        @endif

                                        <div
                                            x-show="showTooltip"
                                            x-transition
                                            x-cloak
                                            style="display: none;"
                                            class="absolute left-1/2 -translate-x-1/2 top-full mt-2 w-56 z-[9999] bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-3 text-left cursor-default"
                                            @click.stop
                                        >
                                            <div class="font-bold text-xs text-gray-900 dark:text-gray-100 mb-1 leading-tight" style="font-family: inherit;">{{ $item['title'] }}</div>
                                            <div class="text-[11px] text-gray-500 mb-2 font-medium space-y-1">
                                                <div>Started: {{ $item['started_at'] }}</div>
                                                <div>{{ $item['expected_by'] }}</div>
                                                @if(!empty($item['estimated_minutes']))
                                                    <div class="text-indigo-600 dark:text-indigo-400">Est: {{ $item['estimated_minutes'] }} mins</div>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-gray-600 dark:text-gray-400 font-normal whitespace-pre-wrap leading-relaxed">
                                                {{ !empty($item['description']) ? $item['description'] : 'No description provided.' }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @empty
                        <div class="tsm-cell tsm-data" style="color:#94a3b8; font-size:13px; padding:12px;">
                            Add employees to populate this schedule.
                        </div>
                    @endforelse
                @endforeach
            </div>
        </div>
    </div>

    <style>
        .tsm-field {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            min-width: 0;
        }
        .tsm-controls {
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            gap: 0.75rem;
        }
        .tsm-field span {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #64748b;
        }
        .tsm-field input[type="date"],
        .tsm-filter-button {
            width: 150px;
            border-radius: 0.5rem;
            border: 1px solid #cbd5e1;
            background: #fff;
            padding: 0.4rem 0.7rem;
            font-size: 12px;
            font-weight: 600;
            color: #0f172a;
        }
        .tsm-user-filter { position: relative; }
        .tsm-filter-button {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            text-align: left;
        }
        .tsm-user-options {
            position: absolute;
            z-index: 60;
            top: calc(100% + 0.25rem);
            left: 0;
            width: 100%;
            max-height: 16rem;
            overflow-y: auto;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            background: #fff;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
        }
        .tsm-user-options button {
            display: block;
            width: 100%;
            padding: 0.5rem 0.7rem;
            overflow: hidden;
            color: #0f172a;
            font-size: 12px;
            font-weight: 600;
            text-align: left;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .tsm-user-options button:hover { background: #eef2ff; color: #4338ca; }
        .tsm-scroll {
            position: relative;
            isolation: isolate;
            max-height: 640px;
            min-height: 280px;
            overflow: auto;
            background: #fff;
        }
        .tsm-scroll::-webkit-scrollbar { height: 8px; width: 8px; }
        .tsm-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
        .tsm-grid {
            display: grid;
            grid-template-columns: 108px repeat(var(--emp-count), minmax(156px, 1fr));
        }
        .tsm-cell {
            border-right: 1px solid #cbd5e1 !important;
            border-bottom: 1px solid #cbd5e1 !important;
            min-height: 48px;
            box-sizing: border-box;
        }
        .tsm-colhead,
        .tsm-data {
            border-left: 1px solid #cbd5e1 !important;
        }
        .tsm-scroll {
            -webkit-overflow-scrolling: touch;
        }
        @media (max-width: 640px) {
            .tsm-controls {
                display: grid;
                width: 100%;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.5rem;
            }
            .tsm-controls > .inline-flex { grid-column: span 2 / span 2; justify-self: start; }
            .tsm-field input[type="date"],
            .tsm-filter-button { width: 100%; min-width: 0; }
            .tsm-user-options { right: 0; left: auto; width: 100%; max-width: 100%; }
            .tsm-grid {
                grid-template-columns: 76px repeat(var(--emp-count), minmax(128px, 1fr));
            }
            .tsm-scroll {
                max-height: 70vh;
                min-height: 220px;
            }
            .tsm-colhead,
            .tsm-rowhead,
            .tsm-data {
                font-size: 12px;
                padding: 8px;
            }
        }
        .tsm-corner {
            position: sticky;
            left: 0;
            top: 0;
            z-index: 40;
            background: #FFB6C1;
            color: #831843;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            padding: 10px 12px;
        }
        .tsm-colhead {
            position: sticky;
            top: 0;
            z-index: 30;
            background: #e5e7eb;
            color: #334155;
            font-weight: 600;
            font-size: 13px;
            display: flex;
            align-items: center;
            padding: 10px 12px;
            box-shadow: 0 1px 0 #d1d5db;
        }
        .tsm-rowhead {
            position: sticky;
            left: 0;
            z-index: 20;
            background: #FFB6C1;
            color: #831843;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            padding: 8px 12px;
            white-space: nowrap;
        }
        .tsm-data {
            position: relative;
            z-index: 1;
            padding: 6px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: #fff;
        }
        .dark .tsm-scroll,
        .dark .tsm-data { background: #111827; }
        .dark .tsm-colhead { background: #374151; color: #e5e7eb; }
        .dark .tsm-cell { border-color: rgba(255,255,255,0.12); }
    </style>
</x-filament-widgets::widget>
