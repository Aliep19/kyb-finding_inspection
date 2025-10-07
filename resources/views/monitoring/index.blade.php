<x-head></x-head>

<style>
    /* 🔹 Fix tinggi tiap slide supaya nggak naik turun */
    #menuCarousel .carousel-item {
        min-height: 100vh;
    }

    /* 🔹 Tombol navigasi tetap di tengah secara vertikal dan nempel di pinggir */
    #menuCarousel .carousel-control-prev,
    #menuCarousel .carousel-control-next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        height: auto;
        width: 5%; /* lebih kecil biar gak nutup elemen */
        z-index: 1000;
    }

    #menuCarousel .carousel-control-prev {
        left: 0; /* nempel di kiri layar */
    }

    #menuCarousel .carousel-control-next {
        right: 0; /* nempel di kanan layar */
    }

    /* 🔹 Styling icon navigasi */
    #menuCarousel .carousel-control-prev-icon,
    #menuCarousel .carousel-control-next-icon {
        background-color: rgba(0, 0, 0, 0.6);
        border-radius: 50%;
        padding: 12px;
    }

    /* 🔹 Hilangkan shadow hitam di area tombol biar bersih */
    #menuCarousel .carousel-control-prev,
    #menuCarousel .carousel-control-next {
        background: none;
    }

    /* 🔹 Hover efek biar tombol lebih jelas */
    #menuCarousel .carousel-control-prev:hover .carousel-control-prev-icon,
    #menuCarousel .carousel-control-next:hover .carousel-control-next-icon {
        background-color: rgba(220, 53, 69, 0.8); /* warna merah KYB */
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
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    {{-- Header Card --}}
                    <div class="row bg-danger p-0 align-items-center mx-0" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                        <h1 class="col-8 mb-0 text-white ps-4 py-3">
                            Final Inspection Finding - {{ \Carbon\Carbon::now()->format('F Y') }}
                        </h1>
                        <div class="col-4 d-flex justify-content-end align-items-center pe-4">
                            <img class="bg-white p-1 rounded shadow-sm" src="{{ asset('img/kyb.png') }}" alt="KYB Logo" style="height:40px;">
                        </div>
                    </div>
                </div>
                @include('monitoring.chart')
                @include('monitoring.stats')
            </div>

            <!-- 🔹 Slide 2 -->
            <div class="carousel-item">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    {{-- Header Card --}}
                    <div class="row bg-danger p-0 align-items-center mx-0" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                                    <h1 class="col-8 mb-0 text-white ps-4 py-3">
                                        Pareto FI Finding by Problem - {{ \Carbon\Carbon::now()->format('F Y') }}
                                    </h1>
                        <div class="col-4 d-flex justify-content-end align-items-center pe-4">
                            <img class="bg-white p-1 rounded shadow-sm" src="{{ asset('img/kyb.png') }}" alt="KYB Logo" style="height:40px;">
                        </div>
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
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                    {{-- Header Card --}}
                    <div class="row bg-danger p-0 align-items-center mx-0" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                            <h1 class="col-8 mb-0 text-white ps-4 py-3">
                                Pareto FI Finding by LINE - {{ \Carbon\Carbon::now()->format('F Y') }}
                            </h1>
                        <div class="col-4 d-flex justify-content-end align-items-center pe-4">
                            <img class="bg-white p-1 rounded shadow-sm" src="{{ asset('img/kyb.png') }}" alt="KYB Logo" style="height:40px;">
                        </div>
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

<script>
    setInterval(function() {
        location.reload();
    }, 600000);
</script>

<x-script></x-script>

