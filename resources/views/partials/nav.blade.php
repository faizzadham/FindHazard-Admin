<aside class="w-full lg:w-56 flex flex-col justify-between shrink-0 space-y-6">
    <div class="space-y-6">
        <div class="flex items-center gap-2.5 px-2">
            <span class="text-lg">⚠️</span>
            <h2 class="text-base font-extrabold text-white tracking-wide">FindHazard VR</h2>
        </div>

        <!-- Workflow Navigation List -->
        <nav class="space-y-1 font-sans">
            <!-- 2. Operations Command Center -->
            <button onclick="navigateView('overview')" id="navBtn-overview" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-bold bg-amber-500 text-neutral-950 shadow-lg shadow-amber-500/25 transition">
                <span class="text-sm">▦</span>
                <span>Overview</span>
            </button>

            <!-- 3. Live VR Session Monitor -->
            <button onclick="navigateView('monitor')" id="navBtn-monitor" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04] transition">
                <span class="text-sm">🥽</span>
                <span> VR Monitor</span>
            </button>

            <!-- 4. Trainee Directory -->
            <button onclick="navigateView('directory')" id="navBtn-directory" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04] transition">
                <span class="text-sm">👥</span>
                <span> Directory</span>
            </button>

            <!-- 5. Telemetry Dossier -->
            <button onclick="navigateView('dossier')" id="navBtn-dossier" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04] transition">
                <span class="text-sm">📋</span>
                <span> Telemetry</span>
            </button>

            <!-- 6. PDF Certificate Export -->
            <button onclick="exportPdfCertificate()" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-semibold text-slate-400 hover:text-amber-300 hover:bg-amber-400/10 transition">
                <span class="text-sm">📄</span>
                <span> PDF Export</span>
            </button>

            <!-- 7. Cohort & Zone Analytics -->
            <button onclick="navigateView('analytics')" id="navBtn-analytics" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04] transition">
                <span class="text-sm">📊</span>
                <span> Analytics</span>
            </button>

            <button onclick="showToast('Supervisor Settings', 'Runtime parameters and OpenXR settings operational.')" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04] transition">
                <span class="text-sm">⚙️</span>
                <span>Settings</span>
            </button>
        </nav>
    </div>

    <!-- Bottom Support Card -->
    <button onclick="showToast('OSHA Safety Guidelines', 'Standard: 29 CFR 1910 Industrial Hazard Identification.')" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl glass-card text-xs font-medium text-slate-300 hover:text-white transition">
        <span class="flex items-center gap-2">
            <span class="text-amber-400">💬</span>
            <span>OSHA Protocol</span>
        </span>
        <span class="text-slate-500 font-mono text-[11px]">→</span>
    </button>
</aside>