<section id="tab-trainee" class="tab-content space-y-5">
    <div class="glass-panel rounded-2xl p-5 space-y-4 border-amber-500/20">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-white/5">
            <div class="flex items-center gap-3.5">
                <div id="traineeAvatar" class="w-12 h-12 rounded-2xl glass-card border-amber-400/30 text-amber-400 font-black text-lg flex items-center justify-center">qq</div>
                <div>
                    <h3 id="traineeHeaderTitle" class="text-base font-bold text-white">qq</h3>
                    <p class="text-xs text-slate-400" id="traineeDateSpan">24 Sep 2026, 06:18</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span id="bannerTraineeRating" class="glass-card px-3 py-1 rounded-xl text-xs font-mono text-emerald-400 border-emerald-500/30">Locked In</span>
                <span id="traineeHeaderScore" class="glass-card px-3 py-1 rounded-xl text-xs font-mono text-amber-400 border-amber-500/30">100 / 100 Pts</span>
            </div>
        </div>

        <div class="space-y-2">
            <div class="flex justify-between text-xs">
                <span id="traineeSummaryRatio" class="text-slate-300 font-mono">10 / 10 Discovered</span>
                <span id="traineeMasteryPercent" class="text-emerald-400 font-bold font-mono">100%</span>
            </div>
            <div class="w-full bg-slate-900/80 h-3 rounded-full overflow-hidden p-0.5 border border-white/10">
                <div id="traineeProgressBar" class="bg-gradient-to-r from-amber-400 via-emerald-400 to-cyan-400 h-full rounded-full transition-all duration-500" style="width: 100%"></div>
            </div>
        </div>
    </div>

    <div class="glass-panel rounded-2xl p-5 space-y-3 border-white/5">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200">10 Checkpoint Inspection Timeline</h4>
        <div id="checkpointsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-2.5"></div>
    </div>

    <div class="glass-panel rounded-2xl p-5 space-y-3 border-white/5">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200">Supervisor Field Evaluation Notes</h4>
        <textarea id="instructorNotes" rows="3" class="w-full glass-input rounded-xl p-3 text-xs text-slate-200 focus:outline-none transition font-mono"></textarea>
        <div class="flex justify-end">
            <button onclick="saveInstructorNotes()" class="bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-neutral-950 font-bold px-4 py-2 rounded-xl text-xs transition cursor-pointer shadow-md">
                Save Evaluation Note
            </button>
        </div>
    </div>
</section>