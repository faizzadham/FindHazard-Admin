<!-- Bottom Bar & Holographic Isometric Cube -->
<div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
    <div class="md:col-span-3 glass-sheet p-5 rounded-[2rem] space-y-2">
        <span class="text-xs font-semibold text-slate-400">System Telemetry</span>
        <div class="text-xs font-bold text-emerald-400 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> All Systems Operational
        </div>
    </div>
    <div class="md:col-span-6 glass-sheet p-5 rounded-[2rem]">
        <div class="grid grid-cols-5 gap-2 text-center text-[10px] font-bold">
            <a href="{{ route('dashboard.overview') }}" class="glass-card p-2 rounded-xl {{ request()->routeIs('dashboard.overview') ? 'text-amber-400 border-amber-500/40 bg-amber-500/10' : 'text-slate-200 hover:text-white' }} block transition">Overview</a>
            <a href="{{ route('dashboard.monitor') }}" class="glass-card p-2 rounded-xl {{ request()->routeIs('dashboard.monitor') ? 'text-amber-400 border-amber-500/40 bg-amber-500/10' : 'text-slate-200 hover:text-white' }} block transition">VR Monitor</a>
            <a href="{{ route('dashboard.directory') }}" class="glass-card p-2 rounded-xl {{ request()->routeIs('dashboard.directory') ? 'text-amber-400 border-amber-500/40 bg-amber-500/10' : 'text-slate-200 hover:text-white' }} block transition">Directory</a>
            <a href="{{ route('dashboard.analytics') }}" class="glass-card p-2 rounded-xl {{ request()->routeIs('dashboard.analytics') ? 'text-amber-400 border-amber-500/40 bg-amber-500/10' : 'text-slate-200 hover:text-white' }} block transition">Analytics</a>
            <a href="{{ route('dashboard.guide') }}" class="glass-card p-2 rounded-xl {{ request()->routeIs('dashboard.guide') ? 'text-amber-400 border-amber-500/40 bg-amber-500/10' : 'text-slate-200 hover:text-white' }} block transition">Guide</a>
        </div>
    </div>
    <div class="md:col-span-3 glass-sheet p-5 rounded-[2rem] flex items-center justify-center h-[122px]">
        <div class="iso-scene scale-90">
            <div class="iso-cube-wrap relative w-[72px] h-[72px]">
                <div class="cube-face cube-face-front"></div>
                <div class="cube-face cube-face-back"></div>
                <div class="cube-face cube-face-right"></div>
                <div class="cube-face cube-face-left"></div>
                <div class="cube-face cube-face-top"></div>
                <div class="cube-face cube-face-bottom"></div>
                <div class="inner-core"></div>
            </div>
        </div>
    </div>
</div>
