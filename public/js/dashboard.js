/**
 * FindHazard VR — Supervisor Glass Dashboard Client Logic
 */

const defaultTrainees = [
    { id: 'amir_inspector', score: 92.3, found: 12, missed: 1, wrong_clicks: 0, is_certified: true, time: '1m 12s', rating: 'Locked In', date: '10 Oct 2026, 03:00 PM', notes: '', missedIndices: [] },
    { id: 'hafiz_pilot', score: 79.6, found: 11, missed: 2, wrong_clicks: 1, is_certified: false, time: '1m 24s', rating: 'Valid Effort', date: '10 Oct 2026, 03:00 PM', notes: '', missedIndices: [] },
    { id: 'sayig', score: 100, found: 13, missed: 0, wrong_clicks: 0, is_certified: true, time: '1m 58s', rating: 'Locked In', date: '10 Oct 2026, 01:25 AM', notes: '', missedIndices: [] },
    { id: 'quest3_pilot', score: 80, found: 8, missed: 5, wrong_clicks: 0, is_certified: true, time: '0m 51s', rating: 'Great', date: '10 Oct 2026, 01:18 AM', notes: '', missedIndices: [] },
    { id: 'vr_test_user', score: 90, found: 9, missed: 4, wrong_clicks: 0, is_certified: true, time: '0m 42s', rating: 'Great', date: '10 Oct 2026, 01:17 AM', notes: '', missedIndices: [] }
];

let trainees = [...defaultTrainees];

function syncTraineesFromDB() {
    if (window.dbTrainees && Array.isArray(window.dbTrainees) && window.dbTrainees.length > 0) {
        trainees = window.dbTrainees.map(r => {
            const d = new Date(r.created_at);
            const dateStr = !isNaN(d)
                ? d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) + ', ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })
                : 'Recent';
            const dateShortStr = !isNaN(d)
                ? d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })
                : '';
            const mins = Math.floor((r.completion_time || 0) / 60);
            const secs = Math.round((r.completion_time || 0) % 60);
            const timeFormatted = `${mins}m ${secs.toString().padStart(2, '0')}s`;
            return {
                db_id: r.id,
                id: r.username,
                score: typeof r.score === 'number' ? r.score : parseFloat(r.score) || 0,
                found: r.hazards_found,
                missed: r.hazards_missed,
                wrong_clicks: r.wrong_clicks || 0,
                is_certified: Boolean(r.is_certified),
                time: timeFormatted,
                completion_time: r.completion_time || 0,
                rating: r.performance_rating || 'Locked In',
                date: dateStr,
                dateShort: dateShortStr,
                created_at: r.created_at,
                found_hazards: r.found_hazards || [],
                missed_hazards: r.missed_hazards || [],
                notes: `VR Headset telemetry. Found: ${(r.found_hazards || []).join(', ') || 'N/A'}. Missed: ${(r.missed_hazards || []).join(', ') || 'None'}`,
                missedIndices: []
            };
        });

        // Ensure date dropdown in directory contains all distinct session dates
        const dateSelect = document.getElementById('directoryDateFilter');
        if (dateSelect && dateSelect.options.length <= 1) {
            const uniqueDates = [...new Set(trainees.map(t => t.dateShort).filter(Boolean))];
            uniqueDates.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d;
                opt.className = 'bg-neutral-900 text-slate-100';
                opt.innerText = d;
                dateSelect.appendChild(opt);
            });
        }
    }
}

const scenarioCheckpoints = [
    { id: 1, name: 'Tarmac Foreign Object Debris (FOD)', cat: 'Airside Cargo Area', time: '00:03.4' },
    { id: 2, name: 'Dislodged Restraint Net on PMC Pallet', cat: 'Airside Cargo Area', time: '00:06.1' },
    { id: 3, name: 'Fluid & Oil Leak Slip Hazard', cat: 'Aircraft Parts Storage Area', time: '00:09.8' },
    { id: 4, name: 'Dislodged Aircraft Parts', cat: 'Aircraft Parts Storage Area', time: '00:13.2' },
    { id: 5, name: 'Unstable Cargo Stack', cat: 'Cargo Storage Area', time: '00:17.5' },
    { id: 6, name: 'Blocked Emergency Exit', cat: 'Cargo Storage Area', time: '00:22.0' },
    { id: 7, name: 'Overloaded Forklift', cat: 'Main Cargo Handling Area', time: '00:27.4' },
    { id: 8, name: 'Unsecured Boxes', cat: 'Main Cargo Handling Area', time: '00:32.1' },
    { id: 9, name: 'Improperly Stacked ULD Container', cat: 'ULD Storage Area', time: '00:37.8' },
    { id: 10, name: 'Damaged ULD', cat: 'ULD Storage Area', time: '00:43.0' },
    { id: 11, name: 'Exposed Power Cable', cat: 'Loading/Unloading Area', time: '00:48.6' },
    { id: 12, name: 'Accumulated Packaging Debris', cat: 'Loading/Unloading Area', time: '00:54.2' },
    { id: 13, name: 'Crushed Bottom Pallet Base', cat: 'Loading/Unloading Area', time: '01:01.5' }
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
    const sortVal = document.getElementById('directorySortFilter')?.value || 'top_to_bottom';
    const certVal = document.getElementById('directoryCertFilter')?.value || 'all';

    // 1. Filter
    let filtered = trainees.filter(t => {
        const matchesSearch = !searchVal || t.id.toLowerCase().includes(searchVal);
        const matchesDate = (dateVal === 'all') || (t.dateShort === dateVal) || (t.date && t.date.includes(dateVal));
        const matchesCert = (certVal === 'all') ||
            (certVal === 'qualified' && t.is_certified) ||
            (certVal === 'retest' && !t.is_certified);

        return matchesSearch && matchesDate && matchesCert;
    });

    // 2. Sort / Leaderboard ranking
    filtered.sort((a, b) => {
        const scoreA = typeof a.score === 'number' ? a.score : parseFloat(a.score) || 0;
        const scoreB = typeof b.score === 'number' ? b.score : parseFloat(b.score) || 0;
        const timeA = typeof a.completion_time === 'number' ? a.completion_time : parseFloat(a.completion_time) || 0;
        const timeB = typeof b.completion_time === 'number' ? b.completion_time : parseFloat(b.completion_time) || 0;
        const dateA = new Date(a.created_at || a.date).getTime() || 0;
        const dateB = new Date(b.created_at || b.date).getTime() || 0;
        const foundA = typeof a.found === 'number' ? a.found : parseInt(a.found) || 0;
        const foundB = typeof b.found === 'number' ? b.found : parseInt(b.found) || 0;

        if (sortVal === 'top_to_bottom') {
            // Leaderboard top to bottom: highest score first, then fastest completion time
            if (scoreB !== scoreA) return scoreB - scoreA;
            return timeA - timeB;
        } else if (sortVal === 'bottom_to_top') {
            // Lowest score first
            if (scoreA !== scoreB) return scoreA - scoreB;
            return timeB - timeA;
        } else if (sortVal === 'fastest_time') {
            return timeA - timeB;
        } else if (sortVal === 'most_found') {
            if (foundB !== foundA) return foundB - foundA;
            return scoreB - scoreA;
        } else if (sortVal === 'date_newest') {
            return dateB - dateA;
        } else if (sortVal === 'date_oldest') {
            return dateA - dateB;
        }
        return scoreB - scoreA;
    });

    const matchEl = document.getElementById('directoryMatchCount');
    if (matchEl) matchEl.innerText = filtered.length;

    const tbody = document.getElementById('traineeTableBody');
    if (!tbody) return;

    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="p-8 text-center text-slate-500">
                    No matching trainees found for the selected filters.
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = filtered.map((t, idx) => {
        const score = typeof t.score === 'number' ? t.score : parseFloat(t.score) || 0;
        const scVal = Math.max(0, Math.min(100, score));
        const scOffset = (100 - scVal).toFixed(1);
        const scColor = scVal >= 90 ? 'text-emerald-400' : (scVal >= 80 ? 'text-teal-400' : (scVal >= 60 ? 'text-amber-400' : 'text-rose-400'));
        const scTextClass = scVal >= 80 ? 'text-emerald-400' : (scVal >= 60 ? 'text-amber-400' : 'text-rose-400');
        const scoreInt = Math.floor(score);
        const scoreFormatted = score.toFixed(1);

        const wrongClicks = t.wrong_clicks || 0;
        const wrongClicksHtml = wrongClicks > 0
            ? `<span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30" title="-${wrongClicks * 5}% Penalty applied">
                 ${wrongClicks} (-${wrongClicks * 5}%)
               </span>`
            : `<span class="text-xs font-mono text-slate-500">0</span>`;

        const certHtml = t.is_certified
            ? `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                 <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                 Qualified
               </span>`
            : `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                 <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                 Retest (&lt;80%)
               </span>`;

        // Leaderboard rank badges
        let rankBadge = '';
        if (sortVal === 'top_to_bottom') {
            if (idx === 0) {
                rankBadge = `<span class="w-6 h-6 rounded-lg bg-amber-400/20 text-amber-300 border border-amber-400/40 text-[10px] font-black font-mono inline-flex items-center justify-center shrink-0 shadow-sm shadow-amber-400/20" title="Rank 1 - Top Performer">#1</span>`;
            } else if (idx === 1) {
                rankBadge = `<span class="w-6 h-6 rounded-lg bg-slate-300/20 text-slate-200 border border-slate-300/40 text-[10px] font-black font-mono inline-flex items-center justify-center shrink-0" title="Rank 2">#2</span>`;
            } else if (idx === 2) {
                rankBadge = `<span class="w-6 h-6 rounded-lg bg-amber-700/20 text-amber-500 border border-amber-700/40 text-[10px] font-black font-mono inline-flex items-center justify-center shrink-0" title="Rank 3">#3</span>`;
            } else {
                rankBadge = `<span class="w-6 h-6 rounded-lg glass-card text-[10px] text-slate-400 font-mono inline-flex items-center justify-center shrink-0">#${idx + 1}</span>`;
            }
        } else {
            rankBadge = `<span class="w-6 h-6 rounded-lg glass-card text-[10px] text-slate-400 font-mono inline-flex items-center justify-center shrink-0">#${idx + 1}</span>`;
        }

        return `
            <tr class="hover:bg-white/[0.03] transition-colors">
                <td class="p-3.5 font-semibold text-white whitespace-nowrap">
                    <div class="flex items-center gap-2.5">
                        ${rankBadge}
                        <span class="font-bold text-white font-mono">${t.id}</span>
                    </div>
                </td>
                <!-- Visual Score Ring & Percentage Score -->
                <td class="p-3.5 whitespace-nowrap">
                    <div class="flex items-center gap-3">
                        <!-- Visual Score Ring -->
                        <div class="relative w-9 h-9 shrink-0 flex items-center justify-center">
                            <svg class="w-9 h-9 -rotate-90 transform" viewBox="0 0 36 36">
                                <path
                                    class="text-white/10"
                                    stroke="currentColor"
                                    stroke-width="3"
                                    fill="none"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                />
                                <path
                                    class="${scColor} transition-all duration-700 ease-out"
                                    stroke="currentColor"
                                    stroke-width="3.2"
                                    stroke-dasharray="100, 100"
                                    stroke-dashoffset="${scOffset}"
                                    stroke-linecap="round"
                                    fill="none"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                />
                            </svg>
                            <span class="absolute text-[8.5px] font-mono font-black text-slate-200">
                                ${scoreInt}
                            </span>
                        </div>
                        <!-- Percentage Score Text -->
                        <span class="font-extrabold font-['JetBrains_Mono'] text-sm ${scTextClass}">
                            ${scoreFormatted}%
                        </span>
                    </div>
                </td>
                <td class="p-3.5 font-mono text-sky-400 font-bold whitespace-nowrap">
                    ${t.time}
                </td>
                <!-- Hazards Identified (Found out of 13 in Green) -->
                <td class="p-3.5 text-center whitespace-nowrap">
                    <span class="inline-flex items-center justify-center gap-1.5 px-3 py-1 rounded-full text-xs font-black font-mono bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 shadow-sm shadow-emerald-500/10">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                        ${t.found}/13
                    </span>
                </td>
                <!-- Hazards Missed (Missed out of 13 in Red) -->
                <td class="p-3.5 text-center whitespace-nowrap">
                    <span class="inline-flex items-center justify-center gap-1.5 px-3 py-1 rounded-full text-xs font-black font-mono bg-rose-500/15 text-rose-400 border border-rose-500/30 shadow-sm shadow-rose-500/10">
                        <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        ${t.missed}/13
                    </span>
                </td>
                <!-- Wrong Clicks Penalty -->
                <td class="p-3.5 text-center whitespace-nowrap">
                    ${wrongClicksHtml}
                </td>
                <!-- Certificate Qualification -->
                <td class="p-3.5 text-center whitespace-nowrap">
                    ${certHtml}
                </td>
                <!-- Standard Time Date -->
                <td class="p-3.5 text-xs text-slate-300 whitespace-nowrap font-mono">
                    ${t.date}
                </td>
                <!-- Action / View Profile Button -->
                <td class="p-3.5 text-center whitespace-nowrap">
                    <a href="/trainee/${t.db_id || t.id}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/15 hover:bg-amber-400 text-amber-300 hover:text-neutral-950 font-mono font-bold text-xs border border-amber-500/30 transition shadow-sm hover:shadow-amber-500/20 cursor-pointer" title="View ${t.id}'s Personal Profile & Zone Breakdown">
                        <span>Profile</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </td>
            </tr>
        `;
    }).join('');
}

function loadTraineeDossier(id) {
    syncTraineesFromDB();
    let trainee = trainees.find(t => t.id === id);
    if (window.currentTraineeRecord) {
        const r = window.currentTraineeRecord;
        if (!trainee || trainee.id === r.username) {
            const d = new Date(r.created_at);
            const dateStr = !isNaN(d) ? d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Verified';
            const mins = Math.floor((r.completion_time || 0) / 60);
            const secs = Math.round((r.completion_time || 0) % 60);
            trainee = {
                id: r.username,
                experience: 'VR Inspection',
                score: r.score,
                found: r.hazards_found,
                missed: r.hazards_missed,
                wrong_clicks: r.wrong_clicks || 0,
                time: `${mins}m ${secs.toString().padStart(2, '0')}s`,
                rating: r.performance_rating || 'Locked In',
                is_certified: Boolean(r.is_certified),
                date: dateStr,
                found_hazards: Array.isArray(r.found_hazards) ? r.found_hazards : [],
                missed_hazards: Array.isArray(r.missed_hazards) ? r.missed_hazards : [],
                notes: `VR Telemetry Verified. Wrong Clicks: ${r.wrong_clicks || 0}.`,
                missedIndices: []
            };
        }
    }
    if (!trainee) trainee = trainees[0];
    selectedTrainee = trainee;

    const titleEl = document.getElementById('dossierTitle');
    const dateEl = document.getElementById('dossierDate');
    const ratingEl = document.getElementById('dossierRating');
    const scoreEl = document.getElementById('dossierScore');
    const certifiedEl = document.getElementById('dossierCertified');
    const wrongEl = document.getElementById('dossierWrongClicks');
    const foundEl = document.getElementById('dossierFoundCount');
    const missedEl = document.getElementById('dossierMissedCount');
    const durationEl = document.getElementById('dossierDuration');
    const activeUserEl = document.getElementById('monitorActiveUser');

    if (titleEl) titleEl.innerText = `Analysis: ${trainee.id}`;
    if (dateEl) dateEl.innerText = `Drill Date: ${trainee.date}`;
    if (ratingEl) {
        ratingEl.innerText = trainee.rating;
        ratingEl.className = `px-3 py-1.5 rounded-xl text-xs font-mono font-bold border ${
            trainee.rating === 'Locked In' ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' :
            trainee.rating === 'Great' ? 'bg-sky-500/15 text-sky-300 border-sky-500/30' :
            trainee.rating === 'Valid Effort' ? 'bg-amber-500/15 text-amber-300 border-amber-500/30' :
            'bg-rose-500/15 text-rose-300 border-rose-500/30'
        }`;
    }
    if (scoreEl) scoreEl.innerText = `${Number(trainee.score).toFixed(1)}% Score`;
    if (wrongEl) wrongEl.innerText = `${trainee.wrong_clicks || 0} misclicks`;
    if (foundEl) foundEl.innerText = `${trainee.found} / 13`;
    if (missedEl) missedEl.innerText = `${trainee.missed} / 13`;
    if (durationEl) durationEl.innerText = trainee.time;
    if (certifiedEl) {
        const isCert = trainee.is_certified ?? (trainee.score >= 80);
        certifiedEl.innerText = isCert ? '✓ Qualified' : '✕ Retest Required (<80%)';
        certifiedEl.className = `px-2.5 py-0.5 rounded-full text-xs font-bold font-mono ${
            isCert ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' :
            'bg-rose-500/15 text-rose-400 border border-rose-500/30'
        }`;
    }
    if (activeUserEl) activeUserEl.innerText = `Operator: Trainee ${trainee.id}`;

    const container = document.getElementById('checkpointsContainer');
    if (container) {
        container.innerHTML = scenarioCheckpoints.map(cp => {
            let missed = false;
            if (trainee.found_hazards && trainee.found_hazards.length > 0) {
                const isFound = trainee.found_hazards.some(f => f.toLowerCase().includes(cp.name.toLowerCase().slice(0, 8)));
                missed = !isFound;
            } else if (trainee.missedIndices && trainee.missedIndices.length > 0) {
                missed = trainee.missedIndices.includes(cp.id);
            } else {
                missed = cp.id > trainee.found;
            }

            return `
                <div class="p-3 rounded-2xl flex justify-between items-center text-xs ${missed ? 'glass-card border-rose-500/30' : 'glass-sheet border-emerald-500/20'}">
                    <div>
                        <span class="${missed ? 'text-slate-400 line-through' : 'text-slate-200 font-medium'}">${cp.id}. ${cp.name}</span>
                        <span class="block text-[10px] text-slate-500 font-mono">${cp.cat} &bull; ${cp.time}</span>
                    </div>
                    <span class="font-mono text-xs px-2.5 py-1 rounded-lg ${missed ? 'bg-rose-500/10 text-rose-400 font-bold' : 'bg-emerald-500/10 text-emerald-400 font-bold'}">
                        ${missed ? 'MISSED (0%)' : 'IDENTIFIED (+7.7%)'}
                    </span>
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
        <div class="glass-sheet p-2.5 rounded-2xl flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-amber-400/10 text-amber-400 font-bold flex items-center justify-center text-xs font-mono">
                    ${run.id.slice(0, 2).toUpperCase()}
                </span>
                <div>
                    <p class="text-xs font-bold text-slate-200">${run.id}</p>
                    <p class="text-[9px] text-slate-400 font-mono">${run.found}/13 Spotted &bull; ${run.time}</p>
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
    const t = window.traineeRecordData || selectedTrainee;
    const isCert = t.is_certified ?? (t.score >= 80);
    const w = window.open('', '_blank');
    w.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>VR Safety Certificate - ${t.id}</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #08090d; color: #f8fafc; padding: 40px; margin: 0; text-align: center; }
                .cert-container { border: 4px solid ${isCert ? '#f59e0b' : '#f43f5e'}; padding: 48px; border-radius: 28px; max-width: 760px; margin: auto; background: radial-gradient(circle at center, rgba(30,34,50,0.9), rgba(11,13,20,0.98)); box-shadow: 0 25px 60px rgba(0,0,0,0.8); }
                .badge { display: inline-block; padding: 6px 18px; border-radius: 9999px; font-size: 11px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase; background: ${isCert ? 'rgba(245,158,11,0.15)' : 'rgba(244,63,94,0.15)'}; color: ${isCert ? '#fbbf24' : '#fb7185'}; border: 1px solid ${isCert ? 'rgba(245,158,11,0.3)' : 'rgba(244,63,94,0.3)'}; margin-bottom: 20px; }
                h1 { margin: 10px 0; font-size: 28px; text-transform: uppercase; letter-spacing: 2px; color: #fbbf24; }
                h2 { margin: 0 0 20px 0; font-size: 14px; font-weight: 500; color: #94a3b8; }
                .trainee-name { font-size: 36px; font-weight: 900; color: #ffffff; margin: 24px 0 10px 0; border-bottom: 2px solid rgba(255,255,255,0.1); padding-bottom: 12px; }
                .stats-grid { display: flex; justify-content: space-around; margin: 28px 0; padding: 18px; background: rgba(255,255,255,0.03); border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); }
                .stat-box { font-size: 12px; color: #94a3b8; }
                .stat-val { font-size: 20px; font-weight: 800; color: #ffffff; font-family: monospace; margin-top: 4px; }
                .status-banner { font-size: 16px; font-weight: 800; padding: 12px; border-radius: 12px; margin: 24px 0; background: ${isCert ? 'rgba(16,185,129,0.15)' : 'rgba(244,63,94,0.15)'}; color: ${isCert ? '#34d399' : '#f87171'}; border: 1px solid ${isCert ? 'rgba(16,185,129,0.3)' : 'rgba(244,63,94,0.3)'}; }
                .footer { margin-top: 40px; display: flex; justify-content: space-between; align-items: flex-end; text-align: left; font-size: 11px; color: #64748b; }
            </style>
        </head>
        <body>
            <div class="cert-container">
                <div class="badge">${isCert ? 'VR Safety Compliance Verified' : 'Evaluation Audit Report'}</div>
                <h1>FindHazard VR Safety Protocol</h1>
                <h2>Aviation Cargo & Warehouse Telemetry Inspection Drill</h2>
                <div class="trainee-name">${t.id}</div>
                <div class="status-banner">
                    ${isCert ? '★ CERTIFIED INSPECTOR — QUALIFICATION APPROVED (≥80%)' : '⚠ RETEST REQUIRED — MINIMUM PASSING THRESHOLD NOT MET (<80%)'}
                </div>
                <div class="stats-grid">
                    <div class="stat-box">FINAL SCORE<div class="stat-val" style="color: ${isCert ? '#34d399' : '#f87171'};">${Number(t.score).toFixed(1)}%</div></div>
                    <div class="stat-box">HAZARDS IDENTIFIED<div class="stat-val" style="color: #38bdf8;">${t.found} / 13</div></div>
                    <div class="stat-box">WRONG CLICKS<div class="stat-val" style="color: #fbbf24;">${t.wrong_clicks || 0} (-${(t.wrong_clicks || 0) * 5}%)</div></div>
                    <div class="stat-box">COMPLETION TIME<div class="stat-val">${t.time}</div></div>
                </div>
                <div class="footer">
                    <div>
                        <div>Evaluation Date: <strong>${t.date}</strong></div>
                        <div>Protocol ID: <strong>FH-VR-2026-SAFETY-13H</strong></div>
                    </div>
                    <div style="text-align: right;">
                        <div>Supervisor Verification:</div>
                        <div style="font-size: 15px; font-weight: 800; color: #fbbf24; margin-top: 4px;">En. Faiz Adham</div>
                        <div style="font-size: 9px; color: #64748b;">Chief Safety Lead &bull; FindHazard VR</div>
                    </div>
                </div>
            </div>
            <script>window.print();<\/script>
        </body>
        </html>
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
});
