<!-- ======================================================== -->
<!-- LIVE VR SESSION MONITOR — META QUEST DEVELOPER HUB CAST  -->
<!-- ======================================================== -->
<div id="view-monitor" class="space-y-4">

    @php
        $hasActive = !empty($activeTrainee) && !empty($activeTrainee['username']);
        $traineeName = $hasActive ? $activeTrainee['username'] : null;
        $hazardsFoundCount = $hasActive ? (int)($activeTrainee['hazards_found'] ?? 0) : 0;
        $traineeScore = $hasActive ? (float)($activeTrainee['score'] ?? 0.0) : 0.0;
        $foundPercent = round(($hazardsFoundCount / 13) * 100, 1);
        $foundList = $hasActive ? ($activeTrainee['found_hazards'] ?? []) : [];
        if (is_string($foundList)) {
            $foundList = json_decode($foundList, true) ?? [];
        }
    @endphp

    <!-- 1. Top Control Bar: MQDH Cast Connection & Session Status -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3 pb-2.5 border-b border-white/5">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-lg">🥽</span>
                <h1 class="text-base sm:text-lg font-black text-white tracking-wide uppercase">
                    Live VR Session Monitor
                </h1>
                <span id="castStatusBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-semibold bg-white/[0.04] border border-white/10 text-slate-400 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                    <span>AWAITING MQDH CAST</span>
                </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5 font-mono">
                Connect live Meta Quest Developer Hub (MQDH) Cast stream &bull; Real-time trainee detection telemetry
            </p>
        </div>

        <!-- Cast Action Buttons -->
        <div class="flex flex-wrap items-center gap-2 text-xs font-mono">
            <button id="btnConnectMqdhCast" onclick="connectMqdhCast()" 
                class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-neutral-950 font-black text-xs transition shadow-md shadow-amber-500/20 flex items-center gap-1.5 cursor-pointer">
                <span>🥽</span><span>Connect MQDH Cast</span>
            </button>

            <button id="btnDisconnectCast" onclick="disconnectMqdhCast()" 
                class="hidden px-3 py-1.5 rounded-xl bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white border border-rose-500/40 font-bold transition flex items-center gap-1.5 cursor-pointer">
                <span>✕</span><span>Disconnect</span>
            </button>

            <button onclick="toggleMonitorFullscreen()" 
                class="glass-card px-3 py-1.5 rounded-xl text-slate-300 hover:text-white transition font-mono flex items-center gap-1.5 cursor-pointer" title="Toggle Fullscreen">
                <span>⛶</span><span>Fullscreen</span>
            </button>
        </div>
    </div>

    <!-- 2. Main Live Dashboard Grid: Left Side VR Monitor, Right Side Trainee Telemetry & Hazards -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

        <!-- LEFT SIDE: Live VR Monitor Viewport (Bigger & Cinematic) -->
        <div class="lg:col-span-7 space-y-2.5">
            <div id="vrMonitorScreenContainer" class="relative bg-neutral-950 rounded-2xl overflow-hidden border border-white/10 shadow-2xl w-full aspect-video flex items-center justify-center group transition-all duration-300">

                <!-- Live WebRTC / DisplayMedia Video Player -->
                <video id="vrLiveCastVideo" autoplay playsinline muted disablepictureinpicture controlslist="nodownload nopictureinpicture" class="w-full h-full object-cover bg-black hidden"></video>

                <!-- Standby / Connect Screen (Spacious & Clean) -->
                <div id="vrCastStandbyScreen" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center space-y-3 bg-gradient-to-b from-neutral-900/90 via-neutral-950 to-neutral-950">
                    <div class="relative animate-float-bob">
                        <div class="w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-3xl shadow-lg shadow-amber-500/10">
                            🥽
                        </div>
                        <span class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full bg-amber-400 animate-ping opacity-75"></span>
                        <span class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full bg-amber-400"></span>
                    </div>

                    <div class="max-w-md space-y-1">
                        <h2 class="text-base sm:text-lg font-black text-white tracking-wide uppercase">
                            Meta Quest Developer Hub Live Cast
                        </h2>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Stream the trainee's Quest 3 POV directly into this dashboard using MQDH Device Cast over USB.
                        </p>
                    </div>

                    <!-- Quick Steps -->
                    <div class="grid grid-cols-3 gap-2.5 text-left max-w-md w-full text-[10.5px] font-mono">
                        <div class="p-2.5 rounded-xl glass-card border-white/5">
                            <span class="text-amber-400 font-bold block">1. Open MQDH</span>
                            <span class="text-slate-400 text-[10px]">Open MQDH on PC.</span>
                        </div>
                        <div class="p-2.5 rounded-xl glass-card border-white/5">
                            <span class="text-amber-400 font-bold block">2. Device Cast</span>
                            <span class="text-slate-400 text-[10px]">Click Device Cast.</span>
                        </div>
                        <div class="p-2.5 rounded-xl glass-card border-white/5">
                            <span class="text-amber-400 font-bold block">3. Connect</span>
                            <span class="text-slate-400 text-[10px]">Select cast window.</span>
                        </div>
                    </div>

                    <div class="pt-1.5">
                        <button onclick="connectMqdhCast()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-400 hover:from-amber-400 text-neutral-950 font-black text-xs transition shadow-md shadow-amber-500/25 flex items-center gap-2 cursor-pointer">
                            <span>🥽</span><span>Select MQDH Cast Window →</span>
                        </button>
                    </div>
                </div>

                <!-- ======================================================== -->
                <!-- FULLSCREEN HUD OVERLAY (Visible only in Fullscreen Mode) -->
                <!-- ======================================================== -->
                <div id="vrFullscreenHudOverlay" class="absolute inset-0 pointer-events-none hidden flex-col justify-between p-6 sm:p-8 z-[100] transition-all duration-300">

                    <!-- TOP ROW: Top-Left Trainee Card, Top-Right Score/Wrong Clicks/Hazard & Exit -->
                    <div class="flex items-start justify-between w-full gap-4">

                        <!-- TOP-LEFT: Current Trainee -->
                        <div id="fsTraineeCard" class="glass-sheet bg-neutral-950/85 backdrop-blur-xl border border-white/15 p-3.5 sm:p-4 rounded-2xl shadow-2xl flex items-center gap-3.5 max-w-sm pointer-events-auto transition-all">
                            <div id="fsTraineeAvatar" class="w-11 h-11 rounded-xl border flex items-center justify-center font-black text-sm shadow-inner shrink-0 {{ $hasActive ? 'bg-amber-400/20 border-amber-400/30 text-amber-300' : 'bg-white/5 border-white/10 text-slate-500' }}">
                                <span>{{ $hasActive ? strtoupper(substr($traineeName, 0, 2)) : 'VR' }}</span>
                            </div>
                            <div class="min-w-0 pr-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold">Current Trainee</span>
                                    <span id="fsTraineeStatus" class="px-2 py-0.5 rounded-full text-[9px] font-mono font-bold {{ $hasActive ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-white/5 text-slate-400 border border-white/10' }}">
                                        {{ $hasActive ? '● In VR Headset' : 'Standby' }}
                                    </span>
                                </div>
                                <h3 id="fsTraineeName" class="font-mono tracking-wide font-black text-base sm:text-lg text-white truncate mt-0.5" title="{{ $hasActive ? $traineeName : 'No trainee active yet' }}">
                                    {{ $hasActive ? $traineeName : 'No trainee active yet' }}
                                </h3>
                            </div>
                        </div>

                        <!-- TOP-RIGHT: Score, Wrong Clicks, Hazard & Exit Button -->
                        <div class="flex items-center gap-3 pointer-events-auto">
                            <!-- Telemetry Metrics Card -->
                            <div id="fsStatsCard" class="glass-sheet bg-neutral-950/85 backdrop-blur-xl border border-white/15 px-5 py-3 sm:py-3.5 rounded-2xl shadow-2xl flex items-center gap-5 sm:gap-6 font-mono">
                                <!-- Score -->
                                <div class="text-center">
                                    <span class="text-[9.5px] uppercase tracking-wider text-slate-400 font-bold block">Score</span>
                                    <span id="fsTraineeScore" class="text-base sm:text-lg font-black text-amber-400 block mt-0.5">
                                        {{ number_format($traineeScore, 1) }}%
                                    </span>
                                </div>

                                <div class="h-8 w-px bg-white/10"></div>

                                <!-- Wrong Clicks -->
                                <div class="text-center">
                                    <span class="text-[9.5px] uppercase tracking-wider text-slate-400 font-bold block">Wrong Clicks</span>
                                    <span id="fsWrongClicks" class="text-base sm:text-lg font-black text-rose-400 block mt-0.5">
                                        {{ $hasActive ? (int)($activeTrainee['wrong_clicks'] ?? 0) : 0 }}
                                    </span>
                                </div>

                                <div class="h-8 w-px bg-white/10"></div>

                                <!-- Hazard -->
                                <div class="text-center">
                                    <span class="text-[9.5px] uppercase tracking-wider text-slate-400 font-bold block">Hazard</span>
                                    <span class="text-base sm:text-lg font-black text-emerald-400 block mt-0.5">
                                        <span id="fsHazardCount">{{ $hazardsFoundCount }}</span>
                                        <span class="text-slate-500 text-xs font-semibold">/ 13</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Exit Fullscreen Button -->
                            <button onclick="toggleMonitorFullscreen()" class="glass-sheet bg-neutral-950/85 hover:bg-neutral-900 backdrop-blur-xl border border-white/15 px-3 py-3 rounded-2xl text-slate-300 hover:text-white transition shadow-2xl cursor-pointer flex items-center gap-1.5 font-mono text-xs font-bold" title="Exit Fullscreen (Esc)">
                                <span class="text-sm">✕</span>
                                <span class="hidden sm:inline">Exit</span>
                            </button>
                        </div>
                    </div>

                    <!-- BOTTOM ROW: Bottom-Middle Live Hazards Found -->
                    <div class="flex items-center justify-center w-full pb-2">
                        <div id="fsBottomHazardsCard" class="glass-sheet bg-neutral-950/90 backdrop-blur-xl border border-amber-500/30 p-3.5 sm:p-4 px-6 rounded-2xl shadow-2xl max-w-md w-full space-y-2 pointer-events-auto">
                            <div class="flex items-center justify-between font-mono">
                                <div class="flex items-center gap-2">
                                    <span class="text-base">⚠️</span>
                                    <span class="text-xs font-black text-white uppercase tracking-wider">Live Hazards Found</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm sm:text-base font-black text-amber-400">
                                        <span id="fsBottomHazardCount">{{ $hazardsFoundCount }}</span>
                                        <span class="text-slate-400 text-xs font-semibold">/ 13</span>
                                    </span>
                                    <span id="fsBottomHazardPercent" class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                        {{ $foundPercent }}%
                                    </span>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full h-2 rounded-full bg-white/10 overflow-hidden">
                                <div id="fsHazardProgressBar" class="h-full bg-gradient-to-r from-amber-500 via-amber-400 to-emerald-400 shimmer-progress transition-all duration-500" style="width: {{ $foundPercent }}%;"></div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- Helper caption below monitor -->
            <div id="vrMonitorCaptionBar" class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-1.5 text-[11px] text-slate-400 font-mono px-1 w-full">
                <span class="flex items-center gap-1.5">
                    <span class="text-amber-400">💡</span>
                    <span>To stream: Open MQDH &rarr; Click Cast &rarr; Click "Connect MQDH Cast".</span>
                </span>
                <span id="syncIndicatorStatus" class="font-semibold flex items-center gap-1.5 text-emerald-400">
                    <span class="flex items-end gap-0.5 h-3 px-0.5" title="Signal Active">
                        <span class="w-0.5 bg-emerald-400 rounded-full live-eq-bar"></span>
                        <span class="w-0.5 bg-emerald-400 rounded-full live-eq-bar"></span>
                        <span class="w-0.5 bg-emerald-400 rounded-full live-eq-bar"></span>
                        <span class="w-0.5 bg-emerald-400 rounded-full live-eq-bar"></span>
                    </span>
                    <span>Listening for VR Headset...</span>
                </span>
            </div>
        </div>

        <!-- RIGHT SIDE: Active Trainee Operator & Live Hazards Found (Stacked) -->
        <div class="lg:col-span-5 space-y-4">

            <!-- Card 1: Active Trainee Operator -->
            <div class="glass-sheet p-4 sm:p-5 rounded-2xl border-white/10 space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between pb-2 border-b border-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-sm">👤</span>
                        <h2 class="text-xs font-bold text-white uppercase tracking-wider">Active Trainee Operator</h2>
                    </div>
                    <span id="badgeActiveStatus" class="px-2 py-0.5 rounded-full text-[9.5px] font-mono font-bold {{ $hasActive ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'bg-white/5 text-slate-400 border border-white/10' }}">
                        {{ $hasActive ? 'In VR Headset' : 'No Trainee Active' }}
                    </span>
                </div>

                <!-- 4 Telemetry Columns: Current Trainee, Score, Wrong Clicks, Hazard -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 font-mono text-xs">
                    <!-- Column 1: Current Trainee -->
                    <div class="p-2.5 rounded-xl bg-white/[0.03] border border-white/5 flex flex-col justify-between">
                        <span class="text-[9.5px] text-slate-400 uppercase tracking-wider block">Current Trainee</span>
                        <span id="displayTraineeName" class="font-mono tracking-wide font-black truncate block mt-1.5 {{ $hasActive ? 'text-sm text-white' : 'text-xs text-slate-400 italic' }}" title="{{ $hasActive ? $traineeName : 'No trainee active yet' }}">
                            {{ $hasActive ? $traineeName : 'No trainee active yet' }}
                        </span>
                    </div>

                    <!-- Column 2: Score -->
                    <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/5 flex flex-col justify-between">
                        <span class="text-[9.5px] text-slate-400 uppercase tracking-wider block">Score</span>
                        <span id="displayCardScore" class="text-sm font-black text-amber-400 block mt-1.5">
                            {{ number_format($traineeScore, 1) }}%
                        </span>
                    </div>

                    <!-- Column 3: Wrong Clicks -->
                    <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/5 flex flex-col justify-between">
                        <span class="text-[9.5px] text-slate-400 uppercase tracking-wider block">Wrong Clicks</span>
                        <span id="displayWrongClicks" class="text-sm font-black text-rose-400 block mt-0.5">
                            {{ $hasActive ? (int)($activeTrainee['wrong_clicks'] ?? 0) : 0 }}
                        </span>
                    </div>

                    <!-- Column 4: Hazard -->
                    <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/5 flex flex-col justify-between">
                        <span class="text-[9.5px] text-slate-400 uppercase tracking-wider block">Hazard</span>
                        <span class="text-sm font-black text-emerald-400 block mt-1.5">
                            <span id="displayCardHazardCount">{{ $hazardsFoundCount }}</span>
                            <span class="text-slate-500 text-xs font-semibold">/ 13</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Live Hazards Found (At bottom of Active Trainee Operator) -->
            <div class="glass-sheet p-4 sm:p-5 rounded-2xl border-amber-500/20 space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between pb-2 border-b border-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-sm">⚠️</span>
                        <h2 class="text-xs font-bold text-white uppercase tracking-wider">Live Hazards Found</h2>
                    </div>
                    <span id="badgeTraineeScore" class="px-2 py-0.5 rounded-full text-[9.5px] font-mono font-bold {{ $hasActive ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30' : 'bg-white/5 text-slate-400 border border-white/10' }}">
                        {{ number_format($traineeScore, 1) }}% Score
                    </span>
                </div>

                <!-- Live Hazard Found Tally & Progress Bar -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-baseline font-mono text-xs">
                        <span class="text-[11px] text-slate-300">Hazards Spotted:</span>
                        <span class="text-base font-black text-amber-400">
                            <span id="displayHazardCount">{{ $hazardsFoundCount }}</span>
                            <span class="text-slate-500 text-xs font-semibold">/ 13</span>
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full h-2 rounded-full bg-white/10 overflow-hidden">
                        <div id="hazardProgressBar" class="h-full bg-gradient-to-r from-amber-500 to-emerald-400 shimmer-progress transition-all duration-500" style="width: {{ $foundPercent }}%;"></div>
                    </div>
                </div>

                <!-- Expand / Collapse Toggle for 13 Facility Hazards Checklist -->
                <div class="pt-0.5">
                    <button type="button" onclick="toggleHazardsChecklist()" id="btnToggleChecklist" 
                        class="w-full flex items-center justify-between py-1.5 px-2 rounded-lg hover:bg-white/[0.04] transition text-[10.5px] font-mono text-slate-400 cursor-pointer group">
                        <span id="hazardsFoundSummaryLabel" class="text-slate-300 font-semibold">{{ $hazardsFoundCount }} of 13 Found</span>
                        <span class="flex items-center gap-1.5 text-amber-400 group-hover:text-amber-300 font-bold transition">
                            <span id="checklistToggleText">Expand 13 Hazards</span>
                            <span id="checklistToggleIcon" class="text-[9px] transition-transform duration-200 inline-block">▼</span>
                        </span>
                    </button>
                </div>

                <!-- Collapsible 13 Facility Hazards Checklist (Hidden by default) -->
                <div id="hazardsChecklistCollapsible" class="hidden space-y-1.5 pt-1 border-t border-white/5 transition-all">
                    <div id="hazardsLiveChecklist" class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 max-h-[220px] overflow-y-auto pr-1">
                        @foreach($allHazards as $hz)
                            @php
                                $isFound = false;
                                if ($hasActive) {
                                    if (!empty($foundList)) {
                                        foreach ($foundList as $item) {
                                            $itemLower = strtolower($item);
                                            foreach ($hz['aliases'] as $alias) {
                                                if (str_contains($itemLower, strtolower($alias))) {
                                                    $isFound = true;
                                                    break 2;
                                                }
                                            }
                                        }
                                    } else {
                                        $isFound = ($hz['id'] <= $hazardsFoundCount);
                                    }
                                }
                            @endphp
                            <div id="hazard-item-{{ $hz['id'] }}" 
                                class="hazard-checklist-item p-1.5 px-2 rounded-xl flex items-center justify-between text-xs transition border {{ $isFound ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300' : 'bg-white/[0.02] border-white/5 text-slate-400' }}">
                                <div class="flex items-center gap-1.5 min-w-0 pr-1">
                                    <span class="hazard-status-icon w-4 h-4 rounded-md flex items-center justify-center text-[9.5px] font-black font-mono shrink-0 {{ $isFound ? 'bg-emerald-500 text-neutral-950' : 'bg-white/10 text-slate-500' }}">
                                        {{ $isFound ? '✓' : $hz['id'] }}
                                    </span>
                                    <div class="truncate">
                                        <p class="font-bold text-white text-[10.5px] leading-tight truncate">{{ $hz['name'] }}</p>
                                        <span class="text-[9px] text-slate-500 font-mono block truncate">{{ $hz['area_icon'] }} {{ $hz['area_name'] }}</span>
                                    </div>
                                </div>

                                <span class="hazard-status-tag px-1.5 py-0.5 rounded text-[8.5px] font-mono font-bold shrink-0 {{ $isFound ? 'bg-emerald-500/20 text-emerald-300' : 'bg-white/5 text-slate-500' }}">
                                    {{ $isFound ? 'SPOTTED' : 'WAITING' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Live Auto-Polling Toggle -->
                <div class="pt-1.5 border-t border-white/5 flex items-center justify-between text-[10px] font-mono text-slate-400">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" id="chkAutoSync" checked class="rounded accent-amber-500 cursor-pointer">
                        <span>Live Sync VR Runs (2s)</span>
                    </label>
                    <button onclick="pollLiveSessionNow()" class="text-amber-400 hover:text-amber-300 underline cursor-pointer">Sync Now</button>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Embedded Live VR Cast & Telemetry JavaScript Logic -->
<script>
    // Global Stream Variables
    let vrCastMediaStream = null;
    let autoSyncInterval = null;

    // Dynamically fit the live monitor container to match the stream aspect ratio
    function fitMonitorToStream() {
        const video = document.getElementById('vrLiveCastVideo');
        const container = document.getElementById('vrMonitorScreenContainer');
        if (!video || !container) return;

        // If in native Fullscreen mode, fill display 100%
        if (document.fullscreenElement) {
            container.style.width = '100vw';
            container.style.maxWidth = '100vw';
            container.style.height = '100vh';
            container.style.aspectRatio = 'auto';
            return;
        }

        // Left column layout: fill column width and match stream's native aspect ratio
        container.style.width = '100%';
        container.style.maxWidth = '100%';
        container.style.height = 'auto';

        if (video.videoWidth && video.videoHeight) {
            container.style.aspectRatio = `${video.videoWidth} / ${video.videoHeight}`;
        } else {
            container.style.aspectRatio = '16 / 9';
        }
    }

    // Reset monitor container to default standby dimensions
    function resetMonitorContainerFit() {
        const container = document.getElementById('vrMonitorScreenContainer');
        if (container) {
            container.style.width = '';
            container.style.maxWidth = '';
            container.style.height = '';
            container.style.aspectRatio = '';
        }
    }

    // Connect to Meta Quest Developer Hub (MQDH) Cast Window
    async function connectMqdhCast(isAuto = false) {
        if (vrCastMediaStream) return;
        try {
            // Prompts browser screen/window picker (automatically bypassed with --auto-select-desktop-capture-source)
            vrCastMediaStream = await navigator.mediaDevices.getDisplayMedia({
                video: {
                    displaySurface: 'window',
                    frameRate: { ideal: 60, max: 90 }
                },
                audio: false
            });

            const video = document.getElementById('vrLiveCastVideo');
            const standby = document.getElementById('vrCastStandbyScreen');
            const disconnectBtn = document.getElementById('btnDisconnectCast');
            const statusBadge = document.getElementById('castStatusBadge');

            if (video) {
                video.disablePictureInPicture = true;
                if (document.pictureInPictureElement) {
                    document.exitPictureInPicture().catch(() => {});
                }
                video.srcObject = vrCastMediaStream;
                video.classList.remove('hidden');
                video.className = 'w-full h-full object-cover bg-black';

                video.onloadedmetadata = () => fitMonitorToStream();
                video.onresize = () => fitMonitorToStream();

                video.play().then(() => fitMonitorToStream()).catch(() => {});
            }
            if (standby) standby.classList.add('hidden');
            if (disconnectBtn) disconnectBtn.classList.remove('hidden');
            if (statusBadge) {
                statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span><span class="text-emerald-300 font-bold">LIVE VR STREAM</span>';
                statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 flex items-center gap-1.5 shadow-sm shadow-emerald-500/20';
            }

            // Detect when user stops sharing through native browser banner
            vrCastMediaStream.getVideoTracks()[0].onended = () => {
                disconnectMqdhCast();
            };

            fitMonitorToStream();
            setTimeout(fitMonitorToStream, 300);
            setTimeout(fitMonitorToStream, 1000);

            if (typeof showToast === 'function') {
                showToast(isAuto ? 'VR Stream Auto-Connected' : 'MQDH Cast Connected', 'Meta Quest 3 stream live in monitor viewport.', '🥽');
            }
        } catch (err) {
            if (!isAuto) {
                console.error('MQDH Cast connection canceled or failed:', err);
                if (typeof showToast === 'function') {
                    showToast('Connection Canceled', 'No cast window was selected.', '⚠️');
                }
            } else {
                console.log('[AutoCast] Silent auto-connect standby (click Connect or launch with auto-select flag):', err.message);
            }
        }
    }

    // Disconnect Cast Stream
    function disconnectMqdhCast() {
        if (document.pictureInPictureElement) {
            document.exitPictureInPicture().catch(() => {});
        }

        if (vrCastMediaStream) {
            vrCastMediaStream.getTracks().forEach(t => t.stop());
            vrCastMediaStream = null;
        }

        const video = document.getElementById('vrLiveCastVideo');
        const standby = document.getElementById('vrCastStandbyScreen');
        const disconnectBtn = document.getElementById('btnDisconnectCast');
        const statusBadge = document.getElementById('castStatusBadge');

        if (video) {
            video.disablePictureInPicture = true;
            video.srcObject = null;
            video.classList.add('hidden');
        }
        if (standby) standby.classList.remove('hidden');
        if (disconnectBtn) disconnectBtn.classList.add('hidden');
        if (statusBadge) {
            statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span><span>AWAITING MQDH CAST</span>';
            statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-mono font-semibold bg-white/[0.04] border border-white/10 text-slate-400 flex items-center gap-1.5';
        }

        resetMonitorContainerFit();
        if (typeof showToast === 'function') {
            showToast('Cast Disconnected', 'VR Monitor returned to standby screen.', 'ℹ️');
        }
    }

    // Fullscreen Viewport Toggle
    function toggleMonitorFullscreen() {
        const container = document.getElementById('vrMonitorScreenContainer');
        const hud = document.getElementById('vrFullscreenHudOverlay');
        if (!container) return;
        const isFs = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);
        if (!isFs) {
            if (hud) {
                hud.style.setProperty('display', 'flex', 'important');
                hud.classList.remove('hidden');
            }
            if (container.requestFullscreen) {
                container.requestFullscreen().catch(() => {});
            } else if (container.webkitRequestFullscreen) {
                container.webkitRequestFullscreen();
            } else if (container.mozRequestFullScreen) {
                container.mozRequestFullScreen();
            } else if (container.msRequestFullscreen) {
                container.msRequestFullscreen();
            }
        } else {
            if (hud) {
                hud.style.setProperty('display', 'none', 'important');
                hud.classList.add('hidden');
            }
            if (document.exitFullscreen) {
                document.exitFullscreen().catch(() => {});
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    }

    // Toggle Expand / Collapse for 13 Facility Hazards Checklist
    function toggleHazardsChecklist() {
        const collapsible = document.getElementById('hazardsChecklistCollapsible');
        const toggleText = document.getElementById('checklistToggleText');
        const toggleIcon = document.getElementById('checklistToggleIcon');
        if (!collapsible) return;

        const isHidden = collapsible.classList.contains('hidden');
        if (isHidden) {
            collapsible.classList.remove('hidden');
            if (toggleText) toggleText.innerText = 'Hide 13 Hazards';
            if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
        } else {
            collapsible.classList.add('hidden');
            if (toggleText) toggleText.innerText = 'Expand 13 Hazards';
            if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
        }
    }

    // Clear Active Trainee Session (Reset to "No trainee active yet")
    function clearActiveTraineeSession() {
        fetch(`{{ url('/api/active-trainee/clear') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(() => {
            applyTraineeTelemetry(null);
            if (typeof showToast === 'function') {
                showToast('Session Cleared', 'No trainee active yet.', 'ℹ️');
            }
        })
        .catch(err => console.error('Error clearing session:', err));
    }

    // Apply Telemetry to UI Card Metrics and Checklist
    function applyTraineeTelemetry(record) {
        const displayName = document.getElementById('displayTraineeName');
        const badgeActive = document.getElementById('badgeActiveStatus');
        const avatar = document.getElementById('displayTraineeAvatar');

        const displayCardScore = document.getElementById('displayCardScore');
        const displayWrongClicks = document.getElementById('displayWrongClicks');
        const displayCardHazardCount = document.getElementById('displayCardHazardCount');
        const displaySessionTime = document.getElementById('displaySessionTime');

        const displayCount = document.getElementById('displayHazardCount');
        const progressBar = document.getElementById('hazardProgressBar');
        const badgeScore = document.getElementById('badgeTraineeScore');
        const summaryLabel = document.getElementById('hazardsFoundSummaryLabel');

        // Fullscreen HUD Elements
        const fsTraineeName = document.getElementById('fsTraineeName');
        const fsTraineeStatus = document.getElementById('fsTraineeStatus');
        const fsTraineeAvatar = document.getElementById('fsTraineeAvatar');
        const fsTraineeScore = document.getElementById('fsTraineeScore');
        const fsWrongClicks = document.getElementById('fsWrongClicks');
        const fsHazardCount = document.getElementById('fsHazardCount');
        const fsBottomHazardCount = document.getElementById('fsBottomHazardCount');
        const fsBottomHazardPercent = document.getElementById('fsBottomHazardPercent');
        const fsHazardProgressBar = document.getElementById('fsHazardProgressBar');

        if (!record || !record.username) {
            // State: NO TRAINEE ACTIVE YET
            if (displayName) {
                displayName.innerText = 'No trainee active yet';
                displayName.className = 'font-mono tracking-wide block mt-0.5 text-xs font-semibold text-slate-400 italic';
            }
            if (badgeActive) {
                badgeActive.innerText = 'No Trainee Active';
                badgeActive.className = 'px-2 py-0.5 rounded-full text-[9.5px] font-mono font-bold bg-white/5 text-slate-400 border border-white/10';
            }
            if (avatar) {
                avatar.className = 'w-9 h-9 rounded-xl border flex items-center justify-center font-black text-xs bg-white/5 border-white/10 text-slate-500';
            }

            // Zero out metrics
            if (displayCardScore) displayCardScore.innerText = '0.0%';
            if (displayWrongClicks) displayWrongClicks.innerText = '0';
            if (displayCardHazardCount) displayCardHazardCount.innerText = '0';
            if (displaySessionTime) displaySessionTime.innerText = '00:00';

            if (displayCount) displayCount.innerText = '0';
            if (progressBar) progressBar.style.width = '0%';
            if (badgeScore) {
                badgeScore.innerText = '0.0% Score';
                badgeScore.className = 'px-2 py-0.5 rounded-full text-[9.5px] font-mono font-bold bg-white/5 text-slate-400 border border-white/10';
            }
            if (summaryLabel) summaryLabel.innerText = '0 of 13 Found';

            // Reset Fullscreen HUD Elements
            if (fsTraineeName) {
                fsTraineeName.innerText = 'No trainee active yet';
                fsTraineeName.className = 'font-mono tracking-wide font-black text-base text-slate-400 italic truncate mt-0.5';
            }
            if (fsTraineeStatus) {
                fsTraineeStatus.innerText = 'Standby';
                fsTraineeStatus.className = 'px-2 py-0.5 rounded-full text-[9px] font-mono font-bold bg-white/5 text-slate-400 border border-white/10';
            }
            if (fsTraineeAvatar) {
                fsTraineeAvatar.innerHTML = '<span>VR</span>';
                fsTraineeAvatar.className = 'w-11 h-11 rounded-xl border flex items-center justify-center font-black text-sm shadow-inner shrink-0 bg-white/5 border-white/10 text-slate-500';
            }
            if (fsTraineeScore) fsTraineeScore.innerText = '0.0%';
            if (fsWrongClicks) fsWrongClicks.innerText = '0';
            if (fsHazardCount) fsHazardCount.innerText = '0';
            if (fsBottomHazardCount) fsBottomHazardCount.innerText = '0';
            if (fsBottomHazardPercent) fsBottomHazardPercent.innerText = '0%';
            if (fsHazardProgressBar) fsHazardProgressBar.style.width = '0%';

            // Mark all 13 hazards as WAITING
            for (let i = 1; i <= 13; i++) {
                const item = document.getElementById(`hazard-item-${i}`);
                if (!item) continue;
                const icon = item.querySelector('.hazard-status-icon');
                const tag = item.querySelector('.hazard-status-tag');
                item.className = 'hazard-checklist-item p-1.5 px-2 rounded-xl flex items-center justify-between text-xs transition border bg-white/[0.02] border-white/5 text-slate-400';
                if (icon) {
                    icon.className = 'hazard-status-icon w-4 h-4 rounded-md flex items-center justify-center text-[9.5px] font-black font-mono shrink-0 bg-white/10 text-slate-500';
                    icon.innerText = i;
                }
                if (tag) {
                    tag.className = 'hazard-status-tag px-1.5 py-0.5 rounded text-[8.5px] font-mono font-bold shrink-0 bg-white/5 text-slate-500';
                    tag.innerText = 'WAITING';
                }
            }
            return;
        }

        // State: ACTIVE TRAINEE PRESENT
        if (displayName) {
            displayName.innerText = record.username;
            displayName.className = 'font-mono tracking-wide block mt-0.5 text-base font-black text-white';
        }
        if (badgeActive) {
            badgeActive.innerText = 'In VR Headset';
            badgeActive.className = 'px-2 py-0.5 rounded-full text-[9.5px] font-mono font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30';
        }
        if (avatar) {
            avatar.className = 'w-9 h-9 rounded-xl border flex items-center justify-center font-black text-xs bg-amber-400/20 border-amber-400/30 text-amber-300';
        }

        const found = parseInt(record.hazards_found) || 0;
        const pct = Math.min(100, Math.max(0, ((found / 13) * 100).toFixed(1)));
        const scoreVal = Number(record.score || 0).toFixed(1);

        if (displayCardScore) displayCardScore.innerText = `${scoreVal}%`;
        if (displayWrongClicks) displayWrongClicks.innerText = record.wrong_clicks || 0;
        if (displayCardHazardCount) displayCardHazardCount.innerText = found;

        if (displaySessionTime) {
            const timeSec = Math.floor(record.completion_time || 0);
            const m = Math.floor(timeSec / 60).toString().padStart(2, '0');
            const s = (timeSec % 60).toString().padStart(2, '0');
            displaySessionTime.innerText = `${m}:${s}`;
        }

        if (displayCount) displayCount.innerText = found;
        if (progressBar) progressBar.style.width = `${pct}%`;
        if (badgeScore) {
            badgeScore.innerText = `${scoreVal}% Score`;
            badgeScore.className = 'px-2 py-0.5 rounded-full text-[9.5px] font-mono font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30';
        }
        if (summaryLabel) summaryLabel.innerText = `${found} of 13 Found`;

        // Update Fullscreen HUD Elements with active telemetry
        const initials = (record.username || 'VR').substring(0, 2).toUpperCase();
        if (fsTraineeName) {
            fsTraineeName.innerText = record.username;
            fsTraineeName.className = 'font-mono tracking-wide font-black text-base sm:text-lg text-white truncate mt-0.5';
        }
        if (fsTraineeStatus) {
            fsTraineeStatus.innerText = '● In VR Headset';
            fsTraineeStatus.className = 'px-2 py-0.5 rounded-full text-[9px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40';
        }
        if (fsTraineeAvatar) {
            fsTraineeAvatar.innerHTML = `<span>${initials}</span>`;
            fsTraineeAvatar.className = 'w-11 h-11 rounded-xl border flex items-center justify-center font-black text-sm shadow-inner shrink-0 bg-amber-400/20 border-amber-400/30 text-amber-300';
        }
        if (fsTraineeScore) fsTraineeScore.innerText = `${scoreVal}%`;
        if (fsWrongClicks) fsWrongClicks.innerText = record.wrong_clicks || 0;
        if (fsHazardCount) fsHazardCount.innerText = found;
        if (fsBottomHazardCount) fsBottomHazardCount.innerText = found;
        if (fsBottomHazardPercent) fsBottomHazardPercent.innerText = `${pct}%`;
        if (fsHazardProgressBar) fsHazardProgressBar.style.width = `${pct}%`;

        let foundList = record.found_hazards || [];
        if (typeof foundList === 'string') {
            try { foundList = JSON.parse(foundList); } catch (e) { foundList = foundList.split(','); }
        }

        for (let i = 1; i <= 13; i++) {
            const item = document.getElementById(`hazard-item-${i}`);
            if (!item) continue;
            const icon = item.querySelector('.hazard-status-icon');
            const tag = item.querySelector('.hazard-status-tag');
            const isFound = (i <= found);

            if (isFound) {
                item.className = 'hazard-checklist-item p-1.5 px-2 rounded-xl flex items-center justify-between text-xs transition border bg-emerald-500/10 border-emerald-500/30 text-emerald-300';
                if (icon) {
                    icon.className = 'hazard-status-icon w-4 h-4 rounded-md flex items-center justify-center text-[9.5px] font-black font-mono shrink-0 bg-emerald-500 text-neutral-950';
                    icon.innerText = '✓';
                }
                if (tag) {
                    tag.className = 'hazard-status-tag px-1.5 py-0.5 rounded text-[8.5px] font-mono font-bold shrink-0 bg-emerald-500/20 text-emerald-300';
                    tag.innerText = 'SPOTTED';
                }
            } else {
                item.className = 'hazard-checklist-item p-1.5 px-2 rounded-xl flex items-center justify-between text-xs transition border bg-white/[0.02] border-white/5 text-slate-400';
                if (icon) {
                    icon.className = 'hazard-status-icon w-4 h-4 rounded-md flex items-center justify-center text-[9.5px] font-black font-mono shrink-0 bg-white/10 text-slate-500';
                    icon.innerText = i;
                }
                if (tag) {
                    tag.className = 'hazard-status-tag px-1.5 py-0.5 rounded text-[8.5px] font-mono font-bold shrink-0 bg-white/5 text-slate-500';
                    tag.innerText = 'WAITING';
                }
            }
        }
    }

    // Poll live session updates from API every 2 seconds
    function pollLiveSessionNow() {
        fetch(`{{ url('/api/live-monitor/status') }}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.has_active && data.record) {
                        applyTraineeTelemetry(data.record);
                    } else {
                        applyTraineeTelemetry(null);
                    }
                }
            })
            .catch(() => {});
    }

    // Set up auto-polling interval & disable picture-in-picture
    document.addEventListener('DOMContentLoaded', () => {
        const video = document.getElementById('vrLiveCastVideo');
        if (video) {
            video.disablePictureInPicture = true;
            video.addEventListener('enterpictureinpicture', () => {
                if (document.exitPictureInPicture) {
                    document.exitPictureInPicture().catch(() => {});
                }
            });
        }

        if (autoSyncInterval) clearInterval(autoSyncInterval);
        autoSyncInterval = setInterval(() => {
            const chk = document.getElementById('chkAutoSync');
            if (chk && chk.checked) {
                pollLiveSessionNow();
            }
        }, 2000);

        // Attempt automatic VR video stream connection only when launched via auto-launcher
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('autoconnect') === '1') {
            setTimeout(() => {
                if (!vrCastMediaStream) {
                    connectMqdhCast(true);
                }
            }, 800);
        }
    });

    // Window resize handler: keeps monitor snug if browser window dimensions change
    window.addEventListener('resize', () => {
        if (vrCastMediaStream) {
            fitMonitorToStream();
        }
    });

    // Native Fullscreen handler: adapts smoothly when entering and exiting fullscreen
    function handleFullscreenChange() {
        const container = document.getElementById('vrMonitorScreenContainer');
        const video = document.getElementById('vrLiveCastVideo');
        const hudOverlay = document.getElementById('vrFullscreenHudOverlay');
        const isFs = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement);

        if (isFs) {
            if (hudOverlay) {
                hudOverlay.style.setProperty('display', 'flex', 'important');
                hudOverlay.classList.remove('hidden');
            }
            if (container) {
                container.style.maxWidth = '100vw';
                container.style.width = '100vw';
                container.style.height = '100vh';
                container.style.aspectRatio = 'auto';
            }
            if (video) {
                video.classList.remove('object-cover');
                video.classList.add('object-contain');
            }
        } else {
            if (hudOverlay) {
                hudOverlay.style.setProperty('display', 'none', 'important');
                hudOverlay.classList.add('hidden');
            }
            if (video) {
                video.classList.remove('object-contain');
                video.classList.add('object-cover');
            }
            if (vrCastMediaStream) {
                fitMonitorToStream();
            } else {
                resetMonitorContainerFit();
            }
        }
    }

    ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach(evt => {
        document.addEventListener(evt, handleFullscreenChange);
    });
</script>

<style>
    /* Fullscreen HUD Overlay Behavior: Separate rules prevent parser dropping */
    #vrMonitorScreenContainer:fullscreen #vrFullscreenHudOverlay {
        display: flex !important;
    }
    #vrMonitorScreenContainer:-webkit-full-screen #vrFullscreenHudOverlay {
        display: flex !important;
    }
</style>
