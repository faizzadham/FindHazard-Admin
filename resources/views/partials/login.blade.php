<main id="view-login" class="relative z-20 w-full max-w-md my-auto flex flex-col items-center justify-center p-4">
    <!-- Header Badge -->
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

    <!-- Login Glass Panel -->
    <div class="glass-sheet rounded-3xl p-6 sm:p-8 space-y-5 w-full">
        <div id="loginErrorMessage" class="hidden p-3 rounded-xl bg-rose-950/40 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-2">
            <span>⚠️</span>
            <span id="loginErrorText">Invalid credentials. Enter supervisor ID and passkey.</span>
        </div>

        <form id="supervisorLoginForm" onsubmit="handleLoginSubmit(event)" class="space-y-4">
            <div class="space-y-1.5">
                <label for="loginUsername" class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider">Username</label>
                <input id="loginUsername" type="text" required value="supervisor"
                    class="w-full glass-input rounded-xl px-4 py-2.5 text-xs text-slate-100 placeholder-slate-600 focus:outline-none transition font-mono">
            </div>

            <div class="space-y-1.5">
                <label for="loginPassword" class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider">Password</label>
                <input id="loginPassword" type="password" required value="admin123"
                    class="w-full glass-input rounded-xl px-4 py-2.5 text-xs text-slate-100 placeholder-slate-600 focus:outline-none transition font-mono">
            </div>

            <button id="loginSubmitBtn" type="submit" 
                class="w-full mt-2 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-neutral-950 font-black text-xs py-3 rounded-xl transition shadow-lg shadow-amber-500/20 cursor-pointer active:scale-95">
                Access Supervisor Portal
            </button>
        </form>
        <p class="text-[10px] text-center text-slate-500 font-mono">Default credentials: supervisor / admin123</p>
    </div>
</main>