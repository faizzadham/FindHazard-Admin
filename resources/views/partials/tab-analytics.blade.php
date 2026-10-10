<!-- TAB 5: EXPERIENCE & ZONE HAZARD ANALYTICS -->
<div id="view-analytics" class="app-view space-y-6">

    <!-- Header & Facility Context -->
    <div class="glass-sheet rounded-3xl p-6 border-white/10 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-mono bg-amber-500/10 text-amber-400 border border-amber-500/30 px-2.5 py-0.5 rounded-full">
                        Warehouse Safety Zone Telemetry
                    </span>
                    <span class="text-xs font-mono bg-sky-500/10 text-sky-400 border border-sky-500/30 px-2.5 py-0.5 rounded-full">
                        6 Operational Warehouse Sectors
                    </span>
                </div>
                <h3 class="text-xl font-black text-white uppercase tracking-wider">
                    Warehouse Hazard Analytics by Area
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    Aggregated hazard detection statistics, blindspot identification, and zone vigilance across all registered VR trainees.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold text-amber-400 bg-amber-500/10 border border-amber-500/30">
                    Total Checkpoints: 13 Hazards
                </span>
                <span class="px-3.5 py-1.5 rounded-xl text-xs font-mono font-bold text-slate-300 bg-white/[0.03] border border-white/10">
                    Cohorts: {{ $summary['totalTrainees'] ?? 0 }} Trainees
                </span>
            </div>
        </div>

        <!-- 4 Top Executive KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
            <!-- Overall Detection Rate -->
            <div class="glass-card p-4 rounded-2xl border-white/5 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Overall Detection</span>
                    <span class="text-xs font-mono text-emerald-400 font-bold">Facility Wide</span>
                </div>
                <div class="text-2xl font-black text-white font-mono">
                    {{ number_format($summary['overallDetectionRate'] ?? 0, 1) }}%
                </div>
                <div class="w-full bg-white/5 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-amber-400 h-full rounded-full" 
                         style="width: {{ min(100, $summary['overallDetectionRate'] ?? 0) }}%;"></div>
                </div>
                <p class="text-[10px] text-slate-400 font-mono">
                    {{ $summary['totalHazardsIdentified'] ?? 0 }} of {{ $summary['totalOpportunities'] ?? 0 }} hazard detections
                </p>
            </div>

            <!-- Best Performing Area -->
            <div class="glass-card p-4 rounded-2xl border-white/5 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Highest Vigilance</span>
                    <span class="text-xs">🏆</span>
                </div>
                <div class="text-base font-black text-emerald-400 truncate font-sans" title="{{ $summary['bestArea']['name'] ?? 'N/A' }}">
                    {{ $summary['bestArea']['icon'] ?? '🛫' }} {{ $summary['bestArea']['name'] ?? 'N/A' }}
                </div>
                <div class="text-xs font-mono font-bold text-slate-300">
                    <span class="text-emerald-400">{{ number_format($summary['bestArea']['detection_rate'] ?? 0, 1) }}%</span> Detection Rate
                </div>
                <p class="text-[10px] text-slate-400 font-mono">
                    Strongest hazard awareness among operators
                </p>
            </div>

            <!-- Primary Training Blindspot -->
            <div class="glass-card p-4 rounded-2xl border-white/5 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Primary Blindspot</span>
                    <span class="text-xs">⚠️</span>
                </div>
                <div class="text-base font-black text-rose-400 truncate font-sans" title="{{ $summary['worstArea']['name'] ?? 'N/A' }}">
                    {{ $summary['worstArea']['icon'] ?? '⚡' }} {{ $summary['worstArea']['name'] ?? 'N/A' }}
                </div>
                <div class="text-xs font-mono font-bold text-slate-300">
                    <span class="text-rose-400">{{ number_format($summary['worstArea']['detection_rate'] ?? 0, 1) }}%</span> Detection Rate
                </div>
                <p class="text-[10px] text-slate-400 font-mono">
                    Requires targeted supervisor drills & retests
                </p>
            </div>

            <!-- Qualification Rate -->
            <div class="glass-card p-4 rounded-2xl border-white/5 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Certification Rate</span>
                    <span class="text-xs">🎖️</span>
                </div>
                <div class="text-2xl font-black text-amber-400 font-mono">
                    {{ number_format($summary['certifiedRate'] ?? 0, 1) }}%
                </div>
                <div class="w-full bg-white/5 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-500 to-yellow-400 h-full rounded-full" 
                         style="width: {{ min(100, $summary['certifiedRate'] ?? 0) }}%;"></div>
                </div>
                <p class="text-[10px] text-slate-400 font-mono">
                    {{ $summary['certifiedCount'] ?? 0 }} of {{ $summary['totalTrainees'] ?? 0 }} passed qualification (≥80%)
                </p>
            </div>
        </div>
    </div>

    <!-- AREA VIGILANCE COMPARATIVE BENCHMARK BAR -->
    <div class="glass-sheet rounded-3xl p-6 border-white/10 space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-white/5">
            <div>
                <h4 class="text-sm font-extrabold text-white uppercase tracking-wider">
                    Zone Detection Rate Ranking
                </h4>
                <p class="text-[11px] text-slate-400">
                    Comparative safety awareness performance across all 6 warehouse zones.
                </p>
            </div>
            <span class="text-xs font-mono text-slate-400">
                Sorted by Vigilance
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach(collect($areaAnalytics)->sortByDesc('detection_rate') as $areaKey => $area)
                <div class="glass-card p-4 rounded-2xl border border-white/5 hover:border-white/10 transition space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">{{ $area['icon'] }}</span>
                            <div>
                                <h5 class="text-xs font-bold text-white leading-tight">{{ $area['name'] }}</h5>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $area['total_hazards'] }} Hazards Assigned</span>
                            </div>
                        </div>
                        <span class="text-xs font-mono font-black px-2.5 py-1 rounded-xl
                            @if($area['detection_rate'] >= 80.0) bg-emerald-500/15 text-emerald-400 border border-emerald-500/30
                            @elseif($area['detection_rate'] >= 65.0) bg-amber-500/15 text-amber-400 border border-amber-500/30
                            @else bg-rose-500/15 text-rose-400 border border-rose-500/30 @endif">
                            {{ number_format($area['detection_rate'], 1) }}%
                        </span>
                    </div>

                    <div class="space-y-1">
                        <div class="w-full bg-white/5 h-2 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500
                                @if($area['detection_rate'] >= 80.0) bg-gradient-to-r from-emerald-500 to-teal-400
                                @elseif($area['detection_rate'] >= 65.0) bg-gradient-to-r from-amber-500 to-yellow-400
                                @else bg-gradient-to-r from-rose-500 to-orange-400 @endif"
                                style="width: {{ min(100, $area['detection_rate']) }}%;"></div>
                        </div>
                        <div class="flex justify-between text-[10px] font-mono text-slate-400 pt-0.5">
                            <span>Total Spotted: {{ $area['total_found'] }} / {{ $area['total_possible'] }}</span>
                            <span class="font-bold {{ $area['detection_rate'] >= 80.0 ? 'text-emerald-400' : ($area['detection_rate'] >= 65.0 ? 'text-amber-400' : 'text-rose-400') }}">
                                {{ $area['status'] }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- THE 6 OPERATIONAL WAREHOUSE AREAS (DETAILED CHECKPOINT CARDS) -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-1">
            <div>
                <h4 class="text-base font-black text-white uppercase tracking-wider">
                    Detailed Hazard Identification by Warehouse Sector
                </h4>
                <p class="text-xs text-slate-400">
                    Individual trainee detection counts and risk classifications for each hazard within its designated operational sector.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span id="sectorMatchCountBadge" class="text-xs font-mono text-amber-400 bg-amber-500/10 border border-amber-500/20 px-3 py-1 rounded-full shrink-0">
                    Showing 1 of 6 Sectors
                </span>
            </div>
        </div>

        <!-- Filter Control Bar for Detailed Warehouse Sectors -->
        <div class="glass-sheet p-4 rounded-3xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="w-full sm:max-w-md space-y-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Filter Warehouse Sector</label>
                <select id="analyticsSectorFilter" onchange="applySectorAnalyticsFilters()" class="w-full glass-card rounded-xl px-3.5 py-2 text-xs text-slate-200 focus:outline-none focus:border-amber-500/50 transition font-mono cursor-pointer">
                    <option value="all" class="bg-neutral-900 text-slate-100">🌐 All 6 Warehouse Sectors</option>
                    @foreach($areaAnalytics as $key => $area)
                        <option value="{{ $key }}" class="bg-neutral-900 text-slate-200" {{ $loop->first ? 'selected' : '' }}>{{ $area['icon'] }} {{ $area['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-xs text-slate-400 font-mono hidden sm:block">
                Select an operational zone to focus on its specific hazard checkpoints.
            </div>
        </div>

        <!-- The Sector Cards Container -->
        <div id="analyticsSectorsContainer" class="grid grid-cols-1 gap-5">
            @foreach($areaAnalytics as $areaKey => $area)
                <div class="sector-detail-card glass-sheet rounded-3xl p-5 border-white/10 space-y-4 relative overflow-hidden transition-all duration-300 {{ $loop->first ? '' : 'hidden' }}"
                     data-area-key="{{ $areaKey }}"
                     data-name="{{ strtolower($area['name']) }}"
                     data-rate="{{ $area['detection_rate'] }}"
                     data-found="{{ $area['total_found'] }}"
                     data-status="{{ $area['detection_rate'] >= 80.0 ? 'optimal' : ($area['detection_rate'] >= 65.0 ? 'moderate' : 'critical') }}"
                     data-default-order="{{ $loop->index }}">
                    <!-- Subtle Ambient Corner Light -->
                    <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full blur-2xl pointer-events-none opacity-20
                        @if($area['color'] === 'emerald') bg-emerald-500
                        @elseif($area['color'] === 'sky') bg-sky-500
                        @elseif($area['color'] === 'amber') bg-amber-500
                        @elseif($area['color'] === 'orange') bg-orange-500
                        @elseif($area['color'] === 'indigo') bg-indigo-500
                        @else bg-yellow-500 @endif">
                    </div>

                    <!-- Area Card Header -->
                    <div class="flex items-start justify-between gap-3 relative z-10 pb-3 border-b border-white/5">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-xl shrink-0
                                @if($area['color'] === 'emerald') bg-emerald-500/15 text-emerald-300 border border-emerald-500/30
                                @elseif($area['color'] === 'sky') bg-sky-500/15 text-sky-300 border border-sky-500/30
                                @elseif($area['color'] === 'amber') bg-amber-500/15 text-amber-300 border border-amber-500/30
                                @elseif($area['color'] === 'orange') bg-orange-500/15 text-orange-300 border border-orange-500/30
                                @elseif($area['color'] === 'indigo') bg-indigo-500/15 text-indigo-300 border border-indigo-500/30
                                @else bg-yellow-500/15 text-yellow-300 border border-yellow-500/30 @endif">
                                {{ $area['icon'] }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h5 class="text-sm font-extrabold text-white leading-tight">{{ $area['name'] }}</h5>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5 leading-snug">{{ $area['description'] }}</p>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="inline-block text-xs font-mono font-black px-2.5 py-1 rounded-xl
                                @if($area['detection_rate'] >= 80.0) bg-emerald-500/15 text-emerald-400 border border-emerald-500/30
                                @elseif($area['detection_rate'] >= 65.0) bg-amber-500/15 text-amber-400 border border-amber-500/30
                                @else bg-rose-500/15 text-rose-400 border border-rose-500/30 @endif">
                                {{ number_format($area['detection_rate'], 1) }}% Rate
                            </span>
                            <span class="block text-[10px] font-mono text-slate-400 mt-1">
                                {{ $area['total_found'] }}/{{ $area['total_possible'] }} Spotted
                            </span>
                        </div>
                    </div>

                    <!-- Area Progress Bar -->
                    <div class="space-y-1">
                        <div class="w-full bg-white/5 h-2 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500
                                @if($area['color'] === 'emerald') bg-gradient-to-r from-emerald-500 to-teal-400
                                @elseif($area['color'] === 'sky') bg-gradient-to-r from-sky-500 to-cyan-400
                                @elseif($area['color'] === 'amber') bg-gradient-to-r from-amber-500 to-yellow-400
                                @elseif($area['color'] === 'orange') bg-gradient-to-r from-orange-500 to-amber-400
                                @elseif($area['color'] === 'indigo') bg-gradient-to-r from-indigo-500 to-blue-400
                                @else bg-gradient-to-r from-yellow-500 to-amber-500 @endif"
                                style="width: {{ min(100, $area['detection_rate']) }}%;"></div>
                        </div>
                    </div>

                    <!-- Hazards Assigned to this Area -->
                    <div class="space-y-2.5 pt-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                            Assigned Hazard Checkpoints ({{ count($area['hazards']) }})
                        </span>

                        @foreach($area['hazards'] as $h)
                            <div class="p-3 rounded-2xl glass-card border border-white/5 space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-100">{{ $h['name'] }}</span>
                                            <span class="text-[9px] font-mono px-2 py-0.5 rounded-full bg-white/[0.04] text-slate-400 border border-white/5">
                                                {{ $h['severity'] }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-right shrink-0">
                                        <span class="text-[11px] font-mono font-bold
                                            @if($h['status'] === 'Optimal') text-emerald-400
                                            @elseif($h['status'] === 'Moderate') text-amber-400
                                            @else text-rose-400 @endif">
                                            {{ $h['found_count'] }} / {{ $summary['totalTrainees'] }} Trainees
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-white/5 h-1.5 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500
                                            @if($h['status'] === 'Optimal') bg-emerald-400
                                            @elseif($h['status'] === 'Moderate') bg-amber-400
                                            @else bg-rose-400 @endif"
                                            style="width: {{ min(100, $h['detection_rate']) }}%;"></div>
                                    </div>

                                    <span class="text-[10px] font-mono font-extrabold shrink-0 px-2 py-0.5 rounded-lg
                                        @if($h['status'] === 'Optimal') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                        @elseif($h['status'] === 'Moderate') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                        @else bg-rose-500/10 text-rose-400 border border-rose-500/20 @endif">
                                        {{ number_format($h['detection_rate'], 1) }}%
                                        @if($h['status'] === 'Optimal') (Optimal)
                                        @elseif($h['status'] === 'Moderate') (Moderate)
                                        @else (Blindspot) @endif
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty state when filtered out -->
        <div id="noSectorMatchMsg" class="hidden glass-sheet p-8 rounded-3xl border-white/10 text-center text-slate-400 space-y-2">
            <span class="text-3xl block">🔍</span>
            <p class="text-sm font-semibold text-white">No warehouse sectors match your filter criteria.</p>
            <p class="text-xs text-slate-500">Try resetting the sector or vigilance status filter to view all areas.</p>
            <button onclick="resetSectorFilters()" class="px-4 py-1.5 rounded-xl bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold font-mono hover:bg-amber-500/30 transition cursor-pointer mt-2">
                Reset Filters
            </button>
        </div>
    </div>

    <script>
    function applySectorAnalyticsFilters() {
        const sectorVal = document.getElementById('analyticsSectorFilter')?.value || 'all';
        const container = document.getElementById('analyticsSectorsContainer');
        const emptyMsg = document.getElementById('noSectorMatchMsg');
        if (!container) return;

        const cards = Array.from(container.querySelectorAll('.sector-detail-card'));
        let visibleCount = 0;

        cards.forEach(card => {
            const key = card.getAttribute('data-area-key');
            const matchesSector = (sectorVal === 'all') || (key === sectorVal);

            if (matchesSector) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });

        const badge = document.getElementById('sectorMatchCountBadge');
        if (badge) {
            badge.innerText = `Showing ${visibleCount} of ${cards.length} Sectors`;
        }

        // Adapt grid layout: full width for 1 area, 2 columns if showing all/multiple
        if (visibleCount > 1) {
            container.classList.add('md:grid-cols-2');
        } else {
            container.classList.remove('md:grid-cols-2');
        }

        if (emptyMsg) {
            if (visibleCount === 0) {
                emptyMsg.classList.remove('hidden');
            } else {
                emptyMsg.classList.add('hidden');
            }
        }
    }

    function resetSectorFilters() {
        const s1 = document.getElementById('analyticsSectorFilter');
        if (s1) {
            s1.selectedIndex = 1; // Default to first specific area
        }
        applySectorAnalyticsFilters();
    }
    </script>
</div>