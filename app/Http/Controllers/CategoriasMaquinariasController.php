<?php

namespace App\Http\Controllers;

use App\Models\CategoriasMaquinarias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Categorías de Maquinarias",
 *     description="Endpoints para gestión de categorías de maquinarias"
 * )
 */
class CategoriasMaquinariasController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ListarCategoriasMaquinarias",
     *     summary="Obtener lista de categorías de maquinarias",
     *     tags={"Categorías de Maquinarias"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de categorías obtenida correctamente",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CategoriaMaquinaria"))
     *     )
     * )
     */
    public function index()
    {
        $categorias = CategoriasMaquinarias::with('maquinas')->get();
        return response()->json($categorias);
    }

    /**
     * @OA\Post(
     *     path="/api/CrearCategoriasMaquinarias",
     *     summary="Crear una nueva categoría de maquinaria",
     *     tags={"Categorías de Maquinarias"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tipoMaquinaria","descripcion"},
     *             @OA\Property(property="tipoMaquinaria", type="string", enum={"lijera", "pesada"}, example="pesada"),
     *             @OA\Property(property="descripcion", type="string", example="Maquinaria pesada para construcción")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categoría creada correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/CategoriaMaquinaria")
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
            'tipoMaquinaria' => 'required|in:lijera,pesada',
            'descripcion' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $categoria = CategoriasMaquinarias::create($validator->validated());
        return response()->json($categoria, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/ObtenerCategoriaMaquinaria/{id}",
     *     summary="Obtener una categoría específica",
     *     tags={"Categorías de Maquinarias"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/CategoriaMaquinaria")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada"
     *     )
     * )
     */
    public function show(string $id)
    {
        $categoria = CategoriasMaquinarias::with('maquinas')->find($id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        return response()->json($categoria);
    }

    /**
     * @OA\Put(
     *     path="/api/ActualizarCategoriasMaquinarias/{id}",
     *     summary="Actualizar una categoría",
     *     tags={"Categorías de Maquinarias"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="tipoMaquinaria", type="string", enum={"lijera", "pesada"}, example="pesada"),
     *             @OA\Property(property="descripcion", type="string", example="Maquinaria pesada actualizada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría actualizada correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/CategoriaMaquinaria")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $categoria = CategoriasMaquinarias::find($id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipoMaquinaria' => 'sometimes|in:lijera,pesada',
            'descripcion' => 'sometimes|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $categoria->update($validator->validated());
        return response()->json($categoria);
    }

    /**
     * @OA\Delete(
     *     path="/api/EliminarCategoriasMaquinarias/{id}",
     *     summary="Eliminar una categoría",
     *     tags={"Categorías de Maquinarias"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría eliminada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Categoría eliminada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $categoria = CategoriasMaquinarias::find($id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        $categoria->delete();
        return response()->json(['message' => 'Categoría eliminada correctamente']);
    }
}

/**
 * @OA\Schema(
 *     schema="CategoriaMaquinaria",
 *     type="object",
 *     title="Categoría de Maquinaria",
 *     required={"id", "tipoMaquinaria", "descripcion"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="tipoMaquinaria", type="string", enum={"lijera", "pesada"}, example="pesada"),
 *     @OA\Property(property="descripcion", type="string", example="Maquinaria pesada para construcción"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */