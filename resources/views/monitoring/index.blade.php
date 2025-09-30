<x-head></x-head>

<style>
    /* 🔹 Fix tinggi tiap slide supaya nggak naik turun */
    #menuCarousel .carousel-item {
        min-height: 100vh; /* fullscreen tinggi, bisa diganti 800px sesuai kebutuhan */
    }

    /* 🔹 Paksa tombol navigasi selalu di tengah */
    #menuCarousel .carousel-control-prev,
    #menuCarousel .carousel-control-next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        height: auto;
    }

    /* 🔹 Ensure cards are evenly spaced and full height */
    .card {
        border: 1px solid #ddd;
    }
    .card-header {
        background: linear-gradient(90deg, #dc3545, #c82333); /* Custom gradient for consistency */
    }
    .overflow-auto {
        overflow-y: auto;
    }
</style>

<div class="container-fluid"
     @if(request()->is('asakai'))
         style="transform: scale(0.6); transform-origin: top left; width: 166%;"
     @endif
>

    <div id="menuCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="20000">
        <div class="carousel-inner">

            <!-- 🔹 Slide 1 -->
            <div class="carousel-item active">
                <div class="row bg-danger p-0 align-items-center">
                    <h1 class="col-8 mb-0 text-white">
                        Final Inspection Finding - {{ \Carbon\Carbon::now()->format('F Y') }}
                    </h1>
                    <div class="col-4 d-flex justify-content-end align-items-center" style="padding-right: 40px;">
                        <img class="bg-white p-1 rounded" src="{{ asset('img/kyb.png') }}" alt="KYB Logo" style="height:40px;">
                    </div>
                </div>

                @include('monitoring.chart')
                @include('monitoring.stats')
            </div>

            <!-- 🔹 Slide 2 -->
            <div class="carousel-item">
                <div class="row bg-danger p-0 align-items-center">
                    <h1 class="col-9 mb-0 text-white">Pareto FI Finding by Problem - {{ \Carbon\Carbon::now()->format('F Y') }}
                    </h1>
                    <div class="col-3 text-right d-flex justify-content-end align-items-center" style="padding-right: 40px;">

                        <img class="bg-white p-1 rounded" src="{{ asset('img/kyb.png') }}" alt="KYB Logo" style="height:40px;">
                    </div>
                </div>
                @include('monitoring.pareto')

                <div class="row mt-3">
                    @include('monitoring.pareto_table')
                    @include('monitoring.pareto_pie')
                    @include('monitoring.pareto_list')
                </div>
            </div>
            <!-- 🔹 Slide 3 -->
            <div class="carousel-item">
                <div class="row bg-danger p-0 align-items-center">
                    <h1 class="col-9 mb-0 text-white">Pareto FI Finding by LINE - {{ \Carbon\Carbon::now()->format('F Y') }}
                    </h1>
                    <div class="col-3 text-right d-flex justify-content-end align-items-center" style="padding-right: 40px;">

                        <img class="bg-white p-1 rounded" src="{{ asset('img/kyb.png') }}" alt="KYB Logo" style="height:40px;">
                    </div>
                </div>
                @include('monitoring.pareto_assembling')
                @include('monitoring.detail_pareto')


            </div>

        </div>

        <!-- 🔹 Tombol Navigasi -->
        <button class="carousel-control-prev" type="button" data-bs-target="#menuCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#menuCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>


<x-script></x-script>

