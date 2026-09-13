<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="admin-kicker">Infraestructura</div>
                <h1 class="admin-title">Estaciones</h1>
                <p class="admin-subtitle">Administra las marcas de radio y su disponibilidad dentro de la plataforma.</p>
            </div>
            <a href="{{ route('admin.stations.create') }}" class="admin-btn-primary">+ Nueva estación</a>
        </div>
    </x-slot>

    <div class="admin-container py-7">
        <div class="admin-table-wrap">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <div>
                    <div class="text-sm font-black text-white">Estaciones registradas</div>
                    <div class="text-xs text-zinc-500">{{ $stations->total() }} en total</div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Estación</th>
                            <th>Identificador</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stations as $station)
                            <tr>
                                <td>
                                    <div class="font-black text-white">{{ $station->name }}</div>
                                    <div class="mt-1 text-xs text-zinc-500">Radio station</div>
                                </td>
                                <td><code class="rounded-lg bg-black/30 px-2 py-1 text-xs text-zinc-400">{{ $station->slug }}</code></td>
                                <td>
                                    @if($station->is_active)
                                        <span class="admin-badge-success"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> Activa</span>
                                    @else
                                        <span class="admin-badge-muted">Inactiva</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('admin.stations.channels', $station) }}" class="admin-btn-secondary !px-3 !py-2">Canales</a>
                                        <a href="{{ route('admin.stations.edit', $station) }}" class="admin-btn-secondary !px-3 !py-2">Editar</a>
                                        <form action="{{ route('admin.stations.destroy', $station) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="admin-btn-danger delete-btn">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="!py-12 text-center text-zinc-500">No hay estaciones registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">{{ $stations->links() }}</div>
    </div>
</x-app-layout>
