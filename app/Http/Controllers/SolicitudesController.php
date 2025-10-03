<?php

namespace App\Http\Controllers;

use App\Models\solicitudes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SolicitudesController extends Controller
{
    public function index()
    {
        // CARGAR SOLO RELACIONES DIRECTAS
        $solicitudes = solicitudes::with(['empresa', 'mantenimientos.maquina', 'empleados', 'maquinas'])->get();
        return response()->json($solicitudes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigoSolicitud' => 'required|unique:solicitudes,codigoSolicitud',
            'fechaSolicitud' => 'required|date',
            'fechaProgramada' => 'required|date', 
            'descripcion' => 'required|string|max:1000',
            'fotos' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'empresas_id' => 'required|exists:empresas,id',
            'empleados' => 'sometimes|array',
            'empleados.*' => 'exists:empleados,id'
            // ELIMINAR maquinas del validation - manejar por separado
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $solicitud = solicitudes::create($validator->validated());
        
        if ($request->has('empleados')) {
            $solicitud->empleados()->sync($request->empleados);
        }

        // CREAR MANTENIMIENTOS POR SEPARADO
        if ($request->has('mantenimientos')) {
            foreach ($request->mantenimientos as $mantenimientoData) {
                $mantenimiento = $solicitud->mantenimientos()->create([
                    'codigo' => $mantenimientoData['codigo'],
                    'nombre' => $mantenimientoData['nombre'],
                    'descripcion' => $mantenimientoData['descripcion'],
                    'costo' => $mantenimientoData['costo'],
                    'tiempoEstimado' => $mantenimientoData['tiempoEstimado'],
                    'manualProcedimiento' => $mantenimientoData['manualProcedimiento'] ?? null,
                    'fechaEntrega' => $mantenimientoData['fechaEntrega'],
                    'maquinas_id' => $mantenimientoData['maquinas_id']
                ]);

                // ASOCIAR MÁQUINAS A TRAVÉS DE LA TABLA PIVOTE
                if (isset($mantenimientoData['maquinas'])) {
                    foreach ($mantenimientoData['maquinas'] as $maquinaData) {
                        $solicitud->maquinas()->attach($maquinaData['maquina_id'], [
                            'cantidad' => $maquinaData['cantidad'],
                            'mantenimientos_id' => $mantenimiento->id
                        ]);
                    }
                }
            }
        }

        return response()->json($solicitud->load(['empresa', 'mantenimientos.maquina', 'empleados', 'maquinas']), 201);
    }

    public function show(string $id)
    {
        $solicitud = solicitudes::with(['empresa', 'mantenimientos.maquina', 'empleados', 'maquinas'])->find($id);
        if (!$solicitud) {
            return response()->json(['message' => 'Solicitud no encontrada'], 404);
        }
        return response()->json($solicitud);
    }

    // ... mantener otros métodos igual pero simplificando las relaciones

    public function reporteSolicitudesDetallado()
    {
        $solicitudes = solicitudes::with(['empresa', 'mantenimientos', 'maquinas'])
            ->get()
            ->map(function($solicitud) {
                $costoTotal = $solicitud->mantenimientos->sum('costo');
                $totalMaquinas = $solicitud->maquinas->sum('pivot.cantidad');
                
                return [
                    'empresa' => $solicitud->empresa->nombreEmpresa,
                    'codigo_solicitud' => $solicitud->codigoSolicitud,
                    'total_maquinas' => $totalMaquinas,
                    'total_mantenimientos' => $solicitud->mantenimientos->count(),
                    'costo_total' => $costoTotal
                ];
            });

        return response()->json($solicitudes);
    }
}