<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="API Maquinaria Pesada",
 *     version="1.0.0",
 *     description="API para gestión de maquinaria pesada del club",
 *     @OA\Contact(
 *         email="admin@club.com",
 *         name="Administrador del Club"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Servidor de Desarrollo"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class MaquinariaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/maquinarias",
     *     summary="Obtener lista de maquinarias",
     *     tags={"Maquinarias"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de maquinarias obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nombre", type="string", example="Excavadora CAT 320"),
     *                     @OA\Property(property="modelo", type="string", example="CAT 320D"),
     *                     @OA\Property(property="estado", type="string", example="disponible")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $maquinarias = [
            [
                'id' => 1,
                'nombre' => 'Excavadora CAT 320',
                'modelo' => 'CAT 320D',
                'estado' => 'disponible',
                'horas_uso' => 1500
            ],
            [
                'id' => 2,
                'nombre' => 'Cargadora frontal Volvo',
                'modelo' => 'L120H',
                'estado' => 'mantenimiento',
                'horas_uso' => 2200
            ],
        ];
        
        return response()->json([
            'success' => true,
            'data' => $maquinarias
        ]);
    }

    /**
     * @OA\Post(
     *     path="/maquinarias",
     *     summary="Registrar nueva maquinaria",
     *     tags={"Maquinarias"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "modelo"},
     *             @OA\Property(property="nombre", type="string", example="Excavadora CAT 320"),
     *             @OA\Property(property="modelo", type="string", example="CAT 320D"),
     *             @OA\Property(property="estado", type="string", example="disponible"),
     *             @OA\Property(property="horas_uso", type="integer", example=0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Maquinaria registrada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Maquinaria registrada correctamente"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'estado' => 'string|in:disponible,mantenimiento,reparacion',
            'horas_uso' => 'integer|min:0'
        ]);

        // Simular creación
        $maquinaria = array_merge($validated, ['id' => 3]);
        
        return response()->json([
            'success' => true,
            'message' => 'Maquinaria registrada correctamente',
            'data' => $maquinaria
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/maquinarias/{id}",
     *     summary="Obtener maquinaria específica",
     *     tags={"Maquinarias"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la maquinaria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Maquinaria encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Maquinaria no encontrada"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $maquinaria = [
            'id' => $id,
            'nombre' => 'Excavadora CAT 320',
            'modelo' => 'CAT 320D',
            'estado' => 'disponible',
            'horas_uso' => 1500
        ];
        
        return response()->json([
            'success' => true,
            'data' => $maquinaria
        ]);
    }

    /**
     * @OA\Put(
     *     path="/maquinarias/{id}",
     *     summary="Actualizar maquinaria",
     *     tags={"Maquinarias"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la maquinaria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string", example="Excavadora CAT 320 Actualizada"),
     *             @OA\Property(property="modelo", type="string", example="CAT 320D"),
     *             @OA\Property(property="estado", type="string", example="mantenimiento"),
     *             @OA\Property(property="horas_uso", type="integer", example=1600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Maquinaria actualizada exitosamente"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Maquinaria actualizada correctamente',
            'data' => array_merge($request->all(), ['id' => $id])
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/maquinarias/{id}",
     *     summary="Eliminar maquinaria",
     *     tags={"Maquinarias"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la maquinaria",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Maquinaria eliminada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Maquinaria no encontrada"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Maquinaria eliminada correctamente'
        ]);
    }
}