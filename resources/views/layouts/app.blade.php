<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHazard VR — Supervisor Glass Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Dashboard Stylesheet -->
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

    <!-- Operations Dashboard Master Container -->
    <div id="view-dashboard" class="relative z-10 w-full max-w-[1440px] space-y-6">

        <!-- Master Glass Sheet -->
        <section class="glass-sheet rounded-[2.2rem] p-6 lg:p-7 flex flex-col lg:flex-row gap-7">

            <!-- Sidebar Navigation -->
            @include('partials.nav')

            <!-- Main Workspace Views -->
            <div class="flex-1 space-y-6">

                <!-- Top Bar -->
                @include('partials.header')

                <!-- Page Content -->
                @yield('content')

            </div>
        </section>

        <!-- Bottom Bar & 3D Isometric Holographic Cube -->
        @include('partials.footer')

    </div>

    <!-- Dashboard JavaScript Logic -->
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>
