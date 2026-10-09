<!-- SIDEBAR NAVIGATION -->
<aside class="w-full lg:w-56 flex flex-col justify-between shrink-0 space-y-6">
    <div class="space-y-6">
        <a href="{{ route('dashboard.overview') }}" class="flex items-center gap-2.5 px-2 hover:opacity-80 transition">
            <span class="text-lg">⚠️</span>
            <h2 class="text-base font-extrabold text-white tracking-wide">FindHazard VR</h2>
        </a>

        <nav class="space-y-1 font-sans">
            <a href="{{ route('dashboard.overview') }}" id="navBtn-overview" 
               class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs {{ request()->routeIs('dashboard.overview') ? 'font-bold bg-amber-500 text-neutral-950 shadow-lg shadow-amber-500/25' : 'font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04]' }} transition">
                <span class="text-sm">▦</span><span>Overview</span>
            </a>
            <a href="{{ route('dashboard.monitor') }}" id="navBtn-monitor" 
               class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs {{ request()->routeIs('dashboard.monitor') ? 'font-bold bg-amber-500 text-neutral-950 shadow-lg shadow-amber-500/25' : 'font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04]' }} transition">
                <span class="text-sm">🥽</span><span>VR Monitor</span>
            </a>
            <a href="{{ route('dashboard.directory') }}" id="navBtn-directory" 
               class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs {{ request()->routeIs('dashboard.directory') ? 'font-bold bg-amber-500 text-neutral-950 shadow-lg shadow-amber-500/25' : 'font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04]' }} transition">
                <span class="text-sm">👥</span><span>Directory</span>
            </a>
            <a href="{{ route('dashboard.analysis') }}" id="navBtn-dossier" 
               class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs {{ request()->routeIs('dashboard.analysis') ? 'font-bold bg-amber-500 text-neutral-950 shadow-lg shadow-amber-500/25' : 'font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04]' }} transition">
                <span class="text-sm">📋</span><span>Analysis</span>
            </a>
            <button onclick="exportPdfCertificate()" 
               class="w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-semibold text-slate-400 hover:text-amber-300 hover:bg-amber-400/10 transition cursor-pointer text-left">
                <span class="text-sm">📄</span><span>PDF Export</span>
            </button>
            <a href="{{ route('dashboard.analytics') }}" id="navBtn-analytics" 
               class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs {{ request()->routeIs('dashboard.analytics') ? 'font-bold bg-amber-500 text-neutral-950 shadow-lg shadow-amber-500/25' : 'font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04]' }} transition">
                <span class="text-sm">📊</span><span>Analytics</span>
            </a>
        </nav>
    </div>

    <div class="px-3.5 py-2.5 rounded-2xl glass-card text-xs font-medium text-slate-300 flex items-center justify-between">
        <span class="flex items-center gap-2">
            <span class="text-amber-400">💬</span><span>OSHA 29 CFR</span>
        </span>
        <span class="text-slate-500 font-mono text-[11px]">Active</span>
    </div>
</aside>