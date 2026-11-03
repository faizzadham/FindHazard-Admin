<!-- Tab 1: Operations Command Center -->
<div id="tab-overview" class="tab-content space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 backdrop-blur-md animate-fade-in-up delay-1 hover:border-amber-500/30 transition-all duration-300">
            <span class="text-sm uppercase tracking-wider text-slate-300 font-bold">Total Trainees</span>
            <div class="text-3xl sm:text-4xl font-black text-white mt-1 font-['JetBrains_Mono']" data-counter="true" data-target="{{ $totalSessions ?? 0 }}" data-duration="900">{{ $totalSessions ?? 0 }}</div>
            <span class="text-xs text-slate-400 mt-1.5 block font-medium">Completed VR inspections</span>
        </div>

        <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 backdrop-blur-md flex items-center justify-between animate-fade-in-up delay-2 hover:border-amber-500/30 transition-all duration-300">
            <div>
                <span class="text-sm uppercase tracking-wider text-slate-300 font-bold">Average Final Score</span>
                <div class="text-3xl sm:text-4xl font-black text-amber-400 mt-1 font-['JetBrains_Mono']" data-counter="true" data-target="{{ number_format($avgScore ?? 0, 1) }}" data-decimals="1" data-suffix="%" data-duration="1200">{{ number_format($avgScore ?? 0, 1) }}%</div>
                <span class="text-xs text-slate-400 mt-1.5 block font-medium">Mean inspection percentage</span>
            </div>
            <!-- Visual Score Ring -->
            <div class="relative w-14 h-14 shrink-0 flex items-center justify-center">
                <svg class="w-14 h-14 -rotate-90 transform" viewBox="0 0 36 36">
                    <path
                        class="text-white/10"
                        stroke="currentColor"
                        stroke-width="3.2"
                        fill="none"
                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                    @php
                        $avgScVal = max(0, min(100, $avgScore ?? 0));
                        $avgScOffset = 100 - $avgScVal;
                        $avgScColor = $avgScVal >= 80 ? 'text-amber-400' : ($avgScVal >= 60 ? 'text-yellow-400' : 'text-rose-400');
                    @endphp
                    <path
                        class="{{ $avgScColor }} svg-score-ring transition-all duration-1000 ease-out"
                        stroke="currentColor"
                        stroke-width="3.5"
                        stroke-dasharray="100, 100"
                        stroke-dashoffset="{{ $avgScOffset }}"
                        data-target-offset="{{ $avgScOffset }}"
                        stroke-linecap="round"
                        fill="none"
                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                </svg>
                <span class="absolute text-xs font-mono font-black text-white">
                    {{ (int) floor($avgScore ?? 0) }}%
                </span>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 backdrop-blur-md animate-fade-in-up delay-3 hover:border-amber-500/30 transition-all duration-300">
            <span class="text-sm uppercase tracking-wider text-slate-300 font-bold">Avg Completion Time</span>
            <div class="text-3xl sm:text-4xl font-black text-sky-400 mt-1 font-['JetBrains_Mono']">{{ floor(($avgTime ?? 0) / 60) }}m {{ sprintf('%02ds', round(fmod($avgTime ?? 0, 60))) }}</div>
            <span class="text-xs text-slate-400 mt-1.5 block font-medium">Average warehouse inspection time</span>
        </div>

        <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/10 backdrop-blur-md animate-fade-in-up delay-4 hover:border-amber-500/30 transition-all duration-300">
            <span class="text-sm uppercase tracking-wider text-slate-300 font-bold">Certified Trainees</span>
            <div class="text-3xl sm:text-4xl font-black text-emerald-400 mt-1 font-['JetBrains_Mono']" data-counter="true" data-target="{{ \App\Models\TraineeResult::where('is_certified', true)->count() }}" data-duration="900">
                {{ \App\Models\TraineeResult::where('is_certified', true)->count() }}
            </div>
            <span class="text-xs text-slate-400 mt-1.5 block font-medium">Score &ge; 80.0% qualification rate</span>
        </div>
    </div>

    <!-- Trainee Inspection Results Table -->
    <div class="rounded-2xl bg-white/[0.03] border border-white/10 backdrop-blur-md overflow-hidden animate-fade-in-up delay-5">
        <div class="p-4 sm:p-5 border-b border-white/10 flex justify-between items-center">
            <div>
                <h3 class="text-base sm:text-lg font-bold tracking-wider uppercase text-amber-400">Trainee Session Records</h3>
                <p class="text-sm text-slate-300 mt-0.5">Live warehouse inspection logs &bull; 13 Total Hazards with wrong-click telemetry</p>
            </div>
            <span class="text-xs sm:text-sm font-mono bg-white/5 border border-white/10 text-slate-200 px-3.5 py-1.5 rounded-full font-semibold">
                5 Recent Sessions &bull; {{ $totalSessions ?? count($records) }} Total
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-white/[0.02] border-b border-white/10 text-slate-300 text-sm font-bold uppercase tracking-wider">
                        <th class="p-3.5">Trainee Username</th>
                        <th class="p-3.5">Score</th>
                        <th class="p-3.5">Time</th>
                        <th class="p-3.5 text-center">Hazards Found</th>
                        <th class="p-3.5 text-center">Hazards Missed</th>
                        <th class="p-3.5 text-center">Wrong Clicks</th>
                        <th class="p-3.5 text-center">Certificate</th>
                        <th class="p-3.5">Date & Time</th>
                        <th class="p-3.5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-200">
                    @forelse($records as $record)
                        <tr class="hover:bg-white/[0.03] transition-colors">
                            <td class="p-3.5 font-bold text-white whitespace-nowrap font-mono text-sm sm:text-base">
                                {{ $record->username }}
                            </td>
                            <!-- Visual Score Ring & Percentage Score -->
                            <td class="p-3.5 whitespace-nowrap">
                                 <div class="flex items-center gap-3">
                                     <!-- Visual Score Ring -->
                                     <div class="relative w-9 h-9 shrink-0 flex items-center justify-center">
                                         <svg class="w-9 h-9 -rotate-90 transform" viewBox="0 0 36 36">
                                             <path
                                                 class="text-white/10"
                                                 stroke="currentColor"
                                                 stroke-width="3"
                                                 fill="none"
                                                 d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                             />
                                             @php
                                                 $scVal = max(0, min(100, $record->score));
                                                 $scOffset = 100 - $scVal;
                                                 $scColor = $scVal >= 90 ? 'text-emerald-400' : ($scVal >= 80 ? 'text-teal-400' : ($scVal >= 60 ? 'text-amber-400' : 'text-rose-400'));
                                             @endphp
                                             <path
                                                 class="{{ $scColor }} transition-all duration-700 ease-out"
                                                 stroke="currentColor"
                                                 stroke-width="3.2"
                                                 stroke-dasharray="100, 100"
                                                 stroke-dashoffset="{{ $scOffset }}"
                                                 stroke-linecap="round"
                                                 fill="none"
                                                 d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                             />
                                         </svg>
                                         <span class="absolute text-[10px] font-mono font-black text-slate-100">
                                             {{ (int) floor($record->score) }}
                                         </span>
                                     </div>

                                     <!-- Percentage Score Text -->
                                     <span class="font-extrabold font-['JetBrains_Mono'] text-base {{ $scVal >= 80 ? 'text-emerald-400' : ($scVal >= 60 ? 'text-amber-400' : 'text-rose-400') }}">
                                         {{ number_format($record->score, 1) }}%
                                     </span>
                                 </div>
                            </td>
                            <td class="p-3.5 font-mono text-sky-400 font-bold whitespace-nowrap text-sm">
                                {{ floor($record->completion_time / 60) }}m {{ sprintf('%02ds', round(fmod($record->completion_time, 60))) }}
                            </td>

                            <!-- Hazards Identified (Found out of 13 in Green) -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center justify-center gap-1.5 px-3 py-1 rounded-full text-xs sm:text-sm font-black font-mono bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 shadow-sm shadow-emerald-500/10">
                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ $record->hazards_found }}/13
                                </span>
                            </td>

                            <!-- Hazards Missed (Missed out of 13 in Red) -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="inline-flex items-center justify-center gap-1.5 px-3 py-1 rounded-full text-xs sm:text-sm font-black font-mono bg-rose-500/15 text-rose-400 border border-rose-500/30 shadow-sm shadow-rose-500/10">
                                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    {{ $record->hazards_missed }}/13
                                </span>
                            </td>

                            <!-- Wrong Clicks Penalty -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                @if($record->wrong_clicks > 0)
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs sm:text-sm font-mono font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30" title="-{{ $record->wrong_clicks * 5 }}% Penalty applied">
                                        {{ $record->wrong_clicks }} (-{{ $record->wrong_clicks * 5 }}%)
                                    </span>
                                @else
                                    <span class="text-sm font-mono text-slate-400">0</span>
                                @endif
                            </td>

                            <!-- Certificate Qualification -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                @if($record->is_certified)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs sm:text-sm font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        Qualified
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs sm:text-sm font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                                        Retest (&lt;80%)
                                    </span>
                                @endif
                            </td>

                            <!-- Standard Time Date -->
                            <td class="p-3.5 text-sm text-slate-200 whitespace-nowrap font-mono font-medium" title="{{ $record->created_at->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s') }}">
                                {{ $record->created_at->timezone('Asia/Kuala_Lumpur')->format('d M Y, h:i A') }}
                            </td>

                            <!-- Action / View Profile Button -->
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <a href="{{ route('dashboard.trainee', $record->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-amber-500/15 hover:bg-amber-400 text-amber-300 hover:text-neutral-950 font-mono font-bold text-xs sm:text-sm border border-amber-500/30 transition shadow-sm hover:shadow-amber-500/20 cursor-pointer" title="View {{ $record->username }}'s Personal Profile & Zone Breakdown">
                                    <span>Profile</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400 text-sm">
                                No trainee training sessions logged yet. Awaiting VR completions...
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-2 text-sm text-slate-300">
            <span class="font-mono">Showing 5 most recent trainee inspection sessions</span>
            <a href="{{ route('dashboard.directory') }}" class="text-amber-400 hover:text-amber-300 font-bold transition flex items-center gap-1 hover:underline">
                View All Records in Directory &rarr;
            </a>
        </div>
    </div>
</div>