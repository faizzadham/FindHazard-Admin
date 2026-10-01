<header class="flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="relative w-full sm:w-80">
        <span class="absolute left-3.5 top-2.5 text-slate-400 text-xs">🔍</span>
        <input id="globalSearchInput" oninput="handleGlobalSearch()" type="text" placeholder="Search trainee, scenario, or hazard ID..." 
            class="w-full glass-card rounded-2xl pl-9 pr-4 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-500/50 transition font-mono">
    </div>

    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
        <button onclick="showToast('Date Filter', 'Current Session: October 2026')" class="glass-card px-3 py-2 rounded-2xl text-xs text-slate-300 hover:text-white transition flex items-center gap-2">
            <span>📅</span>
            <span class="font-mono text-[11px]">01 Oct 2026</span>
        </button>
        <button class="glass-card w-9 h-9 rounded-2xl flex items-center justify-center text-slate-300 hover:text-white transition relative text-xs">
            🔔
            <span class="absolute top-2 right-2 w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
        </button>
        <div class="flex items-center gap-2 pl-1">
            <div class="w-9 h-9 rounded-2xl bg-amber-400/20 border border-amber-400/30 flex items-center justify-center text-amber-300 font-bold text-xs">
                FZ
            </div>
        </div>
        <button onclick="handleLogout()" class="glass-card hover:bg-rose-950/40 text-rose-300 border-rose-500/30 px-3 py-2 rounded-2xl text-xs font-semibold transition cursor-pointer">
            Sign Out
        </button>
    </div>
</header>