{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Finca Gonzo')</title>

    {{-- Tu build de Tailwind --}}
    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    {{-- JS de Bootstrap (dropdowns, etc.) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- =========================================================
         Mostrar NAVBAR/FOOTER solo si NO estamos en auth pages
         (login, register, password.*)
         ========================================================= --}}
    @php
        $esAuthPage = request()->routeIs('login') ||
                      request()->routeIs('register') ||
                      request()->routeIs('password.*');
    @endphp

    @unless($esAuthPage)
    {{-- ============================ NAVBAR ============================ --}}
    <nav class="bg-green-600 text-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex h-16 items-center justify-between">

                {{-- Flechas + Brand --}}
                <div class="flex items-center gap-3">
                    <button onclick="history.back()" class="text-white hover:opacity-80" title="Atrás">
                        <i class="bi bi-arrow-left-circle-fill fs-4"></i>
                    </button>
                    <button onclick="history.forward()" class="text-white hover:opacity-80" title="Adelante">
                        <i class="bi bi-arrow-right-circle-fill fs-4"></i>
                    </button>

                    {{-- Brand -> Dashboard --}}
                    <a href="{{ route('dashboard') }}" class="font-bold text-lg hover:opacity-90">
                        Finca Gonzo
                    </a>
                </div>

                {{-- MENÚ DESKTOP --}}
                <ul class="hidden md:flex items-center gap-4 mb-0">
                    <li>
                        <a href="{{ route('clientes.index') }}"
                           class="px-2 py-1 rounded hover:bg-green-700 {{ request()->routeIs('clientes.*') ? 'bg-green-700' : '' }}">
                            Clientes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('productos.index') }}"
                           class="px-2 py-1 rounded hover:bg-green-700 {{ request()->routeIs('productos.*') ? 'bg-green-700' : '' }}">
                            Productos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('entregas.index') }}"
                           class="px-2 py-1 rounded hover:bg-green-700 {{ request()->routeIs('entregas.*') ? 'bg-green-700' : '' }}">
                            Entregas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('facturas.index') }}"
                           class="px-2 py-1 rounded hover:bg-green-700 {{ request()->routeIs('facturas.*') ? 'bg-green-700' : '' }}">
                            Facturas
                        </a>
                    </li>

                    {{-- Solo ADMIN: Usuarios --}}
                    @auth
                        @if(auth()->user()->rol === 'admin')
                            <li>
                                <a href="{{ route('admin.users.index') }}"
                                   class="px-2 py-1 rounded hover:bg-green-700 {{ request()->routeIs('admin.users.*') ? 'bg-green-700' : '' }}">
                                    <i class="bi bi-people me-1"></i> Usuarios
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                {{-- DERECHA: perfil / auth links + hamburguesa --}}
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        {{-- Dropdown del usuario --}}
                        <div class="dropdown">
                            <a class="text-white text-decoration-none dropdown-toggle d-flex align-items-center"
                               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-2"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li class="px-3 py-2 text-muted small">{{ auth()->user()->email }}</li>
                                <li><hr class="dropdown-divider"></li>
                                <li class="px-3 py-2">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="btn btn-danger w-100">
                                            <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        {{-- Invitados --}}
                        <a class="px-2 py-1 rounded hover:bg-green-700" href="{{ route('login') }}">Entrar</a>
                        <a class="px-2 py-1 rounded hover:bg-green-700" href="{{ route('register') }}">Registrarse</a>
                    @endauth
                </div>

                {{-- Botón hamburguesa (móvil) --}}
                <button id="navToggle"
                        class="md:hidden inline-flex items-center p-2 rounded hover:bg-green-700 focus:outline-none">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- MENÚ MÓVIL --}}
        <div id="mobileMenu" class="md:hidden hidden border-t border-green-500">
            <div class="px-4 py-3 space-y-2">
                <a href="{{ route('clientes.index') }}"
                   class="block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('clientes.*') ? 'bg-green-700' : '' }}">
                    Clientes
                </a>
                <a href="{{ route('productos.index') }}"
                   class="block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('productos.*') ? 'bg-green-700' : '' }}">
                    Productos
                </a>
                <a href="{{ route('entregas.index') }}"
                   class="block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('entregas.*') ? 'bg-green-700' : '' }}">
                    Entregas
                </a>
                <a href="{{ route('facturas.index') }}"
                   class="block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('facturas.*') ? 'bg-green-700' : '' }}">
                    Facturas
                </a>

                {{-- Solo ADMIN en móvil: Usuarios --}}
                @auth
                    @if(auth()->user()->rol === 'admin')
                        <a href="{{ route('admin.users.index') }}"
                           class="block px-3 py-2 rounded hover:bg-green-700 {{ request()->routeIs('admin.users.*') ? 'bg-green-700' : '' }}">
                            <i class="bi bi-people me-1"></i> Usuarios
                        </a>
                    @endif
                @endauth

                <hr class="border-green-500">

                {{-- Acceso / Cierre (móvil) --}}
                @auth
                    <form action="{{ route('logout') }}" method="POST" class="px-3">
                        @csrf
                        <button class="btn btn-danger w-100">
                            <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
                        </button>
                    </form>
                @else
                    <div class="flex gap-2">
                        <a class="btn btn-light flex-1" href="{{ route('login') }}">Entrar</a>
                        <a class="btn btn-outline-light flex-1" href="{{ route('register') }}">Registrarse</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>
    @endunless

    {{-- ============================ CONTENIDO ============================ --}}
    <main class="flex-grow max-w-7xl mx-auto w-full p-6">
        @yield('content')
    </main>

    @unless($esAuthPage)
    {{-- ============================ FOOTER ============================ --}}
    <footer class="bg-green-700 text-white p-4 text-center">
        &copy; {{ date('Y') }} Finca Gonzo - Todos los derechos reservados
    </footer>
    @endunless

    {{-- Toggle menú móvil --}}
    <script>
        document.getElementById('navToggle')?.addEventListener('click', () => {
            const m = document.getElementById('mobileMenu');
            m.classList.toggle('hidden');
        });
    </script>
</body>
</html>
