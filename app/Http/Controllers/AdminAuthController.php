<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function create()
    {
        return view('admin.login');
    }

    public function store(Request $request)
    {
        $request->validate(['password' => 'required|string'], ['password.required' => 'Digite a senha.']);

        $expected = (string) config('barbearia.admin_password');

        // Sem senha configurada no .env, ninguém entra
        if ($expected === '' || ! hash_equals($expected, $request->input('password'))) {
            return back()->withErrors(['password' => 'Senha incorreta.']);
        }

        $request->session()->regenerate();
        $request->session()->put('is_admin', true);

        return redirect()->route('appointments.index');
    }

    public function destroy(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('appointments.index');
    }
}