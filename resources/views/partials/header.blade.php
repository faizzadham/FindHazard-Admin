<!-- TOPBAR NAVIGATION SECTION -->
<header class="flex flex-col xl:flex-row items-center justify-between gap-3 pb-4 border-b border-white/10">
    <!-- Brand / Logo -->
    <a href="{{ route('dashboard.overview') }}" class="flex items-center gap-2.5 px-1 hover:opacity-85 transition shrink-0 group">
        <span class="text-2xl drop-shadow group-hover:scale-105 transition-transform">⚠️</span>
        <div class="flex flex-col">
            <span class="text-base font-extrabold text-white tracking-wide leading-tight">FindHazard <span class="text-amber-400">VR</span></span>
            <span class="text-[11px] font-mono text-slate-400 uppercase tracking-widest font-bold">Supervisor Command</span>
        </div>
    </a>

    <!-- Topbar Navigation Links (Strict Single Line / No Wrap) -->
    <nav id="topbar-nav" class="flex items-center flex-nowrap justify-center gap-1 p-1 bg-white/[0.03] border border-white/10 rounded-2xl font-sans shadow-inner shrink-0 overflow-x-auto max-w-full">
        <a href="{{ route('dashboard.overview') }}" id="navBtn-overview" 
           class="nav-item flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl text-xs sm:text-[13px] whitespace-nowrap shrink-0 transition duration-200 {{ request()->routeIs('dashboard.overview') ? 'font-bold bg-amber-500 text-neutral-950 shadow-md shadow-amber-500/25' : 'font-semibold text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <span class="text-xs sm:text-sm">▦</span><span>Overview</span>
        </a>
        <a href="{{ route('dashboard.monitor') }}" id="navBtn-monitor" 
           class="nav-item flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl text-xs sm:text-[13px] whitespace-nowrap shrink-0 transition duration-200 {{ request()->routeIs('dashboard.monitor') ? 'font-bold bg-amber-500 text-neutral-950 shadow-md shadow-amber-500/25' : 'font-semibold text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <span class="text-xs sm:text-sm">🥽</span><span>VR Monitor</span>
        </a>
        <a href="{{ route('dashboard.directory') }}" id="navBtn-directory" 
           class="nav-item flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl text-xs sm:text-[13px] whitespace-nowrap shrink-0 transition duration-200 {{ request()->routeIs('dashboard.directory') ? 'font-bold bg-amber-500 text-neutral-950 shadow-md shadow-amber-500/25' : 'font-semibold text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <span class="text-xs sm:text-sm">👥</span><span>Directory</span>
        </a>
        <a href="{{ route('dashboard.analytics') }}" id="navBtn-analytics" 
           class="nav-item flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl text-xs sm:text-[13px] whitespace-nowrap shrink-0 transition duration-200 {{ request()->routeIs('dashboard.analytics') ? 'font-bold bg-amber-500 text-neutral-950 shadow-md shadow-amber-500/25' : 'font-semibold text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <span class="text-xs sm:text-sm">📊</span><span>Analytics</span>
        </a>
        <a href="{{ route('dashboard.guide') }}" id="navBtn-guide" 
           class="nav-item flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl text-xs sm:text-[13px] whitespace-nowrap shrink-0 transition duration-200 {{ request()->routeIs('dashboard.guide') ? 'font-bold bg-amber-500 text-neutral-950 shadow-md shadow-amber-500/25' : 'font-semibold text-slate-300 hover:text-white hover:bg-white/[0.06]' }}">
            <span class="text-xs sm:text-sm">📖</span><span>Evaluation Guide</span>
        </a>
    </nav>

    <!-- Topbar Right Controls -->
    <div class="flex items-center gap-1.5 sm:gap-2 shrink-0 w-full xl:w-auto justify-end">

        <!-- Date Indicator -->
        <div class="glass-card px-2.5 sm:px-3 py-1.5 rounded-xl text-xs text-slate-200 transition font-mono font-medium flex items-center gap-1.5">
            <span>📅</span>
            <span>{{ now()->timezone('Asia/Kuala_Lumpur')->format('d M Y') }}</span>
        </div>

        <!-- Theme Mode Toggle Button (Logo/Icon Only, No Text) -->
        <button type="button" 
                id="btnThemeToggle" 
                onclick="toggleDashboardTheme()" 
                class="glass-card hover:bg-white/[0.08] text-slate-300 hover:text-white w-8 h-8 sm:w-9 sm:h-9 rounded-xl transition cursor-pointer flex items-center justify-center border border-white/10 active:scale-95 group shrink-0" 
                title="Toggle Light / Dark Mode" 
                aria-label="Toggle Light / Dark Mode">
            <!-- Sun Logo Icon (Visible in Dark Mode -> Click to activate Light Mode) -->
            <svg id="themeIconSun" class="w-4 h-4 text-amber-400 group-hover:scale-110 group-hover:rotate-45 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <!-- Moon Logo Icon (Visible in Light Mode -> Click to activate Dark Mode) -->
            <svg id="themeIconMoon" class="w-4 h-4 text-amber-500 group-hover:scale-110 group-hover:-rotate-12 transition-all hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>

        <!-- Fullscreen Web Page Toggle Button -->
        <button type="button" 
                id="btnHeaderFullscreen" 
                onclick="togglePageFullscreen()" 
                class="glass-card hover:bg-white/[0.08] text-slate-200 hover:text-white px-2.5 sm:px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer flex items-center gap-1.5 border border-white/10 active:scale-95 group" 
                title="Toggle Fullscreen Mode">
            <!-- Expand Icon (Default) -->
            <svg id="headerFullscreenIconExpand" class="w-3.5 h-3.5 text-amber-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
            </svg>
            <!-- Exit/Compress Icon (When in Fullscreen) -->
            <svg id="headerFullscreenIconExit" class="w-3.5 h-3.5 text-amber-400 hidden group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 4v4m0 0H5m4 0L4 3m11 1v4m0 0h4m-4 0l5-5M9 20v-4m0 0H5m4 0l-5 5m11-5v4m0-4h4m-4 0l5 5" />
            </svg>
            <span id="headerFullscreenText" class="font-mono text-xs font-bold">Fullscreen</span>
        </button>

        <!-- Supervisor Avatar -->
        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-amber-400/20 border border-amber-400/30 flex items-center justify-center text-amber-300 font-extrabold text-xs sm:text-sm" title="Supervisor: Faiz Adham">
            FZ
        </div>

        <!-- Logout Action -->
        <a href="{{ route('login') }}" class="glass-card hover:bg-rose-950/40 text-rose-300 border-rose-500/30 px-2.5 sm:px-3 py-1.5 rounded-xl text-xs font-bold transition cursor-pointer">
            Log Out
        </a>
    </div>
</header>
