<!DOCTYPE html>
<html lang="es">

<head>

    {{-- Bootstrap 5 (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Finca Gonzo')</title>
    @vite(['resources/css/app.css'])
</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-green-600 text-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex h-16 items-center justify-between">

                <!-- Flechas + Brand -->
                <div class="flex items-center gap-3">
                    <!-- Flecha atrás -->
                    <button onclick="history.back()" class="text-white hover:opacity-80">
                        <i class="bi bi-arrow-left-circle-fill fs-4"></i>
                    </button>
                    <!-- Flecha adelante -->
                    <button onclick="history.forward()" class="text-white hover:opacity-80">
                        <i class="bi bi-arrow-right-circle-fill fs-4"></i>
                    </button>
                    <!-- Brand -> Dashboard -->
                    <a href="{{ route('dashboard') }}" class="font-bold text-lg hover:opacity-90">
                        Finca Gonzo
                    </a>
                </div>

                <!-- Desktop menu -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('clientes.index') }}"
                        class="px-2 py-1 rounded hover:bg-green-700 {{ request()->routeIs('clientes.*') ? 'bg-green-700' : '' }}">
                        Clientes
                    </a>
                    <a href="{{ route('productos.index') }}"
                        class="px-2 py-1 rounded hover:bg-green-700 {{ request()->routeIs('productos.*') ? 'bg-green-700' : '' }}">
                        Productos
                    </a>
                    <a href="{{ route('entregas.index') }}"
                        class="px-2 py-1 rounded hover:bg-green-700 {{ request()->routeIs('entregas.*') ? 'bg-green-700' : '' }}">
                        Entregas
                    </a>
                    <a href="{{ route('facturas.index') }}"
                        class="px-2 py-1 rounded hover:bg-green-700 {{ request()->routeIs('facturas.*') ? 'bg-green-700' : '' }}">
                        Facturas
                    </a>
                </div>

                <!-- Mobile button -->
                <button id="navToggle" class="md:hidden inline-flex items-center p-2 rounded hover:bg-green-700 focus:outline-none">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
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
            </div>
        </div>
    </nav>

    <!-- Contenido -->
    <main class="flex-grow max-w-7xl mx-auto w-full p-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-green-700 text-white p-4 text-center">
        &copy; {{ date('Y') }} Finca Gonzo - Todos los derechos reservados
    </footer>

    <!-- Tiny JS para el menú móvil -->
    <script>
        document.getElementById('navToggle')?.addEventListener('click', () => {
            const m = document.getElementById('mobileMenu');
            m.classList.toggle('hidden');
        });
    </script>
</body>

</html>
