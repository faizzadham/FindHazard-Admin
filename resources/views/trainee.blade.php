@extends('layouts.app')

@section('content')
<script>
    window.traineeRecordData = {
        id: @json($record->username),
        score: @json($record->score),
        found: @json($record->hazards_found),
        missed: @json($record->hazards_missed),
        wrong_clicks: @json($record->wrong_clicks),
        is_certified: @json((bool)$record->is_certified),
        time: @json(floor($record->completion_time / 60) . 'm ' . sprintf('%02ds', round(fmod($record->completion_time, 60)))),
        date: @json($record->created_at->timezone('Asia/Kuala_Lumpur')->format('d M Y, h:i A'))
    };
</script>

<div class="space-y-6">
    <!-- 1. Breadcrumb Navigation & Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-3 border-b border-white/5">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.directory') }}" class="glass-card hover:bg-white/[0.06] text-amber-400 hover:text-amber-300 px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                &larr; Back to Directory
            </a>
            <div class="h-4 w-px bg-white/10 hidden sm:block"></div>
            <div class="text-xs text-slate-400 flex items-center gap-1.5 font-mono">
                <span>Directory</span>
                <span class="text-slate-600">/</span>
                <span class="text-white font-semibold">Trainee Profile</span>
                <span class="text-slate-600">/</span>
                <span class="text-amber-400 font-bold">{{ $record->username }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($record->is_certified)
                <button onclick="openCertificateModal()" class="px-4 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-400 text-neutral-950 font-black text-xs hover:from-amber-400 hover:to-amber-300 transition shadow-lg shadow-amber-500/25 flex items-center gap-1.5 cursor-pointer">
                    📜 View Certificate
                </button>
            @endif
            <button onclick="window.print()" class="glass-card px-3.5 py-1.5 rounded-xl text-xs text-slate-300 hover:text-white transition font-mono flex items-center gap-1.5 cursor-pointer">
                🖨️ Print Profile
            </button>
        </div>
    </div>

    <!-- 2. Hero Trainee Profile Header Card -->
    <div class="glass-sheet p-6 rounded-3xl relative overflow-hidden border-white/10 shadow-2xl">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-5 relative z-10">
            <!-- Trainee Identity -->
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-400/30 to-amber-600/20 border border-amber-400/40 flex items-center justify-center text-amber-300 font-black text-xl font-mono shadow-lg shadow-amber-500/10 shrink-0">
                    {{ strtoupper(substr($record->username, 0, 2)) }}
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl font-black text-white font-['JetBrains_Mono'] tracking-tight">{{ $record->username }}</h1>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">
                            🏆 Leaderboard Rank #{{ $rankNumber }} of {{ $totalTrainees }}
                        </span>
                        @if($record->is_certified)
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold font-mono bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 inline-flex items-center gap-1.5 shadow-sm shadow-emerald-500/10">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                ✓ Qualified
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold font-mono bg-rose-500/20 text-rose-300 border border-rose-500/40 inline-flex items-center gap-1.5 shadow-sm shadow-rose-500/10">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                ✕ Retest Required (&lt;80%)
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400 font-mono flex items-center gap-3">
                        <span>📅 {{ $record->created_at->timezone('Asia/Kuala_Lumpur')->format('d M Y, h:i A') }}</span>
                        <span>&bull;</span>
                        <span>VR Session Telemetry Record #{{ $record->id }}</span>
                    </p>
                </div>
            </div>

            <!-- Grade & Status Badge -->
            <div class="flex items-center gap-3 self-stretch md:self-auto justify-end">
                <div class="glass-card px-4 py-2.5 rounded-2xl text-right">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Evaluation Tier</span>
                    <span class="text-sm font-black font-mono {{ $statement['grade_badge_class'] }} px-2.5 py-0.5 rounded-lg inline-block mt-1">
                        {{ $statement['grade'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Key Personal Statistics (4-Card Metric Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Visual Score Ring & Final Score -->
        <div class="glass-sheet p-5 rounded-3xl border-white/5 flex items-center justify-between">
            <div>
                <span class="text-[11px] uppercase font-bold text-slate-400 tracking-wider">Final Score</span>
                <div class="text-3xl font-black mt-1 font-['JetBrains_Mono'] {{ $record->score >= 80 ? 'text-emerald-400' : ($record->score >= 60 ? 'text-amber-400' : 'text-rose-400') }}">
                    {{ number_format($record->score, 1) }}%
                </div>
                <span class="text-[11px] text-slate-500 mt-1 block">Pass Standard &ge; 80%</span>
            </div>
            <!-- Circular SVG Ring with Floored Integer Score -->
            <div class="relative w-14 h-14 shrink-0 flex items-center justify-center">
                <svg class="w-14 h-14 -rotate-90 transform" viewBox="0 0 36 36">
                    <path
                        class="text-white/10"
                        stroke="currentColor"
                        stroke-width="3.2"
                        fill="none"
                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                    @php
                        $scVal = max(0, min(100, $record->score));
                        $scOffset = 100 - $scVal;
                        $scColor = $scVal >= 90 ? 'text-emerald-400' : ($scVal >= 80 ? 'text-teal-400' : ($scVal >= 60 ? 'text-amber-400' : 'text-rose-400'));
                    @endphp
                    <path
                        class="{{ $scColor }} transition-all duration-700 ease-out"
                        stroke="currentColor"
                        stroke-width="3.5"
                        stroke-dasharray="100, 100"
                        stroke-dashoffset="{{ $scOffset }}"
                        stroke-linecap="round"
                        fill="none"
                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                </svg>
                <span class="absolute text-[12px] font-mono font-black text-slate-200">
                    {{ (int) floor($record->score) }}
                </span>
            </div>
        </div>

        <!-- Metric 2: Hazards Found -->
        <div class="glass-sheet p-5 rounded-3xl border-white/5">
            <span class="text-[11px] uppercase font-bold text-slate-400 tracking-wider">Hazards Identified</span>
            <div class="text-3xl font-black text-emerald-400 mt-1 font-['JetBrains_Mono']">
                {{ $record->hazards_found }} <span class="text-base text-slate-500 font-semibold">/ 13</span>
            </div>
            <span class="text-[11px] text-emerald-400/80 mt-1 block font-mono">
                {{ round(($record->hazards_found / 13) * 100, 1) }}% detection coverage
            </span>
        </div>

        <!-- Metric 3: Hazards Missed -->
        <div class="glass-sheet p-5 rounded-3xl border-white/5">
            <span class="text-[11px] uppercase font-bold text-slate-400 tracking-wider">Hazards Missed</span>
            <div class="text-3xl font-black {{ $record->hazards_missed > 0 ? 'text-rose-400' : 'text-emerald-400' }} mt-1 font-['JetBrains_Mono']">
                {{ $record->hazards_missed }} <span class="text-base text-slate-500 font-semibold">/ 13</span>
            </div>
            <span class="text-[11px] text-slate-500 mt-1 block">
                {{ $record->hazards_missed === 0 ? '✓ Zero hazards overlooked' : $record->hazards_missed . ' critical threats bypassed' }}
            </span>
        </div>

        <!-- Metric 4: Duration & Penalty -->
        <div class="glass-sheet p-5 rounded-3xl border-white/5">
            <span class="text-[11px] uppercase font-bold text-slate-400 tracking-wider">Scan Duration &amp; Clicks</span>
            <div class="text-3xl font-black text-sky-400 mt-1 font-['JetBrains_Mono']">
                {{ floor($record->completion_time / 60) }}m {{ sprintf('%02ds', round(fmod($record->completion_time, 60))) }}
            </div>
            <span class="text-[11px] mt-1 block font-mono {{ $record->wrong_clicks > 0 ? 'text-amber-400' : 'text-slate-500' }}">
                @if($record->wrong_clicks > 0)
                    {{ $record->wrong_clicks }} misclick(s) (-{{ $record->wrong_clicks * 5 }}% penalty)
                @else
                    0 false alarms (clean accuracy)
                @endif
            </span>
        </div>
    </div>

    <!-- 4. Supervisor Performance Evaluation & Statement System -->
    <div class="glass-sheet p-6 rounded-3xl border-amber-500/20 relative overflow-hidden shadow-2xl space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-3 border-b border-white/5">
            <div>
                <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-amber-400 block">Automated Performance Evaluation System</span>
                <h2 class="text-lg font-black text-white tracking-tight mt-0.5">{{ $statement['headline'] }}</h2>
            </div>
            <span class="px-3 py-1 rounded-xl text-xs font-mono font-bold {{ $statement['grade_badge_class'] }}">
                {{ $statement['grade'] }}
            </span>
        </div>

        <!-- Official Performance Narrative Statement -->
        <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 text-sm text-slate-200 leading-relaxed font-sans">
            {{ $statement['narrative'] }}
        </div>

        <!-- Key Highlights Chips -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
            <div class="p-3 rounded-2xl glass-card border-white/5 space-y-1">
                <span class="text-[10px] uppercase font-bold text-emerald-400 tracking-wider flex items-center gap-1.5">
                    <span>🌟</span> Perfect Mastery Areas
                </span>
                <p class="text-xs text-slate-300 font-mono">
                    {{ !empty($statement['perfect_areas']) ? implode(', ', $statement['perfect_areas']) : 'None' }}
                </p>
            </div>

            <div class="p-3 rounded-2xl glass-card border-white/5 space-y-1">
                <span class="text-[10px] uppercase font-bold text-amber-400 tracking-wider flex items-center gap-1.5">
                    <span>⚠️</span> Areas for Improvement
                </span>
                <p class="text-xs text-slate-300 font-mono">
                    {{ !empty($statement['deficient_areas']) ? implode(', ', $statement['deficient_areas']) : 'Flawless — All 6 zones fully mastered' }}
                </p>
            </div>

            <div class="p-3 rounded-2xl glass-card border-white/5 space-y-1">
                <span class="text-[10px] uppercase font-bold text-sky-400 tracking-wider flex items-center gap-1.5">
                    <span>🎯</span> Trigger Discipline &amp; Time
                </span>
                <p class="text-xs text-slate-300 font-mono">
                    {{ $statement['wrong_clicks_commentary'] }}
                </p>
            </div>
        </div>

        <!-- Official Certification Action Bar -->
        <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/10 flex flex-col sm:flex-row justify-between items-center gap-4 pt-4">
            <div class="space-y-0.5 text-center sm:text-left">
                <span class="text-xs font-bold text-white flex items-center gap-2 justify-center sm:justify-start">
                    @if($record->is_certified)
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span class="text-emerald-300">Safety Certificate Qualified</span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                        <span class="text-rose-300">Certificate Status: Ineligible (&lt;80.0% Required)</span>
                    @endif
                </span>
                <p class="text-[11px] text-slate-400">
                    @if($record->is_certified)
                        Official credential authorized for active airside &amp; warehouse handling operations.
                    @else
                        Trainee must retake the VR hazard identification drill to achieve certification eligibility.
                    @endif
                </p>
            </div>

            <!-- Certificate Button -->
            <div class="flex items-center gap-2 shrink-0">
                @if($record->is_certified)
                    <button onclick="openCertificateModal()" class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-300 text-neutral-950 font-black text-xs hover:from-amber-400 hover:to-amber-300 transition shadow-xl shadow-amber-500/25 flex items-center gap-2 cursor-pointer">
                        📜 View Official Certificate
                    </button>
                    <button onclick="exportPdfCertificate()" class="px-4 py-2.5 rounded-2xl glass-card text-amber-300 hover:text-white border-amber-500/30 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                        🖨️ Print Certificate PDF
                    </button>
                @else
                    <span class="px-4 py-2 rounded-2xl bg-rose-500/10 text-rose-400 border border-rose-500/30 text-xs font-bold font-mono">
                        🔒 Certificate Locked (&lt;80%)
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- 5. Individual Detection Breakdown Across All 6 Warehouse Areas -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 pb-2 border-b border-white/5">
            <div>
                <h3 class="text-base font-black text-white tracking-tight flex items-center gap-2">
                    <span>Individual Detection Breakdown Across All 6 Warehouse Areas</span>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">6 Zones &bull; 13 Hazards</span>
                </h3>
                <p class="text-xs text-slate-400">Detailed spatial hazard recognition telemetry recorded during {{ $record->username }}'s VR inspection drill.</p>
            </div>
            <span class="text-xs font-mono text-slate-400">
                Total Identified: <strong class="text-emerald-400">{{ $record->hazards_found }}/13</strong> ({{ round(($record->hazards_found / 13) * 100, 1) }}%)
            </span>
        </div>

        <!-- 6 Zone Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($areaBreakdown as $zoneKey => $zone)
                <div class="glass-sheet p-5 rounded-3xl border-white/5 space-y-4 flex flex-col justify-between hover:border-white/10 transition">
                    <!-- Zone Header -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="w-8 h-8 rounded-xl glass-card flex items-center justify-center text-base">
                                {{ $zone['icon'] }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-mono font-bold {{ $zone['status_badge_class'] }}">
                                {{ $zone['status_text'] }}
                            </span>
                        </div>

                        <div>
                            <h4 class="text-sm font-bold text-white tracking-tight">{{ $zone['name'] }}</h4>
                            <span class="text-[10px] uppercase font-mono tracking-wider text-slate-500">{{ $zone['tag'] }}</span>
                        </div>

                        <!-- Progress Bar & Rate -->
                        <div class="space-y-1">
                            <div class="flex justify-between items-center text-xs font-mono">
                                <span class="text-slate-400">Detected:</span>
                                <span class="font-bold {{ $zone['found_count'] === $zone['total_count'] ? 'text-emerald-400' : ($zone['rate'] >= 50 ? 'text-amber-400' : 'text-rose-400') }}">
                                    {{ $zone['found_count'] }} / {{ $zone['total_count'] }} ({{ $zone['rate'] }}%)
                                </span>
                            </div>
                            <div class="w-full bg-white/5 h-2 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $zone['found_count'] === $zone['total_count'] ? 'bg-emerald-400' : ($zone['rate'] >= 50 ? 'bg-amber-400' : 'bg-rose-500') }}"
                                     style="width: {{ $zone['rate'] }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Hazards Checklist for this Area -->
                    <div class="space-y-2 pt-2 border-t border-white/5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Hazard Checklist:</span>
                        <div class="space-y-1.5">
                            @foreach($zone['hazards'] as $h)
                                <div class="p-2 rounded-xl flex items-center justify-between gap-2 text-xs {{ $h['is_found'] ? 'bg-emerald-500/[0.04] border border-emerald-500/20 text-slate-200' : 'bg-rose-500/[0.04] border border-rose-500/20 text-slate-300' }}">
                                    <div class="space-y-0.5">
                                        <div class="font-medium text-xs leading-tight">{{ $h['name'] }}</div>
                                        <span class="text-[10px] text-slate-500 font-mono">{{ $h['severity'] }}</span>
                                    </div>

                                    @if($h['is_found'])
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shrink-0">
                                            ✓ Found
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30 shrink-0">
                                            ✕ Missed
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- 6. Official Certificate Modal for Qualified Trainees -->
@if($record->is_certified)
    <div id="certificateModal" class="fixed inset-0 z-50 bg-neutral-950/85 backdrop-blur-md flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300">
        <div class="relative w-full max-w-2xl bg-neutral-950 border-4 border-amber-500/50 rounded-3xl p-8 sm:p-10 shadow-2xl space-y-6 text-center text-white"
             style="background: radial-gradient(circle at center, rgba(30,34,50,0.95), rgba(11,13,20,0.99));">
            
            <!-- Close Button -->
            <button onclick="closeCertificateModal()" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center text-sm transition cursor-pointer">
                ✕
            </button>

            <!-- Certificate Header -->
            <div class="space-y-2">
                <span class="inline-block px-4 py-1 rounded-full text-[10px] font-mono font-bold uppercase tracking-widest bg-amber-500/15 text-amber-300 border border-amber-500/30">
                    VR Safety Compliance Verified
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-amber-400 uppercase tracking-widest font-['JetBrains_Mono']">
                    Certificate of Competency
                </h2>
                <p class="text-xs text-slate-400 uppercase tracking-wider">Aviation Cargo &amp; Warehouse Telemetry Inspection Protocol</p>
            </div>

            <!-- Recipient Name -->
            <div class="py-4 border-y border-white/10 space-y-1">
                <span class="text-xs text-slate-400 uppercase tracking-wider">This credential is proudly awarded to:</span>
                <div class="text-3xl sm:text-4xl font-black text-white tracking-tight font-mono text-amber-300">
                    {{ $record->username }}
                </div>
                <p class="text-xs text-emerald-400 font-semibold font-mono">
                    ★ QUALIFICATION APPROVED &bull; GRADE: {{ $statement['grade'] }}
                </p>
            </div>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3.5 rounded-2xl bg-white/[0.03] border border-white/5 font-mono text-xs">
                <div>
                    <span class="text-slate-500 block text-[10px]">FINAL SCORE</span>
                    <strong class="text-base text-emerald-400 font-black">{{ number_format($record->score, 1) }}%</strong>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px]">HAZARDS FOUND</span>
                    <strong class="text-base text-sky-400 font-black">{{ $record->hazards_found }} / 13</strong>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px]">WRONG CLICKS</span>
                    <strong class="text-base text-amber-400 font-black">{{ $record->wrong_clicks }}</strong>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px]">TIME VELOCITY</span>
                    <strong class="text-base text-slate-200 font-black">{{ floor($record->completion_time / 60) }}m {{ sprintf('%02ds', round(fmod($record->completion_time, 60))) }}</strong>
                </div>
            </div>

            <!-- Signatures & Verification -->
            <div class="pt-4 flex flex-col sm:flex-row justify-between items-center sm:items-end gap-4 text-xs text-slate-400 text-left border-t border-white/10">
                <div class="space-y-1 font-mono text-center sm:text-left">
                    <div>Issue Date: <strong class="text-white">{{ $record->created_at->timezone('Asia/Kuala_Lumpur')->format('d M Y') }}</strong></div>
                    <div>Protocol ID: <strong class="text-white">FH-VR-{{ strtoupper(substr(md5($record->id . $record->username), 0, 8)) }}-2026</strong></div>
                </div>

                <div class="text-center sm:text-right space-y-1">
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider block">Authorized Lead Supervisor</span>
                    <div class="font-bold text-base text-amber-300 font-mono">En. Faiz Adham</div>
                    <span class="text-[10px] text-slate-400 block">Chief Safety Lead &bull; FindHazard VR</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-center gap-3 pt-2">
                <button onclick="exportPdfCertificate()" class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-400 text-neutral-950 font-black text-xs hover:from-amber-400 hover:to-amber-300 transition shadow-lg shadow-amber-500/25 cursor-pointer">
                    🖨️ Print Certificate Document
                </button>
                <button onclick="closeCertificateModal()" class="px-5 py-2.5 rounded-2xl glass-card text-slate-300 hover:text-white text-xs font-bold transition cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        function openCertificateModal() {
            const m = document.getElementById('certificateModal');
            if (m) {
                m.classList.remove('opacity-0', 'pointer-events-none');
            }
        }

        function closeCertificateModal() {
            const m = document.getElementById('certificateModal');
            if (m) {
                m.classList.add('opacity-0', 'pointer-events-none');
            }
        }
    </script>
@endif
@endsection
