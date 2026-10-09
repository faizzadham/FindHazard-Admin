<!-- ======================================================== -->
<!-- 1. LOGIN SCREEN CONTAINER                                -->
<!-- ======================================================== -->
<div id="view-login" class="relative z-20 w-full max-w-md my-auto flex flex-col items-center justify-center">
    <div class="text-center space-y-2 mb-6">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full glass-card border border-amber-500/30 text-amber-400 text-xs font-mono tracking-widest uppercase">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
            Industrial VR Safety
        </div>
        <h1 class="text-3xl font-black uppercase tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-amber-200 to-yellow-500">
            FindHazard VR
        </h1>
        <p class="text-xs text-slate-400">Supervisor Command Center</p>
    </div>

    <div class="glass-sheet rounded-3xl p-6 sm:p-8 space-y-5 w-full">
        <div class="space-y-4">
            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider">Username</label>
                <input id="loginUsername" type="text" value="supervisor"
                    class="w-full glass-input rounded-xl px-4 py-2.5 text-xs text-slate-100 placeholder-slate-600 focus:outline-none transition font-mono">
            </div>

            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider">Password</label>
                <input id="loginPassword" type="password" value="admin123"
                    class="w-full glass-input rounded-xl px-4 py-2.5 text-xs text-slate-100 placeholder-slate-600 focus:outline-none transition font-mono">
            </div>

            <!-- Pure Button Click (Does NOT submit or reload page) -->
            <button type="button" id="loginBtnTrigger" onclick="forceUnlockDashboard()"
                class="w-full mt-2 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-neutral-950 font-black text-xs py-3 rounded-xl transition shadow-lg shadow-amber-500/25 cursor-pointer active:scale-95">
                Access Supervisor Portal →
            </button>
        </div>
        <p class="text-[10px] text-center text-slate-500 font-mono">Default credentials: supervisor / admin123</p>
    </div>
</div>