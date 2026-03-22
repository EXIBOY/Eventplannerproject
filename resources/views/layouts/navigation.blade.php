<nav x-data="{ open: false }" class="px-4 pt-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl rounded-[24px] border border-slate-900/10 bg-slate-950/90 px-4 py-3 text-white shadow-[0_24px_60px_rgba(15,23,42,0.35)] backdrop-blur sm:rounded-[30px] sm:px-6 sm:py-4 sm:shadow-[0_30px_80px_rgba(15,23,42,0.35)]">
        <div class="flex items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-3 sm:gap-4">
                <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex min-w-0 items-center gap-3">
                    <x-application-logo class="h-10 w-10 sm:h-11 sm:w-11" />
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-orange-300 sm:text-[11px] sm:tracking-[0.32em]">Event Planner</p>
                        <p class="truncate text-base text-white sm:text-xl">Event Planner</p>
                    </div>
                </a>

                @auth
                    <div class="hidden md:flex md:items-center md:gap-2">
                        <a href="{{ route('dashboard') }}"
                           class="{{ request()->routeIs('dashboard') ? 'bg-white text-slate-950' : 'text-white/70 hover:bg-white/10 hover:text-white' }} rounded-full px-4 py-2 text-sm font-semibold transition">
                            Dashboard
                        </a>
                        <a href="{{ route('events.index') }}"
                           class="{{ request()->routeIs('events.*') ? 'bg-white text-slate-950' : 'text-white/70 hover:bg-white/10 hover:text-white' }} rounded-full px-4 py-2 text-sm font-semibold transition">
                            Events
                        </a>
                    </div>
                @else
                    <div class="hidden md:flex md:items-center md:gap-2">
                        <a href="{{ route('home') }}"
                           class="{{ request()->routeIs('home') ? 'bg-white text-slate-950' : 'text-white/70 hover:bg-white/10 hover:text-white' }} rounded-full px-4 py-2 text-sm font-semibold transition">
                            Home
                        </a>
                    </div>
                @endauth
            </div>

            <div class="hidden md:flex md:items-center md:gap-3">
                @auth
                    <a href="{{ route('events.create') }}" class="rounded-full bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-orange-400">
                        New Event
                    </a>

                    <div class="text-right">
                        <p class="text-[11px] uppercase tracking-[0.28em] text-white/45">Signed in</p>
                        <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="rounded-full border border-white/15 px-4 py-2 text-sm font-semibold text-white/80 transition hover:border-white/25 hover:bg-white/10 hover:text-white">
                        Profile
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-white/15 px-4 py-2 text-sm font-semibold text-white/80 transition hover:border-white/25 hover:bg-white/10 hover:text-white">
                            Log Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-full border border-white/15 px-4 py-2 text-sm font-semibold text-white/80 transition hover:border-white/25 hover:bg-white/10 hover:text-white">
                        Log In
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-full bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-orange-400">
                            Start Planning
                        </a>
                    @endif
                @endauth
            </div>

            <button @click="open = ! open" class="inline-flex items-center justify-center rounded-full border border-white/15 p-2 text-white transition hover:bg-white/10 md:hidden">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div :class="{'block': open, 'hidden': ! open}" class="hidden pt-4 md:hidden">
            <div class="soft-rule"></div>

            <div class="mt-4 space-y-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('dashboard') ? 'bg-white text-slate-950' : 'bg-white/5 text-white/80' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('events.index') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('events.*') ? 'bg-white text-slate-950' : 'bg-white/5 text-white/80' }}">
                        Events
                    </a>
                    <a href="{{ route('events.create') }}" class="block rounded-2xl bg-orange-500 px-4 py-3 text-sm font-semibold text-white">
                        New Event
                    </a>
                    <a href="{{ route('profile.edit') }}" class="block rounded-2xl bg-white/5 px-4 py-3 text-sm font-semibold text-white/80">
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full rounded-2xl bg-white/5 px-4 py-3 text-left text-sm font-semibold text-white/80">
                            Log Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('home') }}" class="block rounded-2xl px-4 py-3 text-sm font-semibold {{ request()->routeIs('home') ? 'bg-white text-slate-950' : 'bg-white/5 text-white/80' }}">
                        Home
                    </a>
                    <a href="{{ route('login') }}" class="block rounded-2xl bg-white/5 px-4 py-3 text-sm font-semibold text-white/80">
                        Log In
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="block rounded-2xl bg-orange-500 px-4 py-3 text-sm font-semibold text-white">
                            Start Planning
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>
