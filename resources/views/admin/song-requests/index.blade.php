<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Solicitudes de canciones</h2>
                <p class="text-sm text-gray-500 mt-1">Participación recibida desde la app de Somos Radio.</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-orange-100 px-3 py-1 text-sm font-semibold text-orange-700">
                {{ $counts['new'] }} nuevas
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100"><div class="text-xs uppercase tracking-wide text-gray-500">Nuevas</div><div class="text-3xl font-bold text-gray-900 mt-1">{{ $counts['new'] }}</div></div>
                <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100"><div class="text-xs uppercase tracking-wide text-gray-500">Vistas</div><div class="text-3xl font-bold text-gray-900 mt-1">{{ $counts['seen'] }}</div></div>
                <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100"><div class="text-xs uppercase tracking-wide text-gray-500">Atendidas</div><div class="text-3xl font-bold text-gray-900 mt-1">{{ $counts['attended'] }}</div></div>
                <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100"><div class="text-xs uppercase tracking-wide text-gray-500">Descartadas</div><div class="text-3xl font-bold text-gray-900 mt-1">{{ $counts['discarded'] }}</div></div>
            </div>

            <form method="GET" class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 flex flex-col md:flex-row gap-3 md:items-end">
                <div class="flex-1">
                    <label for="channel_id" class="block text-sm font-medium text-gray-700 mb-1">Estación</label>
                    <select id="channel_id" name="channel_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Todas</option>
                        @foreach($channels as $channel)
                            <option value="{{ $channel->id }}" @selected((int) $channelId === $channel->id)>{{ $channel->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="status" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Todos</option>
                        <option value="new" @selected($status === 'new')>Nueva</option>
                        <option value="seen" @selected($status === 'seen')>Vista</option>
                        <option value="attended" @selected($status === 'attended')>Atendida</option>
                        <option value="discarded" @selected($status === 'discarded')>Descartada</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 rounded-md bg-gray-900 text-white font-semibold hover:bg-black transition">Aplicar filtros</button>
                    <a href="{{ route('admin.song-requests.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition">Limpiar</a>
                </div>
            </form>

            <div class="space-y-4">
                @forelse($songRequests as $songRequest)
                    <article class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-5">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-1 text-xs font-semibold text-orange-700">{{ $songRequest->channel?->name ?? 'Estación' }}</span>
                                        @php
                                            $statusClasses = ['new' => 'bg-red-100 text-red-700', 'seen' => 'bg-blue-100 text-blue-700', 'attended' => 'bg-green-100 text-green-700', 'discarded' => 'bg-gray-100 text-gray-600'];
                                            $statusLabels = ['new' => 'Nueva', 'seen' => 'Vista', 'attended' => 'Atendida', 'discarded' => 'Descartada'];
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$songRequest->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $statusLabels[$songRequest->status] ?? $songRequest->status }}</span>
                                        <span class="text-xs text-gray-400">{{ $songRequest->created_at?->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-900">{{ $songRequest->song }} <span class="font-normal text-gray-500">— {{ $songRequest->artist }}</span></h3>
                                    <p class="text-sm text-gray-600 mt-2">Solicitada por <span class="font-semibold text-gray-800">{{ $songRequest->listener_name }}</span></p>
                                    @if($songRequest->dedication)
                                        <div class="mt-3 rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-700 border border-gray-100">“{{ $songRequest->dedication }}”</div>
                                    @endif
                                </div>

                                <form action="{{ route('admin.song-requests.update', $songRequest) }}" method="POST" class="flex flex-col sm:flex-row sm:items-end gap-2 shrink-0">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label for="status-{{ $songRequest->id }}" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Cambiar estado</label>
                                        <select id="status-{{ $songRequest->id }}" name="status" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-orange-500 focus:ring-orange-500">
                                            <option value="new" @selected($songRequest->status === 'new')>Nueva</option>
                                            <option value="seen" @selected($songRequest->status === 'seen')>Vista</option>
                                            <option value="attended" @selected($songRequest->status === 'attended')>Atendida</option>
                                            <option value="discarded" @selected($songRequest->status === 'discarded')>Descartada</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="px-4 py-2 rounded-md bg-orange-500 text-white text-sm font-bold hover:bg-orange-600 transition">Guardar estado</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center">
                        <div class="text-lg font-semibold text-gray-800">Aún no hay solicitudes</div>
                        <p class="text-sm text-gray-500 mt-1">Cuando un oyente mande una canción desde la app aparecerá aquí.</p>
                    </div>
                @endforelse
            </div>

            <div>{{ $songRequests->links() }}</div>
        </div>
    </div>

    <x-sweet-alerts />
</x-app-layout>
