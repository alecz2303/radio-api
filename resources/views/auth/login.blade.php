<x-guest-layout>
    <main class="relative min-h-screen overflow-hidden bg-zinc-950">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_18%_20%,rgba(249,115,22,.16),transparent_30%),radial-gradient(circle_at_82%_25%,rgba(249,115,22,.10),transparent_24%),linear-gradient(135deg,#050505_0%,#0d0d0d_48%,#050505_100%)]"></div>
            <div class="absolute left-0 top-0 h-full w-full opacity-30" style="background-image:linear-gradient(rgba(255,255,255,.018) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.018) 1px,transparent 1px);background-size:42px 42px"></div>
            <div class="absolute -left-20 bottom-[-120px] h-80 w-[48rem] rotate-[-8deg] rounded-[4rem] border border-orange-500/10 bg-gradient-to-tr from-orange-500/10 via-zinc-900/30 to-transparent blur-sm"></div>
            <div class="absolute bottom-12 left-0 right-0 flex h-24 items-end gap-1.5 px-10 opacity-25">
                @foreach([18,42,28,64,38,78,30,50,26,68,44,82,34,58,24,72,40,88,32,54,22,70,36,80,28,62,46,76,34,56] as $height)
                    <span class="block flex-1 rounded-full bg-orange-500" style="height: {{ $height }}%"></span>
                @endforeach
            </div>
        </div>

        <div class="relative mx-auto grid min-h-screen max-w-7xl gap-12 px-6 py-8 lg:grid-cols-[1.08fr_.92fr] lg:items-center lg:px-10 xl:px-4">
            <section class="hidden lg:block">
                <div class="mb-14 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-orange-500/30 bg-orange-500/10 text-2xl text-orange-400">◫</div>
                    <div class="text-3xl font-light tracking-tight text-white">RADIO <span class="font-black text-orange-500">API</span></div>
                </div>

                <div class="max-w-2xl">
                    <div class="mb-5 text-xs font-bold uppercase tracking-[.45em] text-orange-200/80">Panel de administración</div>
                    <h1 class="text-6xl font-black leading-[.95] tracking-tight text-white xl:text-7xl">
                        Bienvenido a<br><span class="text-orange-500">Radio API</span>
                    </h1>
                    <p class="mt-7 max-w-xl text-xl leading-relaxed text-zinc-300">Gestiona tus estaciones, controla la transmisión y conecta con tu audiencia desde un solo lugar.</p>
                </div>

                <div class="mt-10 grid max-w-2xl grid-cols-2 gap-4 xl:grid-cols-4">
                    @foreach([
                        ['🎵','Solicitudes','de canciones'],
                        ['📡','Estaciones','y canales'],
                        ['🎙️','Operación','en vivo'],
                        ['⚙️','Control','centralizado'],
                    ] as [$icon,$title,$subtitle])
                        <div class="rounded-2xl border border-orange-500/20 bg-white/[.035] p-4 backdrop-blur-sm">
                            <div class="mb-3 text-2xl">{{ $icon }}</div>
                            <div class="text-sm font-bold text-white">{{ $title }}</div>
                            <div class="text-xs text-zinc-500">{{ $subtitle }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10 text-2xl font-semibold italic text-white/85">La radio que te <span class="text-orange-500">conecta.</span></div>
            </section>

            <section class="flex items-center justify-center">
                <div class="w-full max-w-xl rounded-[2rem] border border-white/10 bg-zinc-900/80 p-7 shadow-2xl shadow-black/40 backdrop-blur-xl sm:p-10">
                    <div class="mb-8 lg:hidden">
                        <div class="text-2xl font-light text-white">RADIO <span class="font-black text-orange-500">API</span></div>
                    </div>

                    <div class="mb-8">
                        <div class="text-xs font-bold uppercase tracking-[.32em] text-orange-400">Acceso seguro</div>
                        <h2 class="mt-3 text-4xl font-black tracking-tight text-white">Iniciar sesión</h2>
                        <p class="mt-2 text-zinc-400">Ingresa tus credenciales para continuar.</p>
                    </div>

                    <x-auth-session-status class="mb-5 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-zinc-300">Correo electrónico</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3.5 text-white placeholder-zinc-600 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                                placeholder="admin@radio.com">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-zinc-300">Contraseña</label>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="w-full rounded-xl border border-white/10 bg-black/30 px-4 py-3.5 text-white placeholder-zinc-600 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20"
                                placeholder="••••••••">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between gap-4 text-sm">
                            <label for="remember_me" class="inline-flex items-center gap-2 text-zinc-400">
                                <input id="remember_me" type="checkbox" class="rounded border-white/20 bg-black/40 text-orange-500 focus:ring-orange-500" name="remember">
                                <span>Recordarme</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="font-semibold text-orange-400 transition hover:text-orange-300" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                            @endif
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-orange-600 to-orange-400 px-5 py-3.5 text-base font-black text-white shadow-lg shadow-orange-950/30 transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 focus:ring-offset-zinc-900">
                            Iniciar sesión →
                        </button>
                    </form>

                    <div class="mt-8 border-t border-white/10 pt-6 text-center">
                        <div class="text-sm text-zinc-500">Más que música, somos conexión.</div>
                        <div class="mt-6 text-xs text-zinc-600">© {{ date('Y') }} Radio API. Acceso administrativo.</div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</x-guest-layout>
