<!-- ======================================================== -->
<!-- 4. TRAINEE DIRECTORY & LEADERBOARD (/trainees)         -->
<!-- ======================================================== -->
<div id="view-directory" class="app-view space-y-5">
@if(isset($records))
    <script>
        window.dbTrainees = @json($records);
    </script>
@endif

    <!-- Header with route badge & live match counter -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-2 border-b border-white/5">
        <div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <span>Trainee Directory &amp; Leaderboard</span>
                <span class="text-[10px] font-mono bg-amber-500/10 text-amber-400 border border-amber-500/30 px-2 py-0.5 rounded-full">/trainees</span>
            </h2>
            <p class="text-xs text-slate-400">Searchable master directory &amp; top-to-bottom leaderboard with date filters and performance telemetry.</p>
        </div>

        <div class="flex items-center gap-2 text-xs font-mono">
            <span class="text-slate-400">Matched Trainees:</span>
            <span id="directoryMatchCount" class="font-bold text-amber-400 font-mono text-sm">{{ isset($records) ? count($records) : 0 }}</span>
        </div>
    </div>

    <!-- Filter & Leaderboard Controls (Search, Date Filter, Top-to-Bottom Leaderboard, Certificate) -->
    <div class="glass-sheet p-4 rounded-3xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- 1. Search Trainee -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Search Trainee</label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-slate-500 text-xs">🔍</span>
                <input id="directorySearchInput" oninput="applyDirectoryFilters()" type="text" placeholder="Worker or username..."
                    class="w-full glass-card rounded-xl pl-8 pr-3 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-500/50 transition font-mono">
            </div>
        </div>

        <!-- 2. Filter by Date -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Filter by Date</label>
            <select id="directoryDateFilter" onchange="applyDirectoryFilters()" class="w-full glass-card rounded-xl px-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-amber-500/50 transition font-mono cursor-pointer">
                <option value="all" class="bg-neutral-900 text-slate-100">📅 All Dates</option>
                @if(isset($availableDates))
                    @foreach($availableDates as $d)
                        <option value="{{ $d }}" class="bg-neutral-900 text-slate-100">{{ $d }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <!-- 3. Leaderboard Ranking Order (Top to Bottom) -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Ranking</label>
            <select id="directorySortFilter" onchange="applyDirectoryFilters()" class="w-full glass-card rounded-xl px-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-amber-500/50 transition font-mono cursor-pointer">
                <option value="top_to_bottom" class="bg-neutral-900 text-amber-300 font-bold" selected>🏆 Top to Bottom (High &rarr; Low)</option>
                <option value="bottom_to_top" class="bg-neutral-900 text-slate-300">📉 Bottom to Top (Low &rarr; High)</option>
                <option value="fastest_time" class="bg-neutral-900 text-sky-300">⚡ Fastest Time First</option>
                <option value="most_found" class="bg-neutral-900 text-emerald-300">🎯 Most Hazards Found First</option>
            </select>
        </div>

        <!-- 4. Filter by Certificate Qualification -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Certificate Status</label>
            <select id="directoryCertFilter" onchange="applyDirectoryFilters()" class="w-full glass-card rounded-xl px-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-amber-500/50 transition font-mono cursor-pointer">
                <option value="all" class="bg-neutral-900 text-slate-100">All Statuses</option>
                <option value="qualified" class="bg-neutral-900 text-emerald-400">✓ Qualified (Pass &ge;80%)</option>
                <option value="retest" class="bg-neutral-900 text-rose-400">✕ Retest Required (&lt;80%)</option>
            </select>
        </div>
    </div>

    <!-- Master Trainee Directory & Leaderboard Table -->
    <div class="glass-sheet rounded-3xl overflow-hidden border-white/5 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-white/[0.02] border-b border-white/10 text-slate-400 text-xs uppercase tracking-wider font-mono">
                        <th class="p-3.5">Trainee Username</th>
                        <th class="p-3.5">Score</th>
                        <th class="p-3.5">Time</th>
                        <th class="p-3.5 text-center">Hazards Found</th>
                        <th class="p-3.5 text-center">Hazards Missed</th>
                        <th class="p-3.5 text-center">Wrong Clicks</th>
                        <th class="p-3.5 text-center">Certificate</th>
                        <th class="p-3.5">Date &amp; Time</th>
                        <th class="p-3.5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="traineeTableBody" class="divide-y divide-white/5 text-slate-300">
                    @if(isset($records))
                        @forelse($records as $idx => $record)
                            <tr class="hover:bg-white/[0.03] transition-colors">
                                <td class="p-3.5 font-semibold text-white whitespace-nowrap">
                                    <div class="flex items-center gap-2.5">
                                        @if($idx === 0)
                                            <span class="w-6 h-6 rounded-lg bg-amber-400/20 text-amber-300 border border-amber-400/40 text-[10px] font-black font-mono inline-flex items-center justify-center shrink-0 shadow-sm shadow-amber-400/20" title="Rank 1 - Top Performer">#1</span>
                                        @elseif($idx === 1)
                                            <span class="w-6 h-6 rounded-lg bg-slate-300/20 text-slate-200 border border-slate-300/40 text-[10px] font-black font-mono inline-flex items-center justify-center shrink-0" title="Rank 2">#2</span>
                                        @elseif($idx === 2)
                                            <span class="w-6 h-6 rounded-lg bg-amber-700/20 text-amber-500 border border-amber-700/40 text-[10px] font-black font-mono inline-flex items-center justify-center shrink-0" title="Rank 3">#3</span>
                                        @else
                                            <span class="w-6 h-6 rounded-lg glass-card text-[10px] text-slate-400 font-mono inline-flex items-center justify-center shrink-0">#{{ $idx + 1 }}</span>
                                        @endif
                                        <span class="font-bold text-white font-mono">{{ $record->username }}</span>
                                    </div>
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
                                             <span class="absolute text-[8.5px] font-mono font-black text-slate-200">
                                                 {{ (int) floor($record->score) }}
                                             </span>
                                         </div>

                                         <!-- Percentage Score Text -->
                                         <span class="font-extrabold font-['JetBrains_Mono'] text-sm {{ $scVal >= 80 ? 'text-emerald-400' : ($scVal >= 60 ? 'text-amber-400' : 'text-rose-400') }}">
                                             {{ number_format($record->score, 1) }}%
                                         </span>
                                     </div>
                                </td>
                                <td class="p-3.5 font-mono text-sky-400 font-bold whitespace-nowrap">
                                    {{ floor($record->completion_time / 60) }}m {{ sprintf('%02ds', round(fmod($record->completion_time, 60))) }}
                                </td>

                                <!-- Hazards Identified (Found out of 13 in Green) -->
                                <td class="p-3.5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center gap-1.5 px-3 py-1 rounded-full text-xs font-black font-mono bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 shadow-sm shadow-emerald-500/10">
                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ $record->hazards_found }}/13
                                    </span>
                                </td>

                                <!-- Hazards Missed (Missed out of 13 in Red) -->
                                <td class="p-3.5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center gap-1.5 px-3 py-1 rounded-full text-xs font-black font-mono bg-rose-500/15 text-rose-400 border border-rose-500/30 shadow-sm shadow-rose-500/10">
                                        <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        {{ $record->hazards_missed }}/13
                                    </span>
                                </td>

                                <!-- Wrong Clicks Penalty -->
                                <td class="p-3.5 text-center whitespace-nowrap">
                                    @if($record->wrong_clicks > 0)
                                        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30" title="-{{ $record->wrong_clicks * 5 }}% Penalty applied">
                                            {{ $record->wrong_clicks }} (-{{ $record->wrong_clicks * 5 }}%)
                                        </span>
                                    @else
                                        <span class="text-xs font-mono text-slate-500">0</span>
                                    @endif
                                </td>

                                <!-- Certificate Qualification -->
                                <td class="p-3.5 text-center whitespace-nowrap">
                                    @if($record->is_certified)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            Qualified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                            Retest (&lt;80%)
                                        </span>
                                    @endif
                                </td>

                                <!-- Standard Time Date -->
                                <td class="p-3.5 text-xs text-slate-300 whitespace-nowrap font-mono" title="{{ $record->created_at->timezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s') }}">
                                    {{ $record->created_at->timezone('Asia/Kuala_Lumpur')->format('d M Y, h:i A') }}
                                </td>

                                <!-- Action / View Profile Button -->
                                <td class="p-3.5 text-center whitespace-nowrap">
                                    <a href="{{ route('dashboard.trainee', $record->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/15 hover:bg-amber-400 text-amber-300 hover:text-neutral-950 font-mono font-bold text-xs border border-amber-500/30 transition shadow-sm hover:shadow-amber-500/20 cursor-pointer" title="View {{ $record->username }}'s Personal Profile & Zone Breakdown">
                                        <span>Profile</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-slate-500">
                                    No trainee records found.
                                </td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-2 text-xs text-slate-400">
            <span class="font-mono">🏆 Leaderboard ranked by final inspection score &amp; completion velocity</span>
            <span class="text-xs text-slate-500 font-mono">Total Recorded Sessions: <strong class="text-slate-300">{{ isset($records) ? count($records) : 0 }}</strong></span>
        </div>
    </div>
</div>
