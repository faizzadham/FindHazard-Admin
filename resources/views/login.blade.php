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
    @include('partials.login')
</body>
</html>
