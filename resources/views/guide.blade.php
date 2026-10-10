@extends('layouts.app')

@section('content')
<div class="space-y-8">

    <!-- 1. Hero Introduction Card -->
    <div class="glass-sheet p-6 sm:p-8 rounded-3xl relative overflow-hidden border-white/10 shadow-2xl">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-72 h-72 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="space-y-3 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/15 border border-amber-500/30 text-amber-300 text-xs font-mono font-bold tracking-wider uppercase">
                    <span>📘</span> Standard Evaluation Protocol &amp; Manual
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-tight">
                    FindHazard VR Trainee Evaluation Guide
                </h1>
                <p class="text-sm text-slate-300 leading-relaxed font-sans">
                    The <strong class="text-amber-300 font-semibold">FindHazard VR Supervisor Command Dashboard</strong> is an automated telemetry evaluation platform designed for aviation cargo and airside logistics operations. It ingests live spatial inspection telemetry from VR headsets during immersive warehouse hazard spot-check drills, objectively quantifying trainee spatial awareness, hazard detection accuracy, false alarm trigger discipline, and operational completion velocity.
                </p>
            </div>

            <!-- Quick Action Links -->
            <div class="flex flex-wrap sm:flex-col gap-2.5 w-full sm:w-auto shrink-0">
                <a href="{{ route('dashboard.directory') }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-neutral-950 font-bold text-xs transition flex items-center justify-center gap-2 shadow-lg shadow-amber-500/20">
                    <span>👥</span><span>Directory Leaderboard</span>
                </a>
                <a href="{{ route('dashboard.analytics') }}" class="px-4 py-2 rounded-xl glass-card hover:bg-white/[0.08] text-slate-200 hover:text-white font-semibold text-xs transition flex items-center justify-center gap-2">
                    <span>📊</span><span>Sector Analytics</span>
                </a>
                <a href="{{ route('dashboard.monitor') }}" class="px-4 py-2 rounded-xl glass-card hover:bg-white/[0.08] text-slate-200 hover:text-white font-semibold text-xs transition flex items-center justify-center gap-2">
                    <span>🥽</span><span>Live VR Monitor</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. How Trainees Are Measured (Scoring Methodology & Telemetry) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-white/5">
            <div>
                <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-amber-400 block">Evaluation Mathematics</span>
                <h2 class="text-xl font-bold text-white tracking-tight">How Trainee Performance is Measured</h2>
            </div>
            <span class="text-xs text-slate-400 font-mono">4 Telemetry Pillars</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Pillar 1: Base Detection Score -->
            <div class="glass-sheet p-5 rounded-3xl border-white/5 space-y-3 flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-lg text-amber-400 font-bold">
                        1
                    </div>
                    <h3 class="text-sm font-bold text-white">13 Core Safety Hazards</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        The virtual warehouse environment features exactly <strong class="text-amber-300">13 industrial hazards</strong> distributed across 6 distinct functional zones. Each recognized hazard contributes:
                    </p>
                </div>
                <div class="p-3 rounded-2xl bg-white/[0.03] border border-white/5 font-mono text-center">
                    <span class="text-lg font-black text-amber-400">+7.69%</span>
                    <span class="block text-[10px] text-slate-400">per identified threat (100 / 13)</span>
                </div>
            </div>

            <!-- Pillar 2: Trigger Discipline Penalty -->
            <div class="glass-sheet p-5 rounded-3xl border-white/5 space-y-3 flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-2xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-lg text-rose-400 font-bold">
                        2
                    </div>
                    <h3 class="text-sm font-bold text-white">Trigger Discipline Penalty</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        To deter guessing and spam-clicking safe objects (distractors, normal tools, clean pallets), every erroneous click penalizes the overall grade:
                    </p>
                </div>
                <div class="p-3 rounded-2xl bg-white/[0.03] border border-white/5 font-mono text-center">
                    <span class="text-lg font-black text-rose-400">-5.0%</span>
                    <span class="block text-[10px] text-slate-400">per misclick false alarm</span>
                </div>
            </div>

            <!-- Pillar 3: Clamped Score Calculation -->
            <div class="glass-sheet p-5 rounded-3xl border-white/5 space-y-3 flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-lg text-emerald-400 font-bold">
                        3
                    </div>
                    <h3 class="text-sm font-bold text-white">Final Clamped Score</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        The final percentage is computed and securely bounded between 0.0% and 100.0%:
                    </p>
                </div>
                <div class="p-2.5 rounded-2xl bg-white/[0.03] border border-white/5 font-mono text-center">
                    <span class="text-xs font-bold text-emerald-300">Base Score &minus; Penalty</span>
                    <span class="block text-[9.5px] text-slate-400">clamped: max(0.0, min(100.0))</span>
                </div>
            </div>

            <!-- Pillar 4: Time Efficiency & Pace -->
            <div class="glass-sheet p-5 rounded-3xl border-white/5 space-y-3 flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="w-10 h-10 rounded-2xl bg-sky-500/15 border border-sky-500/30 flex items-center justify-center text-lg text-sky-400 font-bold">
                        4
                    </div>
                    <h3 class="text-sm font-bold text-white">Time &amp; Scan Pace</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Continuous timer tracks traversal from entry to egress. Used as a secondary tie-breaker on leaderboards when percentage scores match:
                    </p>
                </div>
                <div class="p-3 rounded-2xl bg-white/[0.03] border border-white/5 font-mono text-center">
                    <span class="text-lg font-black text-sky-400">Fastest Sweep</span>
                    <span class="block text-[10px] text-slate-400">ranks higher at equal score</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Performance Classification & Evaluation Tiers -->
    <div class="space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-white/5">
            <div>
                <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-amber-400 block">Competency Standards</span>
                <h2 class="text-xl font-bold text-white tracking-tight">The 4 Evaluation Tiers</h2>
            </div>
            <span class="text-xs text-slate-400 font-mono">Performance Tiers</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Tier 1: Master Inspector (Locked In) -->
            <div class="glass-sheet p-5 rounded-3xl border-emerald-500/20 relative overflow-hidden flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                            &ge; 90.0%
                        </span>
                        <span class="text-xs font-mono text-emerald-400 font-bold">Tier 1</span>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white">Master Inspector</h3>
                        <span class="text-xs font-mono font-bold text-emerald-400">&ldquo;Locked In&rdquo;</span>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Demonstrates instantaneous spatial hazard recognition and flawless trigger discipline with 0–1 false clicks. Spots 12 to 13 hazards rapidly.
                    </p>
                    <div class="space-y-1 text-xs font-mono text-slate-400 border-t border-white/5 pt-2">
                        <div>&bull; Certificate: <strong class="text-emerald-300 font-bold">Qualified ★</strong></div>
                        <div>&bull; Deployment: <span class="text-slate-200">Autonomous Airside Duty</span></div>
                    </div>
                </div>

                <div class="p-2.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center font-mono">
                    <span class="text-xs text-emerald-300 font-bold">{{ $tierStats['locked_in']['count'] }} Trainees</span>
                    <span class="text-[10px] text-slate-400 block">({{ $tierStats['locked_in']['rate'] }}% of cohort)</span>
                </div>
            </div>

            <!-- Tier 2: Proficient Inspector (Great) -->
            <div class="glass-sheet p-5 rounded-3xl border-sky-500/20 relative overflow-hidden flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-sky-500/15 text-sky-300 border border-sky-500/30">
                            80.0% – 89.9%
                        </span>
                        <span class="text-xs font-mono text-sky-400 font-bold">Tier 2</span>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white">Proficient Inspector</h3>
                        <span class="text-xs font-mono font-bold text-sky-400">&ldquo;Great&rdquo;</span>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Meets the comprehensive safety benchmark. Identified at least 11 hazards with controlled trigger discipline. Eligible for official credentials.
                    </p>
                    <div class="space-y-1 text-xs font-mono text-slate-400 border-t border-white/5 pt-2">
                        <div>&bull; Certificate: <strong class="text-sky-300 font-bold">Qualified ✓</strong></div>
                        <div>&bull; Deployment: <span class="text-slate-200">Standard Warehouse Duty</span></div>
                    </div>
                </div>

                <div class="p-2.5 rounded-2xl bg-sky-500/10 border border-sky-500/20 text-center font-mono">
                    <span class="text-xs text-sky-300 font-bold">{{ $tierStats['great']['count'] }} Trainees</span>
                    <span class="text-[10px] text-slate-400 block">({{ $tierStats['great']['rate'] }}% of cohort)</span>
                </div>
            </div>

            <!-- Tier 3: Developing Inspector (Valid Effort) -->
            <div class="glass-sheet p-5 rounded-3xl border-amber-500/20 relative overflow-hidden flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                            60.0% – 79.9%
                        </span>
                        <span class="text-xs font-mono font-bold text-amber-400">Tier 3</span>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white">Developing Inspector</h3>
                        <span class="text-xs font-mono font-bold text-amber-400">&ldquo;Valid Effort&rdquo;</span>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Understands general hazard concepts, but missed key high-risk threats or suffered score degradation through false click deductions.
                    </p>
                    <div class="space-y-1 text-xs font-mono text-slate-400 border-t border-white/5 pt-2">
                        <div>&bull; Certificate: <strong class="text-amber-400 font-bold">Ineligible (&lt;80%)</strong></div>
                        <div>&bull; Deployment: <span class="text-slate-200">Refresher Drill Required</span></div>
                    </div>
                </div>

                <div class="p-2.5 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-center font-mono">
                    <span class="text-xs text-amber-300 font-bold">{{ $tierStats['valid_effort']['count'] }} Trainees</span>
                    <span class="text-[10px] text-slate-400 block">({{ $tierStats['valid_effort']['rate'] }}% of cohort)</span>
                </div>
            </div>

            <!-- Tier 4: Critical Retest (Needs Review) -->
            <div class="glass-sheet p-5 rounded-3xl border-rose-500/20 relative overflow-hidden flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                            &lt; 60.0%
                        </span>
                        <span class="text-xs font-mono text-rose-400 font-bold">Tier 4</span>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white">Critical Retest</h3>
                        <span class="text-xs font-mono font-bold text-rose-400">&ldquo;Needs Review&rdquo;</span>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Critical hazard oversights or erratic false triggering. Poses workplace safety vulnerability. Mandatory supervised retraining in simulator.
                    </p>
                    <div class="space-y-1 text-xs font-mono text-slate-400 border-t border-white/5 pt-2">
                        <div>&bull; Certificate: <strong class="text-rose-400 font-bold">Locked ✕</strong></div>
                        <div>&bull; Deployment: <span class="text-slate-200">Restricted from Airside</span></div>
                    </div>
                </div>

                <div class="p-2.5 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-center font-mono">
                    <span class="text-xs text-rose-300 font-bold">{{ $tierStats['needs_review']['count'] }} Trainees</span>
                    <span class="text-[10px] text-slate-400 block">({{ $tierStats['needs_review']['rate'] }}% of cohort)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Certificate Pass Rate & Qualification Benchmark -->
    <div class="glass-sheet p-6 sm:p-8 rounded-3xl border-amber-500/20 relative overflow-hidden space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-white/10">
            <div>
                <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-amber-400 block">Credentialing Policy</span>
                <h2 class="text-xl font-black text-white tracking-tight mt-0.5">VR Safety Certificate Qualification Standard</h2>
            </div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-2xl bg-amber-500/15 text-amber-300 border border-amber-500/30 font-mono text-xs font-bold">
                <span>📜</span> Minimum Qualifying Score: 80.0%
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
            <!-- Left: Pass Rate Breakdown Bar -->
            <div class="lg:col-span-7 space-y-4">
                <p class="text-sm text-slate-300 leading-relaxed">
                    Candidates who achieve a final score of <strong class="text-white font-bold">&ge; 80.0%</strong> qualify for the official <strong class="text-amber-300 font-semibold">VR Safety Certificate of Competency</strong>. This credential validates that the trainee has demonstrated high spatial vigilance and accurate discrimination between hazardous conditions and non-hazardous distractor items.
                </p>

                <!-- Visual Gradient Distribution Bar -->
                <div class="space-y-2 pt-2">
                    <div class="flex justify-between items-center text-xs font-mono">
                        <span class="text-emerald-400 font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            Qualified: {{ $certifiedCount }} ({{ $passRate }}%)
                        </span>
                        <span class="text-rose-400 font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                            Retest: {{ $retestCount }} ({{ $totalSessions > 0 ? round(($retestCount / $totalSessions) * 100, 1) : 0 }}%)
                        </span>
                    </div>

                    <div class="w-full h-3 rounded-full bg-white/10 overflow-hidden flex">
                        <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-700" style="width: {{ $passRate }}%;"></div>
                        <div class="h-full bg-gradient-to-r from-rose-500 to-red-600 transition-all duration-700" style="width: {{ $totalSessions > 0 ? 100 - $passRate : 100 }}%;"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs font-mono pt-2">
                    <div class="p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20">
                        <span class="text-emerald-400 font-bold block">✓ Score &ge; 80.0%</span>
                        <span class="text-slate-300 text-[11px]">Instant certificate generation, printable credential badge, airside deployment eligible.</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-rose-500/10 border border-rose-500/20">
                        <span class="text-rose-400 font-bold block">✕ Score &lt; 80.0%</span>
                        <span class="text-slate-300 text-[11px]">Certificate locked, mandatory retest flagged on supervisor dashboard.</span>
                    </div>
                </div>
            </div>

            <!-- Right: Certificate Card Preview -->
            <div class="lg:col-span-5 glass-card p-5 rounded-2xl border-amber-500/30 text-center relative space-y-3 bg-gradient-to-b from-white/[0.04] to-transparent">
                <span class="w-10 h-10 rounded-2xl bg-amber-400/20 text-amber-300 inline-flex items-center justify-center text-xl shadow-lg shadow-amber-400/20">
                    📜
                </span>
                <div>
                    <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">VR Safety Certificate</h3>
                    <p class="text-[11px] text-slate-400 font-mono">Industrial Hazard Detection Protocol</p>
                </div>
                <div class="p-3 rounded-xl bg-white/[0.02] border border-white/5 text-left text-xs space-y-1 font-mono text-slate-300">
                    <div>&bull; Trainee Verified Identity</div>
                    <div>&bull; Detection Coverage &ge; 80.0%</div>
                    <div>&bull; Supervisor Authorization</div>
                    <div>&bull; Serialized Verification ID</div>
                </div>
                <p class="text-[10px] text-amber-400/90 font-mono">
                    Supervisors can view, audit, and print certificates via trainee profile screens.
                </p>
            </div>
        </div>
    </div>

    <!-- 5. The 6 Warehouse Inspection Sectors & The 13 Hazards -->
    <div class="space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-white/5">
            <div>
                <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-amber-400 block">Spatial Breakdown</span>
                <h2 class="text-xl font-bold text-white tracking-tight">The 6 Warehouse Sectors &amp; 13 Hazards</h2>
            </div>
            <span class="text-xs text-slate-400 font-mono">Airside Logistics Facility</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($warehouseAreas as $key => $area)
                <div class="glass-sheet p-5 rounded-3xl border-white/5 space-y-3.5 flex flex-col justify-between">
                    <div class="space-y-2.5">
                        <div class="flex justify-between items-center">
                            <span class="text-2xl">{{ $area['icon'] }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-500/10 text-amber-300 border border-amber-500/25">
                                {{ count($area['hazards']) }} {{ count($area['hazards']) === 1 ? 'Hazard' : 'Hazards' }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-white">{{ $area['name'] }}</h3>
                            <span class="text-[10px] text-slate-400 font-mono block">{{ $area['tag'] }}</span>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            {{ $area['description'] }}
                        </p>
                    </div>

                    <div class="space-y-2 border-t border-white/5 pt-3">
                        <span class="text-[10px] font-mono font-bold uppercase text-slate-400 block tracking-wider">Assessed Threats:</span>
                        <div class="space-y-1.5">
                            @foreach($area['hazards'] as $h)
                                <div class="p-2 rounded-xl bg-white/[0.03] border border-white/5 flex items-center justify-between text-xs">
                                    <span class="text-slate-200 font-medium text-[11px]">{{ $h['id'] }}. {{ $h['name'] }}</span>
                                    <span class="px-2 py-0.5 rounded-lg text-[9.5px] font-mono font-bold bg-amber-400/10 text-amber-300 shrink-0">
                                        {{ $h['severity'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 6. Automated Supervisor Performance Statement System -->
    <div class="glass-sheet p-6 sm:p-8 rounded-3xl border-white/10 relative overflow-hidden space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-3 border-b border-white/5">
            <div>
                <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-amber-400 block">Algorithmic Telemetry Synthesis</span>
                <h2 class="text-lg font-black text-white tracking-tight">Automated Performance Evaluation System</h2>
            </div>
            <span class="text-xs font-mono text-emerald-400">Real-Time Commentary</span>
        </div>

        <p class="text-sm text-slate-300 leading-relaxed">
            When a supervisor inspects any trainee profile (accessible by clicking <strong class="text-amber-300 font-mono">Profile &rarr;</strong> in the Directory or Overview tables), the dashboard synthesizes a targeted narrative assessment covering:
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
            <div class="p-3.5 rounded-2xl glass-card border-white/5 space-y-1">
                <span class="text-[11px] uppercase font-bold text-emerald-400 tracking-wider flex items-center gap-1.5 font-mono">
                    <span>🌟</span> Perfect Mastery Areas
                </span>
                <p class="text-xs text-slate-300">
                    Highlights operational sectors where the trainee successfully detected 100% of present hazards.
                </p>
            </div>

            <div class="p-3.5 rounded-2xl glass-card border-white/5 space-y-1">
                <span class="text-[11px] uppercase font-bold text-amber-400 tracking-wider flex items-center gap-1.5 font-mono">
                    <span>⚠️</span> Areas for Improvement
                </span>
                <p class="text-xs text-slate-300">
                    Identifies specific sectors where critical hazards were bypassed, indicating spatial inspection blindspots.
                </p>
            </div>

            <div class="p-3.5 rounded-2xl glass-card border-white/5 space-y-1">
                <span class="text-[11px] uppercase font-bold text-sky-400 tracking-wider flex items-center gap-1.5 font-mono">
                    <span>🎯</span> Trigger Discipline &amp; Time
                </span>
                <p class="text-xs text-slate-300">
                    Evaluates false clicks against inspection duration, diagnosing whether errors arose from rushing or uncertainty.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
