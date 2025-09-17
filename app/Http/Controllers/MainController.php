<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MainController extends Controller
{
    public function index()
    {
        // Jika user sudah login, redirect ke dashboard
        if (Session::has('authenticated') && Session::get('authenticated') === true) {
            return redirect()->route('dashboard');
        }

        // Kalau belum login, tampilkan login
        return view('auth.login');
    }

    public function shipping()
    {
        return view('shipping');
    }

    public function register()
    {
        return view('register');
    }

    public function login()
    {
        // Sama seperti index, cek session dulu
        if (Session::has('authenticated') && Session::get('authenticated') === true) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }
}
