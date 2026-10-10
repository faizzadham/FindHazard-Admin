<!-- ======================================================== -->
<!-- 4. TRAINEE DIRECTORY (/trainees)                  -->
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
                <span>Trainee Directory</span>
                <span class="text-[10px] font-mono bg-amber-500/10 text-amber-400 border border-amber-500/30 px-2 py-0.5 rounded-full">/trainees</span>
            </h2>
            <p class="text-xs text-slate-400">Searchable master directory with industrial background and tier evaluations.</p>
        </div>

        <div class="flex items-center gap-2 text-xs font-mono">
            <span class="text-slate-400">Matched Workers:</span>
            <span id="directoryMatchCount" class="font-bold text-amber-400 font-mono text-sm">5</span>
        </div>
    </div>

    <!-- 4 Filter Controls (Search, Date, Experience, Tier) -->
    <div class="glass-sheet p-4 rounded-3xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Search Input -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Search Worker</label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-slate-500 text-xs">🔍</span>
                <input id="directorySearchInput" oninput="applyDirectoryFilters()" type="text" placeholder="Worker ID or name..."
                    class="w-full glass-card rounded-xl pl-8 pr-3 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-500/50 transition font-mono">
            </div>
        </div>

        <!-- Filter by Date -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Filter Date</label>
            <select id="directoryDateFilter" onchange="applyDirectoryFilters()" class="w-full glass-card rounded-xl px-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-amber-500/50 transition font-mono cursor-pointer">
                <option value="all" class="bg-neutral-900 text-slate-100">All Dates</option>
                <option value="01 Oct 2026" class="bg-neutral-900 text-slate-100">01 Oct 2026 (Today)</option>
                <option value="30 Sep 2026" class="bg-neutral-900 text-slate-100">30 Sep 2026</option>
            </select>
        </div>

        <!-- Filter by Industrial Experience -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Industrial Experience</label>
            <select id="directoryExpFilter" onchange="applyDirectoryFilters()" class="w-full glass-card rounded-xl px-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-amber-500/50 transition font-mono cursor-pointer">
                <option value="all" class="bg-neutral-900 text-slate-100">All Experience</option>
                <option value="Logistics Center" class="bg-neutral-900 text-slate-100">Logistics Center</option>
                <option value="Manufacturing" class="bg-neutral-900 text-slate-100">Manufacturing</option>
                <option value="Retail" class="bg-neutral-900 text-slate-100">Retail</option>
            </select>
        </div>

        <!-- Filter by Evaluation Tier -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Evaluation Tier</label>
            <select id="directoryTierFilter" onchange="applyDirectoryFilters()" class="w-full glass-card rounded-xl px-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-amber-500/50 transition font-mono cursor-pointer">
                <option value="all" class="bg-neutral-900 text-slate-100">All Tiers</option>
                <option value="Locked In" class="bg-neutral-900 text-emerald-400">Locked In (95-100 pts)</option>
                <option value="Great" class="bg-neutral-900 text-amber-300">Great (85-94 pts)</option>
                <option value="Valid Effort" class="bg-neutral-900 text-yellow-400">Valid Effort (75-84 pts)</option>
                <option value="Cooked" class="bg-neutral-900 text-rose-400">Cooked (&lt;75 pts / Retrain)</option>
            </select>
        </div>
    </div>

    <!-- Master Trainee Directory Table -->
    <div class="glass-sheet rounded-3xl overflow-hidden border-white/5 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-white/[0.03] text-slate-400 font-mono uppercase border-b border-white/5">
                    <tr>
                        <th class="p-3.5">Rank &amp; Trainee ID</th>
                        <th class="p-3.5">Industrial Experience</th>
                        <th class="p-3.5">Score</th>
                        <th class="p-3.5">Discovered</th>
                        <th class="p-3.5">Missed</th>
                        <th class="p-3.5">Duration</th>
                        <th class="p-3.5">Evaluation Tier</th>
                        <th class="p-3.5">Session Date</th>
                        <th class="p-3.5 text-center">Inspect</th>
                    </tr>
                </thead>
                <tbody id="traineeTableBody" class="divide-y divide-white/5 text-slate-300">
                    <!-- Populated dynamically via applyDirectoryFilters() -->
                </tbody>
            </table>
        </div>
    </div>
</div>
