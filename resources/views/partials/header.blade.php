<!-- TOPBAR NAVIGATION SECTION -->
<header class="flex flex-col md:flex-row items-center justify-between gap-4 pb-4 border-b border-white/10">
    <!-- Brand / Logo -->
    <a href="{{ route('dashboard.overview') }}" class="flex items-center gap-3 px-1 hover:opacity-85 transition shrink-0 group">
        <span class="text-2xl drop-shadow group-hover:scale-105 transition-transform">⚠️</span>
        <div class="flex flex-col">
            <span class="text-base font-extrabold text-white tracking-wide leading-tight">FindHazard <span class="text-amber-400">VR</span></span>
            <span class="text-[9.5px] font-mono text-slate-400 uppercase tracking-widest font-semibold">Supervisor Command</span>
        </div>
    </a>

    <!-- Topbar Navigation Links -->
    <nav class="flex items-center flex-wrap justify-center gap-1.5 p-1.5 bg-white/[0.03] border border-white/10 rounded-2xl font-sans shadow-inner">
        <a href="{{ route('dashboard.overview') }}" id="navBtn-overview" 
           class="nav-item flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs transition duration-200 {{ request()->routeIs('dashboard.overview') ? 'font-bold bg-amber-500 text-neutral-950 shadow-md shadow-amber-500/25' : 'font-semibold text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <span class="text-sm">▦</span><span>Overview</span>
        </a>
        <a href="{{ route('dashboard.monitor') }}" id="navBtn-monitor" 
           class="nav-item flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs transition duration-200 {{ request()->routeIs('dashboard.monitor') ? 'font-bold bg-amber-500 text-neutral-950 shadow-md shadow-amber-500/25' : 'font-semibold text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <span class="text-sm">🥽</span><span>VR Monitor</span>
        </a>
        <a href="{{ route('dashboard.directory') }}" id="navBtn-directory" 
           class="nav-item flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs transition duration-200 {{ request()->routeIs('dashboard.directory') ? 'font-bold bg-amber-500 text-neutral-950 shadow-md shadow-amber-500/25' : 'font-semibold text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <span class="text-sm">👥</span><span>Directory</span>
        </a>
        <a href="{{ route('dashboard.analytics') }}" id="navBtn-analytics" 
           class="nav-item flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs transition duration-200 {{ request()->routeIs('dashboard.analytics') ? 'font-bold bg-amber-500 text-neutral-950 shadow-md shadow-amber-500/25' : 'font-semibold text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <span class="text-sm">📊</span><span>Analytics</span>
        </a>
        <a href="{{ route('dashboard.guide') }}" id="navBtn-guide" 
           class="nav-item flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs transition duration-200 {{ request()->routeIs('dashboard.guide') ? 'font-bold bg-amber-500 text-neutral-950 shadow-md shadow-amber-500/25' : 'font-semibold text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <span class="text-sm">📖</span><span>Evaluation Guide</span>
        </a>
    </nav>

    <!-- Topbar Right Controls -->
    <div class="flex items-center gap-2.5 shrink-0 w-full md:w-auto justify-end">
        <div class="hidden xl:flex items-center gap-2 px-3 py-1.5 rounded-xl glass-card text-xs font-medium text-slate-300">
            <span class="text-amber-400 text-xs">💬</span>
            <span class="text-[11px] font-mono">Safety Standard</span>
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
        </div>

        <div class="glass-card px-3 py-1.5 rounded-xl text-xs text-slate-300 transition font-mono text-[11px] flex items-center gap-1.5">
            <span>📅</span>
            <span>{{ now()->timezone('Asia/Kuala_Lumpur')->format('d M Y') }}</span>
        </div>

        <div class="w-8 h-8 rounded-xl bg-amber-400/20 border border-amber-400/30 flex items-center justify-center text-amber-300 font-bold text-xs" title="Supervisor: Faiz Adham">
            FZ
        </div>

        <a href="{{ route('login') }}" class="glass-card hover:bg-rose-950/40 text-rose-300 border-rose-500/30 px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer">
            Lock
        </a>
    </div>
</header>