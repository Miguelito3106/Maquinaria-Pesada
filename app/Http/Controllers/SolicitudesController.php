<?php

namespace App\Http\Controllers;

use App\Models\Solicitudes;
use App\Models\Maquinas;
use App\Models\Mantenimientos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SolicitudesController extends Controller
{
    /**
     * Mostrar todas las solicitudes.
     */
    public function index()
    {
        try {
            $solicitudes = Solicitudes::with(['maquina', 'mantenimiento', 'empleados'])->get();

            return response()->json([
                'success' => true,
                'data' => $solicitudes,
                'count' => $solicitudes->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener solicitudes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear una nueva solicitud.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'maquina_id' => 'required|exists:maquinas,id',
                'mantenimiento_id' => 'nullable|exists:mantenimientos,id',
                'cantidad' => 'required|integer|min:1',
                'fecha_deseada' => 'required|date|after_or_equal:today',
                'descripcion' => 'required|string|max:1000',
                'foto' => 'nullable|image|max:2048'
            ]);

            DB::beginTransaction();

            // Subir la foto si existe
            $rutaFoto = null;
            if ($request->hasFile('foto')) {
                $rutaFoto = $request->file('foto')->store('fotos_mantenimientos', 'public');
            }

            // Crear la solicitud
            $solicitud = Solicitudes::create([
                'codigo' => 'SOL-' . strtoupper(Str::random(6)),
                'maquina_id' => $validated['maquina_id'],
                'mantenimiento_id' => $validated['mantenimiento_id'] ?? null,
                'cantidad' => $validated['cantidad'],
                'fecha_deseada' => $validated['fecha_deseada'],
                'descripcion' => $validated['descripcion'] ?? null,
                'foto' => $rutaFoto,
                'estado' => 'pendiente'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Solicitud creada correctamente.',
                'data' => $solicitud
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una solicitud específica.
     */
    public function show($id)
    {
        try {
            $solicitud = Solicitudes::with(['maquina', 'mantenimiento', 'empleados'])->find($id);

            if (!$solicitud) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solicitud no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $solicitud
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar una solicitud.
     */
    public function update(Request $request, $id)
    {
        try {
            $solicitud = Solicitudes::find($id);

            if (!$solicitud) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solicitud no encontrada'
                ], 404);
            }

            $validated = $request->validate([
                'maquina_id' => 'required|exists:maquinas,id',
                'mantenimiento_id' => 'nullable|exists:mantenimientos,id',
                'cantidad' => 'required|integer|min:1',
                'fecha_deseada' => 'required|date',
                'descripcion' => 'nullable|string|max:1000',
                'foto' => 'nullable|image|max:2048',
                'estado' => 'nullable|string|in:pendiente,en proceso,finalizada'
            ]);

            DB::beginTransaction();

            if ($request->hasFile('foto')) {
                $rutaFoto = $request->file('foto')->store('fotos_mantenimientos', 'public');
                $solicitud->foto = $rutaFoto;
            }

            $solicitud->update([
                'maquina_id' => $validated['maquina_id'],
                'mantenimiento_id' => $validated['mantenimiento_id'] ?? null,
                'cantidad' => $validated['cantidad'],
                'fecha_deseada' => $validated['fecha_deseada'],
                'descripcion' => $validated['descripcion'] ?? null,
                'estado' => $validated['estado'] ?? $solicitud->estado
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Solicitud actualizada correctamente.',
                'data' => $solicitud
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una solicitud.
     */
    public function destroy($id)
    {
        try {
            $solicitud = Solicitudes::find($id);

            if (!$solicitud) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solicitud no encontrada'
                ], 404);
            }

            $solicitud->delete();

            return response()->json([
                'success' => true,
                'message' => 'Solicitud eliminada correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
