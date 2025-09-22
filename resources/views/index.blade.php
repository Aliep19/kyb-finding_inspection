<!-- views/index.blade.php -->
<!DOCTYPE html>
<html lang="en">

<x-head></x-head>

<body class="g-sidenav-show bg-gray-100">
    <x-sidebar></x-sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <!-- Navbar -->
        @include('components.navbar', ['slot' => 'Dashboard'])

        <div class="container-fluid py-4">
            <div class="row g-4">
                    <!-- KPI -->
    <div class="col-lg-12">
        @include('components.kpi', ['comparison' => $comparison])
    </div>
                <!-- Chart Defect (1/2 lebar) -->
                <div class="col-lg-6 col-md-12">
                    @include('layouts.chart-defect')
                </div>
                <!-- Chart Painting (full row) -->
                <div class="col-lg-6 col-md-12">
                    @include('layouts.chart-painting')
                </div>
                <!-- Chart Ratio (1/2 lebar) -->
                <div class="col-lg-12 ">
                    @include('layouts.chart-ratio')
                </div>
                <div class="col-lg-12 ">
                    @include('layouts.pareto-findings')
                </div>


            </div>

            <x-footer></x-footer>
        </div>
    </main>
    <x-script></x-script>
</body>
</html>
