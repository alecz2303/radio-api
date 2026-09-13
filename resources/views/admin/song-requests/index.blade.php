<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="admin-kicker">Participación</div>
                <h1 class="admin-title">Solicitudes de canciones</h1>
                <p class="admin-subtitle">Gestiona en tiempo real las peticiones que llegan desde la app de Somos Radio.</p>
            </div>
            <span class="admin-badge-live">{{ $counts['new'] }} nuevas</span>
        </div>
    </x-slot>

    <div class="admin-container py-7 space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="admin-stat">
                <div class="admin-kicker">Nuevas</div>
                <div class="mt-2 text-4xl font-black text-white">{{ $counts['new'] }}</div>
                <div class="mt-3 text-xs text-zinc-500">Pendientes de revisar</div>
            </div>
            <div class="admin-stat">
                <div class="admin-kicker">Vistas</div>
                <div class="mt-2 text-4xl font-black text-white">{{ $counts['seen'] }}</div>
                <div class="mt-3 text-xs text-zinc-500">Ya revisadas</div>
            </div>
            <div class="admin-stat">
                <div class="admin-kicker">Atendidas</div>
                <div class="mt-2 text-4xl font-black text-white">{{ $counts['attended'] }}</div>
                <div class="mt-3 text-xs text-zinc-500">Marcadas como resueltas</div>
            </div>
            <div class="admin-stat">
                <div class="admin-kicker">Descartadas</div>
                <div class="mt-2 text-4xl font-black text-white">{{ $counts['discarded'] }}</div>
                <div class="mt-3 text-xs text-zinc-500">Fuera de programación</div>
            </div>
        </section>

        <form method="GET" class="admin-panel p-5">
            <div class="grid gap-4 md:grid-cols-[1fr_1fr_auto] md:items-end">
                <div>
                    <label for="channel_id" class="mb-2 block text-xs font-black uppercase tracking-wider text-zinc-500">Estación</label>
                    <select id="channel_id" name="channel_id" class="admin-select">
                        <option value="">Todas</option>
                        @foreach($channels as $channel)
                            <option value="{{ $channel->id }}" @selected((int) $channelId === $channel->id)>{{ $channel->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="mb-2 block text-xs font-black uppercase tracking-wider text-zinc-500">Estado</label>
                    <select id="status" name="status" class="admin-select">
                        <option value="">Todos</option>
                        <option value="new" @selected($status === 'new')>Nueva</option>
                        <option value="seen" @selected($status === 'seen')>Vista</option>
                        <option value="attended" @selected($status === 'attended')>Atendida</option>
                        <option value="discarded" @selected($status === 'discarded')>Descartada</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="admin-btn-primary">Aplicar</button>
                    <a href="{{ route('admin.song-requests.index') }}" class="admin-btn-secondary">Limpiar</a>
                </div>
            </div>
        </form>

        <section class="space-y-4">
            @forelse($songRequests as $songRequest)
                @php
                    $statusLabels = ['new' => 'Nueva', 'seen' => 'Vista', 'attended' => 'Atendida', 'discarded' => 'Descartada'];
                @endphp
                <article class="admin-panel overflow-hidden">
                    <div class="grid gap-5 p-5 lg:grid-cols-[1fr_auto] lg:items-start">
                        <div class="min-w-0">
                            <div class="mb-3 flex flex-wrap items-center gap-2">
                                <span class="rounded-full border border-orange-500/20 bg-orange-500/10 px-2.5 py-1 text-xs font-bold text-orange-300">{{ $songRequest->channel?->name ?? 'Estación' }}</span>
                                @if($songRequest->status === 'new')
                                    <span class="admin-badge-live">Nueva</span>
                                @elseif($songRequest->status === 'attended')
                                    <span class="admin-badge-success">Atendida</span>
                                @else
                                    <span class="admin-badge-muted">{{ $statusLabels[$songRequest->status] ?? ucfirst($songRequest->status) }}</span>
                                @endif
                                <span class="text-xs text-zinc-600">{{ $songRequest->created_at?->format('d/m/Y H:i') }}</span>
                            </div>

                            <h2 class="text-xl font-black text-white sm:text-2xl">{{ $songRequest->song }} <span class="font-medium text-zinc-500">— {{ $songRequest->artist }}</span></h2>
                            <p class="mt-2 text-sm text-zinc-400">Solicitada por <span class="font-bold text-zinc-200">{{ $songRequest->listener_name }}</span></p>

                            @if($songRequest->dedication)
                                <div class="mt-4 rounded-xl border border-white/[0.07] bg-black/20 px-4 py-3 text-sm italic text-zinc-300">“{{ $songRequest->dedication }}”</div>
                            @endif
                        </div>

                        <form action="{{ route('admin.song-requests.update', $songRequest) }}" method="POST" class="admin-panel-soft flex min-w-[220px] flex-col gap-3 p-4">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label for="status-{{ $songRequest->id }}" class="mb-2 block text-[10px] font-black uppercase tracking-[0.18em] text-zinc-500">Cambiar estado</label>
                                <select id="status-{{ $songRequest->id }}" name="status" class="admin-select text-sm">
                                    <option value="new" @selected($songRequest->status === 'new')>Nueva</option>
                                    <option value="seen" @selected($songRequest->status === 'seen')>Vista</option>
                                    <option value="attended" @selected($songRequest->status === 'attended')>Atendida</option>
                                    <option value="discarded" @selected($songRequest->status === 'discarded')>Descartada</option>
                                </select>
                            </div>
                            <button type="submit" class="admin-btn-primary w-full">Guardar estado</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="admin-panel p-12 text-center">
                    <div class="text-lg font-black text-white">Aún no hay solicitudes</div>
                    <p class="mt-1 text-sm text-zinc-500">Cuando un oyente mande una canción desde la app aparecerá aquí.</p>
                </div>
            @endforelse
        </section>

        <div>{{ $songRequests->links() }}</div>
    </div>
</x-app-layout>
