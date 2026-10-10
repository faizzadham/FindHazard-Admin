<!-- ======================================================== -->
<!-- LIVE VR SESSION MONITOR — META QUEST DEVELOPER HUB CAST  -->
<!-- ======================================================== -->
<div id="view-monitor" class="space-y-6">

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
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-3 border-b border-white/5">
        <div>
            <div class="flex items-center gap-2.5">
                <span class="text-xl">🥽</span>
                <h1 class="text-lg font-black text-white tracking-wide uppercase">
                    Live VR Session Monitor
                </h1>
                <span id="castStatusBadge" class="px-3 py-1 rounded-full text-[11px] font-mono font-semibold bg-white/[0.04] border border-white/10 text-slate-400 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                    <span>AWAITING MQDH CAST</span>
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1 font-mono">
                Connect live Meta Quest Developer Hub (MQDH) Cast stream &bull; Real-time trainee detection telemetry
            </p>
        </div>

        <!-- Cast Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5 text-xs font-mono">
            <button id="btnConnectMqdhCast" onclick="connectMqdhCast()" 
                class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-neutral-950 font-black text-xs transition shadow-lg shadow-amber-500/25 flex items-center gap-2 cursor-pointer">
                <span>🥽</span><span>Connect MQDH Cast</span>
            </button>

            <button id="btnDisconnectCast" onclick="disconnectMqdhCast()" 
                class="hidden px-3.5 py-2 rounded-xl bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white border border-rose-500/40 font-bold transition flex items-center gap-1.5 cursor-pointer">
                <span>✕</span><span>Disconnect</span>
            </button>

            <button onclick="toggleMonitorFullscreen()" 
                class="glass-card px-3.5 py-2 rounded-xl text-slate-300 hover:text-white transition font-mono flex items-center gap-1.5 cursor-pointer" title="Toggle Fullscreen">
                <span>⛶</span><span>Fullscreen</span>
            </button>
        </div>
    </div>

    <!-- 2. Main Grid: Live Cast Viewport (Left) + Live Trainee & Hazard Panel (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left: Live VR Monitor Viewport (Span 8) -->
        <div class="lg:col-span-8 space-y-3">
            <div id="vrMonitorScreenContainer" class="relative bg-neutral-950 rounded-3xl overflow-hidden border border-white/10 shadow-2xl aspect-[16/9] flex items-center justify-center group">

                <!-- Live WebRTC / DisplayMedia Video Player -->
                <video id="vrLiveCastVideo" autoplay playsinline muted disablepictureinpicture controlslist="nodownload nopictureinpicture" class="w-full h-full object-contain bg-black hidden"></video>

                <!-- Standby / Connect Screen (Visible when not streaming) -->
                <div id="vrCastStandbyScreen" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center space-y-5 bg-gradient-to-b from-neutral-900/90 via-neutral-950 to-neutral-950">
                    <div class="relative">
                        <div class="w-20 h-20 rounded-3xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-4xl shadow-2xl shadow-amber-500/10">
                            🥽
                        </div>
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-amber-400 animate-ping opacity-75"></span>
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-amber-400"></span>
                    </div>

                    <div class="max-w-md space-y-2">
                        <h2 class="text-lg font-black text-white tracking-wide uppercase">
                            Meta Quest Developer Hub Live Cast
                        </h2>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Stream the trainee's Quest 3 headset POV directly into this dashboard using MQDH Device Cast over USB or Wi-Fi.
                        </p>
                    </div>

                    <!-- Step-by-step Quick Guide -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-left max-w-lg w-full text-[11px] font-mono">
                        <div class="p-3 rounded-2xl glass-card border-white/5 space-y-1">
                            <span class="text-amber-400 font-bold block">1. Launch MQDH</span>
                            <span class="text-slate-400 text-[10px]">Open Meta Quest Developer Hub on your PC.</span>
                        </div>
                        <div class="p-3 rounded-2xl glass-card border-white/5 space-y-1">
                            <span class="text-amber-400 font-bold block">2. Device Cast</span>
                            <span class="text-slate-400 text-[10px]">Click Device Cast to open the Quest stream window.</span>
                        </div>
                        <div class="p-3 rounded-2xl glass-card border-white/5 space-y-1">
                            <span class="text-amber-400 font-bold block">3. Connect</span>
                            <span class="text-slate-400 text-[10px]">Click below and select the MQDH Cast window.</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-center pt-2">
                        <button onclick="connectMqdhCast()" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-400 hover:from-amber-400 text-neutral-950 font-black text-xs transition shadow-xl shadow-amber-500/25 flex items-center gap-2 cursor-pointer">
                            <span>🥽</span><span>Select MQDH Cast Window →</span>
                        </button>
                    </div>
                </div>

                <!-- HUD Overlay 1: Live Status & Timer (Top-Left) -->
                <div class="absolute top-4 left-4 pointer-events-none flex items-center gap-2">
                    <div class="glass-sheet px-3 py-1.5 rounded-xl border-amber-500/30 flex items-center gap-2 text-xs font-mono shadow-xl backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="text-white font-bold">MQDH CAST</span>
                        <span class="text-slate-500">&bull;</span>
                        <span id="liveMonitorTimer" class="text-amber-400 font-bold">00:00.0</span>
                    </div>
                </div>

                <!-- HUD Overlay 2: Live Trainee Name (Top-Right) -->
                <div class="absolute top-4 right-4 pointer-events-none">
                    <div class="glass-sheet px-3.5 py-1.5 rounded-xl border-white/10 flex items-center gap-2 text-xs font-mono shadow-xl backdrop-blur-md">
                        <span class="text-slate-400 text-[11px]">Trainee:</span>
                        <strong id="hudTraineeName" class="font-black text-sm tracking-wide {{ $hasActive ? 'text-white' : 'text-slate-400 italic' }}">
                            {{ $hasActive ? $traineeName : 'No trainee active yet' }}
                        </strong>
                        <span id="hudTraineeDot" class="w-1.5 h-1.5 rounded-full {{ $hasActive ? 'bg-emerald-400 animate-pulse' : 'bg-slate-600' }}"></span>
                    </div>
                </div>

                <!-- HUD Overlay 3: Live Hazard Found Counter (Bottom-Left) -->
                <div class="absolute bottom-4 left-4 pointer-events-none">
                    <div class="glass-sheet px-4 py-2 rounded-2xl border-amber-500/30 flex items-center gap-3 text-xs font-mono shadow-xl backdrop-blur-md">
                        <div class="flex items-center gap-2">
                            <span class="text-base">⚠️</span>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Hazards Found</span>
                                <div class="flex items-baseline gap-1.5">
                                    <span id="hudHazardCount" class="text-base font-black font-mono {{ $hasActive ? 'text-amber-400' : 'text-slate-400' }}">
                                        {{ $hazardsFoundCount }} / 13
                                    </span>
                                    <span id="hudHazardPercent" class="text-xs font-bold font-mono {{ $hasActive ? 'text-emerald-400' : 'text-slate-500' }}">
                                        ({{ $foundPercent }}%)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HUD Overlay 4: Headset Stream Quality (Bottom-Right) -->
                <div class="absolute bottom-4 right-4 pointer-events-none hidden sm:block">
                    <div class="glass-card px-3 py-1 rounded-xl text-[10px] font-mono text-slate-400 backdrop-blur-md">
                        Quest 3 &bull; 60 FPS &bull; Low Latency
                    </div>
                </div>

            </div>

            <!-- Bottom helper caption -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 text-xs text-slate-400 font-mono px-1">
                <span class="flex items-center gap-1.5">
                    <span class="text-amber-400">💡</span>
                    <span>To stream: Open MQDH &rarr; Click Cast &rarr; Click "Connect MQDH Cast" in this dashboard.</span>
                </span>
                <span id="syncIndicatorStatus" class="font-semibold flex items-center gap-1 text-emerald-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Listening for VR Headset...</span>
                </span>
            </div>
        </div>

        <!-- Right: Live Trainee Name & Hazard Found Tracking Panel (Span 4) -->
        <div class="lg:col-span-4 space-y-4">

            <!-- Card 1: Live Active Trainee Appearance -->
            <div class="glass-sheet p-5 rounded-3xl border-white/10 space-y-3.5 shadow-xl">
                <div class="flex items-center justify-between pb-2 border-b border-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-base">👤</span>
                        <h2 class="text-xs font-bold text-white uppercase tracking-wider">Active Trainee Operator</h2>
                    </div>
                    <span id="badgeActiveStatus" class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $hasActive ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'bg-white/5 text-slate-400 border border-white/10' }}">
                        {{ $hasActive ? 'In VR Headset' : 'No Trainee Active' }}
                    </span>
                </div>

                <!-- Live Trainee Name Appearance -->
                <div class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/5 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-mono text-slate-400 uppercase tracking-wider block">Current Candidate:</span>
                        <span id="displayTraineeName" class="font-mono tracking-wide block mt-0.5 {{ $hasActive ? 'text-lg font-black text-white' : 'text-sm font-semibold text-slate-400 italic' }}">
                            {{ $hasActive ? $traineeName : 'No trainee active yet' }}
                        </span>
                    </div>
                    <div id="displayTraineeAvatar" class="w-10 h-10 rounded-2xl border flex items-center justify-center font-black text-sm {{ $hasActive ? 'bg-amber-400/20 border-amber-400/30 text-amber-300' : 'bg-white/5 border-white/10 text-slate-500' }}">
                        VR
                    </div>
                </div>

                <!-- Live VR Sync Notice -->
                <div class="p-3.5 rounded-2xl bg-white/[0.02] border border-white/5 space-y-1.5 text-xs font-mono">
                    <div class="flex items-center gap-2 text-amber-400 font-semibold text-[11px]">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        <span>VR Headset Automatic Sync</span>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Trainees enter their username directly inside the FindHazard VR headset. Their name and hazard spotting telemetry will automatically appear here in real time.
                    </p>
                </div>
            </div>

            <!-- Card 2: Live Hazards Found Appearance & Checklist -->
            <div class="glass-sheet p-5 rounded-3xl border-amber-500/20 space-y-4 shadow-xl">
                <div class="flex items-center justify-between pb-2 border-b border-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-base">⚠️</span>
                        <h2 class="text-xs font-bold text-white uppercase tracking-wider">Live Hazards Found</h2>
                    </div>
                    <span id="badgeTraineeScore" class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $hasActive ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30' : 'bg-white/5 text-slate-400 border border-white/10' }}">
                        {{ number_format($traineeScore, 1) }}% Score
                    </span>
                </div>

                <!-- Live Hazard Found Tally & Progress Bar -->
                <div class="space-y-2">
                    <div class="flex justify-between items-baseline font-mono">
                        <span class="text-xs text-slate-300">Hazards Spotted:</span>
                        <span class="text-xl font-black text-amber-400">
                            <span id="displayHazardCount">{{ $hazardsFoundCount }}</span>
                            <span class="text-slate-500 text-sm font-semibold">/ 13</span>
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full h-2.5 rounded-full bg-white/10 overflow-hidden">
                        <div id="hazardProgressBar" class="h-full bg-gradient-to-r from-amber-500 to-emerald-400 transition-all duration-500" style="width: {{ $foundPercent }}%;"></div>
                    </div>
                </div>

                <!-- Real-time Hazards Checklist Feed -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center text-[10px] font-mono text-slate-400 uppercase tracking-wider">
                        <span>13 Facility Hazards</span>
                        <span id="hazardsFoundSummaryLabel">{{ $hazardsFoundCount }} of 13 Found</span>
                    </div>

                    <div id="hazardsLiveChecklist" class="space-y-1.5 max-h-[320px] overflow-y-auto pr-1">
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
                                class="hazard-checklist-item p-2.5 rounded-2xl flex items-center justify-between text-xs transition border {{ $isFound ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300' : 'bg-white/[0.02] border-white/5 text-slate-400' }}">
                                <div class="flex items-center gap-2">
                                    <span class="hazard-status-icon w-5 h-5 rounded-lg flex items-center justify-center text-[11px] font-black font-mono shrink-0 {{ $isFound ? 'bg-emerald-500 text-neutral-950' : 'bg-white/10 text-slate-500' }}">
                                        {{ $isFound ? '✓' : $hz['id'] }}
                                    </span>
                                    <div>
                                        <p class="font-bold text-white text-[11px] leading-tight">{{ $hz['name'] }}</p>
                                        <span class="text-[9.5px] text-slate-500 font-mono">{{ $hz['area_icon'] }} {{ $hz['area_name'] }}</span>
                                    </div>
                                </div>

                                <span class="hazard-status-tag px-2 py-0.5 rounded-lg text-[9px] font-mono font-bold shrink-0 {{ $isFound ? 'bg-emerald-500/20 text-emerald-300' : 'bg-white/5 text-slate-500' }}">
                                    {{ $isFound ? 'SPOTTED' : 'WAITING' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Live Auto-Polling Toggle -->
                <div class="pt-2 border-t border-white/5 flex items-center justify-between text-[11px] font-mono text-slate-400">
                    <label class="flex items-center gap-2 cursor-pointer">
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
    let liveTimerInterval = null;
    let liveTimerSeconds = 0;
    let autoSyncInterval = null;

    // Connect to Meta Quest Developer Hub (MQDH) Cast Window
    async function connectMqdhCast() {
        try {
            // Prompts browser screen/window picker where supervisor selects "Meta Quest Developer Hub" Cast window
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
                video.play().catch(() => {});
            }
            if (standby) standby.classList.add('hidden');
            if (disconnectBtn) disconnectBtn.classList.remove('hidden');
            if (statusBadge) {
                statusBadge.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span><span class="text-emerald-300 font-bold">LIVE MQDH STREAM (ACTIVE)</span>';
                statusBadge.className = 'px-3 py-1 rounded-full text-[11px] font-mono font-bold bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 flex items-center gap-2 shadow-sm shadow-emerald-500/20';
            }

            // Detect when user stops sharing through native browser banner
            vrCastMediaStream.getVideoTracks()[0].onended = () => {
                disconnectMqdhCast();
            };

            startLiveMonitorTimer();
            if (typeof showToast === 'function') {
                showToast('MQDH Cast Connected', 'Meta Quest 3 stream live in monitor viewport.', '🥽');
            }
        } catch (err) {
            console.error('MQDH Cast connection canceled or failed:', err);
            if (typeof showToast === 'function') {
                showToast('Connection Canceled', 'No cast window was selected.', '⚠️');
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
            statusBadge.innerHTML = '<span class="w-2 h-2 rounded-full bg-slate-500"></span><span>AWAITING MQDH CAST</span>';
            statusBadge.className = 'px-3 py-1 rounded-full text-[11px] font-mono font-semibold bg-white/[0.04] border border-white/10 text-slate-400 flex items-center gap-2';
        }

        stopLiveMonitorTimer();
        if (typeof showToast === 'function') {
            showToast('Cast Disconnected', 'VR Monitor returned to standby screen.', 'ℹ️');
        }
    }

    // Fullscreen Viewport Toggle
    function toggleMonitorFullscreen() {
        const container = document.getElementById('vrMonitorScreenContainer');
        if (!container) return;
        if (!document.fullscreenElement) {
            container.requestFullscreen().catch(() => {});
        } else {
            document.exitFullscreen().catch(() => {});
        }
    }

    // Live Session Timer Logic
    function startLiveMonitorTimer() {
        if (liveTimerInterval) clearInterval(liveTimerInterval);
        liveTimerSeconds = 0;
        liveTimerInterval = setInterval(() => {
            liveTimerSeconds += 0.1;
            const mins = Math.floor(liveTimerSeconds / 60).toString().padStart(2, '0');
            const secs = (liveTimerSeconds % 60).toFixed(1).padStart(4, '0');
            const timerEl = document.getElementById('liveMonitorTimer');
            if (timerEl) timerEl.innerText = `${mins}:${secs}`;
        }, 100);
    }

    function stopLiveMonitorTimer() {
        if (liveTimerInterval) {
            clearInterval(liveTimerInterval);
            liveTimerInterval = null;
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

    // Apply Telemetry to HUD and Checklist
    function applyTraineeTelemetry(record) {
        const hudName = document.getElementById('hudTraineeName');
        const hudDot = document.getElementById('hudTraineeDot');
        const displayName = document.getElementById('displayTraineeName');
        const badgeActive = document.getElementById('badgeActiveStatus');
        const avatar = document.getElementById('displayTraineeAvatar');

        const hudCount = document.getElementById('hudHazardCount');
        const hudPercent = document.getElementById('hudHazardPercent');
        const displayCount = document.getElementById('displayHazardCount');
        const progressBar = document.getElementById('hazardProgressBar');
        const badgeScore = document.getElementById('badgeTraineeScore');
        const summaryLabel = document.getElementById('hazardsFoundSummaryLabel');

        if (!record || !record.username) {
            // State: NO TRAINEE ACTIVE YET
            if (hudName) {
                hudName.innerText = 'No trainee active yet';
                hudName.className = 'font-mono text-sm italic text-slate-400';
            }
            if (hudDot) {
                hudDot.className = 'w-1.5 h-1.5 rounded-full bg-slate-600';
            }
            if (displayName) {
                displayName.innerText = 'No trainee active yet';
                displayName.className = 'font-mono tracking-wide block mt-0.5 text-sm font-semibold text-slate-400 italic';
            }
            if (badgeActive) {
                badgeActive.innerText = 'No Trainee Active';
                badgeActive.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/5 text-slate-400 border border-white/10';
            }
            if (avatar) {
                avatar.className = 'w-10 h-10 rounded-2xl border flex items-center justify-center font-black text-sm bg-white/5 border-white/10 text-slate-500';
            }

            // Zero out hazards
            if (hudCount) {
                hudCount.innerText = '0 / 13';
                hudCount.className = 'text-base font-black font-mono text-slate-400';
            }
            if (hudPercent) {
                hudPercent.innerText = '(0.0%)';
                hudPercent.className = 'text-xs font-bold font-mono text-slate-500';
            }
            if (displayCount) displayCount.innerText = '0';
            if (progressBar) progressBar.style.width = '0%';
            if (badgeScore) {
                badgeScore.innerText = '0.0% Score';
                badgeScore.className = 'px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/5 text-slate-400 border border-white/10';
            }
            if (summaryLabel) summaryLabel.innerText = '0 of 13 Found';

            // Mark all 13 hazards as WAITING
            for (let i = 1; i <= 13; i++) {
                const item = document.getElementById(`hazard-item-${i}`);
                if (!item) continue;
                const icon = item.querySelector('.hazard-status-icon');
                const tag = item.querySelector('.hazard-status-tag');
                item.className = 'hazard-checklist-item p-2.5 rounded-2xl flex items-center justify-between text-xs transition border bg-white/[0.02] border-white/5 text-slate-400';
                if (icon) {
                    icon.className = 'hazard-status-icon w-5 h-5 rounded-lg flex items-center justify-center text-[11px] font-black font-mono shrink-0 bg-white/10 text-slate-500';
                    icon.innerText = i;
                }
                if (tag) {
                    tag.className = 'hazard-status-tag px-2 py-0.5 rounded-lg text-[9px] font-mono font-bold shrink-0 bg-white/5 text-slate-500';
                    tag.innerText = 'WAITING';
                }
            }
            return;
        }

        // State: ACTIVE TRAINEE PRESENT
        if (hudName) {
            hudName.innerText = record.username;
            hudName.className = 'font-mono text-sm font-black text-white';
        }
        if (hudDot) {
            hudDot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse';
        }
        if (displayName) {
            displayName.innerText = record.username;
            displayName.className = 'font-mono tracking-wide block mt-0.5 text-lg font-black text-white';
        }
        if (badgeActive) {
            badgeActive.innerText = 'In VR Headset';
            badgeActive.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30';
        }
        if (avatar) {
            avatar.className = 'w-10 h-10 rounded-2xl border flex items-center justify-center font-black text-sm bg-amber-400/20 border-amber-400/30 text-amber-300';
        }

        const found = parseInt(record.hazards_found) || 0;
        const pct = Math.min(100, Math.max(0, ((found / 13) * 100).toFixed(1)));

        if (hudCount) {
            hudCount.innerText = `${found} / 13`;
            hudCount.className = 'text-base font-black font-mono text-amber-400';
        }
        if (hudPercent) {
            hudPercent.innerText = `(${pct}%)`;
            hudPercent.className = 'text-xs font-bold font-mono text-emerald-400';
        }
        if (displayCount) displayCount.innerText = found;
        if (progressBar) progressBar.style.width = `${pct}%`;
        if (badgeScore) {
            badgeScore.innerText = `${Number(record.score || 0).toFixed(1)}% Score`;
            badgeScore.className = 'px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30';
        }
        if (summaryLabel) summaryLabel.innerText = `${found} of 13 Found`;

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
                item.className = 'hazard-checklist-item p-2.5 rounded-2xl flex items-center justify-between text-xs transition border bg-emerald-500/10 border-emerald-500/30 text-emerald-300';
                if (icon) {
                    icon.className = 'hazard-status-icon w-5 h-5 rounded-lg flex items-center justify-center text-[11px] font-black font-mono shrink-0 bg-emerald-500 text-neutral-950';
                    icon.innerText = '✓';
                }
                if (tag) {
                    tag.className = 'hazard-status-tag px-2 py-0.5 rounded-lg text-[9px] font-mono font-bold shrink-0 bg-emerald-500/20 text-emerald-300';
                    tag.innerText = 'SPOTTED';
                }
            } else {
                item.className = 'hazard-checklist-item p-2.5 rounded-2xl flex items-center justify-between text-xs transition border bg-white/[0.02] border-white/5 text-slate-400';
                if (icon) {
                    icon.className = 'hazard-status-icon w-5 h-5 rounded-lg flex items-center justify-center text-[11px] font-black font-mono shrink-0 bg-white/10 text-slate-500';
                    icon.innerText = i;
                }
                if (tag) {
                    tag.className = 'hazard-status-tag px-2 py-0.5 rounded-lg text-[9px] font-mono font-bold shrink-0 bg-white/5 text-slate-500';
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
    });
</script>
