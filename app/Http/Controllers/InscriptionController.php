<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\InscriptionRequest;

class InscriptionController extends Controller
{
   
    public function create(){
        return view('auth.inscription');
    }

    public function store(InscriptionRequest $request ){

            $donnees = $request->validated();

            $utilisateur = new User();
            $utilisateur->name = $donnees['name'];
            $utilisateur->email = $donnees['email'];
            $utilisateur->password = Hash::make($donnees['password']);

            $utilisateur->save();

            return redirect()
            ->route('register')
            ->with('succes', 'Inscription faite et Compte créé');

    }
}
