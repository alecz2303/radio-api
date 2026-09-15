<x-app-layout>
    <x-slot name="header"><div><div class="admin-kicker">Concursos y dinámicas</div><h1 class="admin-title">{{ $contest->title }}</h1><p class="admin-subtitle">Participantes, ganadores y control de entrega de premios.</p></div></x-slot>
    <div class="admin-container py-7">
        <div class="mb-5"><a href="{{ route('admin.contests.index') }}" class="text-sm font-bold text-orange-300">← Volver a dinámicas</a></div>
        @if(session('success'))<div class="mb-5 rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="mb-5 rounded-2xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-200">{{ session('error') }}</div>@endif
        <div class="admin-panel overflow-x-auto"><table class="w-full text-left text-sm">
            <thead class="border-b border-white/10 text-[10px] uppercase tracking-wider text-zinc-500"><tr><th class="p-4">Participante</th><th class="p-4">Respuesta</th><th class="p-4">Resultado</th><th class="p-4">Folio</th><th class="p-4">Aviso</th><th class="p-4">Premio</th></tr></thead>
            <tbody class="divide-y divide-white/10">@forelse($entries as $entry)<tr>
                <td class="p-4 text-white"><b>{{ $entry->listener_name ?: 'Oyente' }}</b><div class="text-xs text-zinc-600">{{ $entry->phone ?: 'Sin teléfono' }}</div></td>
                <td class="p-4 text-zinc-300">{{ $entry->selected_option }}</td>
                <td class="p-4">@if($entry->is_winner)<span class="font-black text-orange-300">GANADOR</span>@elseif($entry->is_correct)<span class="text-emerald-300">Correcta</span>@else<span class="text-zinc-500">Incorrecta</span>@endif</td>
                <td class="p-4 font-mono text-xs text-white">{{ $entry->claim_code ?: '—' }}</td>
                <td class="p-4">@if($entry->is_winner) @if($entry->push_token)<form method="POST" action="{{ route('admin.contests.entries.notify-winner',$entry) }}">@csrf<button class="text-xs font-black text-orange-300">{{ $entry->winner_notified_at ? 'Reenviar push' : 'Enviar aviso al ganador' }}</button></form>@if($entry->winner_notified_at)<div class="mt-1 text-[10px] text-zinc-600">Enviado {{ $entry->winner_notified_at->format('d/m H:i') }}</div>@endif @else<span class="text-xs text-zinc-600">App anterior / sin token</span>@endif @else<span class="text-zinc-600">—</span>@endif</td>
                <td class="p-4">@if($entry->is_winner) @if($entry->claimed_at)<span class="font-bold text-emerald-300">Entregado {{ $entry->claimed_at->format('d/m H:i') }}</span>@else<form method="POST" action="{{ route('admin.contests.entries.claim',$entry) }}">@csrf @method('PATCH')<button class="admin-btn-primary">Marcar entregado</button></form>@endif @else<span class="text-zinc-600">—</span>@endif</td>
            </tr>@empty<tr><td colspan="6" class="p-12 text-center text-zinc-500">Aún no hay participantes.</td></tr>@endforelse</tbody>
        </table></div><div class="mt-5">{{ $entries->links() }}</div>
    </div>
</x-app-layout>
