<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="admin-kicker">Monetización</div>
                <h1 class="admin-title">Publicidad</h1>
                <p class="admin-subtitle">Administra anuncios de apertura, banners del inicio, vigencia y métricas desde un solo lugar.</p>
            </div>
            <div class="flex flex-wrap gap-2 text-xs font-bold text-zinc-300">
                <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1.5">{{ $stats['active'] }} activas</span>
                <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1.5">{{ number_format($stats['impressions']) }} impresiones</span>
                <span class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1.5">{{ number_format($stats['clicks']) }} clics</span>
            </div>
        </div>
    </x-slot>

    <div class="admin-container space-y-7 py-7">
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-2xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                <div class="font-black">Revisa los datos de la campaña.</div>
                <ul class="mt-2 list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <section class="admin-panel p-6">
            <div class="mb-6">
                <div class="admin-kicker">Nueva campaña</div>
                <h2 class="mt-1 text-xl font-black text-white">Agregar publicidad</h2>
                <p class="mt-2 text-sm text-zinc-500">Configura el anuncio y elige dónde aparecerá. Se publica sin actualizar la app.</p>
            </div>
            <form method="POST" action="{{ route('admin.ads.store') }}" enctype="multipart/form-data" class="space-y-5">@csrf
                <div class="grid gap-4 lg:grid-cols-2">
                    <div><label class="mb-2 block text-xs font-black uppercase tracking-[.18em] text-zinc-500">Nombre de campaña</label><input name="name" value="{{ old('name') }}" required maxlength="120" placeholder="Ej. Restaurante La Casona · Septiembre" class="w-full rounded-xl border border-white/10 bg-white/[0.04] px-4 py-3 text-white placeholder:text-zinc-600 focus:border-orange-500 focus:ring-orange-500"></div>
                    <div><label class="mb-2 block text-xs font-black uppercase tracking-[.18em] text-zinc-500">Anunciante</label><input name="advertiser" value="{{ old('advertiser') }}" maxlength="120" placeholder="Nombre comercial" class="w-full rounded-xl border border-white/10 bg-white/[0.04] px-4 py-3 text-white placeholder:text-zinc-600 focus:border-orange-500 focus:ring-orange-500"></div>
                </div>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div><label class="mb-2 block text-xs font-black uppercase tracking-[.18em] text-zinc-500">Estación</label><select name="station_id" class="w-full rounded-xl border border-white/10 bg-zinc-950 px-4 py-3 text-white"><option value="">Ambas estaciones</option>@foreach($stations as $station)<option value="{{ $station->id }}" @selected((string)old('station_id') === (string)$station->id)>{{ $station->name }}</option>@endforeach</select></div>
                    <div><label class="mb-2 block text-xs font-black uppercase tracking-[.18em] text-zinc-500">Ubicación en la app</label><select id="placement" name="placement" class="w-full rounded-xl border border-white/10 bg-zinc-950 px-4 py-3 text-white"><option value="splash" @selected(old('placement','splash')==='splash')>Pantalla completa al abrir la app</option><option value="home" @selected(old('placement')==='home')>Banner dentro de Inicio</option></select><p id="placement-help" class="mt-2 text-xs text-zinc-600"></p></div>
                </div>
                <div class="grid gap-4 lg:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-black uppercase tracking-[.18em] text-zinc-500">Imagen publicitaria</label>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required class="block w-full rounded-xl border border-white/10 bg-white/[0.04] px-4 py-3 text-sm text-zinc-300 file:mr-4 file:rounded-lg file:border-0 file:bg-orange-500 file:px-3 file:py-2 file:text-xs file:font-black file:text-black">
                        <div class="mt-3 grid gap-2 sm:grid-cols-2">
                            <div class="rounded-xl border border-orange-500/20 bg-orange-500/[0.06] px-3 py-2.5">
                                <div class="text-[10px] font-black uppercase tracking-[.14em] text-orange-300">Apertura</div>
                                <div class="mt-1 text-sm font-black text-white">1080 × 1920 px</div>
                                <div class="mt-0.5 text-[10px] text-zinc-500">Formato vertical · relación 9:16</div>
                            </div>
                            <div class="rounded-xl border border-sky-500/20 bg-sky-500/[0.06] px-3 py-2.5">
                                <div class="text-[10px] font-black uppercase tracking-[.14em] text-sky-300">Inicio</div>
                                <div class="mt-1 text-sm font-black text-white">1200 × 450 px</div>
                                <div class="mt-0.5 text-[10px] text-zinc-500">Formato horizontal · banner</div>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-zinc-600">Medidas recomendadas. WEBP preferido para menor peso · máximo 4 MB.</p>
                    </div>
                    <div><label class="mb-2 block text-xs font-black uppercase tracking-[.18em] text-zinc-500">Enlace al tocar</label><input type="url" name="target_url" value="{{ old('target_url') }}" placeholder="https://..." class="w-full rounded-xl border border-white/10 bg-white/[0.04] px-4 py-3 text-white"></div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label class="mb-2 block text-xs font-black uppercase tracking-[.18em] text-zinc-500">Inicia</label><input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" class="w-full rounded-xl border border-white/10 bg-zinc-950 px-4 py-3 text-white"></div>
                    <div><label class="mb-2 block text-xs font-black uppercase tracking-[.18em] text-zinc-500">Termina</label><input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" class="w-full rounded-xl border border-white/10 bg-zinc-950 px-4 py-3 text-white"></div>
                </div>
                <input type="hidden" name="sort_order" value="0">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <label class="flex items-center gap-3"><input type="checkbox" name="is_active" value="1" checked class="rounded border-white/20 bg-zinc-900 text-orange-500"><span><span class="block text-sm font-bold text-white">Campaña activa</span><span class="block text-xs text-zinc-600">Se mostrará dentro de su periodo de vigencia.</span></span></label>
                    <button type="submit" class="admin-btn-primary justify-center sm:min-w-56">Crear campaña</button>
                </div>
            </form>
        </section>

        <section>
            <div class="mb-4 flex items-end justify-between gap-4">
                <div><div class="admin-kicker">Campañas</div><h2 class="mt-1 text-xl font-black text-white">Publicidad configurada</h2></div>
                <div class="text-xs text-zinc-600">{{ $campaigns->total() }} en total</div>
            </div>

            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
                @forelse($campaigns as $campaign)
                    @php
                        $now = now();
                        $isLive = $campaign->is_active && (!$campaign->starts_at || $campaign->starts_at <= $now) && (!$campaign->ends_at || $campaign->ends_at >= $now);
                        $isScheduled = $campaign->is_active && $campaign->starts_at && $campaign->starts_at > $now;
                        $statusLabel = $isLive ? 'Al aire' : ($isScheduled ? 'Programada' : 'Inactiva');
                        $statusClass = $isLive ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20' : ($isScheduled ? 'bg-sky-500/10 text-sky-300 border-sky-500/20' : 'bg-zinc-500/10 text-zinc-400 border-white/10');
                        $placementLabel = $campaign->placement === 'splash' ? 'Apertura' : 'Inicio';
                    @endphp

                    <article class="admin-panel p-3">
                        <div class="flex gap-3">
                            <div class="shrink-0">
                                <div class="flex h-24 w-16 items-center justify-center overflow-hidden rounded-lg border border-white/10 bg-black/40">
                                    <img src="{{ asset('storage/'.$campaign->image_path) }}" alt="{{ $campaign->name }}" class="h-full w-full object-cover">
                                </div>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap gap-1">
                                    <span class="rounded-full border px-1.5 py-0.5 text-[8px] font-black uppercase {{ $statusClass }}">{{ $statusLabel }}</span>
                                    <span class="rounded-full border border-orange-500/20 bg-orange-500/10 px-1.5 py-0.5 text-[8px] font-black uppercase text-orange-300">{{ $placementLabel }}</span>
                                </div>

                                <h3 class="mt-2 truncate text-sm font-black text-white" title="{{ $campaign->name }}">{{ $campaign->name }}</h3>
                                <p class="mt-0.5 truncate text-[10px] text-zinc-500">{{ $campaign->advertiser ?: 'Sin anunciante' }}</p>
                                <p class="truncate text-[10px] text-zinc-600">{{ $campaign->station?->name ?: 'Ambas estaciones' }}</p>

                                <div class="mt-3 grid grid-cols-3 divide-x divide-white/10">
                                    <div class="pr-1.5"><div class="text-xs font-black text-white">{{ number_format($campaign->impressions) }}</div><div class="text-[8px] uppercase text-zinc-600">Imp.</div></div>
                                    <div class="px-1.5"><div class="text-xs font-black text-white">{{ number_format($campaign->clicks) }}</div><div class="text-[8px] uppercase text-zinc-600">Clics</div></div>
                                    <div class="pl-1.5"><div class="text-xs font-black text-white">{{ number_format($campaign->ctr,2) }}%</div><div class="text-[8px] uppercase text-zinc-600">CTR</div></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between gap-2 border-t border-white/10 pt-2.5">
                            <div class="truncate text-[9px] text-zinc-600">
                                @if($campaign->starts_at || $campaign->ends_at)
                                    {{ $campaign->starts_at?->format('d/m/y') ?? 'Ahora' }} → {{ $campaign->ends_at?->format('d/m/y') ?? 'Sin fin' }}
                                @else
                                    Sin periodo definido
                                @endif
                            </div>
                            <form method="POST" action="{{ route('admin.ads.destroy',$campaign) }}" onsubmit="return confirm('¿Eliminar esta campaña?')">@csrf @method('DELETE')<button class="text-[9px] font-bold text-red-300 hover:text-red-200">Eliminar</button></form>
                        </div>

                        <details class="mt-2 rounded-lg border border-white/10 bg-black/20">
                            <summary class="cursor-pointer px-3 py-2 text-[10px] font-bold text-zinc-300">Editar campaña</summary>
                            <form method="POST" action="{{ route('admin.ads.update',$campaign) }}" enctype="multipart/form-data" class="space-y-2 border-t border-white/10 p-3">@csrf @method('PUT')
                                <input name="name" value="{{ $campaign->name }}" required class="w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-xs text-white">
                                <input name="advertiser" value="{{ $campaign->advertiser }}" placeholder="Anunciante" class="w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-xs text-white">
                                <select name="station_id" class="w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-xs text-white"><option value="">Ambas estaciones</option>@foreach($stations as $station)<option value="{{ $station->id }}" @selected($campaign->station_id===$station->id)>{{ $station->name }}</option>@endforeach</select>
                                <select name="placement" class="w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-xs text-white"><option value="splash" @selected($campaign->placement==='splash')>Pantalla completa al abrir</option><option value="home" @selected($campaign->placement==='home')>Banner dentro de Inicio</option></select>
                                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-[10px] text-zinc-300">
                                <input type="url" name="target_url" value="{{ $campaign->target_url }}" placeholder="https://..." class="w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-xs text-white">
                                <div class="grid gap-2"><input type="datetime-local" name="starts_at" value="{{ $campaign->starts_at?->format('Y-m-d\TH:i') }}" class="w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-[10px] text-white"><input type="datetime-local" name="ends_at" value="{{ $campaign->ends_at?->format('Y-m-d\TH:i') }}" class="w-full rounded-lg border border-white/10 bg-zinc-950 px-3 py-2 text-[10px] text-white"></div>
                                <input type="hidden" name="sort_order" value="{{ $campaign->sort_order }}">
                                <div class="flex items-center justify-between"><label class="flex items-center gap-2 text-[10px] font-bold text-zinc-300"><input type="checkbox" name="is_active" value="1" @checked($campaign->is_active)> Activa</label><button class="admin-btn-primary">Guardar</button></div>
                            </form>
                        </details>
                    </article>
                @empty
                    <div class="admin-panel px-6 py-16 text-center md:col-span-2 lg:col-span-3 2xl:col-span-4"><div class="text-4xl">📢</div><div class="mt-3 font-black text-white">Aún no hay campañas</div><p class="mt-1 text-sm text-zinc-600">Crea el primer anuncio para comenzar.</p></div>
                @endforelse
            </div>
            <div class="mt-6">{{ $campaigns->links() }}</div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const placement = document.getElementById('placement');
            const help = document.getElementById('placement-help');
            const updateHelp = () => {
                if (!placement || !help) return;
                help.textContent = placement.value === 'splash'
                    ? 'Pantalla completa · recomendado 1080 × 1920 px (9:16) · 5 segundos una vez cargada.'
                    : 'Banner de Inicio · recomendado 1200 × 450 px · carrusel debajo de las estaciones y antes de Explora.';
            };
            placement?.addEventListener('change', updateHelp);
            updateHelp();
        });
    </script>
</x-app-layout>
