<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Estaciones de Radio
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Botón para nueva estación --}}
            <div class="flex justify-end mb-4">
                <a href="{{ route('admin.stations.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    + Nueva Estación
                </a>
            </div>

            {{-- Tabla de estaciones --}}
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full text-sm text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 border-b">Nombre</th>
                            <th class="px-6 py-3 border-b">Slug</th>
                            <th class="px-6 py-3 border-b text-center">Activa</th>
                            <th class="px-6 py-3 border-b text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($stations as $station)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3">{{ $station->name }}</td>
                                <td class="px-6 py-3">{{ $station->slug }}</td>
                                <td class="px-6 py-3 text-center">
                                    @if($station->is_active)
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                            Activa
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">
                                            Inactiva
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right space-x-2">
                                    {{-- Ver canales --}}
                                    <a href="{{ route('admin.stations.channels', $station) }}"
                                        class="text-green-600 hover:text-green-800 font-medium">
                                        Ver canales
                                    </a>
                                    {{-- Editar --}}
                                    <a href="{{ route('admin.stations.edit', $station) }}"
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        Editar
                                    </a>

                                    {{-- Eliminar --}}
                                    <form action="{{ route('admin.stations.destroy', $station) }}"
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
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    No hay estaciones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-4">
                {{ $stations->links() }}
            </div>

        </div>
    </div>

    {{-- SweetAlerts globales --}}
    <x-sweet-alerts />

</x-app-layout>
