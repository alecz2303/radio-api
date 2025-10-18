<x-app-layout>
    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Canales / Emisoras
        </h2>

        @if(isset($station))
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Canales de {{ $station->name }}
                </h2>
                <a href="{{ route('admin.stations.index') }}"
                class="text-sm text-blue-600 hover:text-blue-800 underline">
                ← Volver a estaciones
                </a>
            </div>
        @else
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Todos los canales
            </h2>
        @endif

    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <a href="{{ isset($station)
                        ? route('admin.channels.create', ['station_id' => $station->id])
                        : route('admin.channels.create') }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                + Nuevo Canal
                </a>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 border-b">Estación</th>
                            <th class="px-6 py-3 border-b">Nombre</th>
                            <th class="px-6 py-3 border-b">Slug</th>
                            <th class="px-6 py-3 border-b">Stream</th>
                            <th class="px-6 py-3 border-b text-center">Activo</th>
                            <th class="px-6 py-3 border-b text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($channels as $channel)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3">{{ $channel->station->name ?? '-' }}</td>
                                <td class="px-6 py-3">{{ $channel->name }}</td>
                                <td class="px-6 py-3">{{ $channel->slug }}</td>
                                <td class="px-6 py-3 truncate max-w-xs">
                                    <a href="{{ $channel->stream_url }}" class="text-blue-600 underline" target="_blank">
                                        {{ Str::limit($channel->stream_url, 40) }}
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    @if($channel->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Sí</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">No</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right space-x-2">
                                    <a href="{{ route('admin.channels.edit', $channel) }}"
                                       class="text-blue-600 hover:text-blue-800 font-medium">Editar</a>

                                    <form action="{{ route('admin.channels.destroy', $channel) }}"
                                          method="POST"
                                          class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="text-red-600 hover:text-red-800 delete-btn font-medium">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No hay canales registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $channels->links() }}</div>
        </div>
    </div>

    <x-sweet-alerts />
</x-app-layout>
