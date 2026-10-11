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
                <span id="liveMonitorTimerBadge" class="hidden px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-500/10 border border-amber-500/30 text-amber-300 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    <span id="liveMonitorTimer">00:00.0</span>
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

    <!-- 2. Compact Live VR Monitor Viewport (Decreased height so cards fit on screen without scrolling) -->
    <div class="space-y-2">
        <div id="vrMonitorScreenContainer" class="relative bg-neutral-950 rounded-2xl overflow-hidden border border-white/10 shadow-xl w-full h-[240px] sm:h-[280px] md:h-[310px] flex items-center justify-center group">

            <!-- Live WebRTC / DisplayMedia Video Player (Clean screen, edge overlays removed) -->
            <video id="vrLiveCastVideo" autoplay playsinline muted disablepictureinpicture controlslist="nodownload nopictureinpicture" class="w-full h-full object-contain bg-black hidden"></video>

            <!-- Standby / Connect Screen (Compact & Sleek) -->
            <div id="vrCastStandbyScreen" class="absolute inset-0 flex flex-col items-center justify-center p-4 text-center space-y-2.5 bg-gradient-to-b from-neutral-900/90 via-neutral-950 to-neutral-950">
                <div class="relative">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-2xl shadow-lg shadow-amber-500/10">
                        🥽
                    </div>
                    <span class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full bg-amber-400 animate-ping opacity-75"></span>
                    <span class="absolute -top-0.5 -right-0.5 w-3 h-3 rounded-full bg-amber-400"></span>
                </div>

                <div class="max-w-md space-y-1">
                    <h2 class="text-sm sm:text-base font-black text-white tracking-wide uppercase">
                        Meta Quest Developer Hub Live Cast
                    </h2>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Stream the trainee's Quest 3 POV directly into this dashboard using MQDH Device Cast over USB.
                    </p>
                </div>

                <!-- Quick Steps -->
                <div class="hidden sm:grid grid-cols-3 gap-2 text-left max-w-md w-full text-[10px] font-mono">
                    <div class="p-2 rounded-xl glass-card border-white/5">
                        <span class="text-amber-400 font-bold block">1. Open MQDH</span>
                        <span class="text-slate-400 text-[9.5px]">Open MQDH on PC.</span>
                    </div>
                    <div class="p-2 rounded-xl glass-card border-white/5">
                        <span class="text-amber-400 font-bold block">2. Device Cast</span>
                        <span class="text-slate-400 text-[9.5px]">Click Device Cast.</span>
                    </div>
                    <div class="p-2 rounded-xl glass-card border-white/5">
                        <span class="text-amber-400 font-bold block">3. Connect</span>
                        <span class="text-slate-400 text-[9.5px]">Select cast window.</span>
                    </div>
                </div>

                <div class="pt-1">
                    <button onclick="connectMqdhCast()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-400 hover:from-amber-400 text-neutral-950 font-black text-xs transition shadow-md shadow-amber-500/25 flex items-center gap-1.5 cursor-pointer">
                        <span>🥽</span><span>Select MQDH Cast Window →</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- Helper caption below monitor -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-1.5 text-[11px] text-slate-400 font-mono px-1">
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

    <!-- 3. Bottom Sections: Active Trainee Operator & Live Hazards Found (Visible without scrolling) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">

        <!-- Card 1: Active Trainee Operator (Span 5) -->
        <div class="lg:col-span-5 space-y-3">
            <div class="glass-sheet p-4 rounded-2xl border-white/10 space-y-3 shadow-xl">
                <div class="flex items-center justify-between pb-1.5 border-b border-white/5">
                    <div class="flex items-center gap-2">
                        <span class="text-sm">👤</span>
                        <h2 class="text-xs font-bold text-white uppercase tracking-wider">Active Trainee Operator</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="badgeActiveStatus" class="px-2 py-0.5 rounded-full text-[9.5px] font-mono font-bold {{ $hasActive ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'bg-white/5 text-slate-400 border border-white/10' }}">
                            {{ $hasActive ? 'In VR Headset' : 'No Trainee Active' }}
                        </span>
                        <button onclick="clearActiveTraineeSession()" class="text-[9.5px] font-mono text-slate-500 hover:text-rose-400 transition cursor-pointer" title="Reset current active trainee">
                            Reset
                        </button>
                    </div>
                </div>

                <!-- 4 Telemetry Rows: Current Trainee, Score, Wrong Clicks, Hazard -->
                <div class="space-y-2 font-mono text-xs">
                    <!-- Row 1: Current Trainee -->
                    <div class="p-2.5 px-3 rounded-xl bg-white/[0.03] border border-white/5 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-slate-400 text-[11px]">
                            <span>👤</span>
                            <span>Current Trainee:</span>
                        </div>
                        <span id="displayTraineeName" class="font-mono tracking-wide font-black {{ $hasActive ? 'text-sm text-white' : 'text-xs text-slate-400 italic' }}">
                            {{ $hasActive ? $traineeName : 'No trainee active yet' }}
                        </span>
                    </div>

                    <!-- Row 2: Score -->
                    <div class="p-2.5 px-3 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-slate-400 text-[11px]">
                            <span>🎯</span>
                            <span>Score:</span>
                        </div>
                        <span id="displayCardScore" class="text-sm font-black text-amber-400">
                            {{ number_format($traineeScore, 1) }}%
                        </span>
                    </div>

                    <!-- Row 3: Wrong Clicks -->
                    <div class="p-2.5 px-3 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-slate-400 text-[11px]">
                            <span>❌</span>
                            <span>Wrong Clicks:</span>
                        </div>
                        <span id="displayWrongClicks" class="text-sm font-black text-rose-400">
                            {{ $hasActive ? (int)($activeTrainee['wrong_clicks'] ?? 0) : 0 }}
                        </span>
                    </div>

                    <!-- Row 4: Hazard -->
                    <div class="p-2.5 px-3 rounded-xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-slate-400 text-[11px]">
                            <span>⚠️</span>
                            <span>Hazard:</span>
                        </div>
                        <span class="text-sm font-black text-emerald-400">
                            <span id="displayCardHazardCount">{{ $hazardsFoundCount }}</span>
                            <span class="text-slate-500 text-xs font-semibold">/ 13</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Live Hazards Found Appearance & Checklist (Span 7) -->
        <div class="lg:col-span-7 space-y-3">
            <div class="glass-sheet p-4 rounded-2xl border-amber-500/20 space-y-3 shadow-xl">
                <div class="flex items-center justify-between pb-1.5 border-b border-white/5">
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
                        <div id="hazardProgressBar" class="h-full bg-gradient-to-r from-amber-500 to-emerald-400 transition-all duration-500" style="width: {{ $foundPercent }}%;"></div>
                    </div>
                </div>

                <!-- Real-time Hazards Checklist Feed (2-Column Grid, Compact Scrollable Height) -->
                <div class="space-y-1">
                    <div class="flex justify-between items-center text-[9.5px] font-mono text-slate-400 uppercase tracking-wider">
                        <span>13 Facility Hazards</span>
                        <span id="hazardsFoundSummaryLabel">{{ $hazardsFoundCount }} of 13 Found</span>
                    </div>

                    <div id="hazardsLiveChecklist" class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 max-h-[190px] overflow-y-auto pr-1">
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
            const timerBadge = document.getElementById('liveMonitorTimerBadge');

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
            if (timerBadge) timerBadge.classList.remove('hidden');
            if (statusBadge) {
                statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span><span class="text-emerald-300 font-bold">LIVE MQDH STREAM</span>';
                statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 flex items-center gap-1.5 shadow-sm shadow-emerald-500/20';
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
        const timerBadge = document.getElementById('liveMonitorTimerBadge');

        if (video) {
            video.disablePictureInPicture = true;
            video.srcObject = null;
            video.classList.add('hidden');
        }
        if (standby) standby.classList.remove('hidden');
        if (disconnectBtn) disconnectBtn.classList.add('hidden');
        if (timerBadge) timerBadge.classList.add('hidden');
        if (statusBadge) {
            statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span><span>AWAITING MQDH CAST</span>';
            statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-mono font-semibold bg-white/[0.04] border border-white/10 text-slate-400 flex items-center gap-1.5';
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
    });
</script>
