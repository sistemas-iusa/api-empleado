<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfoController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }

    public function __invoke(Request $request)
    {
        $user = $request->user();
        /* return response()->json([
        'email' => $user->email,
        'name' => $user->name,
        'employee_code' => $user->employee_code,
        ]); */
        $data_arr = array();
        $noempleado = auth()->user()->employee_code;
        $status = DB::connection('mysql_main')->table('usuarios_evaluacion as ue')
            ->select('ue.no_empleado', 'ue.activo', 'ue.evaluar', 'ue.ev_area', 'ue.id_tipousuario')
            ->where('ue.no_empleado', '=', $noempleado)
            ->where('ue.interno', '=', 1)
            ->where('ue.login', '=', 1)
            ->get();
        $statusCount = $status->count();

        if ($statusCount > 0) {
            foreach ($status as $row) {

                if ($row->activo == 1) {
                    $home = true;
                } else {
                    $home = false;
                }
                if ($row->evaluar == 1) {
                    $ev_user = true;
                } else {
                    $ev_user = false;
                }

                if ($row->ev_area == 1) {
                    $ev_area = true;
                } else {
                    $ev_area = false;
                }

                if ($row->id_tipousuario == 1) {
                    $director = true;
                } else {
                    $director = false;
                }

                $data_arr[] = array("contingency" => true, "home" => $home, "ev_user" => $ev_user, "ev_area" => $ev_area, "director" => $director);
            }
        } elseif ($statusCount == 0) {
            $data_arr[] = array("contingency" => false);
        }

        $datos_total = ['user' => $user, 'role' => $data_arr];

        return response()->json($datos_total);
    }
}
