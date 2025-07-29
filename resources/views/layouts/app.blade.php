<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Finca Gonzo')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-green-600 p-4 text-white flex justify-between items-center">
        <h1 class="text-xl font-bold">Finca Gonzo</h1>
        <div>
            <a href="{{ route('clientes.index') }}" class="px-3">Clientes</a>
            <a href="{{ route('productos.index') }}" class="px-3">Productos</a>
            <a href="{{ route('entregas.index') }}" class="px-3">Entregas</a>
            <a href="{{ route('facturas.index') }}" class="px-3">Facturas</a>
        </div>
    </nav>

    <!-- Contenido -->
    <main class="flex-grow container mx-auto p-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-green-700 text-white p-4 text-center">
        &copy; {{ date('Y') }} Finca Gonzo - Todos los derechos reservados
    </footer>
</body>
</html>
