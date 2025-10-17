<?php

namespace App\Http\Controllers;

use App\Models\Mantenimientos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @OA\Tag(
 *     name="Mantenimientos",
 *     description="Endpoints para gestión de mantenimientos de maquinaria"
 * )
 */
class MantenimientosController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ListarMantenimientos",
     *     summary="Obtener lista de mantenimientos",
     *     tags={"Mantenimientos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de mantenimientos obtenida correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Mantenimiento")),
     *             @OA\Property(property="count", type="integer", example=10)
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $mantenimientos = Mantenimientos::with(['maquina.categoria', 'pagos', 'solicitud.empresa'])
                ->orderBy('fechaEntrega', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $mantenimientos,
                'count' => $mantenimientos->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los mantenimientos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/CrearMantenimientos",
     *     summary="Crear un nuevo mantenimiento",
     *     tags={"Mantenimientos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"codigo","nombre","descripcion","costo","tiempoEstimado","fechaEntrega","maquinas_id","solicitud_id"},
     *             @OA\Property(property="codigo", type="string", example="MANT-001"),
     *             @OA\Property(property="nombre", type="string", example="Mantenimiento preventivo excavadora"),
     *             @OA\Property(property="descripcion", type="string", example="Cambio de aceite y filtros"),
     *             @OA\Property(property="costo", type="number", format="float", example=1500000.00),
     *             @OA\Property(property="tiempoEstimado", type="integer", example=24),
     *             @OA\Property(property="manualProcedimiento", type="string", example="Manual técnico CAT"),
     *             @OA\Property(property="fechaEntrega", type="string", format="date", example="2024-01-15"),
     *             @OA\Property(property="maquinas_id", type="integer", example=1),
     *             @OA\Property(property="solicitud_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Mantenimiento creado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mantenimiento creado correctamente"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:100|unique:mantenimientos,codigo',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'costo' => 'required|numeric|min:0',
            'tiempoEstimado' => 'required|integer|min:1|max:720', 
            'manualProcedimiento' => 'nullable|string',
            'fechaEntrega' => 'required|date|after_or_equal:today',
            'maquinas_id' => 'required|exists:maquinas,id',
        ], [
            'codigo.required' => 'El código es obligatorio',
            'codigo.unique' => 'El código ya existe',
            'nombre.required' => 'El nombre del mantenimiento es obligatorio',
            'descripcion.required' => 'La descripción es obligatoria',
            'costo.required' => 'El costo es obligatorio',
            'costo.min' => 'El costo debe ser mayor o igual a 0',
            'tiempoEstimado.required' => 'El tiempo estimado es obligatorio',
            'tiempoEstimado.min' => 'El tiempo estimado debe ser al menos 1 hora',
            'tiempoEstimado.max' => 'El tiempo estimado no puede exceder 720 horas (30 días)',
            'fechaEntrega.required' => 'La fecha de entrega es obligatoria',
            'fechaEntrega.after_or_equal' => 'La fecha de entrega no puede ser anterior a hoy',
            'maquinas_id.required' => 'La máquina es obligatoria',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mantenimiento = Mantenimientos::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Mantenimiento creado correctamente',
                'data' => $mantenimiento->load(['maquina.categoria', 'solicitud'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el mantenimiento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/ObtenerMantenimiento/{id}",
     *     summary="Obtener un mantenimiento específico",
     *     tags={"Mantenimientos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del mantenimiento",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mantenimiento encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Mantenimiento no encontrado"
     *     )
     * )
     */
    public function show(string $id)
    {
        try {
            $mantenimiento = Mantenimientos::with(['maquina.categoria', 'pagos', 'solicitud.empresa'])
                ->find($id);

            if (!$mantenimiento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mantenimiento no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $mantenimiento
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el mantenimiento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/ActualizarMantenimientos/{id}",
     *     summary="Actualizar un mantenimiento",
     *     tags={"Mantenimientos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del mantenimiento",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="codigo", type="string", example="MANT-001-UPDATED"),
     *             @OA\Property(property="nombre", type="string", example="Mantenimiento preventivo actualizado"),
     *             @OA\Property(property="descripcion", type="string", example="Cambio de aceite, filtros y revisión general"),
     *             @OA\Property(property="costo", type="number", format="float", example=1800000.00),
     *             @OA\Property(property="tiempoEstimado", type="integer", example=36),
     *             @OA\Property(property="fechaEntrega", type="string", format="date", example="2024-01-20")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mantenimiento actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mantenimiento actualizado correctamente"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Mantenimiento no encontrado"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $mantenimiento = Mantenimientos::find($id);
        
        if (!$mantenimiento) {
            return response()->json([
                'success' => false,
                'message' => 'Mantenimiento no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'codigo' => ['sometimes', 'string', 'max:100', Rule::unique('mantenimientos')->ignore($id)],
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string|max:1000',
            'costo' => 'sometimes|numeric|min:0',
            'tiempoEstimado' => 'sometimes|integer|min:1|max:720',
            'manualProcedimiento' => 'nullable|string',
            'fechaEntrega' => 'sometimes|date',
            'maquinas_id' => 'sometimes|exists:maquinas,id',
            'solicitud_id' => 'sometimes|exists:solicitudes,id'
        ], [
            'codigo.unique' => 'El código ya existe',
            'costo.min' => 'El costo debe ser mayor o igual a 0',
            'tiempoEstimado.min' => 'El tiempo estimado debe ser al menos 1 hora',
            'tiempoEstimado.max' => 'El tiempo estimado no puede exceder 720 horas (30 días)',
            'maquinas_id.exists' => 'La máquina seleccionada no existe',
            'solicitud_id.exists' => 'La solicitud seleccionada no existe'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mantenimiento->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Mantenimiento actualizado correctamente',
                'data' => $mantenimiento->load(['maquina.categoria', 'solicitud'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el mantenimiento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/EliminarMantenimientos/{id}",
     *     summary="Eliminar un mantenimiento",
     *     tags={"Mantenimientos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del mantenimiento",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mantenimiento eliminado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mantenimiento eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Mantenimiento no encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="No se puede eliminar porque tiene pagos asociados"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $mantenimiento = Mantenimientos::find($id);
        
        if (!$mantenimiento) {
            return response()->json([
                'success' => false,
                'message' => 'Mantenimiento no encontrado'
            ], 404);
        }

        try {

            if ($mantenimiento->pagos()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el mantenimiento porque tiene pagos asociados'
                ], 422);
            }

            $mantenimiento->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mantenimiento eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el mantenimiento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/MantenimientosRetroexcavadoras",
     *     summary="Contar mantenimientos de retroexcavadoras",
     *     tags={"Mantenimientos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Conteo de mantenimientos obtenido",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="count", type="integer", example=5)
     *             )
     *         )
     *     )
     * )
     */
    public function contarMantenimientosRetroexcavadoras()
    {
        try {
            $count = Mantenimientos::whereHas('maquina', function($query) {
                $query->where('TipoMaquina', 'like', '%retroexcavadora%');
            })->count();

            return response()->json([
                'success' => true,
                'data' => ['count' => $count]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al contar mantenimientos de retroexcavadoras',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ... (otros métodos mantienen su lógica original)
    public function porRangoFechas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mantenimientos = Mantenimientos::with(['maquina.categoria', 'solicitud.empresa'])
                ->entreFechas($request->fecha_inicio, $request->fecha_fin)
                ->orderBy('fechaEntrega')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $mantenimientos,
                'count' => $mantenimientos->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener mantenimientos por rango de fechas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function mantenimientosCostosos()
    {
        try {
            $mantenimientos = Mantenimientos::with(['maquina.categoria', 'solicitud.empresa'])
                ->costosos()
                ->orderBy('costo', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $mantenimientos,
                'count' => $mantenimientos->count(),
                'costo_total' => $mantenimientos->sum('costo')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener mantenimientos costosos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function buscar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'termino' => 'required|string|min:2'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mantenimientos = Mantenimientos::buscar($request->termino)
                ->load(['maquina.categoria', 'solicitud.empresa']);

            return response()->json([
                'success' => true,
                'data' => $mantenimientos,
                'count' => $mantenimientos->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar mantenimientos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function estadisticas()
    {
        try {
            $total = Mantenimientos::count();
            $completados = Mantenimientos::where('fechaEntrega', '<=', now())->count();
            $pendientes = Mantenimientos::where('fechaEntrega', '>', now())->count();
            $costoTotal = Mantenimientos::sum('costo');
            $costoPromedio = $total > 0 ? $costoTotal / $total : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_mantenimientos' => $total,
                    'mantenimientos_completados' => $completados,
                    'mantenimientos_pendientes' => $pendientes,
                    'costo_total' => $costoTotal,
                    'costo_promedio' => round($costoPromedio, 2),
                    'porcentaje_completados' => $total > 0 ? round(($completados / $total) * 100, 2) : 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

/**
 * @OA\Schema(
 *     schema="Mantenimiento",
 *     type="object",
 *     title="Mantenimiento",
 *     required={"id", "codigo", "nombre", "descripcion", "costo", "tiempoEstimado", "fechaEntrega", "maquinas_id", "solicitud_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="codigo", type="string", example="MANT-001"),
 *     @OA\Property(property="nombre", type="string", example="Mantenimiento preventivo excavadora"),
 *     @OA\Property(property="descripcion", type="string", example="Cambio de aceite y filtros"),
 *     @OA\Property(property="costo", type="number", format="float", example=1500000.00),
 *     @OA\Property(property="tiempoEstimado", type="integer", example=24),
 *     @OA\Property(property="manualProcedimiento", type="string", example="Manual técnico CAT"),
 *     @OA\Property(property="fechaEntrega", type="string", format="date", example="2024-01-15"),
 *     @OA\Property(property="maquinas_id", type="integer", example=1),
 *     @OA\Property(property="solicitud_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */