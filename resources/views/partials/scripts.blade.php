<script>
    

    // 5-Zone Definition Structure
const warehouseZones = [
    { id: 1, name: 'Zone 1: ULD Storage', hazardCount: 2 },
    { id: 2, name: 'Zone 2: Electrical Vault', hazardCount: 2 },
    { id: 3, name: 'Zone 3: Conveyor Line', hazardCount: 2 },
    { id: 4, name: 'Zone 4: Chemical Egress', hazardCount: 1 },
    { id: 5, name: 'Zone 5: Loading Dock', hazardCount: 1 }
];

// Expanded Trainee Data with 8 Hazards, Distractors, Velocity, and HIRARC
const traineeDossierDetails = {
    'qq': {
        hazardsFound: 8,
        distractors: 0,
        discriminationRate: '100%',
        uninspectedZones: [],
        hirarc: { mechanical: 100, electrical: 100, housekeeping: 100, chemical: 100 },
        velocity: [
            { time: '00:05.1', hazard: '480V Arc Flash Box', zone: 'Zone 2', pause: '0.0s (Immediate Discovery)' },
            { time: '00:12.4', hazard: 'Unguarded Conveyor Belt', zone: 'Zone 3', pause: '+7.3s Inspection Interval' },
            { time: '00:18.9', hazard: 'Unstable ULD Stacking', zone: 'Zone 1', pause: '+6.5s Inspection Interval' },
            { time: '00:26.3', hazard: 'Hydraulic Oil Floor Spill', zone: 'Zone 5', pause: '+7.4s Inspection Interval' },
            { time: '00:32.1', hazard: 'Missing Eye Wash Station', zone: 'Zone 4', pause: '+5.8s Inspection Interval' },
            { time: '00:38.7', hazard: 'Blocked Fire Extinguisher', zone: 'Zone 3', pause: '+6.6s Inspection Interval' },
            { time: '00:43.2', hazard: 'Unanchored Scaffolding', zone: 'Zone 1', pause: '+4.5s Inspection Interval' },
            { time: '00:47.9', hazard: 'Chemical Barrel Without GHS', zone: 'Zone 4', pause: '+4.7s Inspection Interval' }
        ]
    },
    'Ahmad_Rizal': {
        hazardsFound: 7,
        distractors: 1,
        discriminationRate: '87.5%',
        uninspectedZones: [5],
        hirarc: { mechanical: 100, electrical: 100, housekeeping: 50, chemical: 100 },
        velocity: [
            { time: '00:08.2', hazard: '480V Arc Flash Box', zone: 'Zone 2', pause: '0.0s' },
            { time: '00:17.5', hazard: 'Unguarded Conveyor', zone: 'Zone 3', pause: '+9.3s' },
            { time: '00:28.1', hazard: 'Unstable ULD Stacking', zone: 'Zone 1', pause: '+10.6s' },
            { time: '00:39.4', hazard: 'Missing Eye Wash Station', zone: 'Zone 4', pause: '+11.3s' },
            { time: '00:46.0', hazard: 'Blocked Extinguisher', zone: 'Zone 3', pause: '+6.6s' },
            { time: '00:52.3', hazard: 'Chemical Barrel Without GHS', zone: 'Zone 4', pause: '+6.3s' }
        ]
    },
    'Sarah_Tan': {
        hazardsFound: 7,
        distractors: 1,
        discriminationRate: '87.5%',
        uninspectedZones: [4],
        hirarc: { mechanical: 100, electrical: 100, housekeeping: 100, chemical: 50 },
        velocity: [
            { time: '00:06.5', hazard: '480V Arc Flash Box', zone: 'Zone 2', pause: '0.0s' },
            { time: '00:15.2', hazard: 'Unguarded Conveyor', zone: 'Zone 3', pause: '+8.7s' },
            { time: '00:27.4', hazard: 'Unstable ULD Stacking', zone: 'Zone 1', pause: '+12.2s' },
            { time: '00:41.3', hazard: 'Hydraulic Oil Spill', zone: 'Zone 5', pause: '+13.9s (Hesitation Pause)' },
            { time: '00:54.1', hazard: 'Unanchored Scaffolding', zone: 'Zone 1', pause: '+12.8s' }
        ]
    },
    'Danial_Haziq': {
        hazardsFound: 6,
        distractors: 2,
        discriminationRate: '75.0%',
        uninspectedZones: [2, 4],
        hirarc: { mechanical: 75, electrical: 50, housekeeping: 100, chemical: 50 },
        velocity: [
            { time: '00:12.1', hazard: 'Unguarded Conveyor', zone: 'Zone 3', pause: '0.0s' },
            { time: '00:29.4', hazard: 'Unstable ULD Stacking', zone: 'Zone 1', pause: '+17.3s (Long Pause)' },
            { time: '00:44.2', hazard: 'Hydraulic Oil Spill', zone: 'Zone 5', pause: '+14.8s' },
            { time: '00:61.5', hazard: 'Blocked Extinguisher', zone: 'Zone 3', pause: '+17.3s' }
        ]
    },
    'Nurul_Ain': {
        hazardsFound: 5,
        distractors: 3,
        discriminationRate: '62.5%',
        uninspectedZones: [2, 4, 5],
        hirarc: { mechanical: 60, electrical: 40, housekeeping: 60, chemical: 20 },
        velocity: [
            { time: '00:19.4', hazard: 'Unstable ULD Stacking', zone: 'Zone 1', pause: '0.0s' },
            { time: '00:38.2', hazard: 'Unguarded Conveyor', zone: 'Zone 3', pause: '+18.8s (Search Pause)' },
            { time: '00:68.2', hazard: 'Blocked Extinguisher', zone: 'Zone 3', pause: '+30.0s (Search Failure)' }
        ]
    }
};

// Function: Load & Render Section 5 Individual Trainee Dossier
function loadTraineeDossier(traineeId) {
    const trainee = trainees.find(t => t.id === traineeId) || trainees[0];
    selectedTrainee = trainee;
    const details = traineeDossierDetails[trainee.id] || traineeDossierDetails['qq'];

    // Header & Route Info
    document.getElementById('dossierAvatar').innerText = trainee.id.slice(0, 2).toUpperCase();
    document.getElementById('dossierTraineeName').innerText = `Trainee Dossier: ${trainee.id}`;
    document.getElementById('dossierRouteBadge').innerText = `/trainees/${trainee.id}`;
    document.getElementById('dossierAuditSubtitle').innerText = `OSHA Audit Date: ${trainee.date} • Previous Exp: ${trainee.experience}`;
    
    const ratingBadge = document.getElementById('dossierRatingBadge');
    ratingBadge.innerText = trainee.rating;
    ratingBadge.className = `glass-pill px-3 py-1.5 rounded-xl text-xs font-mono font-bold ${
        trainee.rating === 'Locked In' ? 'text-emerald-400' : (trainee.rating === 'Cooked' ? 'text-rose-400' : 'text-amber-400')
    }`;

    // 1. Precision Score
    document.getElementById('dossierHazardsFound').innerText = details.hazardsFound;
    document.getElementById('dossierFalseDistractors').innerText = details.distractors;
    document.getElementById('dossierDiscriminationRate').innerText = details.discriminationRate;

    // 2. 5-Zone Vulnerability Summary
    const clearedZonesCount = 5 - details.uninspectedZones.length;
    document.getElementById('dossierZoneClearedCount').innerText = `${clearedZonesCount}/5 Zones Cleared`;

    const zonesGrid = document.getElementById('dossierZonesGrid');
    zonesGrid.innerHTML = warehouseZones.map(zone => {
        const isUninspected = details.uninspectedZones.includes(zone.id);
        return `
            <div class="p-3 rounded-2xl border ${
                isUninspected 
                    ? 'glass-card border-rose-500/30' 
                    : 'glass-sheet border-emerald-500/30'
            } space-y-1">
                <span class="block text-[10px] font-bold text-slate-400 font-mono">ZONE ${zone.id}</span>
                <p class="text-xs font-bold text-white leading-tight">${zone.name.replace(/^Zone \d+: /, '')}</p>
                <span class="inline-block mt-1 text-[9px] font-mono px-2 py-0.5 rounded-full font-bold ${
                    isUninspected 
                        ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30 animate-pulse' 
                        : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30'
                }">
                    ${isUninspected ? '⚠️ Uninspected' : '✓ Cleared'}
                </span>
            </div>
        `;
    }).join('');

    // 3. Detection Velocity Timeline
    const velocityContainer = document.getElementById('dossierVelocityTimeline');
    velocityContainer.innerHTML = details.velocity.map((step, idx) => `
        <div class="glass-sheet p-2.5 rounded-2xl border border-white/5 flex items-center justify-between text-xs font-mono">
            <div class="flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-lg bg-amber-400/10 text-amber-400 flex items-center justify-center font-bold text-[10px]">
                    #${idx + 1}
                </span>
                <div>
                    <p class="text-slate-200 font-bold text-[11px] leading-tight">${step.hazard}</p>
                    <span class="text-[9px] text-slate-400 font-sans">${step.zone}</span>
                </div>
            </div>
            <div class="text-right">
                <span class="text-amber-400 font-bold text-[11px]">${step.time}</span>
                <span class="block text-[8px] text-slate-500">${step.pause}</span>
            </div>
        </div>
    `).join('');

    // 4. HIRARC Risk Profile
    document.getElementById('hirarcMechanicalVal').innerText = `${details.hirarc.mechanical}% Awareness`;
    document.getElementById('hirarcMechanicalBar').style.width = `${details.hirarc.mechanical}%`;

    document.getElementById('hirarcElectricalVal').innerText = `${details.hirarc.electrical}% Awareness`;
    document.getElementById('hirarcElectricalBar').style.width = `${details.hirarc.electrical}%`;

    document.getElementById('hirarcHousekeepingVal').innerText = `${details.hirarc.housekeeping}% Awareness`;
    document.getElementById('hirarcHousekeepingBar').style.width = `${details.hirarc.housekeeping}%`;

    document.getElementById('hirarcChemicalVal').innerText = `${details.hirarc.chemical}% Awareness`;
    document.getElementById('hirarcChemicalBar').style.width = `${details.hirarc.chemical}%`;

    // Notes
    document.getElementById('supervisorRemarksInput').value = trainee.notes;

    navigateView('dossier');
    showToast('Trainee Dossier Loaded', `Loaded audit details for ${trainee.id}`, '📋');
}

    let sessionSeconds = 47.9;
    let timerInterval = null;
    let hazardHitsCount = 4;
    let misclicksCount = 1;

    // Initial click event feed distinguishing hits from safe distractor misclicks
    const clickEventFeed = [
        { time: '00:05.1', label: '480V Arc Box', type: 'hit', detail: 'Hazard Identified', points: '+10 PTS' },
        { time: '00:07.8', label: 'Hydraulic Spill', type: 'hit', detail: 'Slip Hazard Spotted', points: '+10 PTS' },
        { time: '00:15.3', label: 'Safe Tool Rack', type: 'misclick', detail: 'Distractor Triggered (Safe Asset)', points: '0 PTS' },
        { time: '00:23.5', label: 'Unanchored Scaffold', type: 'hit', detail: 'Fall Hazard Identified', points: '+10 PTS' },
        { time: '00:41.0', label: 'Emergency Exit Door', type: 'hit', detail: 'Egress Path Identified', points: '+10 PTS' }
    ];

    // 1. Live Active Session Timer
    function startActiveSessionTimer() {
        if (timerInterval) clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            sessionSeconds += 0.1;
            const mins = Math.floor(sessionSeconds / 60).toString().padStart(2, '0');
            const secs = (sessionSeconds % 60).toFixed(1).padStart(4, '0');
            const timerEl = document.getElementById('liveSessionTimer');
            if (timerEl) timerEl.innerText = `${mins}:${secs}`;
        }, 100);
    }

    // 2. Warehouse Zone Switcher
    function changeWarehouseZone(newZone) {
        const hudZone = document.getElementById('hudWarehouseZoneLabel');
        if (hudZone) hudZone.innerText = newZone;
        showToast('Zone Shifted', `Meta Quest 3 viewport moved to ${newZone}`, '📍');
    }

    // 3. Render Click Event Feed
    function renderClickEventFeed() {
        const list = document.getElementById('clickEventFeedList');
        if (!list) return;

        list.innerHTML = clickEventFeed.map(item => `
            <div class="p-2 rounded-xl text-xs flex items-center justify-between border ${
                item.type === 'hit' 
                    ? 'glass-sheet border-emerald-500/30' 
                    : 'glass-sheet border-rose-500/30'
            }">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold ${
                        item.type === 'hit' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400'
                    }">
                        ${item.type === 'hit' ? '✓' : '✗'}
                    </span>
                    <div>
                        <p class="font-bold text-white text-[11px] leading-tight">${item.label}</p>
                        <p class="text-[9px] text-slate-400 font-mono">${item.detail}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="font-mono font-bold text-[10px] ${
                        item.type === 'hit' ? 'text-emerald-400' : 'text-rose-400'
                    }">
                        ${item.points}
                    </span>
                    <span class="block text-[8px] text-slate-500 font-mono">${item.time}</span>
                </div>
            </div>
        `).join('');

        const hitsEl = document.getElementById('statHazardHits');
        const misEl = document.getElementById('statMisclicks');
        if (hitsEl) hitsEl.innerText = hazardHitsCount;
        if (misEl) misEl.innerText = misclicksCount;
    }

    function clearEventFeed() {
        clickEventFeed.length = 0;
        hazardHitsCount = 0;
        misclicksCount = 0;
        renderClickEventFeed();
        showToast('Feed Cleared', 'Click event history reset.', '🧹');
    }

    // 4. VR Canvas: Rendering Safe Distractor & Hazard + Click Detection
    const canvas = document.getElementById('vrMonitorCanvas');
    const ctx = canvas ? canvas.getContext('2d') : null;
    let mouseX = null, mouseY = null;

    function resizeCanvas() {
        if (!canvas) return;
        const rect = canvas.getBoundingClientRect();
        if (rect.width > 0) {
            canvas.width = rect.width;
            canvas.height = rect.height;
        }
    }

    if (canvas) {
        canvas.addEventListener('mousemove', (e) => {
            const rect = canvas.getBoundingClientRect();
            mouseX = e.clientX - rect.left;
            mouseY = e.clientY - rect.top;
        });

        // Click Event: Detect Hazard Hit vs Safe Distractor Misclick
        canvas.addEventListener('click', (e) => {
            const rect = canvas.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const clickY = e.clientY - rect.top;
            const w = canvas.width;
            const h = canvas.height;

            // Target 1: 480V Hazard Box
            const boxX = w * 0.65;
            const boxY = h * 0.48;
            const isHazardHit = Math.hypot(clickX - boxX, clickY - boxY) < 55;

            // Target 2: Safe Distractor Tool Rack
            const distractorX = w * 0.28;
            const distractorY = h * 0.52;
            const isDistractorHit = Math.hypot(clickX - distractorX, clickY - distractorY) < 45;

            const mins = Math.floor(sessionSeconds / 60).toString().padStart(2, '0');
            const secs = (sessionSeconds % 60).toFixed(1).padStart(4, '0');
            const currentTimeStr = `${mins}:${secs}`;

            if (isHazardHit) {
                hazardHitsCount++;
                clickEventFeed.unshift({
                    time: currentTimeStr,
                    label: '480V Arc Box',
                    type: 'hit',
                    detail: 'Electrical Hazard Detected',
                    points: '+10 PTS'
                });
                showToast('Hazard Confirmed', 'Trainee tagged 480V junction box (+10 PTS)', '✅');
            } else if (isDistractorHit) {
                misclicksCount++;
                clickEventFeed.unshift({
                    time: currentTimeStr,
                    label: 'Safe Tool Rack',
                    type: 'misclick',
                    detail: 'Distractor Clicked (Compliant Asset)',
                    points: '0 PTS'
                });
                showToast('Safe Distractor Click', 'Safe compliant asset clicked (0 PTS)', '⚠️');
            } else {
                misclicksCount++;
                clickEventFeed.unshift({
                    time: currentTimeStr,
                    label: 'Empty Space',
                    type: 'misclick',
                    detail: 'False Trigger Misclick',
                    points: '0 PTS'
                });
                showToast('Misclick', 'Triggered non-hazard area.', '⚠️');
            }

            renderClickEventFeed();
        });
    }

    function drawMonitor() {
        if (!canvas || !ctx) return;
        const w = canvas.width;
        const h = canvas.height;

        if (w > 0 && h > 0) {
            ctx.fillStyle = '#06070a';
            ctx.fillRect(0, 0, w, h);

            // Floor Perspective Lines
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.05)';
            ctx.lineWidth = 1;
            const vpX = w / 2;
            const vpY = h * 0.42;

            for (let i = 0; i <= w; i += w / 7) {
                ctx.beginPath();
                ctx.moveTo(vpX, vpY);
                ctx.lineTo(i, h);
                ctx.stroke();
            }

            // 1. Hazard Node: 480V Electrical Box
            const boxX = w * 0.65;
            const boxY = h * 0.48;
            ctx.fillStyle = '#1c1f2e';
            ctx.strokeStyle = '#f59e0b';
            ctx.lineWidth = 2;
            ctx.fillRect(boxX - 35, boxY - 40, 70, 80);
            ctx.strokeRect(boxX - 35, boxY - 40, 70, 80);

            ctx.fillStyle = '#f59e0b';
            ctx.font = 'bold 11px JetBrains Mono';
            ctx.fillText('⚡ 480V', boxX - 22, boxY - 15);
            ctx.fillStyle = '#e2e8f0';
            ctx.font = '9px JetBrains Mono';
            ctx.fillText('[HAZARD]', boxX - 22, boxY + 15);

            // 2. Safe Distractor: Compliant Tool Storage Rack
            const distractorX = w * 0.28;
            const distractorY = h * 0.52;
            ctx.fillStyle = '#141824';
            ctx.strokeStyle = 'rgba(255, 255, 255, 0.2)';
            ctx.lineWidth = 1.5;
            ctx.fillRect(distractorX - 32, distractorY - 30, 64, 60);
            ctx.strokeRect(distractorX - 32, distractorY - 30, 64, 60);

            ctx.fillStyle = '#94a3b8';
            ctx.font = '9px JetBrains Mono';
            ctx.fillText('TOOL RACK', distractorX - 26, distractorY - 5);
            ctx.fillStyle = '#10b981';
            ctx.fillText('[SAFE ASSET]', distractorX - 28, distractorY + 12);

            // 3. Controller Raycast Aim
            const targetX = mouseX !== null ? mouseX : boxX;
            const targetY = mouseY !== null ? mouseY : boxY;

            ctx.strokeStyle = '#f59e0b';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(w * 0.85, h);
            ctx.lineTo(targetX, targetY);
            ctx.stroke();

            // Lock-on Reticle
            const isHoverHazard = Math.hypot(targetX - boxX, targetY - boxY) < 55;
            ctx.strokeStyle = isHoverHazard ? '#10b981' : '#f59e0b';
            ctx.lineWidth = 2;
            ctx.strokeRect(targetX - 35, targetY - 25, 70, 50);

            ctx.fillStyle = isHoverHazard ? '#10b981' : '#f59e0b';
            ctx.font = 'bold 9px JetBrains Mono';
            ctx.fillText(isHoverHazard ? '[LOCK: HAZARD]' : '[AIMING]', targetX - 33, targetY - 30);
        }

        requestAnimationFrame(drawMonitor);
    }

    function centerReticle() {
        if (!canvas) return;
        mouseX = canvas.width * 0.65;
        mouseY = canvas.height * 0.48;
        showToast('Reticle Centered', 'Aim laser locked on 480V junction box.', '🎯');
    }

    // Start timer and render feed on page load
    window.addEventListener('DOMContentLoaded', () => {
        startActiveSessionTimer();
        renderClickEventFeed();
        resizeCanvas();
        drawMonitor();
    });

    // 1. Dataset with exact required Industrial Experience and Tiers
const trainees = [
    { 
        id: 'qq', 
        experience: 'Manufacturing', 
        score: 100, 
        found: 10, 
        missed: 0, 
        time: '47.9s', 
        rating: 'Locked In', 
        date: '01 Oct 2026, 06:18', 
        dateShort: '01 Oct 2026', 
        notes: 'Detected 480V arc-flash hazard under 5.1s.', 
        missedIndices: [] 
    },
    { 
        id: 'Ahmad_Rizal', 
        experience: 'Logistics Center', 
        score: 90, 
        found: 9, 
        missed: 1, 
        time: '52.3s', 
        rating: 'Great', 
        date: '01 Oct 2026, 05:42', 
        dateShort: '01 Oct 2026', 
        notes: 'Overlooked overhead crane cable fray.', 
        missedIndices: [7] 
    },
    { 
        id: 'Sarah_Tan', 
        experience: 'Manufacturing', 
        score: 90, 
        found: 9, 
        missed: 1, 
        time: '54.1s', 
        rating: 'Great', 
        date: '01 Oct 2026, 04:15', 
        dateShort: '01 Oct 2026', 
        notes: 'Missed blocked extinguisher in corridor 3.', 
        missedIndices: [4] 
    },
    { 
        id: 'Danial_Haziq', 
        experience: 'Retail', 
        score: 80, 
        found: 8, 
        missed: 2, 
        time: '61.5s', 
        rating: 'Valid Effort', 
        date: '30 Sep 2026, 17:30', 
        dateShort: '30 Sep 2026', 
        notes: 'Missed 480V enclosure latch and eye wash station.', 
        missedIndices: [2, 8] 
    },
    { 
        id: 'Nurul_Ain', 
        experience: 'Logistics Center', 
        score: 70, 
        found: 7, 
        missed: 3, 
        time: '68.2s', 
        rating: 'Cooked', 
        date: '30 Sep 2026, 15:10', 
        dateShort: '30 Sep 2026', 
        notes: 'Failed chemical GHS protocol; mandatory retraining required.', 
        missedIndices: [2, 5, 9] 
    }
];

// Helper: Evaluation Tier styling badge
function getTierBadge(tier) {
    if (tier === 'Locked In') {
        return `<span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Locked In</span>`;
    }
    if (tier === 'Great') {
        return `<span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-lg bg-amber-400/10 text-amber-300 border border-amber-400/30">Great</span>`;
    }
    if (tier === 'Valid Effort') {
        return `<span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-lg bg-yellow-500/10 text-yellow-400 border border-yellow-500/30">Valid Effort</span>`;
    }
    return `<span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-lg bg-rose-500/20 text-rose-400 border border-rose-500/40 animate-pulse">Cooked</span>`;
}

// Helper: Experience styling badge
function getExperienceBadge(exp) {
    if (exp === 'Logistics Center') {
        return `<span class="px-2 py-0.5 text-[10px] rounded-lg glass-card text-cyan-300 font-mono">📦 Logistics</span>`;
    }
    if (exp === 'Manufacturing') {
        return `<span class="px-2 py-0.5 text-[10px] rounded-lg glass-card text-amber-400 font-mono">🏭 Manufacturing</span>`;
    }
    return `<span class="px-2 py-0.5 text-[10px] rounded-lg glass-card text-purple-300 font-mono">🏬 Retail</span>`;
}

// 2. Multi-Filter Engine
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

    const matchEl = document.getElementById('directoryMatchCount');
    if (matchEl) matchEl.innerText = filtered.length;

    const tbody = document.getElementById('traineeTableBody');
    if (!tbody) return;

    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="p-6 text-center text-slate-500 font-mono">
                    No trainees match the chosen filter parameters.
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = filtered.map((t, idx) => `
        <tr class="hover:bg-white/[0.02] transition">
            <td class="p-3.5 font-bold text-white flex items-center gap-2">
                <span class="w-5 h-5 rounded-lg glass-card text-[10px] text-amber-400 font-mono flex items-center justify-center">#${idx + 1}</span>
                <span>${t.id}</span>
            </td>
            <td class="p-3.5">${getExperienceBadge(t.experience)}</td>
            <td class="p-3.5 font-mono ${t.score >= 90 ? 'text-emerald-400' : (t.score >= 75 ? 'text-amber-400' : 'text-rose-400')} font-bold">${t.score} pts</td>
            <td class="p-3.5 font-mono text-slate-200">${t.found} / 10</td>
            <td class="p-3.5 font-mono ${t.missed > 0 ? 'text-rose-400 font-bold' : 'text-slate-500'}">${t.missed}</td>
            <td class="p-3.5 font-mono text-slate-300">${t.time}</td>
            <td class="p-3.5">${getTierBadge(t.rating)}</td>
            <td class="p-3.5 text-slate-400 font-mono">${t.date}</td>
            <td class="p-3.5 text-center">
                <button onclick="loadTraineeDossier('${t.id}')" class="px-3 py-1 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-neutral-950 font-bold text-[11px] transition shadow-sm cursor-pointer active:scale-95">
                    Inspect
                </button>
            </td>
        </tr>
    `).join('');
}

    // Universal Login Submission Handler (Supports both handleLoginSubmit and handleSupervisorLogin)
    function handleLoginSubmit(event) {
        if (event && event.preventDefault) {
            event.preventDefault();
        }

        const usernameInput = document.getElementById('loginUsername');
        const passwordInput = document.getElementById('loginPassword');
        const u = usernameInput ? usernameInput.value.trim() : '';
        const p = passwordInput ? passwordInput.value.trim() : '';
        const errorBox = document.getElementById('loginErrorMessage');

        if ((u === 'supervisor' && p === 'admin123') || (u.length >= 3 && p.length >= 4)) {
            if (errorBox) errorBox.classList.add('hidden');

            // 1. Hide Login View
            const loginView = document.getElementById('view-login');
            if (loginView) {
                loginView.classList.add('hidden');
                loginView.style.setProperty('display', 'none', 'important');
            }

            // 2. Show Master Dashboard View
            const dashView = document.getElementById('view-dashboard');
            if (dashView) {
                dashView.classList.remove('hidden');
                dashView.style.setProperty('display', 'block', 'important');
            }

            // 3. Show Toast Notice
            if (typeof showToast === 'function') {
                showToast('Welcome, En. Faiz', 'Supervisor Operations Center Verified', '✅');
            }

            // 4. Navigate to Overview Tab Safely
            if (typeof navigateView === 'function') {
                try { navigateView('overview'); } catch (err) { console.warn(err); }
            }

            // 5. Populate Tables and Telemetry Safely
            try {
                if (typeof renderDirectoryTable === 'function') renderDirectoryTable();
                if (typeof applyDirectoryFilters === 'function') applyDirectoryFilters();
                if (typeof loadTraineeDossier === 'function') loadTraineeDossier('qq');
                if (typeof renderIncomingRunsLog === 'function') renderIncomingRunsLog();
                if (typeof renderClickEventFeed === 'function') renderClickEventFeed();
                if (typeof startActiveSessionTimer === 'function') startActiveSessionTimer();
            } catch (err) {
                console.warn('Init render error:', err);
            }

            // 6. Resize Monitor Canvas once visible
            setTimeout(() => {
                try {
                    if (typeof resizeCanvas === 'function') resizeCanvas();
                    if (typeof drawMonitor === 'function') drawMonitor();
                } catch (err) {
                    console.warn(err);
                }
            }, 100);

        } else {
            if (errorBox) {
                errorBox.classList.remove('hidden');
            }
        }
    }

    // Alias in case any button still references handleSupervisorLogin
    const handleSupervisorLogin = handleLoginSubmit;
    
    // ========================================================
    // AUTO-REFRESH & INCOMING RUNS ENGINE (DISPLAY 2 OPTIMIZED)
    // ========================================================
    let refreshSecondsRemaining = 10;

    function renderIncomingRunsLog() {
        const container = document.getElementById('incomingRunsContainer');
        if (!container) return;

        // Render top 4 most recent trainee VR runs
        container.innerHTML = trainees.slice(0, 4).map(run => `
            <div onclick="loadTraineeDossier('${run.id}')" class="glass-sheet p-2.5 rounded-2xl flex items-center justify-between cursor-pointer hover:border-amber-400/40 transition">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-amber-400/10 border border-amber-400/20 text-amber-400 font-bold flex items-center justify-center text-xs font-mono">
                        ${run.id.slice(0, 2).toUpperCase()}
                    </span>
                    <div>
                        <p class="text-xs font-bold text-slate-200">${run.id}</p>
                        <p class="text-[9px] text-slate-400 font-mono">${run.found}/10 Spotted &bull; ${run.time}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-mono font-bold ${run.score >= 90 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30'}">
                        ${run.score} pts
                    </span>
                    <span class="block text-[8px] text-slate-500 font-mono mt-0.5">${run.rating}</span>
                </div>
            </div>
        `).join('');
    }

    // Auto-refresh timer loop (every 1 second)
    setInterval(() => {
        refreshSecondsRemaining--;
        const countdownEl = document.getElementById('refreshCountdown');
        if (countdownEl) {
            countdownEl.innerText = `${refreshSecondsRemaining}s`;
        }

        if (refreshSecondsRemaining <= 0) {
            refreshSecondsRemaining = 10;
            // Simulated live telemetry sync
            renderIncomingRunsLog();
        }
    }, 1000);

    // Secondary Monitor (Display 2) Fullscreen Handler
    function toggleDisplay2Fullscreen() {
        const btnText = document.getElementById('display2BtnText');
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().then(() => {
                if (btnText) btnText.innerText = 'Exit Fullscreen (Display 2)';
                showToast('Display 2 Mode', 'Fullscreen enabled. Drag window to secondary monitor.', '🖥️');
            }).catch(() => {
                showToast('Display 2 Notice', 'Press F11 to lock to secondary screen.', '🖥️');
            });
        } else {
            document.exitFullscreen().then(() => {
                if (btnText) btnText.innerText = 'Display 2 Fullscreen';
            });
        }
    }

    // Initialize the incoming trainee log on load
    window.addEventListener('DOMContentLoaded', () => {
        renderIncomingRunsLog();
    });

    const trainees = [
        { id: 'qq', score: 100, found: 10, missed: 0, time: '47.9s', rating: 'Locked In', date: '01 Oct 2026, 06:18', notes: 'Trainee detected 480V arc-flash hazard under 5.1s.', missedIndices: [] },
        { id: 'Ahmad_Rizal', score: 90, found: 9, missed: 1, time: '52.3s', rating: 'Great', date: '01 Oct 2026, 05:42', notes: 'Overlooked overhead crane fray.', missedIndices: [7] },
        { id: 'Sarah_Tan', score: 90, found: 9, missed: 1, time: '54.1s', rating: 'Great', date: '01 Oct 2026, 04:15', notes: 'Missed blocked extinguisher in corridor 3.', missedIndices: [4] },
        { id: 'Danial_Haziq', score: 80, found: 8, missed: 2, time: '61.5s', rating: 'Valid Effort', date: '30 Sep 2026, 17:30', notes: 'Missed 480V enclosure latch and PPE eye station.', missedIndices: [2, 8] },
        { id: 'Nurul_Ain', score: 70, found: 7, missed: 3, time: '68.2s', rating: 'Needs Review', date: '30 Sep 2026, 15:10', notes: 'Retraining recommended for chemical GHS symbols.', missedIndices: [2, 5, 9] }
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

    function showToast(title, body, icon = '⚡') {
        const toast = document.getElementById('toastMessage');
        if (!toast) return;
        document.getElementById('toastTitle').innerText = title;
        document.getElementById('toastBody').innerText = body;
        const iconEl = document.getElementById('toastIcon');
        if (iconEl) iconEl.innerText = icon;
        toast.classList.remove('opacity-0', 'pointer-events-none', '-translate-y-10');
        setTimeout(() => toast.classList.add('opacity-0', 'pointer-events-none', '-translate-y-10'), 2500);
    }

    function handleSupervisorLogin(e) {
        e.preventDefault();
        const u = document.getElementById('loginUsername').value.trim();
        const p = document.getElementById('loginPassword').value.trim();
        if ((u === 'supervisor' && p === 'admin123') || (u.length >= 3 && p.length >= 4)) {
            document.getElementById('view-login').classList.add('hidden');
            document.getElementById('view-dashboard').classList.remove('hidden');
            showToast('Welcome, En. Faiz', 'Supervisor Operations Center Verified', '✅');
            navigateView('overview');
            renderDirectoryTable();
            loadTraineeDossier('qq');
            setTimeout(resizeCanvas, 100);
        } else {
            document.getElementById('loginErrorMessage').classList.remove('hidden');
        }
    }

    function handleLogout() {
        document.getElementById('view-dashboard').classList.add('hidden');
        document.getElementById('view-login').classList.remove('hidden');
        showToast('Signed Out', 'Supervisor session ended.', '🔒');
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

        if (viewKey === 'monitor') {
            setTimeout(resizeCanvas, 50);
        }
    }

    function renderDirectoryTable() {
        const tbody = document.getElementById('traineeTableBody');
        if (!tbody) return;
        tbody.innerHTML = trainees.map((t, idx) => `
            <tr class="hover:bg-white/[0.02] transition">
                <td class="p-3.5 font-bold text-white flex items-center gap-2">
                    <span class="w-5 h-5 rounded-lg glass-card text-[10px] text-amber-400 font-mono flex items-center justify-center">#${idx + 1}</span>
                    <span>${t.id}</span>
                </td>
                <td class="p-3.5 font-mono ${t.score >= 90 ? 'text-emerald-400' : 'text-amber-400'} font-bold">${t.score} pts</td>
                <td class="p-3.5 font-mono text-slate-200">${t.found}</td>
                <td class="p-3.5 font-mono ${t.missed > 0 ? 'text-rose-400 font-bold' : 'text-slate-500'}">${t.missed}</td>
                <td class="p-3.5 font-mono text-slate-300">${t.time}</td>
                <td class="p-3.5"><span class="px-2 py-0.5 text-[10px] font-semibold rounded-lg glass-card">${t.rating}</span></td>
                <td class="p-3.5 text-slate-400 font-mono">${t.date}</td>
                <td class="p-3.5 text-center">
                    <button onclick="loadTraineeDossier('${t.id}')" class="px-3 py-1 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 text-neutral-950 font-bold text-[11px] transition shadow-sm cursor-pointer">
                        Inspect
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function filterDirectoryTable() {
        const q = document.getElementById('directorySearchInput').value.toLowerCase();
        document.querySelectorAll('#traineeTableBody tr').forEach(r => {
            r.style.display = r.innerText.toLowerCase().includes(q) ? '' : 'none';
        });
    }

    function handleGlobalSearch() {
        const q = document.getElementById('globalSearchInput').value.toLowerCase();
        if (q.length > 1) {
            navigateView('directory');
            const dirInput = document.getElementById('directorySearchInput');
            if (dirInput) dirInput.value = q;
            filterDirectoryTable();
        }
    }

    function loadTraineeDossier(traineeId) {
        const trainee = trainees.find(t => t.id === traineeId);
        if (!trainee) return;
        selectedTrainee = trainee;

        document.getElementById('dossierAvatar').innerText = trainee.id.slice(0, 2);
        document.getElementById('dossierTitle').innerText = `5. Telemetry Dossier: ${trainee.id}`;
        document.getElementById('dossierDate').innerText = `Drill Date: ${trainee.date}`;
        document.getElementById('dossierRating').innerText = trainee.rating;
        document.getElementById('dossierScore').innerText = `${trainee.score} / 100 Pts`;
        document.getElementById('dossierRatio').innerText = `${trainee.found} / 10 Hazards Discovered (${trainee.missed} Missed)`;
        document.getElementById('dossierAccuracy').innerText = `${trainee.found * 10}% Accuracy`;
        document.getElementById('dossierBar').style.width = `${trainee.found * 10}%`;
        document.getElementById('supervisorRemarksInput').value = trainee.notes;
        
        const monitorUser = document.getElementById('monitorActiveUser');
        if (monitorUser) monitorUser.innerText = `Operator: Trainee ${trainee.id} (${trainee.score} Pts)`;

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
        showToast('Dossier Loaded', `Loaded telemetry for ${trainee.id}`, '👤');
    }

    function saveRemarks() {
        selectedTrainee.notes = document.getElementById('supervisorRemarksInput').value;
        showToast('Remarks Saved', `Evaluation updated for ${selectedTrainee.id}.`, '💾');
    }

  function exportPdfCertificate() {
    const t = selectedTrainee || {
        id: 'qq',
        score: 100,
        time: '47.9s',
        found: 8,
        date: '01 Oct 2026',
        rating: 'Locked In',
        notes: 'Detected 480V arc-flash hazard under 5.1s.'
    };

    const certWindow = window.open('', '_blank');
    certWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Certificate — ${t.id}</title>
            <style>
                /* Strips browser header/footer (date, time, about:blank, page numbers) */
                @page {
                    size: A4 landscape;
                    margin: 0;
                }
                * {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                }
                html, body {
                    width: 100%;
                    height: 100%;
                    background: #ffffff;
                    color: #1e293b;
                    font-family: Arial, sans-serif;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .cert-box {
                    width: 90%;
                    max-width: 950px;
                    border: 4px solid #f59e0b;
                    border-radius: 20px;
                    padding: 40px 50px;
                    text-align: center;
                    background: #ffffff;
                }
                h1.title {
                    color: #f59e0b;
                    font-size: 28px;
                    font-weight: 800;
                    margin-bottom: 6px;
                    text-transform: uppercase;
                }
                h2.subtitle {
                    color: #64748b;
                    font-size: 16px;
                    font-weight: 600;
                    margin-bottom: 24px;
                }
                .trainee-name {
                    font-size: 34px;
                    font-weight: bold;
                    color: #0f172a;
                    margin: 15px 0 20px 0;
                }
                .stats {
                    font-size: 15px;
                    color: #475569;
                    margin-bottom: 25px;
                }
                .notes {
                    font-size: 13px;
                    color: #64748b;
                    font-style: italic;
                    margin-bottom: 35px;
                }
                .supervisor {
                    font-size: 14px;
                    color: #334155;
                }
            </style>
        </head>
        <body>
            <div class="cert-box">
                <h1 class="title">FindHazard VR Safety Certificate</h1>
                <h2 class="subtitle">OSHA Industrial Hazard Detection Protocol</h2>

                <div class="trainee-name">${t.id}</div>

                <div class="stats">
                    Score: <strong>${t.score} / 100</strong> &nbsp;|&nbsp; Duration: <strong>${t.time}</strong>
                </div>

                <div class="notes">
                    "${t.notes || 'Hazard detection drill verified.'}"
                </div>

                <div class="supervisor">
                    Supervisor: <strong>En. Faiz</strong>
                </div>
            </div>

            <script>
                window.onload = function() {
                    window.print();
                };
            <\/script>
        </body>
        </html>
    `);
    certWindow.document.close();
}

    // VR Monitor Raycast Simulation
    const canvas = document.getElementById('vrMonitorCanvas');
    const ctx = canvas ? canvas.getContext('2d') : null;
    let mouseX = null, mouseY = null;

    function resizeCanvas() {
        if (!canvas) return;
        const rect = canvas.getBoundingClientRect();
        if (rect.width === 0) return;
        canvas.width = rect.width;
        canvas.height = rect.height;
    }

    if (canvas) {
        canvas.addEventListener('mousemove', (e) => {
            const rect = canvas.getBoundingClientRect();
            mouseX = e.clientX - rect.left;
            mouseY = e.clientY - rect.top;
        });
    }

    function drawMonitor() {
        if (!canvas || !ctx) return;
        const w = canvas.width;
        const h = canvas.height;

        if (w > 0 && h > 0) {
            const grad = ctx.createLinearGradient(0, 0, 0, h);
            grad.addColorStop(0, '#06070a');
            grad.addColorStop(0.5, '#10131e');
            grad.addColorStop(1, '#06070a');
            ctx.fillStyle = grad;
            ctx.fillRect(0, 0, w, h);

            ctx.strokeStyle = 'rgba(255, 255, 255, 0.05)';
            ctx.lineWidth = 1;
            const vpX = w / 2;
            const vpY = h * 0.42;

            for (let i = 0; i <= w; i += w / 7) {
                ctx.beginPath();
                ctx.moveTo(vpX, vpY);
                ctx.lineTo(i, h);
                ctx.stroke();
            }

            const boxX = w * 0.65;
            const boxY = h * 0.48;
            ctx.fillStyle = '#1c1f2e';
            ctx.strokeStyle = '#f59e0b';
            ctx.lineWidth = 2;
            ctx.fillRect(boxX - 35, boxY - 40, 70, 80);
            ctx.strokeRect(boxX - 35, boxY - 40, 70, 80);

            ctx.fillStyle = '#f59e0b';
            ctx.font = 'bold 11px JetBrains Mono';
            ctx.fillText('⚡ 480V', boxX - 22, boxY - 15);

            const targetX = mouseX !== null ? mouseX : boxX;
            const targetY = mouseY !== null ? mouseY : boxY;

            ctx.strokeStyle = '#f59e0b';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(w * 0.85, h);
            ctx.lineTo(targetX, targetY);
            ctx.stroke();

            ctx.strokeStyle = '#10b981';
            ctx.lineWidth = 2;
            ctx.strokeRect(targetX - 35, targetY - 25, 70, 50);

            ctx.fillStyle = '#10b981';
            ctx.font = 'bold 9px JetBrains Mono';
            ctx.fillText('[LOCKED: 99.4%]', targetX - 33, targetY - 30);
        }

        requestAnimationFrame(drawMonitor);
    }

    function centerReticle() {
        if (!canvas) return;
        mouseX = canvas.width * 0.65;
        mouseY = canvas.height * 0.48;
        showToast('Reticle Centered', 'Aim laser locked on 480V junction box.');
    }

    window.addEventListener('resize', resizeCanvas);
    window.addEventListener('DOMContentLoaded', () => {
        renderDirectoryTable();
        loadTraineeDossier('qq');
        resizeCanvas();
        drawMonitor();
    });
</script>