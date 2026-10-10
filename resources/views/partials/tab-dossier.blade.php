<div id="view-dossier" class="app-view space-y-5">
@if(isset($record))
    <script>
        window.currentTraineeRecord = @json($record);
    </script>
@endif
    <div class="glass-card rounded-3xl p-5 border-amber-500/20 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-white/5">
            <div>
                <h3 id="dossierTitle" class="text-base font-bold text-white">Analysis: ${trainee.id}</h3>
                <p class="text-xs text-slate-400" id="dossierDate">Session Verified</p>
            </div>
            <div class="flex items-center gap-2">
                <span id="dossierRating" class="glass-pill px-3 py-1 rounded-xl text-xs font-mono text-emerald-400">Locked In</span>
                <span id="dossierScore" class="glass-pill px-3 py-1 rounded-xl text-xs font-mono text-amber-400 font-bold">100 / 100 Pts</span>
                <button onclick="exportPdfCertificate()" class="bg-gradient-to-r from-amber-400 to-amber-500 text-neutral-950 font-bold px-3 py-1 rounded-xl text-xs">
                    📄 Certificate
                </button>
            </div>
        </div>
    </div>
    <div class="glass-card rounded-3xl p-5 space-y-3">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-200">10 Safety Hazard Checkpoints</h4>
        <div id="checkpointsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-2.5"></div>
    </div>
</div>
