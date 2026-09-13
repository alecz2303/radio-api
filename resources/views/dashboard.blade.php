<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="admin-kicker">Centro de control</div>
                <h1 class="admin-title">Resumen de operación</h1>
                <p class="admin-subtitle">Estado general de estaciones, canales y participación de la audiencia.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.song-requests.index') }}" class="admin-btn-secondary">Ver solicitudes</a>
                <a href="{{ route('admin.channels.index') }}" class="admin-btn-primary">Administrar canales</a>
            </div>
        </div>
    </x-slot>

    <div class="admin-container py-7 space-y-7">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="admin-stat">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="admin-kicker">Estaciones</div>
                        <div class="mt-2 text-4xl font-black text-white">{{ $stats['stations'] }}</div>
                    </div>
                    <div class="rounded-xl bg-orange-500/10 p-3 text-orange-400">🎙️</div>
                </div>
                <div class="mt-4 text-xs text-zinc-500"><span class="font-bold text-emerald-300">{{ $stats['activeStations'] }}</span> activas</div>
            </div>

            <div class="admin-stat">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="admin-kicker">Canales</div>
                        <div class="mt-2 text-4xl font-black text-white">{{ $stats['channels'] }}</div>
                    </div>
                    <div class="rounded-xl bg-orange-500/10 p-3 text-orange-400">📡</div>
                </div>
                <div class="mt-4 text-xs text-zinc-500"><span class="font-bold text-emerald-300">{{ $stats['activeChannels'] }}</span> transmitiendo</div>
            </div>

            <div class="admin-stat">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="admin-kicker">Solicitudes nuevas</div>
                        <div class="mt-2 text-4xl font-black text-white">{{ $stats['newRequests'] }}</div>
                    </div>
                    <div class="rounded-xl bg-red-500/10 p-3 text-red-300">🎵</div>
                </div>
                <div class="mt-4 text-xs text-zinc-500">Pendientes de revisar</div>
            </div>

            <div class="admin-stat">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="admin-kicker">Participación hoy</div>
                        <div class="mt-2 text-4xl font-black text-white">{{ $stats['requestsToday'] }}</div>
                    </div>
                    <div class="rounded-xl bg-orange-500/10 p-3 text-orange-400">⚡</div>
                </div>
                <div class="mt-4 text-xs text-zinc-500">Solicitudes recibidas hoy</div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.15fr_.85fr]">
            <div class="admin-panel overflow-hidden">
                <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                    <div>
                        <div class="text-sm font-black text-white">Actividad reciente</div>
                        <div class="text-xs text-zinc-500">Últimas solicitudes enviadas desde la app</div>
                    </div>
                    <a href="{{ route('admin.song-requests.index') }}" class="admin-link text-xs">Ver todas →</a>
                </div>

                <div class="divide-y divide-white/[0.07]">
                    @forelse($recentRequests as $request)
                        <div class="flex items-start gap-4 px-5 py-4">
                            <div class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-orange-500/10 text-orange-400">♪</div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="truncate font-bold text-white">{{ $request->song }}</div>
                                    <div class="text-zinc-500">— {{ $request->artist }}</div>
                                </div>
                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-zinc-500">
                                    <span>{{ $request->listener_name }}</span>
                                    <span>{{ $request->channel?->name ?? 'Sin canal' }}</span>
                                    <span>{{ $request->created_at?->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            @if($request->status === 'new')
                                <span class="admin-badge-live">Nueva</span>
                            @elseif($request->status === 'attended')
                                <span class="admin-badge-success">Atendida</span>
                            @else
                                <span class="admin-badge-muted">{{ ucfirst($request->status) }}</span>
                            @endif
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-zinc-500">Todavía no hay solicitudes registradas.</div>
                    @endforelse
                </div>
            </div>

            <div class="admin-panel overflow-hidden">
                <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                    <div>
                        <div class="text-sm font-black text-white">Canales publicados</div>
                        <div class="text-xs text-zinc-500">Estado de las señales configuradas</div>
                    </div>
                    <a href="{{ route('admin.channels.index') }}" class="admin-link text-xs">Administrar →</a>
                </div>

                <div class="divide-y divide-white/[0.07]">
                    @forelse($channels as $channel)
                        <div class="flex items-center gap-4 px-5 py-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/[0.05] text-orange-400">◉</div>
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-bold text-white">{{ $channel->name }}</div>
                                <div class="truncate text-xs text-zinc-500">{{ $channel->station?->name ?? 'Sin estación' }}</div>
                            </div>
                            @if($channel->is_active)
                                <span class="admin-badge-success"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> En línea</span>
                            @else
                                <span class="admin-badge-muted">Inactivo</span>
                            @endif
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-zinc-500">No hay canales configurados.</div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
