<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use App\Helpers\GeneradorTmp;

class AutentController extends Controller
{
    public function login(Request $request){
       
        $credenciles = $request->validate(
        [
            'email' => ['required', 'email'],
            'password' => ['required']
        ],
        [
            'email.required'=>'Debe ingresar Email',
            'email.email'=>'Email no válido',
            'password.required'=>'Debe ingresar Contraseña',
        ]);

        if (Auth::attempt($credenciles)){
            $request->session()->regenerate();
            // Obtener el usrGuid del usuario logueado
            $usrGuid = Auth::user()->usrGuid ?? null;

            if ($usrGuid) {
                // Obtener la fecha desde el formulario
                $fechaIngreso = $request->input('txtFecIngresoStock');

                // 🔹 Guardar la fecha en la sesión
                session(['fechaIngresoStock' => $fechaIngreso]);
                
                // Ejecutar el método pasando el usrGuid
                GeneradorTmp::TmpCompras($usrGuid);
                GeneradorTmp::TmpVentas($usrGuid);
                
            }

            return  redirect('UsrAutoriz');
        } else {
                throw ValidationException::withMessages(['credNoValidas'=>'Las credenciales ingresadas son incorrectas. Acceso Denegado']);
        }
    }

    public function UsrAutoriz(){
        return view('panelctrl');
    }

    public function logout(Request $request, Redirector $redirect){

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $redirect->to('/');
    }

}
