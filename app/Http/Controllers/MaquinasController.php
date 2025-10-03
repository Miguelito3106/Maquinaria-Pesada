<?php

namespace App\Http\Controllers;

use App\Models\Maquina;
use App\Models\Maquinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Máquinas",
 *     description="Endpoints para gestión de máquinas"
 * )
 */
class MaquinasController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ListarMaquinas",
     *     summary="Obtener lista de máquinas",
     *     tags={"Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de máquinas obtenida correctamente",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Maquina"))
     *     )
     * )
     */
    public function index()
    {
        $maquinas = Maquinas::with('categoria', 'mantenimientos')->get();
        return response()->json($maquinas);
    }

    /**
     * @OA\Post(
     *     path="/api/CrearMaquinas",
     *     summary="Crear una nueva máquina",
     *     tags={"Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"TipoMaquina","categorias_maquinarias_id"},
     *             @OA\Property(property="TipoMaquina", type="string", example="Excavadora CAT 320"),
     *             @OA\Property(property="categorias_maquinarias_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Máquina creada correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Maquina")
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
            'TipoMaquina' => 'required|string|max:255',
            'categorias_maquinarias_id' => 'required|exists:categorias_maquinarias,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $maquina = Maquinas::create($validator->validated());
        return response()->json($maquina, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/ObtenerMaquina/{id}",
     *     summary="Obtener una máquina específica",
     *     tags={"Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la máquina",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Máquina encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Maquina")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Máquina no encontrada"
     *     )
     * )
     */
    public function show(string $id)
    {
        $maquina = Maquinas::with('categoria', 'mantenimientos')->find($id);
        if (!$maquina) {
            return response()->json(['message' => 'Máquina no encontrada'], 404);
        }
        return response()->json($maquina);
    }

    /**
     * @OA\Put(
     *     path="/api/ActualizarMaquinas/{id}",
     *     summary="Actualizar una máquina",
     *     tags={"Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la máquina",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="TipoMaquina", type="string", example="Excavadora CAT 320 Actualizada"),
     *             @OA\Property(property="categorias_maquinarias_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Máquina actualizada correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Maquina")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Máquina no encontrada"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $maquina = Maquinas::find($id);
        if (!$maquina) {
            return response()->json(['message' => 'Máquina no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'TipoMaquina' => 'sometimes|string|max:255',
            'categorias_maquinarias_id' => 'sometimes|exists:categorias_maquinarias,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $maquina->update($validator->validated());
        return response()->json($maquina);
    }

    /**
     * @OA\Delete(
     *     path="/api/EliminarMaquinas/{id}",
     *     summary="Eliminar una máquina",
     *     tags={"Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la máquina",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Máquina eliminada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Máquina eliminada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Máquina no encontrada"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $maquina = Maquinas::find($id);
        if (!$maquina) {
            return response()->json(['message' => 'Máquina no encontrada'], 404);
        }
        $maquina->delete();
        return response()->json(['message' => 'Máquina eliminada correctamente']);
    }

    /**
     * @OA\Get(
     *     path="/api/MaquinasPesadasCostosas",
     *     summary="Obtener máquinas pesadas con mantenimientos costosos",
     *     tags={"Máquinas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de máquinas pesadas costosas",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Maquina"))
     *     )
     * )
     */
    public function maquinasPesadasCostosas()
    {
        $maquinas = Maquinas::with(['categoria', 'mantenimientos' => function($query) {
                $query->where('costo', '>', 1000000);
            }])
            ->whereHas('categoria', function($query) {
                $query->where('tipoMaquinaria', 'pesada');
            })
            ->whereHas('mantenimientos', function($query) {
                $query->where('costo', '>', 1000000);
            })
            ->get();

        return response()->json($maquinas);
    }
}

/**
 * @OA\Schema(
 *     schema="Maquina",
 *     type="object",
 *     title="Máquina",
 *     required={"id", "TipoMaquina", "categorias_maquinarias_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="TipoMaquina", type="string", example="Excavadora CAT 320"),
 *     @OA\Property(property="categorias_maquinarias_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */