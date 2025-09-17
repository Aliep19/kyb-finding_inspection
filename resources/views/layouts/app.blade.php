<!DOCTYPE html>
<html lang="en">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    {{-- Sidebar --}}
    <x-sidebar></x-sidebar>

    {{-- Main Content --}}
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">

        {{-- Navbar --}}
        <x-navbar>{{ $title ?? 'Dashboard' }}</x-navbar>

        <div class="container-fluid py-4">
            {{-- Konten halaman --}}
            @yield('content')

            {{-- Footer --}}
            <x-footer></x-footer>
        </div>
    </main>

    <x-script></x-script>

    {{-- Tambahin script reusable untuk semua tabel --}}
    <script src="{{ asset('js/table-control.js') }}"></script>

    {{-- ini supaya script dari @push('scripts') di view bisa tampil --}}
    @stack('scripts')
</body>
</html>
