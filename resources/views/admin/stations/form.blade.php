<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($station) ? 'Editar estación' : 'Nueva estación' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 bg-white p-6 rounded-lg shadow">
            <form method="POST"
                  action="{{ isset($station)
                      ? route('admin.stations.update', $station)
                      : route('admin.stations.store') }}">
                @csrf
                @if(isset($station))
                    @method('PUT')
                @endif

                {{-- Nombre --}}
                <div class="mb-4">
                    <x-input-label value="Nombre" />
                    <x-text-input
                        name="name"
                        class="w-full"
                        value="{{ old('name', $station->name ?? '') }}"
                        required />
                    @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                {{-- Slug --}}
                <div class="mb-4">
                    <x-input-label value="Slug (único)" />
                    <x-text-input
                        name="slug"
                        class="w-full"
                        value="{{ old('slug', $station->slug ?? '') }}"
                        required />
                    @error('slug') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                {{-- Logo URL --}}
                <div class="mb-4">
                    <x-input-label value="Logo URL" />
                    <x-text-input
                        name="logo_url"
                        class="w-full"
                        value="{{ old('logo_url', $station->logo_url ?? '') }}" />
                    @error('logo_url') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                {{-- Activa --}}
                <div class="mb-4 flex items-center space-x-2">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        {{ old('is_active', $station->is_active ?? true) ? 'checked' : '' }}>
                    <x-input-label value="Activa" />
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.stations.index') }}" class="px-4 py-2 bg-gray-300 rounded-md">Cancelar</a>
                    <x-primary-button>
                        {{ isset($station) ? 'Actualizar' : 'Guardar' }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
