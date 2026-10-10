/**
 * FindHazard VR — Supervisor Glass Dashboard Client Logic
 */

const defaultTrainees = [
    { id: 'qq', experience: 'Manufacturing', score: 100, found: 10, missed: 0, time: '47.9s', rating: 'Locked In', date: '01 Oct 2026, 06:18', dateShort: '01 Oct 2026', notes: 'Detected 480V arc-flash hazard under 5.1s.', missedIndices: [] },
    { id: 'Ahmad_Rizal', experience: 'Logistics Center', score: 90, found: 9, missed: 1, time: '52.3s', rating: 'Great', date: '01 Oct 2026, 05:42', dateShort: '01 Oct 2026', notes: 'Overlooked overhead crane fray.', missedIndices: [7] },
    { id: 'Sarah_Tan', experience: 'Manufacturing', score: 90, found: 9, missed: 1, time: '54.1s', rating: 'Great', date: '01 Oct 2026, 04:15', dateShort: '01 Oct 2026', notes: 'Missed blocked extinguisher in corridor 3.', missedIndices: [4] },
    { id: 'Danial_Haziq', experience: 'Retail', score: 80, found: 8, missed: 2, time: '61.5s', rating: 'Valid Effort', date: '30 Sep 2026, 17:30', dateShort: '30 Sep 2026', notes: 'Missed 480V enclosure latch.', missedIndices: [2, 8] },
    { id: 'Nurul_Ain', experience: 'Logistics Center', score: 70, found: 7, missed: 3, time: '68.2s', rating: 'Cooked', date: '30 Sep 2026, 15:10', dateShort: '30 Sep 2026', notes: 'Retraining recommended for GHS symbols.', missedIndices: [2, 5, 9] }
];

let trainees = [...defaultTrainees];

function syncTraineesFromDB() {
    if (window.dbTrainees && Array.isArray(window.dbTrainees) && window.dbTrainees.length > 0) {
        trainees = window.dbTrainees.map(r => {
            const d = new Date(r.created_at);
            const dateStr = !isNaN(d) ? d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Recent';
            const dateShortStr = !isNaN(d) ? d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : 'Today';
            return {
                id: r.username,
                experience: 'VR Inspection',
                score: r.score,
                found: r.hazards_found,
                missed: r.hazards_missed,
                time: (r.completion_time || 0) + 's',
                rating: r.performance_rating || 'Locked In',
                date: dateStr,
                dateShort: dateShortStr,
                notes: `VR Headset telemetry. Found: ${(r.found_hazards || []).join(', ') || 'N/A'}. Missed: ${(r.missed_hazards || []).join(', ') || 'None'}`,
                missedIndices: []
            };
        });
    }
}

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

function showToast(title, body, icon = '⚡') {
    const toast = document.getElementById('toastMessage');
    if (!toast) return;
    const tTitle = document.getElementById('toastTitle');
    const tBody = document.getElementById('toastBody');
    const tIcon = document.getElementById('toastIcon');
    if (tTitle) tTitle.innerText = title;
    if (tBody) tBody.innerText = body;
    if (tIcon) tIcon.innerText = icon;
    toast.classList.remove('opacity-0', 'pointer-events-none', '-translate-y-10');
    setTimeout(() => toast.classList.add('opacity-0', 'pointer-events-none', '-translate-y-10'), 2500);
}

function handleGlobalSearch(val) {
    const dirInput = document.getElementById('directorySearchInput');
    if (dirInput) {
        dirInput.value = val;
        applyDirectoryFilters();
    }
}

function applyDirectoryFilters() {
    syncTraineesFromDB();
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
                <a href="/analysis?trainee=${encodeURIComponent(t.id)}" class="px-3 py-1 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 text-neutral-950 font-bold text-[11px] inline-block hover:brightness-110 transition cursor-pointer">
                    Inspect
                </a>
            </td>
        </tr>
    `).join('');
}

function loadTraineeDossier(id) {
    syncTraineesFromDB();
    let trainee = trainees.find(t => t.id === id);
    if (!trainee && window.currentTraineeRecord) {
        const r = window.currentTraineeRecord;
        const d = new Date(r.created_at);
        const dateStr = !isNaN(d) ? d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Verified';
        trainee = {
            id: r.username,
            experience: 'VR Inspection',
            score: r.score,
            found: r.hazards_found,
            missed: r.hazards_missed,
            time: (r.completion_time || 0) + 's',
            rating: r.performance_rating || 'Locked In',
            date: dateStr,
            notes: `VR Headset telemetry. Found: ${(r.found_hazards || []).join(', ') || 'N/A'}. Missed: ${(r.missed_hazards || []).join(', ') || 'None'}`,
            missedIndices: []
        };
    }
    if (!trainee) trainee = trainees[0];
    selectedTrainee = trainee;

    const titleEl = document.getElementById('dossierTitle');
    const dateEl = document.getElementById('dossierDate');
    const ratingEl = document.getElementById('dossierRating');
    const scoreEl = document.getElementById('dossierScore');
    const activeUserEl = document.getElementById('monitorActiveUser');

    if (titleEl) titleEl.innerText = `Analysis: ${trainee.id}`;
    if (dateEl) dateEl.innerText = `Drill Date: ${trainee.date}`;
    if (ratingEl) ratingEl.innerText = trainee.rating;
    if (scoreEl) scoreEl.innerText = `${trainee.score} / 100 Pts`;
    if (activeUserEl) activeUserEl.innerText = `Operator: Trainee ${trainee.id}`;

    const container = document.getElementById('checkpointsContainer');
    if (container) {
        container.innerHTML = scenarioCheckpoints.map(cp => {
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
    }
}

function renderIncomingRunsLog() {
    syncTraineesFromDB();
    const container = document.getElementById('incomingRunsContainer');
    if (!container) return;
    container.innerHTML = trainees.slice(0, 4).map(run => `
        <a href="/analysis?trainee=${encodeURIComponent(run.id)}" class="glass-sheet p-2.5 rounded-2xl flex items-center justify-between cursor-pointer hover:border-amber-400/40 transition block">
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
        </a>
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
    const hitEl = document.getElementById('statHazardHits');
    const misEl = document.getElementById('statMisclicks');
    if (hitEl) hitEl.innerText = hazardHitsCount;
    if (misEl) misEl.innerText = misclicksCount;
}

function clearEventFeed() {
    clickEventFeed.length = 0;
    hazardHitsCount = 0;
    misclicksCount = 0;
    renderClickEventFeed();
}

function changeWarehouseZone(z) {
    const el = document.getElementById('hudWarehouseZoneLabel');
    if (el) el.innerText = z;
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

// VR Monitor Canvas Setup
let canvas = null;
let ctx = null;
let mouseX = null, mouseY = null;

function setupCanvas() {
    canvas = document.getElementById('vrMonitorCanvas');
    if (!canvas) return;
    ctx = canvas.getContext('2d');

    resizeCanvas();

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

    drawMonitor();
}

function resizeCanvas() {
    if (!canvas) return;
    const r = canvas.getBoundingClientRect();
    if (r.width > 0) { canvas.width = r.width; canvas.height = r.height; }
}

function drawMonitor() {
    if (!canvas || !ctx) return;
    const w = canvas.width, h = canvas.height;
    if (w > 0 && h > 0) {
        ctx.fillStyle = '#07080c';
        ctx.fillRect(0, 0, w, h);

        ctx.strokeStyle = 'rgba(255,255,255,0.06)';
        for (let i = 0; i <= w; i += w / 7) {
            ctx.beginPath(); ctx.moveTo(w / 2, h * 0.42); ctx.lineTo(i, h); ctx.stroke();
        }

        // 480V Hazard Box
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

// Automatic Page Initialization based on current page elements
document.addEventListener('DOMContentLoaded', () => {
    // 1. Overview Page
    if (document.getElementById('incomingRunsContainer')) {
        renderIncomingRunsLog();
    }

    // 2. Directory Page
    if (document.getElementById('traineeTableBody')) {
        applyDirectoryFilters();
    }

    // 3. Live VR Monitor Page
    if (document.getElementById('vrMonitorCanvas')) {
        setupCanvas();
        startActiveSessionTimer();
        renderClickEventFeed();
    }

    // 4. Analysis / Checkpoints Page
    if (document.getElementById('checkpointsContainer')) {
        const urlParams = new URLSearchParams(window.location.search);
        const traineeId = urlParams.get('trainee') || 'qq';
        loadTraineeDossier(traineeId);
    }
});
