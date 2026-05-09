<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home.index');
    }

    public function login(): View
    {
        return view('auth.login');
    }

    public function registro(): View
    {
        return view('auth.registro');
    }

    public function logout(): RedirectResponse
    {
        return redirect()->route('home');
    }

    public function dashboardVoluntario(): View
    {
        return view('dashboard.voluntario');
    }

    public function dashboardOng(): View
    {
        return view('dashboard.ong');
    }
}
