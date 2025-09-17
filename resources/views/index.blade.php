<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <!-- Navbar -->
        @include('components.navbar', ['slot' => 'Dashboard'])

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12 mb-4">
                    @include('layouts.chart-defect')
                </div>

            </div>

            <x-footer></x-footer>
        </div>
    </main>
    <x-script></x-script>


</body>
</html>
