<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="admin-kicker">Comunicación directa</div>
                <h1 class="admin-title">Notificaciones push</h1>
                <p class="admin-subtitle">Envía avisos a los teléfonos que tienen instalada la app de Somos Radio.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1.5 text-xs font-bold text-zinc-300">{{ $activeDevices }} dispositivos activos</span>
                <span class="rounded-full px-3 py-1.5 text-xs font-black {{ $configured ? 'bg-emerald-500/10 text-emerald-300' : 'bg-amber-500/10 text-amber-300' }}">{{ $configured ? 'Firebase conectado' : 'Firebase pendiente' }}</span>
            </div>
        </div>
    </x-slot>

    <div class="admin-container py-7 space-y-7">
        @if(session('success'))<div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('success') }}</div>@endif
        @if(session('warning'))<div class="rounded-2xl border border-amber-500/20 bg-amber-500/10 px-4 py-3 text-sm text-amber-200">{{ session('warning') }}</div>@endif
        @if(session('error'))<div class="rounded-2xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-200">{{ session('error') }}</div>@endif

        <section class="admin-panel p-6">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-500/15 text-xl">🧪</div>
                <div><h2 class="text-lg font-black text-white">Prueba por dispositivo</h2><p class="text-sm text-zinc-500">Envía un mensaje únicamente al token seleccionado para diagnosticar FCM.</p></div>
            </div>
            <form method="POST" action="{{ route('admin.push-notifications.test-device') }}" class="flex flex-col gap-3 lg:flex-row lg:items-end">
                @csrf
                <div class="min-w-0 flex-1">
                    <label for="device_id" class="mb-2 block text-xs font-black uppercase tracking-[.18em] text-zinc-500">Dispositivo registrado</label>
                    <select id="device_id" name="device_id" required class="w-full rounded-xl border border-white/10 bg-zinc-950 px-4 py-3 text-white focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Selecciona un dispositivo</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" @disabled(!$device->is_active)>
                                #{{ $device->id }} · {{ $device->platform ?? 'sin plataforma' }} · {{ $device->is_active ? 'ACTIVO' : 'INACTIVO' }} · visto {{ $device->last_seen_at?->format('d/m/Y H:i:s') ?? 'sin fecha' }} · token …{{ substr($device->token, -10) }}
                            </option>
                        @endforeach
                    </select>
                    @error('device_id')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="admin-btn-primary justify-center lg:min-w-52" {{ !$configured || $activeDevices === 0 ? 'disabled' : '' }}>Enviar prueba directa</button>
            </form>
            <p class="mt-3 text-xs text-zinc-600">La prueba no se envía a los demás teléfonos y muestra solo los últimos 10 caracteres del token para identificarlo sin exponerlo completo.</p>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.05fr_.95fr]">
            <div class="admin-panel p-6">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-500 text-black">🔔</div>
                    <div><h2 class="text-lg font-black text-white">Nueva notificación</h2><p class="text-sm text-zinc-500">Se enviará a todos los dispositivos activos.</p></div>
                </div>

                <form method="POST" action="{{ route('admin.push-notifications.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="template" class="mb-2 block text-xs font-black uppercase tracking-[.18em] text-zinc-500">Plantilla rápida</label>
                        <select id="template" class="w-full rounded-xl border border-white/10 bg-zinc-950 px-4 py-3 text-white focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Escribir mensaje manualmente</option>
                            <option value="test">🔔 Prueba de notificación</option>
                            <option value="live891">▶ Escuchar 89.1 FM</option>
                            <option value="live1029">▶ Escuchar 102.9 FM</option>
                            <option value="participate">🎙️ Invitar a participar</option>
                            <option value="latest">📰 Abrir Lo último</option>
                            <option value="general">📻 Abrir En vivo</option>
                        </select>
                        <p class="mt-2 text-xs text-zinc-600">La plantilla llena título, mensaje y destino; puedes editar todo antes de enviar.</p>
                    </div>
                    <div>
                        <label for="title" class="mb-2 block text-xs font-black uppercase tracking-[.18em] text-zinc-500">Título</label>
                        <input id="title" name="title" value="{{ old('title') }}" maxlength="120" required placeholder="Ej. Estamos al aire" class="w-full rounded-xl border border-white/10 bg-white/[0.04] px-4 py-3 text-white placeholder:text-zinc-600 focus:border-orange-500 focus:ring-orange-500">
                        @error('title')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="body" class="mb-2 block text-xs font-black uppercase tracking-[.18em] text-zinc-500">Mensaje</label>
                        <textarea id="body" name="body" rows="5" maxlength="500" required placeholder="Ej. Somos Radio 89.1 FM está en vivo. Escúchanos ahora." class="w-full rounded-xl border border-white/10 bg-white/[0.04] px-4 py-3 text-white placeholder:text-zinc-600 focus:border-orange-500 focus:ring-orange-500">{{ old('body') }}</textarea>
                        @error('body')<p class="mt-2 text-xs text-red-300">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="action" class="mb-2 block text-xs font-black uppercase tracking-[.18em] text-zinc-500">Destino en la app</label>
                        <select id="action" name="action" class="w-full rounded-xl border border-white/10 bg-zinc-950 px-4 py-3 text-white focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Abrir app</option>
                            <option value="play:somos-radio-89-1" @selected(old('action') === 'play:somos-radio-89-1')>▶ Escuchar 89.1 FM</option>
                            <option value="play:somos-radio-102-9" @selected(old('action') === 'play:somos-radio-102-9')>▶ Escuchar 102.9 FM</option>
                            <option value="live" @selected(old('action') === 'live')>Abrir En vivo</option>
                            <option value="participate" @selected(old('action') === 'participate')>Abrir Participa</option>
                            <option value="latest" @selected(old('action') === 'latest')>Abrir Lo último</option>
                        </select>
                        <p class="mt-2 text-xs text-zinc-600">Los destinos “Escuchar” abren En vivo e inician automáticamente la estación elegida.</p>
                    </div>
                    <div class="rounded-2xl border border-orange-500/15 bg-orange-500/[0.06] p-4 text-sm text-zinc-400"><span class="font-bold text-orange-300">Vista previa:</span> la notificación aparecerá con el nombre e icono de la app en el teléfono del oyente.</div>
                    <button type="submit" class="admin-btn-primary w-full justify-center" {{ !$configured || $activeDevices === 0 ? 'disabled' : '' }}>Enviar notificación</button>
                    @if(!$configured)<p class="text-center text-xs text-amber-300">Configura las credenciales de Firebase en el servidor para habilitar el envío.</p>@elseif($activeDevices === 0)<p class="text-center text-xs text-zinc-500">Aún no hay dispositivos registrados desde la app.</p>@endif
                </form>
            </div>

            <div class="admin-panel overflow-hidden">
                <div class="border-b border-white/10 px-6 py-5"><div class="admin-kicker">Historial</div><h2 class="mt-1 text-lg font-black text-white">Últimos envíos</h2></div>
                <div class="divide-y divide-white/5">
                    @forelse($notifications as $notification)
                        <div class="px-6 py-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0"><div class="truncate font-black text-white">{{ $notification->title }}</div><p class="mt-1 line-clamp-2 text-sm text-zinc-500">{{ $notification->body }}</p></div>
                                @php $statusClass = match($notification->status) { 'sent' => 'bg-emerald-500/10 text-emerald-300', 'failed' => 'bg-red-500/10 text-red-300', default => 'bg-zinc-500/10 text-zinc-300' }; @endphp
                                <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-wider {{ $statusClass }}">{{ $notification->status }}</span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-zinc-600"><span>{{ $notification->created_at?->format('d/m/Y H:i') }}</span><span>{{ $notification->success_count }} enviadas</span><span>{{ $notification->failure_count }} fallidas</span></div>
                            @if($notification->error_text)<div class="mt-4 rounded-xl border border-red-500/20 bg-red-500/[0.08] p-3 text-xs leading-relaxed text-red-200"><div class="mb-1 font-black uppercase tracking-[.14em] text-red-300">Error de Firebase</div><div class="whitespace-pre-wrap break-words">{{ $notification->error_text }}</div></div>@endif
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-sm text-zinc-600">Todavía no se han enviado notificaciones.</div>
                    @endforelse
                </div>
                <div class="px-6 py-4">{{ $notifications->links() }}</div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const templates = {
                test: ['🔔 Prueba de Somos Radio', 'Esta es una notificación de prueba de la app de Somos Radio.', ''],
                live891: ['🔴 Somos Radio 89.1 está al aire', 'Acompáñanos ahora. Toca para escuchar en vivo. 📻', 'play:somos-radio-89-1'],
                live1029: ['🔴 Somos Radio 102.9 está al aire', 'Acompáñanos ahora. Toca para escuchar en vivo. 📻', 'play:somos-radio-102-9'],
                participate: ['🎙️ Tu voz también es parte de Somos', 'Pide tu canción y envía tu dedicatoria directamente desde la app.', 'participate'],
                latest: ['📰 Lo último en Somos Radio', 'Descubre el contenido disponible en la app de Somos Radio.', 'latest'],
                general: ['📻 Somos Radio en tu teléfono', 'Escucha nuestras estaciones en vivo desde la app.', 'live'],
            };
            const template = document.getElementById('template');
            const title = document.getElementById('title');
            const body = document.getElementById('body');
            const action = document.getElementById('action');
            template?.addEventListener('change', () => {
                const selected = templates[template.value];
                if (!selected) return;
                [title.value, body.value, action.value] = selected;
            });
        });
    </script>
</x-app-layout>
