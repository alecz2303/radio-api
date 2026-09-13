<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="admin-kicker">Señales</div>
                <h1 class="admin-title">{{ isset($station) ? 'Canales de '.$station->name : 'Canales' }}</h1>
                <p class="admin-subtitle">Administra streams, estado y pertenencia de cada señal publicada.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if(isset($station))
                    <a href="{{ route('admin.stations.index') }}" class="admin-btn-secondary">← Estaciones</a>
                @endif
                <a href="{{ isset($station) ? route('admin.channels.create', ['station_id' => $station->id]) : route('admin.channels.create') }}" class="admin-btn-primary">+ Nuevo canal</a>
            </div>
        </div>
    </x-slot>

    <div class="admin-container py-7">
        <div class="admin-table-wrap">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <div>
                    <div class="text-sm font-black text-white">Señales configuradas</div>
                    <div class="text-xs text-zinc-500">{{ $channels->total() }} registros</div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Estación</th>
                            <th>Canal</th>
                            <th>Slug</th>
                            <th>Stream</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($channels as $channel)
                            <tr>
                                <td class="text-zinc-400">{{ $channel->station->name ?? '-' }}</td>
                                <td>
                                    <div class="font-black text-white">{{ $channel->name }}</div>
                                    <div class="mt-1 text-xs text-zinc-500">{{ $channel->frequency ?? '' }} {{ $channel->city ?? '' }}</div>
                                </td>
                                <td><code class="rounded-lg bg-black/30 px-2 py-1 text-xs text-zinc-400">{{ $channel->slug }}</code></td>
                                <td class="max-w-xs">
                                    @if($channel->stream_url)
                                        <a href="{{ $channel->stream_url }}" target="_blank" class="admin-link block truncate text-xs">{{ Str::limit($channel->stream_url, 45) }}</a>
                                    @else
                                        <span class="text-xs text-zinc-600">Sin stream</span>
                                    @endif
                                </td>
                                <td>
                                    @if($channel->is_active)
                                        <span class="admin-badge-success"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> En línea</span>
                                    @else
                                        <span class="admin-badge-muted">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('admin.channels.edit', $channel) }}" class="admin-btn-secondary !px-3 !py-2">Editar</a>
                                        <form action="{{ route('admin.channels.destroy', $channel) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="admin-btn-danger delete-btn">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="!py-12 text-center text-zinc-500">No hay canales registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">{{ $channels->links() }}</div>
    </div>
</x-app-layout>
