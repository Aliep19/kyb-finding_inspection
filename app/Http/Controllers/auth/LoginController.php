<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Lembur\CtUser;
use App\Models\Hp;
use App\Models\Otp;
use Captcha;

class LoginController extends Controller
{
    // Tampilkan form login
    public function showLogin()
    {
        return view('auth.login');
    }

public function login(Request $request)
{
    // Validasi input
    $request->validate([
        'npk' => 'required|string|max:10',
        'password' => 'required|string',
        'captcha' => 'required|captcha',
    ]);

    // Cari user di ct_users_hash (DB lembur)
    $user = CtUser::where('npk', $request->npk)->first();

    if (!$user || !Hash::check($request->password, $user->pwd)) {
        return redirect()->back()->withErrors(['error' => 'NPK atau password salah.']);
    }

    // Cek apakah NPK ada di tabel hp (DB isd)
    $hpData = Hp::where('npk', $request->npk)->first();

    if (!$hpData) {
        Session::flush();
        return redirect()->route('login')->withErrors(['error' => 'Data HP tidak ditemukan. Silakan hubungi admin.']);
    }

    // Hapus OTP lama untuk NPK ini
    Otp::where('npk', $request->npk)->delete();

    // Generate OTP 6 digit angka
    $otpCode = rand(100000, 999999);

    // Simpan ke tabel otp (DB inspeksi)
    Otp::create([
        'npk' => $request->npk,
        'no_hp' => $hpData->no_hp,
        'kode_otp' => $otpCode,
        'created_at' => now(),
        'expired_at' => now()->addMinutes(5),
    ]);

    // Simpan NPK sementara di session
    Session::put('pending_npk', $request->npk);

    // 🔑 Tambahkan daftar OTP manual (misalnya untuk admin/test)
    Session::put('manual_otps', ['111111','222222','333333','444444','555555','666666']);

    // Redirect ke tampilan OTP
    return redirect()->route('show.otp')->with('success', 'OTP telah dikirim ke nomor HP Anda.');
}


    // Tampilkan form OTP
    public function showOtp()
    {
        if (!Session::has('pending_npk')) {
            return redirect()->route('login')->withErrors(['error' => 'Sesi login expired. Silakan login ulang.']);
        }

        return view('auth.otp');
    }

public function verifyOtp(Request $request)
{
    $request->validate([
        'otp' => 'required|digits:6',
    ]);

    $npk = Session::get('pending_npk');

    if (!$npk) {
        return redirect()->route('login')->withErrors(['error' => 'Sesi invalid.']);
    }

    $inputOtp = $request->otp;

    // 🔑 OTP manual khusus (bisa simpan di .env biar lebih aman)
    $manualOtp = '123456';

    if ($inputOtp === $manualOtp) {
        // Login langsung tanpa cek DB
        Session::put('authenticated', true);
        Session::put('user_npk', $npk);
        Session::forget('pending_npk');

        return redirect()->route('dashboard')->with('success', 'Login berhasil dengan OTP manual!');
    }

    // ✅ Kalau bukan manual → cek OTP dari DB
    $otpData = Otp::where('npk', $npk)
                  ->where('kode_otp', $inputOtp)
                  ->where('expired_at', '>', now())
                  ->latest()
                  ->first();

    if (!$otpData) {
        return redirect()->back()->withErrors(['error' => 'OTP salah atau expired.']);
    }

    // Hapus OTP setelah dipakai
    $otpData->delete();

    Session::put('authenticated', true);
    Session::put('user_npk', $npk);
    Session::forget('pending_npk');

    return redirect()->route('dashboard')->with('success', 'Login berhasil!');
}


    // Request OTP baru
    public function requestNewOtp(Request $request)
    {
        $npk = Session::get('pending_npk');

        if (!$npk) {
            return redirect()->route('login')->withErrors(['error' => 'Sesi invalid. Silakan login ulang.']);
        }

        // Cek apakah NPK ada di tabel hp (DB isd)
        $hpData = Hp::where('npk', $npk)->first();

        if (!$hpData) {
            Session::flush();
            return redirect()->route('login')->withErrors(['error' => 'Data HP tidak ditemukan.']);
        }

        // Hapus OTP lama untuk NPK ini
        Otp::where('npk', $npk)->delete();

        // Generate OTP baru 6 digit angka
        $otpCode = rand(100000, 999999);

        // Simpan OTP baru
        Otp::create([
            'npk' => $npk,
            'no_hp' => $hpData->no_hp,
            'kode_otp' => $otpCode,
            'created_at' => now(),
            'expired_at' => now()->addMinutes(5),
        ]);

        return redirect()->route('show.otp')->with('success', 'OTP baru telah dikirim.');
    }

    public function dashboard()
    {
        if (!Session::has('authenticated')) {
            return redirect()->route('login');
        }

        // Ambil data user dari ct_users_hash berdasarkan NPK
        $npk = Session::get('user_npk');
        $user = CtUser::where('npk', $npk)->first();

        // Jika user tidak ditemukan, logout
        if (!$user) {
            Session::flush();
            return redirect()->route('login')->withErrors(['error' => 'Data user tidak ditemukan. Silakan login ulang.']);
        }

        // Kirim data user ke view
        return view('index', compact('user'));
    }

    // Logout
    public function logout()
    {
        Session::flush();
        return redirect()->route('login')->with('success', 'Logout berhasil.');
    }
}
