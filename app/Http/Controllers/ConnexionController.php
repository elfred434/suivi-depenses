<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests\ConnexionRequest;
use Illuminate\Support\Facades\Auth;

class ConnexionController extends Controller
{
    public function create()
    {
        return view('auth.connexion');
    }

    public function store(ConnexionRequest $request)
    {
        $identifiants = $request->validated();

        if (! Auth::attempt($identifiants)) {
            return back()
                ->withErrors([
                    'email' => 'L\'email ou le mot de passe sont incorrecte',
                ])
                ->onlyInput('email');

        }
        $request->session()->regenerate();

        return redirect()
            ->route('categories.index')
            ->with('succes', 'La connexion a réussie');

    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('succes', 'Déconnexion réussie');
    }
}
