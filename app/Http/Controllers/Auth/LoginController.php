<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // En una aplicación real, validaríamos y autenticaríamos al usuario
        // Para el demo, simplemente redireccionamos al dashboard
        
        return redirect()->route('dashboard');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // En una aplicación real, desloguearíamos al usuario
        // Para el demo, simplemente redireccionamos a la página de login
        
        return redirect()->route('login');
    }
}