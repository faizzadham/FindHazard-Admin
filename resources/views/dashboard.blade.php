<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHazard VR — Supervisor Glass Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #08090d; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        .glass-sheet {
            background: rgba(15, 17, 26, 0.85);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.8), inset 0 1px 0 rgba(255, 255, 255, 0.12);
        }

        .glass-card {
            background: rgba(22, 25, 38, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            transition: all 0.25s ease;
        }

        .glass-card:hover {
            border-color: rgba(245, 158, 11, 0.35);
            box-shadow: 0 16px 36px rgba(245, 158, 11, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .glass-pill {
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.3);
            backdrop-filter: blur(8px);
        }

        .glass-input {
            background: rgba(10, 12, 18, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-input:focus { border-color: rgba(245, 158, 11, 0.5); }

        .iso-scene { perspective: 800px; }
        .iso-cube-wrap { transform-style: preserve-3d; transform: rotateX(58deg) rotateZ(45deg); }
        .cube-face {
            position: absolute; width: 72px; height: 72px;
            background: rgba(245, 158, 11, 0.18);
            border: 1.5px solid rgba(245, 158, 11, 0.7);
            box-shadow: 0 0 25px rgba(245, 158, 11, 0.4), inset 0 0 15px rgba(245, 158, 11, 0.25);
        }
        .cube-face-front  { transform: translateZ(36px); }
        .cube-face-back   { transform: rotateY(180deg) translateZ(36px); }
        .cube-face-right  { transform: rotateY(90deg) translateZ(36px); }
        .cube-face-left   { transform: rotateY(-90deg) translateZ(36px); }
        .cube-face-top    { transform: rotateX(90deg) translateZ(36px); }
        .cube-face-bottom { transform: rotateX(-90deg) translateZ(36px); }
        .inner-core {
            position: absolute; top: 20px; left: 20px; width: 32px; height: 32px; border-radius: 6px;
            background: #fef08a; box-shadow: 0 0 35px 12px rgba(245, 158, 11, 0.95);
            animation: pulse-core 2.4s ease-in-out infinite alternate;
        }
        @keyframes pulse-core { from { transform: scale(0.9); } to { transform: scale(1.15); filter: brightness(1.4); } }

        .app-view { display: none; }
        .app-view.active { display: block; }
    </style>
</head>
<body class="text-slate-100 min-h-screen relative overflow-x-hidden p-4 sm:p-7 flex flex-col items-center justify-center selection:bg-amber-400 selection:text-neutral-950">

    <!-- Atmospheric Yellow Ambient Glow -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 left-1/3 w-[46rem] h-[46rem] bg-amber-500/10 blur-[160px] rounded-full"></div>
        <div class="absolute bottom-10 right-10 w-[38rem] h-[38rem] bg-yellow-500/[0.08] blur-[150px] rounded-full"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#ffffff06_1px,transparent_1px)] [background-size:32px_32px]"></div>
    </div>

    <!-- Toast Notification -->
    <div id="toastMessage" class="fixed top-6 right-6 z-50 transform -translate-y-10 opacity-0 pointer-events-none transition-all duration-300 glass-card px-4 py-3 rounded-2xl flex items-center gap-3 border-amber-500/40">
        <span id="toastIcon" class="text-amber-400 text-lg">⚡</span>
        <div>
            <p id="toastTitle" class="text-xs font-bold text-white">System Alert</p>
            <p id="toastBody" class="text-[11px] text-slate-400">Headset analysis operational.</p>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 1. LOGIN SCREEN CONTAINER                                -->
    <!-- ======================================================== -->
    <div id="view-login" class="relative z-20 w-full max-w-md my-auto flex flex-col items-center justify-center">
        <div class="text-center space-y-2 mb-6">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full glass-card border border-amber-500/30 text-amber-400 text-xs font-mono tracking-widest uppercase">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                Industrial VR Safety
            </div>
            <h1 class="text-3xl font-black uppercase tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-amber-200 to-yellow-500">
                FindHazard VR
            </h1>
            <p class="text-xs text-slate-400">Supervisor Command Center</p>
        </div>

        <div class="glass-sheet rounded-3xl p-6 sm:p-8 space-y-5 w-full">
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider">Username</label>
                    <input id="loginUsername" type="text" value="supervisor"
                        class="w-full glass-input rounded-xl px-4 py-2.5 text-xs text-slate-100 placeholder-slate-600 focus:outline-none transition font-mono">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider">Password</label>
                    <input id="loginPassword" type="password" value="admin123"
                        class="w-full glass-input rounded-xl px-4 py-2.5 text-xs text-slate-100 placeholder-slate-600 focus:outline-none transition font-mono">
                </div>

                <!-- Pure Button Click (Does NOT submit or reload page) -->
                <button type="button" id="loginBtnTrigger" onclick="forceUnlockDashboard()"
                    class="w-full mt-2 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-neutral-950 font-black text-xs py-3 rounded-xl transition shadow-lg shadow-amber-500/25 cursor-pointer active:scale-95">
                    Access Supervisor Portal →
                </button>
            </div>
            <p class="text-[10px] text-center text-slate-500 font-mono">Default credentials: supervisor / admin123</p>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- 2. OPERATIONS DASHBOARD CONTAINER (HIDDEN UNTIL UNLOCKED)-->
    <!-- ======================================================== -->
    <div id="view-dashboard" style="display: none;" class="relative z-10 w-full max-w-[1440px] space-y-6">

        <!-- Top Master Glass Sheet -->
        <section class="glass-sheet rounded-[2.2rem] p-6 lg:p-7 flex flex-col lg:flex-row gap-7">

            <!-- SIDEBAR NAVIGATION -->
            <aside class="w-full lg:w-56 flex flex-col justify-between shrink-0 space-y-6">
                <div class="space-y-6">
                    <div class="flex items-center gap-2.5 px-2">
                        <span class="text-lg">⚠️</span>
                        <h2 class="text-base font-extrabold text-white tracking-wide">FindHazard VR</h2>
                    </div>

                    <nav class="space-y-1 font-sans">
                        <button onclick="navigateView('overview')" id="navBtn-overview" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-bold bg-amber-500 text-neutral-950 shadow-lg shadow-amber-500/25 transition">
                            <span class="text-sm">▦</span><span>Overview</span>
                        </button>
                        <button onclick="navigateView('monitor')" id="navBtn-monitor" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04] transition">
                            <span class="text-sm">🥽</span><span>VR Monitor</span>
                        </button>
                        <button onclick="navigateView('directory')" id="navBtn-directory" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04] transition">
                            <span class="text-sm">👥</span><span>Directory</span>
                        </button>
                        <button onclick="navigateView('dossier')" id="navBtn-dossier" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04] transition">
                            <span class="text-sm">📋</span><span>Analysis</span>
                        </button>
                        <button onclick="exportPdfCertificate()" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-semibold text-slate-400 hover:text-amber-300 hover:bg-amber-400/10 transition">
                            <span class="text-sm">📄</span><span>PDF Export</span>
                        </button>
                        <button onclick="navigateView('analytics')" id="navBtn-analytics" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04] transition">
                            <span class="text-sm">📊</span><span>Analytics</span>
                        </button>
                    </nav>
                </div>

                <div class="px-3.5 py-2.5 rounded-2xl glass-card text-xs font-medium text-slate-300 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="text-amber-400">💬</span><span>OSHA 29 CFR</span>
                    </span>
                    <span class="text-slate-500 font-mono text-[11px]">Active</span>
                </div>
            </aside>

            <!-- MAIN WORKSPACE VIEWS -->
            <div class="flex-1 space-y-6">

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
                        <button onclick="lockDashboard()" class="glass-card hover:bg-rose-950/40 text-rose-300 border-rose-500/30 px-3 py-2 rounded-2xl text-xs font-semibold transition cursor-pointer">
                            Lock
                        </button>
                    </div>
                </header>

                <!-- TAB 1: OPERATIONS COMMAND CENTER (OVERVIEW) -->
                <div id="view-overview" class="app-view active space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-1 border-b border-white/5">
                        <div>
                            <h1 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2">Operations Command Center 👋</h1>
                            <p class="text-xs text-slate-400 mt-0.5">Live supervisor analysis feed optimized for secondary monitor (Display 2).</p>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <button onclick="toggleDisplay2Fullscreen()" class="glass-pill px-3 py-1.5 rounded-xl text-xs font-mono text-amber-300 hover:text-white transition">
                                🖥️ Display 2 Fullscreen
                            </button>
                            <div class="glass-card px-3 py-1.5 rounded-xl text-xs font-mono flex items-center gap-2 border-emerald-500/30 text-emerald-400">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Sync: <strong id="refreshCountdown" class="text-white">10s</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- 4 Required Experience KPIs -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                        <div class="glass-card p-4 rounded-3xl space-y-2">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase">Total Sessions</span>
                            <div class="text-2xl font-black text-white font-mono">{{ $totalSessions ?? 5 }}</div>
                            <span class="text-[10px] text-emerald-400 font-mono">↑ 100% Synced</span>
                        </div>
                        <div class="glass-card p-4 rounded-3xl space-y-2">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase">Cohort Avg Score</span>
                            <div class="text-2xl font-black text-amber-400 font-mono">{{ isset($avgScore) ? round($avgScore, 1) : '86.4' }} pts</div>
                            <span class="text-[10px] text-emerald-400 font-mono">↑ +11.4 pts over passing mark</span>
                        </div>
                        <div class="glass-card p-4 rounded-3xl space-y-2">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase">Mean Inspection Time</span>
                            <div class="text-2xl font-black text-white font-mono">{{ isset($avgTime) ? round($avgTime, 1) : '56.8' }}s</div>
                            <span class="text-[10px] text-cyan-400 font-mono">⚡ 90 FPS Latency</span>
                        </div>
                        <div class="glass-card p-4 rounded-3xl space-y-2">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase">Hazards Identified</span>
                            <div class="text-2xl font-black text-white font-mono">{{ $totalHazardsFound ?? 43 }} <span class="text-xs text-slate-500">/ 50</span></div>
                            <span class="text-[10px] text-emerald-400 font-mono">86% Detection Rate</span>
                        </div>
                    </div>

                    <!-- Middle: Reaction Curve & Incoming Runs -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                        <div class="lg:col-span-2 glass-card p-5 rounded-3xl space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Hazard Detection &amp; Reaction Curve</h3>
                                <span class="glass-pill px-3 py-1 rounded-xl text-xs font-mono text-amber-300">Live Experience ▾</span>
                            </div>
                            <div class="relative h-44 w-full">
                                <svg class="w-full h-full" viewBox="0 0 500 160" preserveAspectRatio="none">
                                    <defs>
                                        <linearGradient id="amberGlowGrad" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.55"/>
                                            <stop offset="100%" stop-color="#08090d" stop-opacity="0.0"/>
                                        </linearGradient>
                                    </defs>
                                    <path d="M 0 135 C 50 130, 90 90, 150 110 C 210 130, 260 50, 310 65 C 370 80, 410 15, 500 30 L 500 160 L 0 160 Z" fill="url(#amberGlowGrad)" />
                                    <path d="M 0 135 C 50 130, 90 90, 150 110 C 210 130, 260 50, 310 65 C 370 80, 410 15, 500 30" fill="none" stroke="#f59e0b" stroke-width="3"/>
                                    <circle cx="410" cy="22" r="5" fill="#fef08a" stroke="#f59e0b" stroke-width="3"/>
                                </svg>
                            </div>
                        </div>

                        <div class="glass-card p-5 rounded-3xl space-y-3 flex flex-col justify-between">
                            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Active Incoming Runs</h3>
                            <div id="incomingRunsContainer" class="space-y-2"></div>
                        </div>
                    </div>
                </div>

               <!-- ======================================================== -->
<!-- 3. LIVE VR SESSION MONITOR (/live-monitor)               -->
<!-- ======================================================== -->
<div id="view-monitor" class="app-view space-y-4">

    <!-- Top Telemetry Bar with Timer & Zone Selector -->
    <div class="flex flex-wrap items-center justify-between gap-3 pb-2 border-b border-white/5">
        <div>
            <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <span>Live VR Session Monitor</span>
                <span class="text-[10px] font-mono bg-amber-500/10 text-amber-400 border border-amber-500/30 px-2 py-0.5 rounded-full">/live-monitor</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
            </h2>
            <p class="text-[11px] text-slate-400 font-mono">Meta Quest 3 • OpenXR 6DoF Real-time Viewport &amp; Event Stream</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 text-xs font-mono">
            <!-- Active Session Timer -->
            <div class="glass-pill px-3 py-1.5 rounded-xl text-amber-300 flex items-center gap-2 border-amber-400/40">
                <span>⏱ Active Timer:</span>
                <strong id="liveSessionTimer" class="text-white font-bold text-sm tracking-widest">00:47.9</strong>
            </div>

            <!-- Current Warehouse Zone Selector -->
            <div class="glass-card px-3 py-1.5 rounded-xl text-slate-200 flex items-center gap-2">
                <span>📍 Zone:</span>
                <select id="warehouseZoneSelect" onchange="changeWarehouseZone(this.value)" class="bg-transparent text-amber-400 font-bold focus:outline-none cursor-pointer">
                    <option value="Zone 2: Electrical Vault" class="bg-neutral-900 text-slate-100">Zone 2: Electrical Vault</option>
                    <option value="Zone 1: Conveyor Line" class="bg-neutral-900 text-slate-100">Zone 1: Conveyor Line</option>
                    <option value="Zone 3: Chemical Egress" class="bg-neutral-900 text-slate-100">Zone 3: Chemical Egress</option>
                </select>
            </div>

            <button onclick="centerReticle()" class="glass-card px-3 py-1.5 rounded-xl text-amber-400 hover:text-white transition cursor-pointer">
                🎯 Center Reticle
            </button>
        </div>
    </div>

    <!-- Main Viewport + Live Click Event Feed Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

        <!-- Left: Interactive Canvas Screen Viewport (Span 8) -->
        <div class="lg:col-span-8 space-y-2">
            <div class="relative bg-black/95 rounded-3xl overflow-hidden border border-white/10 aspect-[16/9] flex items-center justify-center cursor-crosshair shadow-2xl">
                <canvas id="vrMonitorCanvas" class="w-full h-full block"></canvas>

                <!-- Floating Current Zone & Trainee Overlay -->
                <div class="absolute top-4 left-4 glass-sheet p-3 rounded-2xl border-amber-500/40 text-left shadow-2xl pointer-events-none">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="text-xs font-bold text-white font-mono" id="hudWarehouseZoneLabel">Zone 2: Electrical Vault</span>
                    </div>
                    <p class="text-[10px] text-amber-300 font-mono mt-0.5" id="monitorActiveUser">Operator: Trainee qq (Quest 3)</p>
                </div>

                <!-- Bottom Telemetry HUD Overlays -->
                <div class="absolute bottom-4 left-4 glass-pill px-3 py-1.5 rounded-xl text-[10px] font-mono text-emerald-400 flex items-center gap-2">
                    <span>AIM LOCK:</span>
                    <span id="currentAimTarget" class="text-white font-bold">480V Electrical Box</span>
                    <span id="currentAimType" class="text-amber-400">(Hazard Target)</span>
                </div>

                <div class="absolute bottom-4 right-4 glass-pill px-3 py-1.5 rounded-xl text-[10px] font-mono text-slate-400">
                    90 FPS &bull; FOVEATED STREAM &bull; 6DoF
                </div>
            </div>

            <p class="text-[11px] text-slate-400 font-mono flex items-center justify-between px-1">
                <span>Click inside the canvas to simulate Quest 3 trigger clicks on hazard boxes or safe tool racks.</span>
                <span class="text-amber-400 font-semibold">Real-time Telemetry Synced</span>
            </p>
        </div>

        <!-- Right: Live Click Event Feed (Hits vs Distractor Misclicks) (Span 4) -->
        <div class="lg:col-span-4 glass-card p-4 rounded-3xl space-y-3 flex flex-col justify-between">
            <div class="flex items-center justify-between border-b border-white/5 pb-2">
                <div>
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        <span>Click Event Feed</span>
                    </h3>
                    <p class="text-[10px] text-slate-400 font-mono">Live Quest 3 trigger stream</p>
                </div>
                <button onclick="clearEventFeed()" class="text-[10px] text-slate-400 hover:text-white font-mono cursor-pointer">Clear</button>
            </div>

            <!-- Tally Counters -->
            <div class="grid grid-cols-2 gap-2 text-center font-mono">
                <div class="glass-sheet p-2 rounded-xl border border-emerald-500/30">
                    <span class="text-[10px] text-emerald-400 block font-bold">HAZARD HITS</span>
                    <span class="text-lg font-black text-white" id="statHazardHits">4</span>
                </div>
                <div class="glass-sheet p-2 rounded-xl border border-rose-500/30">
                    <span class="text-[10px] text-rose-400 block font-bold">MISCLICKS</span>
                    <span class="text-lg font-black text-white" id="statMisclicks">1</span>
                </div>
            </div>

            <!-- Event Feed Items -->
            <div id="clickEventFeedList" class="space-y-2 overflow-y-auto max-h-[300px] pr-1"></div>
        </div>

    </div>
</div>

                <!-- ======================================================== -->
<!-- 4. TRAINEE DIRECTORY (/trainees)                  -->
<!-- ======================================================== -->
<div id="view-directory" class="app-view space-y-5">

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
                <!-- TAB 4: ANALYSIS -->
                <div id="view-dossier" class="app-view space-y-5">
                    <div class="glass-card rounded-3xl p-5 border-amber-500/20 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-white/5">
                            <div>
                                <h3 id="dossierTitle" class="text-base font-bold text-white">Analysis: ${trainee.id}</h3>
                                <p class="text-xs text-slate-400" id="dossierDate">Session Verified</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span id="dossierRating" class="glass-pill px-3 py-1 rounded-xl text-xs font-mono text-emerald-400">Locked In</span>
                                <span id="dossierScore" class="glass-pill px-3 py-1 rounded-xl text-xs font-mono text-amber-400 font-bold">100 / 100 Pts</span>
                                <button onclick="exportPdfCertificate()" class="bg-gradient-to-r from-amber-400 to-amber-500 text-neutral-950 font-bold px-3 py-1 rounded-xl text-xs">
                                    📄 Certificate
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="glass-card rounded-3xl p-5 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200">10 Safety Hazard Checkpoints</h4>
                        <div id="checkpointsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-2.5"></div>
                    </div>
                </div>

                <!-- TAB 5: EXPERIENCE ANALYTICS -->
                <div id="view-analytics" class="app-view space-y-5">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="glass-card p-6 rounded-3xl text-center space-y-4">
                            <h3 class="text-xs font-bold uppercase text-slate-200">Cohort Benchmark Average</h3>
                            <div class="text-4xl font-black text-amber-400 font-mono py-6">85%</div>
                            <p class="text-[11px] text-slate-400 font-mono">Calculated across registered Quest 3 trainees.</p>
                        </div>
                        <div class="glass-card p-6 rounded-3xl space-y-4">
                            <h3 class="text-xs font-bold uppercase text-slate-200">Hazard Zone Distribution</h3>
                            <div class="grid grid-cols-2 gap-3 text-xs font-mono">
                                <div class="glass-sheet p-3 rounded-xl text-amber-400">⚡ Electrical: 35%</div>
                                <div class="glass-sheet p-3 rounded-xl text-amber-400">⚙️ Mechanical: 25%</div>
                                <div class="glass-sheet p-3 rounded-xl text-amber-400">🧪 Chemical: 20%</div>
                                <div class="glass-sheet p-3 rounded-xl text-amber-400">🧯 Fire/Egress: 20%</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- Bottom Bar -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
            <div class="md:col-span-3 glass-sheet p-5 rounded-[2rem] space-y-2">
                <span class="text-xs font-semibold text-slate-400">Analysis</span>
                <div class="text-xs font-bold text-emerald-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> All Systems Operational
                </div>
            </div>
            <div class="md:col-span-6 glass-sheet p-5 rounded-[2rem]">
                <div class="grid grid-cols-4 gap-2 text-center text-[10px] font-bold">
                    <button onclick="navigateView('overview')" class="glass-card p-2 rounded-xl text-slate-200 hover:text-white">Overview</button>
                    <button onclick="navigateView('monitor')" class="glass-card p-2 rounded-xl text-slate-200 hover:text-white">VR Monitor</button>
                    <button onclick="navigateView('directory')" class="glass-card p-2 rounded-xl text-slate-200 hover:text-white">Directory</button>
                    <button onclick="exportPdfCertificate()" class="glass-card p-2 rounded-xl text-amber-400">Certificate</button>
                </div>
            </div>
            <div class="md:col-span-3 glass-sheet p-5 rounded-[2rem] flex items-center justify-center h-[122px]">
                <div class="iso-scene scale-90">
                    <div class="iso-cube-wrap relative w-[72px] h-[72px]">
                        <div class="cube-face cube-face-front"></div>
                        <div class="cube-face cube-face-back"></div>
                        <div class="cube-face cube-face-right"></div>
                        <div class="cube-face cube-face-left"></div>
                        <div class="cube-face cube-face-top"></div>
                        <div class="cube-face cube-face-bottom"></div>
                        <div class="inner-core"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ======================================================== -->
    <!-- COMPLETE JAVASCRIPT LOGIC (NO EXTERNAL ERRORS)           -->
    <!-- ======================================================== -->
    <script>
        const trainees = [
            { id: 'qq', experience: 'Manufacturing', score: 100, found: 10, missed: 0, time: '47.9s', rating: 'Locked In', date: '01 Oct 2026, 06:18', dateShort: '01 Oct 2026', notes: 'Detected 480V arc-flash hazard under 5.1s.', missedIndices: [] },
            { id: 'Ahmad_Rizal', experience: 'Logistics Center', score: 90, found: 9, missed: 1, time: '52.3s', rating: 'Great', date: '01 Oct 2026, 05:42', dateShort: '01 Oct 2026', notes: 'Overlooked overhead crane fray.', missedIndices: [7] },
            { id: 'Sarah_Tan', experience: 'Manufacturing', score: 90, found: 9, missed: 1, time: '54.1s', rating: 'Great', date: '01 Oct 2026, 04:15', dateShort: '01 Oct 2026', notes: 'Missed blocked extinguisher in corridor 3.', missedIndices: [4] },
            { id: 'Danial_Haziq', experience: 'Retail', score: 80, found: 8, missed: 2, time: '61.5s', rating: 'Valid Effort', date: '30 Sep 2026, 17:30', dateShort: '30 Sep 2026', notes: 'Missed 480V enclosure latch.', missedIndices: [2, 8] },
            { id: 'Nurul_Ain', experience: 'Logistics Center', score: 70, found: 7, missed: 3, time: '68.2s', rating: 'Cooked', date: '30 Sep 2026, 15:10', dateShort: '30 Sep 2026', notes: 'Retraining recommended for GHS symbols.', missedIndices: [2, 5, 9] }
        ];

        const scenarioCheckpoints = [
            { id: 1, name: 'Unguarded Conveyor Belt', cat: 'Mechanical Hazard', time: '00:03.4' },
            { id: 2, name: 'Exposed 480V Junction Box', cat: 'Electrical Hazard', time: '00:05.1' },
            { id: 3, name: 'Hydraulic Oil Fluid Spill', cat: 'Slip & Fall', time: '00:07.8' },
            { id: 4, name: 'Blocked Fire Extinguisher', cat: 'Fire & Egress', time: '00:12.0' },
            { id: 5, name: 'Chemical Barrel without GHS', cat: 'Chemical Protocol', time: '00:18.2' },
            { id: 6, name: 'Unanchored Scaffolding', cat: 'Fall Hazard', time: '00:23.5' },
            { id: 7, name: 'Overhead Crane Cable Fray', cat: 'Rigging Hazard', time: '00:29.1' },
            { id: 8, name: 'Missing Eye Protection Station', cat: 'PPE Compliance', time: '00:34.7' },
            { id: 9, name: 'Locked Emergency Exit Door', cat: 'Fire & Egress', time: '00:41.0' },
            { id: 10, name: 'Heavy Extension Cord in Pathway', cat: 'Trip Hazard', time: '00:46.3' }
        ];

        let selectedTrainee = trainees[0];
        let sessionSeconds = 47.9;
        let timerInterval = null;
        let hazardHitsCount = 4;
        let misclicksCount = 1;

        const clickEventFeed = [
            { time: '00:05.1', label: '480V Arc Box', type: 'hit', points: '+10 PTS' },
            { time: '00:07.8', label: 'Hydraulic Spill', type: 'hit', points: '+10 PTS' },
            { time: '00:15.3', label: 'Safe Tool Rack', type: 'misclick', points: '0 PTS' }
        ];

        // 1. UNLOCK DASHBOARD FUNCTION (Direct DOM Manipulation)
        function forceUnlockDashboard() {
            const loginBox = document.getElementById('view-login');
            const dashBox = document.getElementById('view-dashboard');

            if (loginBox) loginBox.style.setProperty('display', 'none', 'important');
            if (dashBox) dashBox.style.setProperty('display', 'block', 'important');

            showToast('Welcome, En. Faiz', 'Supervisor Operations Center Verified', '✅');
            navigateView('overview');
            renderIncomingRunsLog();
            applyDirectoryFilters();
            loadTraineeDossier('qq');
            startActiveSessionTimer();
            renderClickEventFeed();

            setTimeout(() => {
                resizeCanvas();
                drawMonitor();
            }, 100);
        }

        function lockDashboard() {
            document.getElementById('view-dashboard').style.display = 'none';
            document.getElementById('view-login').style.display = 'flex';
            showToast('Locked', 'Supervisor session locked.', '🔒');
        }

        function showToast(title, body, icon = '⚡') {
            const toast = document.getElementById('toastMessage');
            if (!toast) return;
            document.getElementById('toastTitle').innerText = title;
            document.getElementById('toastBody').innerText = body;
            document.getElementById('toastIcon').innerText = icon;
            toast.classList.remove('opacity-0', 'pointer-events-none', '-translate-y-10');
            setTimeout(() => toast.classList.add('opacity-0', 'pointer-events-none', '-translate-y-10'), 2500);
        }

        function navigateView(viewKey) {
            document.querySelectorAll('.app-view').forEach(v => v.classList.remove('active'));
            const target = document.getElementById('view-' + viewKey);
            if (target) target.classList.add('active');

            document.querySelectorAll('.nav-item').forEach(b => {
                b.className = 'nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-semibold text-slate-400 hover:text-slate-100 hover:bg-white/[0.04] transition';
            });
            const activeNav = document.getElementById('navBtn-' + viewKey);
            if (activeNav) {
                activeNav.className = 'nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-bold bg-amber-500 text-neutral-950 shadow-lg shadow-amber-500/25 transition';
            }
            if (viewKey === 'monitor') setTimeout(resizeCanvas, 50);
        }

        function applyDirectoryFilters() {
            const searchVal = document.getElementById('directorySearchInput')?.value.toLowerCase().trim() || '';
            const dateVal = document.getElementById('directoryDateFilter')?.value || 'all';
            const expVal = document.getElementById('directoryExpFilter')?.value || 'all';
            const tierVal = document.getElementById('directoryTierFilter')?.value || 'all';

            const filtered = trainees.filter(t => {
                const matchesSearch = !searchVal || t.id.toLowerCase().includes(searchVal);
                const matchesDate = (dateVal === 'all') || (t.dateShort === dateVal);
                const matchesExp = (expVal === 'all') || (t.experience === expVal);
                const matchesTier = (tierVal === 'all') || (t.rating === tierVal);
                return matchesSearch && matchesDate && matchesExp && matchesTier;
            });

            const tbody = document.getElementById('traineeTableBody');
            if (!tbody) return;

            tbody.innerHTML = filtered.map((t, idx) => `
                <tr class="hover:bg-white/[0.02] transition">
                    <td class="p-3.5 font-bold text-white flex items-center gap-2">
                        <span class="w-5 h-5 rounded-lg glass-card text-[10px] text-amber-400 font-mono flex items-center justify-center">#${idx + 1}</span>
                        <span>${t.id}</span>
                    </td>
                    <td class="p-3.5 text-slate-300 font-mono">${t.experience}</td>
                    <td class="p-3.5 font-mono ${t.score >= 90 ? 'text-emerald-400' : 'text-amber-400'} font-bold">${t.score} pts</td>
                    <td class="p-3.5 font-mono text-slate-200">${t.found} / 10</td>
                    <td class="p-3.5 font-mono text-slate-300">${t.time}</td>
                    <td class="p-3.5"><span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-lg ${t.rating === 'Locked In' ? 'bg-emerald-500/10 text-emerald-400' : (t.rating === 'Cooked' ? 'bg-rose-500/20 text-rose-400' : 'bg-amber-500/10 text-amber-400')}">${t.rating}</span></td>
                    <td class="p-3.5 text-slate-400 font-mono">${t.date}</td>
                    <td class="p-3.5 text-center">
                        <button onclick="loadTraineeDossier('${t.id}')" class="px-3 py-1 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 text-neutral-950 font-bold text-[11px] cursor-pointer">
                            Inspect
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function loadTraineeDossier(id) {
            const trainee = trainees.find(t => t.id === id);
            if (!trainee) return;
            selectedTrainee = trainee;

            document.getElementById('dossierTitle').innerText = `Analysis: ${trainee.id}`;
            document.getElementById('dossierDate').innerText = `Drill Date: ${trainee.date}`;
            document.getElementById('dossierRating').innerText = trainee.rating;
            document.getElementById('dossierScore').innerText = `${trainee.score} / 100 Pts`;
            document.getElementById('monitorActiveUser').innerText = `Operator: Trainee ${trainee.id}`;

            document.getElementById('checkpointsContainer').innerHTML = scenarioCheckpoints.map(cp => {
                const missed = trainee.missedIndices.includes(cp.id);
                return `
                    <div class="p-2.5 rounded-2xl flex justify-between items-center text-xs ${missed ? 'glass-card border-rose-500/30' : 'glass-sheet border-white/5'}">
                        <div>
                            <span class="${missed ? 'text-slate-400 line-through' : 'text-slate-200 font-medium'}">${cp.id}. ${cp.name}</span>
                            <span class="block text-[10px] text-slate-500 font-mono">${cp.cat} &bull; ${cp.time}</span>
                        </div>
                        <span class="font-mono ${missed ? 'text-rose-400' : 'text-emerald-400'} font-bold">${missed ? '0 pts' : '+10 pts'}</span>
                    </div>
                `;
            }).join('');

            navigateView('dossier');
            showToast('Dossier Loaded', `Loaded analysis for ${trainee.id}`, '👤');
        }

        function renderIncomingRunsLog() {
            const container = document.getElementById('incomingRunsContainer');
            if (!container) return;
            container.innerHTML = trainees.slice(0, 4).map(run => `
                <div onclick="loadTraineeDossier('${run.id}')" class="glass-sheet p-2.5 rounded-2xl flex items-center justify-between cursor-pointer hover:border-amber-400/40 transition">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-amber-400/10 text-amber-400 font-bold flex items-center justify-center text-xs font-mono">
                            ${run.id.slice(0, 2).toUpperCase()}
                        </span>
                        <div>
                            <p class="text-xs font-bold text-slate-200">${run.id}</p>
                            <p class="text-[9px] text-slate-400 font-mono">${run.found}/10 Spotted &bull; ${run.time}</p>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-mono font-bold ${run.score >= 90 ? 'text-emerald-400' : 'text-amber-400'}">
                        ${run.score} pts
                    </span>
                </div>
            `).join('');
        }

        function startActiveSessionTimer() {
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                sessionSeconds += 0.1;
                const mins = Math.floor(sessionSeconds / 60).toString().padStart(2, '0');
                const secs = (sessionSeconds % 60).toFixed(1).padStart(4, '0');
                const el = document.getElementById('liveSessionTimer');
                if (el) el.innerText = `${mins}:${secs}`;
            }, 100);
        }

        function renderClickEventFeed() {
            const list = document.getElementById('clickEventFeedList');
            if (!list) return;
            list.innerHTML = clickEventFeed.map(item => `
                <div class="p-2 rounded-xl text-xs flex items-center justify-between border ${item.type === 'hit' ? 'glass-sheet border-emerald-500/30' : 'glass-sheet border-rose-500/30'}">
                    <div>
                        <p class="font-bold text-white text-[11px]">${item.label}</p>
                        <span class="text-[9px] text-slate-400 font-mono">${item.time}</span>
                    </div>
                    <span class="font-mono font-bold text-[10px] ${item.type === 'hit' ? 'text-emerald-400' : 'text-rose-400'}">${item.points}</span>
                </div>
            `).join('');
            document.getElementById('statHazardHits').innerText = hazardHitsCount;
            document.getElementById('statMisclicks').innerText = misclicksCount;
        }

        function clearEventFeed() {
            clickEventFeed.length = 0;
            hazardHitsCount = 0;
            misclicksCount = 0;
            renderClickEventFeed();
        }

        function changeWarehouseZone(z) {
            document.getElementById('hudWarehouseZoneLabel').innerText = z;
            showToast('Zone Switched', z, '📍');
        }

        function toggleDisplay2Fullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                document.exitFullscreen().catch(() => {});
            }
        }

        function exportPdfCertificate() {
            const t = selectedTrainee;
            const w = window.open('', '_blank');
            w.document.write(`
                <html><body style="font-family:sans-serif;background:#0b0d14;color:#fff;padding:40px;text-align:center;">
                    <div style="border:4px solid #f59e0b;padding:40px;border-radius:20px;max-width:700px;margin:auto;">
                        <h1 style="color:#f59e0b;">FindHazard VR Safety Certificate</h1>
                        <h2>OSHA Industrial Hazard Detection Protocol</h2>
                        <h1 style="margin:20px 0;">${t.id}</h1>
                        <p>Score: <strong>${t.score}/100</strong> | Duration: <strong>${t.time}</strong></p>
                        <p style="margin-top:30px;font-style:italic;">"${t.notes}"</p>
                        <p style="margin-top:40px;">Supervisor: <strong>En. Faiz</strong></p>
                    </div>
                    <script>window.print();<\/script>
                </body></html>
            `);
            w.document.close();
        }

        // VR Monitor Canvas
        const canvas = document.getElementById('vrMonitorCanvas');
        const ctx = canvas.getContext('2d');
        let mouseX = null, mouseY = null;

        function resizeCanvas() {
            if (!canvas) return;
            const r = canvas.getBoundingClientRect();
            if (r.width > 0) { canvas.width = r.width; canvas.height = r.height; }
        }

        canvas.addEventListener('mousemove', e => {
            const r = canvas.getBoundingClientRect();
            mouseX = e.clientX - r.left; mouseY = e.clientY - r.top;
        });

        canvas.addEventListener('click', e => {
            const r = canvas.getBoundingClientRect();
            const cx = e.clientX - r.left; const cy = e.clientY - r.top;
            const boxX = canvas.width * 0.65; const boxY = canvas.height * 0.48;
            const hit = Math.hypot(cx - boxX, cy - boxY) < 55;

            if (hit) {
                hazardHitsCount++;
                clickEventFeed.unshift({ time: 'NOW', label: '480V Arc Box', type: 'hit', points: '+10 PTS' });
                showToast('Hazard Hit!', '480V Junction Identified (+10)', '✅');
            } else {
                misclicksCount++;
                clickEventFeed.unshift({ time: 'NOW', label: 'Safe Distractor', type: 'misclick', points: '0 PTS' });
                showToast('Misclick', 'Safe element triggered.', '⚠️');
            }
            renderClickEventFeed();
        });

        function drawMonitor() {
            const w = canvas.width, h = canvas.height;
            if (w > 0 && h > 0) {
                ctx.fillStyle = '#07080c';
                ctx.fillRect(0, 0, w, h);

                ctx.strokeStyle = 'rgba(255,255,255,0.06)';
                for (let i = 0; i <= w; i += w / 7) {
                    ctx.beginPath(); ctx.moveTo(w / 2, h * 0.42); ctx.lineTo(i, h); ctx.stroke();
                }

                // 480V Hazard
                const bx = w * 0.65, by = h * 0.48;
                ctx.fillStyle = '#1c1f2e'; ctx.strokeStyle = '#f59e0b'; ctx.lineWidth = 2;
                ctx.fillRect(bx - 35, by - 40, 70, 80); ctx.strokeRect(bx - 35, by - 40, 70, 80);
                ctx.fillStyle = '#f59e0b'; ctx.font = 'bold 11px JetBrains Mono';
                ctx.fillText('⚡ 480V', bx - 22, by - 15);

                // Tool Rack Safe Distractor
                const dx = w * 0.28, dy = h * 0.52;
                ctx.fillStyle = '#141824'; ctx.strokeStyle = 'rgba(255,255,255,0.2)';
                ctx.fillRect(dx - 30, dy - 30, 60, 60); ctx.strokeRect(dx - 30, dy - 30, 60, 60);
                ctx.fillStyle = '#10b981'; ctx.font = '9px JetBrains Mono';
                ctx.fillText('[SAFE]', dx - 16, dy + 5);

                // Raycast
                const tx = mouseX !== null ? mouseX : bx;
                const ty = mouseY !== null ? mouseY : by;
                ctx.strokeStyle = '#f59e0b'; ctx.beginPath(); ctx.moveTo(w * 0.85, h); ctx.lineTo(tx, ty); ctx.stroke();

                ctx.strokeStyle = '#10b981'; ctx.strokeRect(tx - 35, ty - 25, 70, 50);
                ctx.fillStyle = '#10b981'; ctx.fillText('[AIM LOCK]', tx - 30, ty - 30);
            }
            requestAnimationFrame(drawMonitor);
        }

        window.addEventListener('resize', resizeCanvas);
    </script>
</body>
</html>