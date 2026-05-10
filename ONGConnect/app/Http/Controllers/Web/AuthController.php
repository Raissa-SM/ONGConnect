<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ONG;
use App\Models\User;
use App\Models\Voluntario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'E-mail ou senha incorretos.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = Auth::user();

        return redirect()
            ->route($user->isOng() ? 'dashboard.ong' : 'dashboard.voluntario')
            ->with('success', 'Bem-vindo de volta, ' . $user->name . '!');
    }

    public function showRegistro(): View
    {
        return view('auth.registro');
    }

    public function registro(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|confirmed|min:8',
            'tipo_perfil'   => 'required|in:ong,voluntario',
            'razao_social'  => 'required_if:tipo_perfil,ong|nullable|string|max:255',
            'cnpj'          => 'nullable|string|max:20',
            'cpf'           => 'nullable|string|max:20',
            'cidade'        => 'nullable|string|max:100',
            'uf'            => 'nullable|string|max:2',
            'telefone'      => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => $validated['password'],
            'tipo_perfil' => $validated['tipo_perfil'],
        ]);

        $campos = [
            'user_id'  => $user->id,
            'telefone' => $validated['telefone'] ?? null,
            'cidade'   => $validated['cidade'] ?? null,
            'uf'       => $validated['uf'] ?? null,
        ];

        if ($user->isOng()) {
            ONG::create(array_merge($campos, [
                'razao_social' => $validated['razao_social'],
                'cnpj'         => $validated['cnpj'] ?? null,
            ]));
        } else {
            Voluntario::create(array_merge($campos, [
                'cpf' => $validated['cpf'] ?? null,
            ]));
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route($user->isOng() ? 'dashboard.ong' : 'dashboard.voluntario')
            ->with('success', 'Conta criada! Bem-vindo ao ONGConnect.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
