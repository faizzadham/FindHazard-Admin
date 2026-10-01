<section id="tab-records" class="tab-content active space-y-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-panel p-5 rounded-2xl border-white/5">
            <p class="text-xs text-slate-400 uppercase font-semibold">Total Sessions</p>
            <p class="text-3xl font-black text-white mt-1 font-mono">{{ $totalSessions ?? 5 }}</p>
        </div>
        <div class="glass-panel p-5 rounded-2xl border-emerald-500/20">
            <p class="text-xs text-slate-400 uppercase font-semibold">Average Score</p>
            <p class="text-3xl font-black text-emerald-400 mt-1 font-mono">{{ isset($avgScore) ? round($avgScore, 1) : 86 }} <span class="text-xs font-sans text-emerald-300">pts</span></p>
        </div>
        <div class="glass-panel p-5 rounded-2xl border-cyan-500/20">
            <p class="text-xs text-slate-400 uppercase font-semibold">Avg Completion Time</p>
            <p class="text-3xl font-black text-cyan-400 mt-1 font-mono">{{ isset($avgTime) ? round($avgTime, 1) : '56.8' }}<span class="text-xs font-sans text-cyan-300">s</span></p>
        </div>
        <div class="glass-panel p-5 rounded-2xl border-amber-500/20">
            <p class="text-xs text-slate-400 uppercase font-semibold">Hazards Spotted</p>
            <p class="text-3xl font-black text-amber-400 mt-1 font-mono">{{ $totalHazardsFound ?? 43 }} <span class="text-xs font-sans text-slate-500">/ 50</span></p>
        </div>
    </div>

    <div class="glass-panel rounded-2xl overflow-hidden border-white/5">
        <div class="p-4 border-b border-white/5 flex flex-col sm:flex-row justify-between items-center gap-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-200">Trainee Records Log</h2>
            <input id="searchInput" type="text" oninput="filterTable()" placeholder="Search trainee name..." 
                class="w-full sm:w-64 glass-input rounded-xl px-3 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:outline-none transition font-mono">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-white/[0.02] text-slate-400 text-xs uppercase font-mono border-b border-white/5">
                    <tr>
                        <th class="p-3.5">Trainee</th>
                        <th class="p-3.5">Score</th>
                        <th class="p-3.5">Found</th>
                        <th class="p-3.5">Missed</th>
                        <th class="p-3.5">Time</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">Date</th>
                        <th class="p-3.5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="traineeTableBody" class="divide-y divide-white/5 text-slate-300"></tbody>
            </table>
        </div>
    </div>
</section>