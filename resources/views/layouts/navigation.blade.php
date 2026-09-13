<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-white/10 bg-black/80 backdrop-blur-xl">
    <div class="admin-container">
        <div class="flex h-16 items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-7">
                <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-500 text-black shadow-lg shadow-orange-500/10">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 10v4m4-7v10m4-14v18m4-14v10m4-7v4" />
                        </svg>
                    </div>
                    <div class="min-w-0 leading-tight">
                        <div class="truncate text-sm font-black tracking-wide text-white">RADIO ADMIN</div>
                        <div class="truncate text-[10px] font-bold uppercase tracking-[0.22em] text-orange-400">Control de emisoras</div>
                    </div>
                </a>

                <div class="hidden items-center gap-1 md:flex">
                    <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-bold transition {{ request()->routeIs('dashboard') ? 'bg-orange-500 text-black' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">Resumen</a>
                    <a href="{{ route('admin.stations.index') }}" class="rounded-lg px-3 py-2 text-sm font-bold transition {{ request()->routeIs('admin.stations.*') ? 'bg-orange-500 text-black' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">Estaciones</a>
                    <a href="{{ route('admin.channels.index') }}" class="rounded-lg px-3 py-2 text-sm font-bold transition {{ request()->routeIs('admin.channels.*') ? 'bg-orange-500 text-black' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">Canales</a>
                    <a href="{{ route('admin.song-requests.index') }}" class="rounded-lg px-3 py-2 text-sm font-bold transition {{ request()->routeIs('admin.song-requests.*') ? 'bg-orange-500 text-black' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">Solicitudes</a>
                </div>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                <div class="text-right leading-tight">
                    <div class="text-xs font-bold text-white">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] uppercase tracking-wider text-zinc-500">Administrador</div>
                </div>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] text-zinc-300 transition hover:border-orange-500/30 hover:text-orange-300">
                            <span class="text-sm font-black">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar sesión</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <button @click="open = !open" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] text-zinc-300 md:hidden">
                <svg x-show="!open" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                <svg x-show="open" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <div x-show="open" x-cloak class="border-t border-white/10 py-3 md:hidden">
            <div class="grid gap-1">
                <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-bold text-zinc-300 hover:bg-white/5">Resumen</a>
                <a href="{{ route('admin.stations.index') }}" class="rounded-lg px-3 py-2 text-sm font-bold text-zinc-300 hover:bg-white/5">Estaciones</a>
                <a href="{{ route('admin.channels.index') }}" class="rounded-lg px-3 py-2 text-sm font-bold text-zinc-300 hover:bg-white/5">Canales</a>
                <a href="{{ route('admin.song-requests.index') }}" class="rounded-lg px-3 py-2 text-sm font-bold text-zinc-300 hover:bg-white/5">Solicitudes</a>
                <a href="{{ route('profile.edit') }}" class="rounded-lg px-3 py-2 text-sm font-bold text-zinc-300 hover:bg-white/5">Perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-bold text-red-300 hover:bg-red-500/10">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </div>
</nav>
