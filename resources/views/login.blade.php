<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHazard VR — Supervisor Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="text-slate-100 min-h-screen relative overflow-x-hidden p-4 sm:p-7 flex flex-col items-center justify-center selection:bg-amber-400 selection:text-neutral-950">

    <!-- Atmospheric Yellow Ambient Glow -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 left-1/3 w-[46rem] h-[46rem] bg-amber-500/10 blur-[160px] rounded-full"></div>
        <div class="absolute bottom-10 right-10 w-[38rem] h-[38rem] bg-yellow-500/[0.08] blur-[150px] rounded-full"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#ffffff06_1px,transparent_1px)] [background-size:32px_32px]"></div>
    </div>

    <!-- Toast Notification -->
    @include('partials.toast')

    <!-- Login Container -->
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
            <form action="{{ route('dashboard.overview') }}" method="GET" class="space-y-4">
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

                <button type="submit" id="loginBtnTrigger"
                    class="w-full mt-2 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 text-neutral-950 font-black text-xs py-3 rounded-xl transition shadow-lg shadow-amber-500/25 cursor-pointer active:scale-95 text-center">
                    Access Supervisor Portal →
                </button>
            </form>
            <p class="text-[10px] text-center text-slate-500 font-mono">Default credentials: supervisor / admin123</p>
        </div>
    </div>
</body>
</html>
