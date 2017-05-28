<?php

namespace App\Http\Controllers;

use App\Alumno;
use App\Calificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CalificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rules = [
            "id" => 'required|integer|exists:t_alumnos,id_t_usuarios',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){

            return response()->json(['errors' => $validator->errors()], 422);
        }
        $alumnoId = $request->id;
        $calificaciones = Alumno::join('t_calificaciones', 't_alumnos.id_t_usuarios', '=', 't_calificaciones.id_t_usuarios')
            ->join('t_materias', 't_materias.id_t_materias', '=', 't_calificaciones.id_t_materias')
            ->where('t_alumnos.id_t_usuarios', $alumnoId)
            ->select('t_alumnos.id_t_usuarios',
                't_alumnos.nombre',
                't_alumnos.ap_paterno as apellido',
                't_materias.nombre as materia',
                't_calificaciones.calificacion',
                't_calificaciones.fecha_registro'
                )
            ->get();

        $promedio = 0;
        $iteraciones = 0;
        foreach ($calificaciones as $index => $calificacion){
            $iteraciones +=1;
            $promedio += $calificacion->calificacion;
        }
        if($iteraciones > 0){
            $promedio = $promedio/$iteraciones;
        }
        $calificaciones = $calificaciones->toArray();
        array_push($calificaciones,["promedio"=>$promedio]);

        return response()->json($calificaciones,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            "calificacion" => 'required|numeric',
            "id_t_materias" => 'required|integer|exists:t_materias,id_t_materias',
            "id_t_usuarios" => 'required|integer|exists:t_alumnos,id_t_usuarios',
            "fecha_registro" => 'required|date'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){

            return response()->json(['errors' => $validator->errors()], 422);
        }
        $calificaicon = Calificacion::create($request->all());

        return response()->json(["success" => "ok", "msg" => "calificacion registrada"], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Calificacion $calificacion
     * @return \Illuminate\Http\Response
     */
    public function show(Calificacion $calificacion)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Calificacion $calificacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Calificacion $calificacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Calificacion $calificacion
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request)
    {
        $rules = [
            "calificacion" => 'required|numeric',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $calificacion = Calificacion::find($id);
        /*Si no existe el registro*/
        if (!$calificacion){
            return response()->json(['errors' => "Calificación no existente"], 422);
        }
        $calificacion->calificacion = $request->calificacion;
        $calificacion->save();
        return response()->json(["success" => "ok", "msg" => "calificacion actualizada"], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Calificacion $calificacion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Calificacion $calificacion)
    {
        $calificacion = Calificacion::find($id);
        if (!$calificacion){
            return response()->json(['errors' => "Calificación no existente"], 422);
        }
        $calificacion->delete();
        return response()->json(["success" => "ok", "msg" => "calificacion eliminada"], 200);
    }
}
