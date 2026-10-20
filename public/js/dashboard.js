/**
 * ============================================================================
 * FindHazard VR — Supervisor Glass Dashboard Client Engine
 * ============================================================================
 * 
 * Modular JavaScript engine managing:
 * - SECTION 1: Global State & Database Hydration
 * - SECTION 2: UI Toasts & Feedback
 * - SECTION 3: Directory Search, Filtering & 10-Item Pagination
 * - SECTION 4: Trainee Dossier Inspection & Checkpoint Highlights
 * - SECTION 5: Live VR Telemetry Feed & Session Timers
 * - SECTION 6: PDF Certificate Export Generator
 * - SECTION 7: Dynamic KPI Metric Counters & SVG Gauge Rings
 * - SECTION 8: DOM Lifecycle Initialization
 */

// ============================================================================
// SECTION 1: GLOBAL STATE & DATABASE HYDRATION
// ============================================================================

/** Fallback trainees used if the database is initially empty or offline */
const defaultTrainees = [
    { id: 'amir_inspector', score: 92.3, found: 12, missed: 1, wrong_clicks: 0, is_certified: true, time: '1m 12s', rating: 'Locked In', date: '10 Oct 2026, 03:00 PM', notes: '', missedIndices: [] },
    { id: 'hafiz_pilot', score: 79.6, found: 11, missed: 2, wrong_clicks: 1, is_certified: false, time: '1m 24s', rating: 'Valid Effort', date: '10 Oct 2026, 03:00 PM', notes: '', missedIndices: [] },
    { id: 'sayig', score: 100, found: 13, missed: 0, wrong_clicks: 0, is_certified: true, time: '1m 58s', rating: 'Locked In', date: '10 Oct 2026, 01:25 AM', notes: '', missedIndices: [] },
    { id: 'quest3_pilot', score: 80, found: 8, missed: 5, wrong_clicks: 0, is_certified: true, time: '0m 51s', rating: 'Great', date: '10 Oct 2026, 01:18 AM', notes: '', missedIndices: [] },
    { id: 'vr_test_user', score: 90, found: 9, missed: 4, wrong_clicks: 0, is_certified: true, time: '0m 42s', rating: 'Great', date: '10 Oct 2026, 01:17 AM', notes: '', missedIndices: [] }
];

let trainees = [...defaultTrainees];

/**
 * Hydrates client-side trainee state from server-injected window.dbTrainees.
 * Formats timestamps, completion times, and populates unique date dropdown options.
 */
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

/** Checkpoint lookup mapping for the 13 warehouse hazards */
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

// ============================================================================
// SECTION 2: UI TOASTS & FEEDBACK
// ============================================================================

/**
 * Display a temporary floating glass toast notification in the UI.
 * @param {string} title - Heading
 * @param {string} body - Subtext message
 * @param {string} icon - Emoji or symbol icon
 */
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

/**
 * Handle global quick-search query input and forward to directory filter.
 * @param {string} val - Query text
 */
function handleGlobalSearch(val) {
    const dirInput = document.getElementById('directorySearchInput');
    if (dirInput) {
        dirInput.value = val;
        applyDirectoryFilters();
    }
}

// ============================================================================
// SECTION 3: DIRECTORY SEARCH, FILTERING & 10-ITEM PAGINATION
// ============================================================================

let directoryCurrentPage = 1;
const directoryPageSize = 10;
let lastFilteredDirectoryTrainees = [];

/**
 * Advance or retreat directory pagination by delta (+1 or -1).
 * @param {number} delta
 */
function changeDirectoryPage(delta) {
    goToDirectoryPage(directoryCurrentPage + delta);
}

/**
 * Navigate directory table directly to a specified page index.
 * @param {number} pageNum
 */
function goToDirectoryPage(pageNum) {
    const totalPages = Math.max(1, Math.ceil(lastFilteredDirectoryTrainees.length / directoryPageSize));
    if (pageNum < 1) pageNum = 1;
    if (pageNum > totalPages) pageNum = totalPages;
    directoryCurrentPage = pageNum;
    renderDirectoryTablePage();

    const tableHeader = document.getElementById('view-directory');
    if (tableHeader) {
        tableHeader.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/**
 * Filter and sort trainees based on active dropdown controls, then render page 1.
 * @param {boolean} resetPage - Whether to jump back to page 1 on filter change
 */
function applyDirectoryFilters(resetPage = true) {
    if (resetPage) {
        directoryCurrentPage = 1;
    }
    syncTraineesFromDB();
    const searchVal = document.getElementById('directorySearchInput')?.value.toLowerCase().trim() || '';
    const dateVal = document.getElementById('directoryDateFilter')?.value || 'all';
    const sortVal = document.getElementById('directorySortFilter')?.value || 'top_to_bottom';
    const certVal = document.getElementById('directoryCertFilter')?.value || 'all';

    // 1. Filter trainees matching search, date, and certification status
    let filtered = trainees.filter(t => {
        const matchesSearch = !searchVal || t.id.toLowerCase().includes(searchVal);
        const matchesDate = (dateVal === 'all') || (t.dateShort === dateVal) || (t.date && t.date.includes(dateVal));
        const matchesCert = (certVal === 'all') ||
            (certVal === 'qualified' && t.is_certified) ||
            (certVal === 'retest' && !t.is_certified);

        return matchesSearch && matchesDate && matchesCert;
    });

    // 2. Sort trainees according to selected leaderboard metric
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
            // Official Leaderboard: highest score first, tie-breaker: fastest time
            if (scoreB !== scoreA) return scoreB - scoreA;
            return timeA - timeB;
        } else if (sortVal === 'bottom_to_top') {
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

    lastFilteredDirectoryTrainees = filtered;

    const matchEl = document.getElementById('directoryMatchCount');
    if (matchEl) matchEl.innerText = filtered.length;

    renderDirectoryTablePage();
}

/**
 * Render the current slice of 10 items for the directory leaderboard table.
 */
function renderDirectoryTablePage() {
    const tbody = document.getElementById('traineeTableBody');
    if (!tbody) return;

    const totalItems = lastFilteredDirectoryTrainees.length;
    const totalPages = Math.max(1, Math.ceil(totalItems / directoryPageSize));

    if (directoryCurrentPage > totalPages) directoryCurrentPage = totalPages;
    if (directoryCurrentPage < 1) directoryCurrentPage = 1;

    if (totalItems === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="p-8 text-center text-slate-500 font-mono">
                    No matching trainees found for the selected filters.
                </td>
            </tr>
        `;
        renderDirectoryPagination(0, 0, 0, 1);
        return;
    }

    const startIndex = (directoryCurrentPage - 1) * directoryPageSize;
    const endIndex = Math.min(startIndex + directoryPageSize, totalItems);
    const pageItems = lastFilteredDirectoryTrainees.slice(startIndex, endIndex);

    const sortVal = document.getElementById('directorySortFilter')?.value || 'top_to_bottom';
    const profileBaseUrl = window.traineeBaseUrl || (window.location.pathname.includes('/public') ? window.location.pathname.substring(0, window.location.pathname.indexOf('/public') + 7) + '/trainee/' : '/trainee/');

    tbody.innerHTML = pageItems.map((t, pageIdx) => {
        // True global leaderboard rank across all pagination pages
        const globalRank = startIndex + pageIdx;
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

        // Medals for Top 3 performers in top_to_bottom mode
        let rankBadge = '';
        if (sortVal === 'top_to_bottom') {
            if (globalRank === 0) {
                rankBadge = `<span class="w-6 h-6 rounded-lg medal-shimmer-gold text-amber-200 border text-[10px] font-black font-mono inline-flex items-center justify-center shrink-0 shadow-md shadow-amber-400/25" title="Rank 1 - Top Performer">#1</span>`;
            } else if (globalRank === 1) {
                rankBadge = `<span class="w-6 h-6 rounded-lg medal-shimmer-silver text-slate-100 border text-[10px] font-black font-mono inline-flex items-center justify-center shrink-0 shadow-md shadow-slate-300/25" title="Rank 2">#2</span>`;
            } else if (globalRank === 2) {
                rankBadge = `<span class="w-6 h-6 rounded-lg medal-shimmer-bronze text-amber-200 border text-[10px] font-black font-mono inline-flex items-center justify-center shrink-0 shadow-md shadow-orange-500/25" title="Rank 3">#3</span>`;
            } else {
                rankBadge = `<span class="w-6 h-6 rounded-lg glass-card text-[10px] text-slate-400 font-mono inline-flex items-center justify-center shrink-0">#${globalRank + 1}</span>`;
            }
        } else {
            rankBadge = `<span class="w-6 h-6 rounded-lg glass-card text-[10px] text-slate-400 font-mono inline-flex items-center justify-center shrink-0">#${globalRank + 1}</span>`;
        }

        const profileLink = `${profileBaseUrl}${t.db_id || t.id}`;

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
                    <a href="${profileLink}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500/15 hover:bg-amber-400 text-amber-300 hover:text-neutral-950 font-mono font-bold text-xs border border-amber-500/30 transition shadow-sm hover:shadow-amber-500/20 cursor-pointer" title="View ${t.id}'s Personal Profile & Zone Breakdown">
                        <span>Profile</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </td>
            </tr>
        `;
    }).join('');

    renderDirectoryPagination(totalItems, startIndex + 1, endIndex, totalPages);
}

/**
 * Render pagination controls, active page pills, and button states.
 */
function renderDirectoryPagination(totalItems, displayStart, displayEnd, totalPages) {
    const rangeEl = document.getElementById('directoryPageRange');
    const totalEl = document.getElementById('directoryTotalCount');
    const currentEl = document.getElementById('directoryCurrentPageNum');
    const pagesEl = document.getElementById('directoryTotalPagesNum');
    const btnPrev = document.getElementById('btnDirectoryPrev');
    const btnNext = document.getElementById('btnDirectoryNext');
    const pageNumbersContainer = document.getElementById('directoryPageNumbers');

    if (totalItems === 0) {
        if (rangeEl) rangeEl.innerText = '0 – 0';
        if (totalEl) totalEl.innerText = '0';
        if (currentEl) currentEl.innerText = '1';
        if (pagesEl) pagesEl.innerText = '1';
        if (btnPrev) btnPrev.disabled = true;
        if (btnNext) btnNext.disabled = true;
        if (pageNumbersContainer) pageNumbersContainer.innerHTML = '';
        return;
    }

    if (rangeEl) rangeEl.innerText = `${displayStart} – ${displayEnd}`;
    if (totalEl) totalEl.innerText = totalItems;
    if (currentEl) currentEl.innerText = directoryCurrentPage;
    if (pagesEl) pagesEl.innerText = totalPages;

    if (btnPrev) btnPrev.disabled = directoryCurrentPage <= 1;
    if (btnNext) btnNext.disabled = directoryCurrentPage >= totalPages;

    if (!pageNumbersContainer) return;

    let html = '';
    const maxVisibleButtons = 5;
    let startPage = Math.max(1, directoryCurrentPage - Math.floor(maxVisibleButtons / 2));
    let endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);

    if (endPage - startPage + 1 < maxVisibleButtons) {
        startPage = Math.max(1, endPage - maxVisibleButtons + 1);
    }

    if (startPage > 1) {
        html += `<button type="button" onclick="goToDirectoryPage(1)" class="w-8 h-8 rounded-xl border border-white/10 bg-white/[0.04] text-slate-300 hover:text-white hover:bg-white/[0.08] transition font-bold font-mono text-xs cursor-pointer flex items-center justify-center">1</button>`;
        if (startPage > 2) {
            html += `<span class="px-1 text-slate-500 font-mono text-xs select-none">...</span>`;
        }
    }

    for (let p = startPage; p <= endPage; p++) {
        if (p === directoryCurrentPage) {
            html += `<button type="button" class="w-8 h-8 rounded-xl border border-amber-500/50 bg-amber-500 text-neutral-950 shadow-md shadow-amber-500/20 font-black font-mono text-xs cursor-default flex items-center justify-center">${p}</button>`;
        } else {
            html += `<button type="button" onclick="goToDirectoryPage(${p})" class="w-8 h-8 rounded-xl border border-white/10 bg-white/[0.04] text-slate-300 hover:text-white hover:bg-white/[0.08] transition font-bold font-mono text-xs cursor-pointer flex items-center justify-center">${p}</button>`;
        }
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<span class="px-1 text-slate-500 font-mono text-xs select-none">...</span>`;
        }
        html += `<button type="button" onclick="goToDirectoryPage(${totalPages})" class="w-8 h-8 rounded-xl border border-white/10 bg-white/[0.04] text-slate-300 hover:text-white hover:bg-white/[0.08] transition font-bold font-mono text-xs cursor-pointer flex items-center justify-center">${totalPages}</button>`;
    }

    pageNumbersContainer.innerHTML = html;
}

// Bind pagination utilities to window for inline onclick handlers
window.applyDirectoryFilters = applyDirectoryFilters;
window.changeDirectoryPage = changeDirectoryPage;
window.goToDirectoryPage = goToDirectoryPage;

// ============================================================================
// SECTION 4: TRAINEE DOSSIER & PROFILE INSPECTION
// ============================================================================

/**
 * Load trainee inspection dossier, calculate checkpoint recognition, and render UI stats.
 * @param {string} id - Username
 */
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

/**
 * Render the 4 most recent drill sessions in the Overview page sidebar.
 */
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

// ============================================================================
// SECTION 5: LIVE VR TELEMETRY & WEBRTC MQDH MONITOR
// ============================================================================

/**
 * Start 100ms increment timer for live session duration tracking.
 */
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

/**
 * Render real-time click event feed (Hazard Hits and Distraction Misclicks).
 */
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

/**
 * Clear the live click events log.
 */
function clearEventFeed() {
    clickEventFeed.length = 0;
    hazardHitsCount = 0;
    misclicksCount = 0;
    renderClickEventFeed();
}

/**
 * Switch warehouse sector camera label in HUD.
 * @param {string} z - Zone name
 */
function changeWarehouseZone(z) {
    const el = document.getElementById('hudWarehouseZoneLabel');
    if (el) el.innerText = z;
    showToast('Zone Switched', z, '📍');
}

/**
 * Toggle entire web page fullscreen mode with persistence.
 */
function togglePageFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen()
            .then(() => {
                sessionStorage.setItem('dashboard_fullscreen_active', 'true');
            })
            .catch((err) => {
                console.warn('Fullscreen request failed:', err);
            });
    } else {
        sessionStorage.removeItem('dashboard_fullscreen_active');
        if (document.exitFullscreen) {
            document.exitFullscreen().catch((err) => {
                console.warn('Exit fullscreen failed:', err);
            });
        }
    }
}

/**
 * Toggle browser fullscreen mode (alias).
 */
function toggleDisplay2Fullscreen() {
    togglePageFullscreen();
}

window.togglePageFullscreen = togglePageFullscreen;
window.toggleDisplay2Fullscreen = toggleDisplay2Fullscreen;

// ============================================================================
// SECTION 6: PDF CERTIFICATE EXPORT GENERATOR
// ============================================================================

/**
 * Open print-ready formatted VR Safety Certificate in a dedicated browser tab.
 */
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

// ============================================================================
// SECTION 7: DYNAMIC KPI METRIC COUNTERS & SVG GAUGE RINGS
// ============================================================================

/**
 * Animate a numeric text element from start to end with an ease-out curve.
 */
function animateNumberCounter(el, start, end, duration = 1000, decimals = 0, suffix = '') {
    if (!el) return;
    const startTime = performance.now();
    const diff = end - start;

    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const ease = 1 - Math.pow(1 - progress, 3); // Smooth easeOutCubic curve
        const currentVal = start + (diff * ease);
        el.innerText = (decimals > 0 ? currentVal.toFixed(decimals) : Math.round(currentVal)) + suffix;

        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        } else {
            el.innerText = (decimals > 0 ? end.toFixed(decimals) : Math.round(end)) + suffix;
        }
    }
    requestAnimationFrame(updateCounter);
}

/**
 * Auto-discover and trigger all elements with data-counter="true".
 */
function initDashboardMetricCounters() {
    document.querySelectorAll('[data-counter="true"]').forEach(el => {
        const target = parseFloat(el.getAttribute('data-target') || '0');
        const decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
        const suffix = el.getAttribute('data-suffix') || '';
        const duration = parseInt(el.getAttribute('data-duration') || '1100', 10);
        animateNumberCounter(el, 0, target, duration, decimals, suffix);
    });
}

/**
 * Animate circular SVG stroke offsets smoothly on page load.
 */
function initSvgScoreRings() {
    document.querySelectorAll('.svg-score-ring').forEach(ring => {
        const targetOffset = ring.getAttribute('data-target-offset');
        if (targetOffset !== null) {
            ring.style.strokeDashoffset = '100';
            setTimeout(() => {
                ring.style.strokeDashoffset = targetOffset;
            }, 80);
        }
    });
}

// ============================================================================
// SECTION 8: DOM LIFECYCLE INITIALIZATION
// ============================================================================

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Animated KPI Counters & Circular Gauges
    initDashboardMetricCounters();
    initSvgScoreRings();

    // 2. Initialize Overview Page Components
    if (document.getElementById('incomingRunsContainer')) {
        renderIncomingRunsLog();
    }

    // 3. Initialize Directory Page Table & Filters
    if (document.getElementById('traineeTableBody')) {
        applyDirectoryFilters();
    }
});

// Helper function to update fullscreen header button UI state
function syncFullscreenUiState() {
    const isFs = Boolean(document.fullscreenElement);
    const textEl = document.getElementById('headerFullscreenText');
    const expandIcon = document.getElementById('headerFullscreenIconExpand');
    const exitIcon = document.getElementById('headerFullscreenIconExit');
    const btn = document.getElementById('btnHeaderFullscreen');

    if (textEl) {
        textEl.innerText = isFs ? 'Exit Full' : 'Fullscreen';
    }
    if (expandIcon) {
        expandIcon.classList.toggle('hidden', isFs);
    }
    if (exitIcon) {
        exitIcon.classList.toggle('hidden', !isFs);
    }
    if (btn) {
        btn.setAttribute('title', isFs ? 'Exit Fullscreen (Esc)' : 'Toggle Fullscreen Mode');
        if (isFs) {
            btn.classList.add('border-amber-500/40', 'bg-amber-500/10', 'text-amber-300');
        } else {
            btn.classList.remove('border-amber-500/40', 'bg-amber-500/10', 'text-amber-300');
        }
    }
}

// Synchronize header fullscreen button with browser fullscreen status
document.addEventListener('fullscreenchange', () => {
    const isFs = Boolean(document.fullscreenElement);
    if (isFs) {
        sessionStorage.setItem('dashboard_fullscreen_active', 'true');
    } else {
        sessionStorage.removeItem('dashboard_fullscreen_active');
    }
    syncFullscreenUiState();
});

// ============================================================================
// SECTION 9: SEAMLESS IN-PAGE NAVIGATION (PRESERVES FULLSCREEN ACROSS SECTIONS)
// ============================================================================

let isPjaxNavigating = false;

/**
 * Navigate to an internal page dynamically without full reload,
 * preserving HTML5 Fullscreen state and active stream connections.
 */
async function navigateToPage(url, pushToHistory = true) {
    if (isPjaxNavigating) return;

    const targetUrl = new URL(url, window.location.origin);
    const currentUrl = new URL(window.location.href);

    // If already on the same URL path & query, skip
    if (targetUrl.pathname === currentUrl.pathname && targetUrl.search === currentUrl.search) {
        return;
    }

    isPjaxNavigating = true;
    const contentContainer = document.getElementById('dashboard-main-content');

    if (contentContainer) {
        contentContainer.style.opacity = '0.35';
        contentContainer.style.pointerEvents = 'none';
    }

    try {
        const response = await fetch(targetUrl.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        // 1. Swap main content
        const newContent = doc.getElementById('dashboard-main-content');
        if (newContent && contentContainer) {
            contentContainer.innerHTML = newContent.innerHTML;

            // Execute scripts inside the new content (like window.dbTrainees = ...)
            const scripts = contentContainer.querySelectorAll('script');
            scripts.forEach(oldScript => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });
        }

        // 2. Update Document Title
        if (doc.title) {
            document.title = doc.title;
        }

        // 3. Update Header Nav Active Classes
        const newTopNav = doc.getElementById('topbar-nav');
        const currentTopNav = document.getElementById('topbar-nav');
        if (newTopNav && currentTopNav) {
            currentTopNav.innerHTML = newTopNav.innerHTML;
        }

        // 4. Update Footer Nav Active Classes
        const newFooterNav = doc.getElementById('footer-nav');
        const currentFooterNav = document.getElementById('footer-nav');
        if (newFooterNav && currentFooterNav) {
            currentFooterNav.innerHTML = newFooterNav.innerHTML;
        }

        // 5. Update History
        if (pushToHistory) {
            window.history.pushState({ url: targetUrl.href }, '', targetUrl.href);
        }

        // 6. Run Page Initializers for the new section
        initDashboardMetricCounters();
        initSvgScoreRings();

        if (document.getElementById('traineeTableBody')) {
            applyDirectoryFilters();
        }

        if (document.getElementById('incomingRunsContainer')) {
            renderIncomingRunsLog();
        }

        // Smooth scroll to top of view
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Ensure Fullscreen Button State stays synchronized
        syncFullscreenUiState();

    } catch (err) {
        console.warn('[PJAX] Navigation fetch failed, falling back to full navigation:', err);
        window.location.href = targetUrl.href;
    } finally {
        if (contentContainer) {
            contentContainer.style.opacity = '1';
            contentContainer.style.pointerEvents = '';
        }
        isPjaxNavigating = false;
    }
}

// Global click interceptor for seamless fullscreen-preserving navigation
document.addEventListener('click', (e) => {
    const anchor = e.target.closest('a');
    if (!anchor) return;

    // Ignore special modifier clicks
    if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0) return;

    // Ignore external or new-tab targets
    if (anchor.target === '_blank' || anchor.hasAttribute('download')) return;
    if (anchor.getAttribute('rel')?.includes('external')) return;

    // Ignore explicit exclusions or login / logout
    if (anchor.hasAttribute('data-no-pjax') || anchor.getAttribute('href')?.includes('/login')) return;

    const href = anchor.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

    // Must be same origin
    const url = new URL(href, window.location.href);
    if (url.origin !== window.location.origin) return;

    // Check if it's an internal app route
    const validPaths = ['/overview', '/dashboard', '/monitor', '/directory', '/analytics', '/guide', '/trainee'];
    const isAppRoute = validPaths.some(p => url.pathname.includes(p));
    if (!isAppRoute) return;

    e.preventDefault();
    navigateToPage(url.href, true);
});

// Handle browser Back / Forward buttons without exiting fullscreen
window.addEventListener('popstate', () => {
    navigateToPage(window.location.href, false);
});
