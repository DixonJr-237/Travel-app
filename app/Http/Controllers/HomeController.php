<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
{
    $user = auth()->user();

    // Vérification de sécurité au cas où l'utilisateur n'a pas de rôle
    if (!$user->role) {
        return view('home');
    }

    switch ($user->role) {
        case 'super_admin':
        case 'company_admin':
        case 'agency_admin':
            // Assurez-vous que cette route n'est pas celle-ci même !
            return redirect()->route('dashboard');

        case 'customer':
            // Vérifiez que le fichier existe : resources/views/customer/home.blade.php
            return view('customers.index');

        default:
            return view('home');
    }
}

}
