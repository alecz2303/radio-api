<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($channel) ? 'Editar Canal' : 'Nuevo Canal' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 bg-white p-6 rounded-lg shadow">
            <form method="POST"
                  action="{{ isset($channel)
                      ? route('admin.channels.update', $channel)
                      : route('admin.channels.store') }}">
                @csrf
                @if(isset($channel))
                    @method('PUT')
                @endif

                @php
                    $selectedStation = old('station_id',
                        isset($channel) ? ($channel->station_id ?? '') : ($defaultStationId ?? '')
                    );
                @endphp
                {{-- Estación --}}
                <div class="mb-4">
                    <x-input-label value="Estación" />
                    <select name="station_id" class="w-full border-gray-300 rounded-md" required>
                        <option value="" {{ $selectedStation === '' ? 'selected' : '' }}>Selecciona una estación</option>
                        @foreach($stations as $id => $name)
                            <option value="{{ $id }}" {{ (string)$selectedStation === (string)$id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('station_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                {{-- Nombre --}}
                <div class="mb-4">
                    <x-input-label value="Nombre" />
                    <x-text-input name="name" class="w-full"
                        value="{{ old('name', $channel->name ?? '') }}" required />
                    @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                {{-- Slug --}}
                <div class="mb-4">
                    <x-input-label value="Slug (único)" />
                    <x-text-input name="slug" class="w-full"
                        value="{{ old('slug', $channel->slug ?? '') }}" required />
                    @error('slug') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                {{-- Stream URL --}}
                <div class="mb-4">
                    <x-input-label value="URL de Stream" />
                    <x-text-input name="stream_url" type="url" class="w-full"
                        value="{{ old('stream_url', $channel->stream_url ?? '') }}" required />
                    @error('stream_url') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                {{-- Backup URL --}}
                <div class="mb-4">
                    <x-input-label value="URL de Respaldo (opcional)" />
                    <x-text-input name="backup_url" type="url" class="w-full"
                        value="{{ old('backup_url', $channel->backup_url ?? '') }}" />
                    @error('backup_url') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                {{-- Orden --}}
                <div class="mb-4">
                    <x-input-label value="Orden de aparición" />
                    <x-text-input name="order" type="number" class="w-full"
                        value="{{ old('order', $channel->order ?? 1) }}" />
                    @error('order') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                {{-- Activo --}}
                <div class="mb-4 flex items-center space-x-2">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $channel->is_active ?? true) ? 'checked' : '' }}>
                    <x-input-label value="Activo" />
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.channels.index') }}" class="px-4 py-2 bg-gray-300 rounded-md">Cancelar</a>
                    <x-primary-button>
                        {{ isset($channel) ? 'Actualizar' : 'Guardar' }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <x-sweet-alerts />
</x-app-layout>
