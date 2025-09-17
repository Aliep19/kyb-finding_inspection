@props([
    'title' => '',
    'icon' => 'fa-solid fa-file-invoice',
    'class' => '',
])

<div class="card shadow-sm rounded {{ $class }}">
    {{-- Header --}}
    <div class="card-header py-3 bg-danger border-bottom">
        <div class="d-flex flex-column align-items-start">
            <div class="d-flex align-items-center">
                <i class="{{ $icon }} fs-4 me-3 text-white"></i>
                <h5 class="mb-0 fw-bold text-white">{{ $title }}</h5>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="card-body p-4">
        {{ $slot }}
    </div>
</div>
