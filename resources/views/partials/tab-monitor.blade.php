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
