<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 d-flex flex-column bg-light"
    id="sidenav-main">

    <!-- Header tetap -->
    <div class="sidenav-header bg-light">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ route('index') }}">
            <img src="{{ asset ('img/kyb.png') }}"
                class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold">Kayaba Indonesia</span>
        </a>
    </div>

    <hr class="horizontal dark mt-0">

    <!-- Menu (scrollable kalau kepanjangan) -->
    <div class="sidenav-menu flex-grow-1 overflow-auto">
        <div class="collapse navbar-collapse" id="sidenav-collapse-main" style="width: 250px;">
            <ul class="navbar-nav">
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Overview</h6>
                </li>
                <x-navlink href="{{ route('dashboard') }}" :active="request()->is('/') " icon="fa-house">Dashboard</x-navlink>
                <x-navlink href="{{ route('monitoring', ['departmentId' => null]) }}"
                        :active="request()->is('monitoring*')"
                        icon="fa-tv">
                    Monitoring
                </x-navlink>


                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Master Menu</h6>
                </li>

                {{-- Dropdown Master Data --}}
                <li class="nav-item">
                    @php
                        $isActive = request()->is('defect') || request()->is('defect/*') || request()->is('defect_subs*') || request()->is('targets*');
                    @endphp
                    <a class="nav-link" data-bs-toggle="collapse" href="#masterDataMenu" role="button"
                        aria-expanded="{{ $isActive ? 'true' : 'false' }}"
                        aria-controls="masterDataMenu">

                        <div class="icon icon-shape icon-sm shadow border-radius-md
                                    d-flex align-items-center justify-content-center me-2
                                    {{ $isActive ? 'bg-danger' : 'bg-white' }}">
                            <i class="fa-solid fa-database fa-lg {{ $isActive ? 'text-white' : 'text-dark' }}"></i>
                        </div>

                        <span class="nav-link-text ms-1">Master Data</span>
                    </a>

                    <div class="collapse {{ $isActive ? 'show' : '' }}" id="masterDataMenu">
                        <ul class="nav ms-4 ps-3">
                            <li class="nav-item mt-3">
                                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder">Master Defect</h6>
                            </li>
                            <x-navlink href="{{ route('defect.index') }}"
                                :active="request()->is('defect') || request()->is('defect/*')"
                                icon="fa-bug">
                                Defect Category
                            </x-navlink>
                            <x-navlink href="{{ route('defect-subs.index') }}"
                                :active="request()->is('defect_subs') || request()->is('defect_subs/*')"
                                icon="fa-list-alt">
                                Defect Jenis
                            </x-navlink>

                            <li class="nav-item mt-3">
                                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder">Master Target</h6>
                            </li>
                            <x-navlink href="{{ route('targets.index') }}"
                                :active="request()->is('targets') || request()->is('targets/*')"
                                icon="fa-bullseye">
                                Target
                            </x-navlink>
                        </ul>
                    </div>
                </li>

                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Input Data</h6>
                </li>

<x-navlink href="{{ route('defect-inputs.summary') }}"
           :active="request()->is('defect-inputs*')"
           icon="fa-solid fa-clipboard">
    Defect Summary
</x-navlink>
            </ul>
        </div>
    </div>

    <!-- Tombol Logout (selalu di bawah) -->
    @if (Session::has('authenticated'))
        <div class="sidenav-footer mt-auto px-3 pb-3" style="width: 250px;">
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit"
                        class="btn btn-danger w-100"
                        onclick="return confirm('Apakah Anda yakin ingin logout?')">
                    <i class="fa-solid fa-door-open me-2"></i> Logout
                </button>
            </form>
        </div>
    @endif
</aside>
