<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - Sistem Finding Defect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .blur {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
        }
        .blur-rounded-sm {
            border-radius: 0.5rem;
        }
        .text-gradient {
            background: linear-gradient(to right, #ff0000, #ff4d4d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        /* Animasi fade-in */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        /* Progress bar timer */
        .timer-progress {
            height: 8px;
            background-color: #f8d7da;
        }
        .timer-progress .progress-bar {
            background-color: #dc3545;
            transition: width 1s linear;
        }
        /* Hover effect untuk tombol */
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
            transition: all 0.3s ease;
        }
        /* Alert styling */
        .alert i {
            margin-right: 8px;
        }
        /* OTP input boxes */
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 1.5rem;
            border: 2px solid #dc3545;
            border-radius: 8px;
            margin: 0 5px;
            transition: border-color 0.3s ease;
        }
        .otp-input:focus {
            border-color: #ff4d4d;
            box-shadow: 0 0 5px rgba(220, 53, 69, 0.5);
            outline: none;
        }
        /* Responsive */
        @media (max-width: 576px) {
            .otp-input {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }
        /* Teks timer */
        .timer-text {
            text-align: center;
            font-size: 0.9rem;
            color: #dc3545;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <main class="main-content mt-0">
        <section>
            <div class="page-header min-vh-75 mt-5 d-flex align-items-center justify-content-center">
                <div class="container">
                    <div class="row justify-content-center">
                        <!-- Logo -->
                        <div class="col-12 text-center mb-3">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/KYB_Corporation_company_logo.svg/2560px-KYB_Corporation_company_logo.svg.png" style="height: 60px; width: auto;">
                        </div>

                        <!-- Box OTP -->
                        <div class="col-xl-4 col-lg-5 col-md-6">
                            <div class="card card-plain blur blur-rounded-sm shadow-xl mt-3 p-3 fade-in">
                                <div class="card-header pb-0 text-left bg-transparent text-center">
                                    <h3 class="font-weight-bolder text-danger text-gradient">
                                        Verifikasi OTP
                                    </h3>
                                    <p class="text-muted">Masukkan 6 digit OTP yang dikirim ke nomor Anda</p>
                                </div>
                                <div class="card-body">
                                    @if ($errors->any())
                                        <div class="alert alert-danger d-flex align-items-center">
                                            <i class="bi bi-x-circle"></i>
                                            <div>
                                                @foreach ($errors->all() as $error)
                                                    <div>{{ $error }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if (session('success'))
                                        <div class="alert alert-success d-flex align-items-center">
                                            <i class="bi bi-check-circle"></i>
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    <!-- Progress bar timer -->
                                    <div class="timer-progress mb-1">
                                        <div class="progress-bar" role="progressbar" style="width: 100%;" id="otp-timer"></div>
                                    </div>
                                    <!-- Teks timer mundur (perbaikan: tampilkan waktu tersisa) -->
                                    <div class="timer-text" id="otp-timer-text">Waktu tersisa: 5:00</div>

                                    <form method="POST" action="{{ route('verify.otp') }}">
                                        @csrf
                                        <label for="otp">Masukkan OTP</label>
                                        <div class="mb-3 d-flex justify-content-center">
                                            <input type="text" name="otp1" id="otp1" class="otp-input" maxlength="1" pattern="[0-9]" required oninput="moveToNext(this, 'otp2')">
                                            <input type="text" name="otp2" id="otp2" class="otp-input" maxlength="1" pattern="[0-9]" required oninput="moveToNext(this, 'otp3')" onkeydown="moveToPrevious(event, 'otp1')">
                                            <input type="text" name="otp3" id="otp3" class="otp-input" maxlength="1" pattern="[0-9]" required oninput="moveToNext(this, 'otp4')" onkeydown="moveToPrevious(event, 'otp2')">
                                            <input type="text" name="otp4" id="otp4" class="otp-input" maxlength="1" pattern="[0-9]" required oninput="moveToNext(this, 'otp5')" onkeydown="moveToPrevious(event, 'otp3')">
                                            <input type="text" name="otp5" id="otp5" class="otp-input" maxlength="1" pattern="[0-9]" required oninput="moveToNext(this, 'otp6')" onkeydown="moveToPrevious(event, 'otp4')">
                                            <input type="text" name="otp6" id="otp6" class="otp-input" maxlength="1" pattern="[0-9]" required onkeydown="moveToPrevious(event, 'otp5')">
                                            <!-- Hidden input untuk menggabungkan OTP -->
                                            <input type="hidden" name="otp" id="otp" value="">
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn bg-danger text-white w-100 mt-4 mb-0">Verifikasi</button>
                                        </div>
                                    </form>

                                    <!-- Tombol Kirim Ulang OTP -->
                                    <div class="text-center mt-3">
                                        <form method="POST" action="{{ route('request.new.otp') }}">
                                            @csrf
                                            <button type="submit" class="btn btn-warning w-100">
                                                <i class="fas fa-sync-alt me-2"></i>Kirim Ulang OTP
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function startOtpTimer(forceReset = false) {
    const progressBar = document.getElementById('otp-timer');
    const timerText = document.getElementById('otp-timer-text');
    const duration = 300; // 5 menit

    let endTime = localStorage.getItem('otpEndTime');

    if (!endTime || forceReset) {
        // Selalu buat endTime baru kalau dipaksa reset (misal login baru / kirim OTP baru)
        endTime = Date.now() + duration * 1000;
        localStorage.setItem('otpEndTime', endTime);
    } else {
        endTime = parseInt(endTime, 10);
    }

    function updateTimer() {
        const now = Date.now();
        let timeLeft = Math.floor((endTime - now) / 1000);

        if (timeLeft < 0) timeLeft = 0;

        const percentage = (timeLeft / duration) * 100;
        progressBar.style.width = percentage + '%';

        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerText.textContent = `Waktu tersisa: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

        if (timeLeft <= 0) {
            clearInterval(interval);
            progressBar.style.width = '0%';
            timerText.textContent = 'OTP expired';
            localStorage.removeItem('otpEndTime');
        }
    }

    updateTimer();
    const interval = setInterval(updateTimer, 1000);
}

        // Pindah ke input berikutnya
        function moveToNext(current, nextFieldId) {
            if (current.value.length === 1 && nextFieldId) {
                document.getElementById(nextFieldId).focus();
            }
            updateHiddenOtp();
        }

        // Pindah ke input sebelumnya saat backspace
        function moveToPrevious(event, prevFieldId) {
            if (event.key === 'Backspace' && prevFieldId && !event.target.value) {
                document.getElementById(prevFieldId).focus();
            }
            updateHiddenOtp();
        }

        // Update hidden input dengan nilai OTP gabungan
        function updateHiddenOtp() {
            const otpInputs = document.querySelectorAll('.otp-input');
            let otpValue = '';
            otpInputs.forEach(input => {
                otpValue += input.value;
            });
            document.getElementById('otp').value = otpValue;
        }

        // Handle paste OTP
        document.querySelector('.otp-input').addEventListener('paste', function(e) {
            e.preventDefault();
            const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            if (pasteData.length >= 6) {
                const otpInputs = document.querySelectorAll('.otp-input');
                for (let i = 0; i < 6; i++) {
                    otpInputs[i].value = pasteData[i] || '';
                }
                updateHiddenOtp();
                document.getElementById('otp6').focus();
            }
        });

        // Auto-focus input pertama
        document.getElementById('otp1').focus();

        // Jalankan timer saat halaman load
        window.onload = startOtpTimer;
    </script>
</body>
</html>
