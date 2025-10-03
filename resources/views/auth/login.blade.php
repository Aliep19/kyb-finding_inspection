<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<x-head></x-head>

<body class="bg-light">
    <main class="main-content mt-0">
        <section>
            <div class="page-header min-vh-100 d-flex align-items-center justify-content-center bg-gradient">
                <div class="container">
                    <div class="row justify-content-center">

                        {{-- Logo --}}
                        <div class="col-12 text-center mb-4">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/KYB_Corporation_company_logo.svg/2560px-KYB_Corporation_company_logo.svg.png"
                                style="height: 60px; width: auto;">
                        </div>

                        {{-- Box Login --}}
                        <div class="col-xl-5 col-lg-6 col-md-8">
                            <div class="card shadow-lg border-0 rounded-4 p-4 bg-white">
                                <div class="card-header text-center bg-transparent pb-3">
                                    <h4 class="fw-bold text-danger mb-0">Sistem Finding Defect</h4>
                                </div>

                                <div class="card-body">

                                    {{-- Error & Success --}}
                                    @if ($errors->any())
                                        <div class="alert alert-danger py-2">
                                            <ul class="mb-0 small">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if (session('success'))
                                        <div class="alert alert-success small py-2">{{ session('success') }}</div>
                                    @endif

                                    {{-- Form Login --}}
                                    <form method="POST" action="{{ route('login.process') }}">
                                        @csrf

                                        {{-- NPK --}}
                                        <div class="mb-3">
                                            <label for="npk" class="form-label small mb-1">NPK</label>
                                            <input type="text" id="npk" name="npk" class="form-control"
                                                placeholder="Masukan NPK" required>
                                        </div>

                                        {{-- Password --}}
                                        <div class="mb-3">
                                            <label for="password" class="form-label small mb-1">Password</label>
                                            <div class="input-group">
                                                <input type="password" id="password" name="password" class="form-control"
                                                    placeholder="Masukan Password" required>
                                                <button type="button" class="btn btn-outline-secondary"
                                                    id="togglePassword">
                                                    <i class="fa fa-eye-slash" id="togglePasswordIcon"></i>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Captcha --}}
                                        <div class="mb-3">
                                            <label for="captcha" class="form-label small mb-1">Captcha</label>
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <img src="{{ captcha_src('numeric') }}" id="captcha-img" alt="captcha"
                                                    class="rounded shadow-sm" style="cursor:pointer; height:38px;">
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                    id="refresh-captcha">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </div>
                                            <input type="text" id="captcha" name="captcha" class="form-control"
                                                placeholder="Masukan captcha" required>
                                        </div>

                                        {{-- Tombol Login --}}
                                        <div class="d-grid">
                                            <button type="submit" class="btn bg-gradient-danger text-white">
                                                Login
                                            </button>
                                            <a href="{{ route('monitoring') }}" class="btn btn-warning mt-2">
                                                Monitoring
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <x-footer></x-footer>
        </section>
    </main>

    <x-script></x-script>

    <style>
        body {
            background: #f8f9fa;
        }

        .bg-gradient {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        }

        .card {
            max-width: 500px;
        }
    </style>

    <script>
        // Toggle Password
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            toggleIcon.classList.toggle('fa-eye');
            toggleIcon.classList.toggle('fa-eye-slash');
        });

        // Refresh Captcha
        function refreshCaptcha() {
            const img = document.getElementById('captcha-img');
            img.style.opacity = "0.5";
            setTimeout(() => {
                img.src = "{{ captcha_src('numeric') }}" + "?_=" + Date.now();
                img.onload = () => { img.style.opacity = "1"; }
            }, 200);
        }

        document.getElementById('refresh-captcha').addEventListener('click', refreshCaptcha);
        document.getElementById('captcha-img').addEventListener('click', refreshCaptcha);
    </script>
</body>
</html>
