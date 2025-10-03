<?php

namespace App\Http\Controllers;

use App\Models\representantes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RepresentantesController extends Controller
{
    public function index()
    {
        $representantes = representantes::with('empresa')->get();
        return response()->json($representantes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Nombre' => 'required|string|max:255',
            'Cedula' => 'required|unique:representantes,Cedula',
            'Telefono' => 'required|string|max:20',
            'Email' => 'required|email|unique:representantes,Email',
            'empresas_id' => 'required|exists:empresas,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $representante = representantes::create($validator->validated());
        return response()->json($representante, 201);
    }

    public function show(string $id)
    {
        $representante = representantes::with('empresa')->find($id);
        if (!$representante) {
            return response()->json(['message' => 'Representante no encontrado'], 404);
        }
        return response()->json($representante);
    }

    public function update(Request $request, string $id)
    {
        $representante = representantes::find($id);
        if (!$representante) {
            return response()->json(['message' => 'Representante no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'Nombre' => 'sometimes|string|max:255',
            'Cedula' => 'sometimes|unique:representantes,Cedula,' . $id,
            'Telefono' => 'sometimes|string|max:20',
            'Email' => 'sometimes|email|unique:representantes,Email,' . $id,
            'empresas_id' => 'sometimes|exists:empresas,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $representante->update($validator->validated());
        return response()->json($representante);
    }

    public function destroy(string $id)
    {
        $representante = representantes::find($id);
        if (!$representante) {
            return response()->json(['message' => 'Representante no encontrado'], 404);
        }
        $representante->delete();
        return response()->json(['message' => 'Representante eliminado correctamente']);
    }

    // CONSULTA EXTRA
    public function representantesEmpresasSinSolicitudes()
    {
        $representantes = representantes::with('empresa')
            ->whereHas('empresa', function($query) {
                $query->doesntHave('solicitudes');
            })
            ->get();

        return response()->json($representantes);
    }
}