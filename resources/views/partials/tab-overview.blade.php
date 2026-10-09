<!-- Tab 1: Operations Command Center -->
<div id="tab-overview" class="tab-content space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 backdrop-blur-md">
            <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Total Sessions</span>
            <div class="text-3xl font-black text-white mt-1 font-['JetBrains_Mono']">{{ $totalSessions ?? 0 }}</div>
            <span class="text-[11px] text-slate-500 mt-1 block">Completed VR inspections</span>
        </div>

        <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 backdrop-blur-md">
            <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Average Final Score</span>
            <div class="text-3xl font-black text-amber-400 mt-1 font-['JetBrains_Mono']">{{ number_format($avgScore ?? 0, 0) }} pts</div>
            <span class="text-[11px] text-slate-500 mt-1 block">Cohort mean inspection points</span>
        </div>

        <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 backdrop-blur-md">
            <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Avg Completion Time</span>
            <div class="text-3xl font-black text-sky-400 mt-1 font-['JetBrains_Mono']">{{ number_format($avgTime ?? 0, 1) }}s</div>
            <span class="text-[11px] text-slate-500 mt-1 block">Mean warehouse scan velocity</span>
        </div>
    </div>

    <!-- Trainee Inspection Results Table -->
    <div class="rounded-2xl bg-white/[0.03] border border-white/10 backdrop-blur-md overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-white/10 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-bold tracking-wider uppercase text-amber-400">Trainee Session Records</h3>
                <p class="text-xs text-slate-400 mt-0.5">Live warehouse inspection logs and hazard detection telemetry</p>
            </div>
            <span class="text-xs font-mono bg-white/5 border border-white/10 text-slate-300 px-3 py-1 rounded-full">
                {{ $records->total() ?? 0 }} Total Entries
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-white/[0.02] border-b border-white/10 text-slate-400 text-xs uppercase tracking-wider">
                        <th class="p-3.5">Trainee Username</th>
                        <th class="p-3.5">Score</th>
                        <th class="p-3.5">Time</th>
                        <th class="p-3.5 min-w-[220px]">Hazards Identified (Found)</th>
                        <th class="p-3.5 min-w-[220px]">Hazards Overlooked (Missed)</th>
                        <th class="p-3.5">Evaluation</th>
                        <th class="p-3.5">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-300">
                    @forelse($records as $record)
                        <tr class="hover:bg-white/[0.03] transition-colors">
                            <td class="p-3.5 font-semibold text-white whitespace-nowrap">
                                {{ $record->username }}
                            </td>
                            <td class="p-3.5 font-bold text-amber-400 font-['JetBrains_Mono'] whitespace-nowrap">
                                {{ $record->score }} pts
                            </td>
                            <td class="p-3.5 font-mono text-slate-300 whitespace-nowrap">
                                {{ number_format($record->completion_time, 1) }}s
                            </td>

                            <!-- Found Hazards (Green Badges) -->
                            <td class="p-3.5">
                                <div class="flex flex-wrap gap-1.5">
                                    @if(!empty($record->found_hazards))
                                        @foreach($record->found_hazards as $found)
                                            <span class="inline-flex items-center gap-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 text-xs px-2.5 py-0.5 rounded-full font-medium">
                                                <svg class="w-3 h-3 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $found }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-xs text-slate-500 italic">None identified</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Missed Hazards (Red Badges) -->
                            <td class="p-3.5">
                                <div class="flex flex-wrap gap-1.5">
                                    @if(!empty($record->missed_hazards))
                                        @foreach($record->missed_hazards as $missed)
                                            <span class="inline-flex items-center gap-1 bg-rose-500/10 text-rose-400 border border-rose-500/25 text-xs px-2.5 py-0.5 rounded-full font-medium">
                                                <svg class="w-3 h-3 text-rose-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $missed }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs px-2.5 py-0.5 rounded-full font-semibold">
                                            ✓ Flawless Scan (Zero Missed)
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Performance Rating Badge -->
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full inline-block
                                    @if($record->performance_rating === 'Locked In') bg-emerald-500/15 text-emerald-300 border border-emerald-500/30
                                    @elseif($record->performance_rating === 'Great') bg-sky-500/15 text-sky-300 border border-sky-500/30
                                    @elseif($record->performance_rating === 'Valid Effort') bg-amber-500/15 text-amber-300 border border-amber-500/30
                                    @else bg-rose-500/15 text-rose-300 border border-rose-500/30 @endif">
                                    {{ $record->performance_rating }}
                                </span>
                            </td>

                            <td class="p-3.5 text-xs text-slate-400 whitespace-nowrap font-mono">
                                {{ $record->created_at->format('d M, H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                No trainee training sessions logged yet. Awaiting VR completions...
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="p-4 border-t border-white/10">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>