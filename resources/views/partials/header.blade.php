<!-- TOP BAR -->
<header class="flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="relative w-full sm:w-80">
        <span class="absolute left-3.5 top-2.5 text-slate-400 text-xs">🔍</span>
        <input oninput="handleGlobalSearch(this.value)" type="text" placeholder="Search trainee or hazard ID..." 
            class="w-full glass-card rounded-2xl pl-9 pr-4 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-500/50 transition font-mono">
    </div>

    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
        <button class="glass-card px-3 py-2 rounded-2xl text-xs text-slate-300 hover:text-white transition font-mono text-[11px]">
            📅 01 Oct 2026
        </button>
        <div class="w-9 h-9 rounded-2xl bg-amber-400/20 border border-amber-400/30 flex items-center justify-center text-amber-300 font-bold text-xs">
            FZ
        </div>
        <a href="{{ route('login') }}" class="glass-card hover:bg-rose-950/40 text-rose-300 border-rose-500/30 px-3 py-2 rounded-2xl text-xs font-semibold transition cursor-pointer">
            Lock
        </a>
    </div>
</header>