<?php
// app/Http/Controllers/SolicitudController.php

namespace App\Http\Controllers;

use App\Models\Solicitudes;
use App\Models\Maquinas;
use App\Models\empleados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SolicitudesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $solicitudes = Solicitudes::with(['user', 'maquinas'])->get();
        return response()->json($solicitudes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $maquinas = Maquinas::where('estado', 'disponible')->get();
        return response()->json($maquinas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha_uso' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'proyecto' => 'required|string|max:255',
            'lugar' => 'required|string|max:255',
            'maquinas' => 'required|array',
            'maquinas.*' => 'exists:maquinas,id',
            'cantidades' => 'required|array',
            'cantidades.*' => 'integer|min:1'
        ]);

        $solicitud = Solicitudes::create([
            'user_id' => Auth::id(),
            'fecha_solicitud' => now(),
            'fecha_uso' => $request->fecha_uso,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'proyecto' => $request->proyecto,
            'lugar' => $request->lugar,
            'estado' => 'pendiente'
        ]);

        // Sincronizar máquinas con cantidades
        $maquinasData = [];
        foreach ($request->maquinas as $index => $maquinaId) {
            $maquinasData[$maquinaId] = ['cantidad' => $request->cantidades[$index]];
        }
        
        $solicitud->maquinas()->sync($maquinasData);

        return response()->json(['message' => 'Solicitud creada correctamente.', 'solicitud' => $solicitud->load('maquinas')], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Solicitudes $solicitud)
    {
        $solicitud->load(['user', 'maquinas']);
        return response()->json($solicitud);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Solicitudes $solicitud)
    {
        $maquinas = Maquinas::where('estado', 'disponible')->get();
        $solicitud->load('maquinas');
        return response()->json(['solicitud' => $solicitud, 'maquinas' => $maquinas]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Solicitudes $solicitud)
    {
        $request->validate([
            'fecha_uso' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'proyecto' => 'required|string|max:255',
            'lugar' => 'required|string|max:255',
            'maquinas' => 'required|array',
            'maquinas.*' => 'exists:maquinas,id',
            'cantidades' => 'required|array',
            'cantidades.*' => 'integer|min:1',
            'estado' => 'required|in:pendiente,aprobada,rechazada,completada'
        ]);

        $solicitud->update([
            'fecha_uso' => $request->fecha_uso,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'proyecto' => $request->proyecto,
            'lugar' => $request->lugar,
            'estado' => $request->estado
        ]);

        // Sincronizar máquinas con cantidades
        $maquinasData = [];
        foreach ($request->maquinas as $index => $maquinaId) {
            $maquinasData[$maquinaId] = ['cantidad' => $request->cantidades[$index]];
        }
        
        $solicitud->maquinas()->sync($maquinasData);

        return response()->json(['message' => 'Solicitud actualizada correctamente.', 'solicitud' => $solicitud->load('maquinas')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Solicitudes $solicitud)
    {
        $solicitud->maquinas()->detach();
        $solicitud->delete();

        return response()->json(['message' => 'Solicitud eliminada correctamente.']);
    }

    /**
     * Cambiar estado de la solicitud
     */
    public function cambiarEstado(Request $request, Solicitudes $solicitud)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,aprobada,rechazada,completada'
        ]);

        $solicitud->update(['estado' => $request->estado]);

        return response()->json(['message' => 'Estado de la solicitud actualizado correctamente.', 'solicitud' => $solicitud]);
    }

    // Total de máquinas solicitadas por empresa
    public function totalMaquinasEmpresa($nombre)
    {
        $total = DB::table('solicitudes')
            ->join('empresas', 'solicitudes.empresas_id', '=', 'empresas.id')
            ->join('solicitud_maquina', 'solicitudes.id', '=', 'solicitud_maquina.solicitud_id')
            ->where('empresas.nombreEmpresa', $nombre)
            ->sum('solicitud_maquina.cantidad');

        return response()->json(['empresa' => $nombre, 'total_maquinas' => $total]);
    }

    // Solicitudes por documento de empleado
    public function solicitudesPorDocumentoEmpleado($documento)
    {
        $solicitudes = empleados::where('Documento', $documento)->first()->solicitudes()->with('maquinas')->get();
        return response()->json($solicitudes);
    }

    // Reporte detallado de solicitudes
    public function reporteSolicitudesDetallado()
    {
        $solicitudes = Solicitudes::with(['user', 'maquinas', 'empleados'])->get();
        return response()->json($solicitudes);
    }

    // Buscar solicitud con empleados
    public function buscarSolicitudConEmpleados($codigo)
    {
        $solicitud = Solicitudes::where('id', $codigo)->with(['user', 'maquinas', 'empleados'])->first();
        return response()->json($solicitud);
    }

    // Reporte de octubre 2023
    public function reporteOctubre2023()
    {
        $solicitudes = Solicitudes::whereYear('fechaSolicitud', 2023)
            ->whereMonth('fechaSolicitud', 10)
            ->with(['user', 'maquinas', 'empresa'])
            ->get();
        return response()->json($solicitudes);
    }
}