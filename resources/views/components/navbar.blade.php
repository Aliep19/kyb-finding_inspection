<link rel="stylesheet" href="{{ asset('css/navbar.css') }}?v={{ filemtime(public_path('css/navbar.css')) }}">

<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <div class="row w-100 align-items-center">
            <!-- Breadcrumb (kiri) -->
            <div class="col-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
                        @php $segments = Request::segments(); @endphp
                        <li class="breadcrumb-item text-sm">
                            <a class="opacity-5 text-dark" href="{{ url('/dashboard') }}">Home</a>
                        </li>
                        @foreach($segments as $index => $segment)
                            @php
                                $url = url(implode('/', array_slice($segments, 0, $index + 1)));
                                $isLast = $loop->last;
                            @endphp
                            <li class="breadcrumb-item text-sm {{ $isLast ? 'text-dark active' : '' }}"
                                aria-current="{{ $isLast ? 'page' : '' }}">
                                @if(!$isLast)
                                    <a class="text-dark" href="{{ $url }}">{{ ucfirst($segment) }}</a>
                                @else
                                    {{ ucfirst($segment) }}
                                @endif
                            </li>
                        @endforeach
                    </ol>
                    <h6 class="font-weight-bolder mb-0">{{ $slot }}</h6>
                </nav>
            </div>

            <!-- Hari, Tanggal, Bulan, Tahun + Jam (tengah) -->
            <div class="col-lg-4 col-md-6 col-sm-12 d-flex justify-content-center my-2">
                <div class="date-time date-time-loading">
                    <span class="date" id="current-date">Loading...</span>
                    <span class="time" id="current-time">--:--:--</span>
                </div>
            </div>

            <!-- User Info (kanan) -->
<div class="col-4 d-flex justify-content-end align-items-center">
    @if (Session::has('authenticated'))
        @php
            $user = App\Models\Lembur\CtUser::where('npk', Session::get('user_npk'))->first();
        @endphp
        @if ($user)
            <div class="user-info d-flex align-items-center">
                <div class="avatar"
                     style="width: 40px; height: 40px; background-color: #dc3545; color: white;
                            border-radius: 50%; display: flex; align-items: center;
                            justify-content: center; font-size: 1.2rem;">
                    <i class="fa fa-user"></i>
                </div>
                <div class="d-none d-sm-block ms-2">
                    <span class="fw-bold">{{ $user->full_name ?? 'User' }}</span><br>
                    <small class="text-muted">{{ $user->dept ?? 'N/A' }}</small>
                </div>
            </div>
        @else
            <div class="user-info d-flex align-items-center">
                <span class="text-muted">Data pengguna tidak ditemukan</span>
            </div>
        @endif
    @else
        <div class="user-info d-flex align-items-center">
            <span class="text-muted">Belum login</span>
        </div>
    @endif
</div>
        </div>
    </div>
</nav>

<script>
    function updateDateTime() {
        const days = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
        const months = [
            "Januari","Februari","Maret","April","Mei","Juni",
            "Juli","Agustus","September","Oktober","November","Desember"
        ];

        const now = new Date();
        const day = days[now.getDay()];
        const date = now.getDate();
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        const time = now.toLocaleTimeString("en-GB", {
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
        });


        document.getElementById("current-date").textContent = `${day}, ${date} ${month} ${year}`;
        document.getElementById("current-time").textContent = time;
    }

    setInterval(updateDateTime, 1000);
    updateDateTime();
</script>
