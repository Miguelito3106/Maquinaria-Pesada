<?php

namespace App\Http\Controllers;

use App\Models\cargos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Cargos",
 *     description="Endpoints para gestión de cargos de empleados"
 * )
 */
class CargosController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ListarCargos",
     *     summary="Obtener lista de cargos",
     *     tags={"Cargos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de cargos obtenida correctamente",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Cargo"))
     *     )
     * )
     */
    public function index()
    {
        $cargos = cargos::with('empleados')->get();
        return response()->json($cargos);
    }

    /**
     * @OA\Post(
     *     path="/api/CrearCargos",
     *     summary="Crear un nuevo cargo",
     *     tags={"Cargos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"NombreCargo","Descripcion"},
     *             @OA\Property(property="NombreCargo", type="string", example="Operador de Maquinaria"),
     *             @OA\Property(property="Descripcion", type="string", example="Encargado de operar maquinaria pesada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cargo creado correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Cargo")
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
            'NombreCargo' => 'required|unique:cargos,NombreCargo|string|max:255',
            'Descripcion' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cargo = cargos::create($validator->validated());
        return response()->json($cargo, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/ObtenerCargo/{id}",
     *     summary="Obtener un cargo específico",
     *     tags={"Cargos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del cargo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cargo encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Cargo")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cargo no encontrado"
     *     )
     * )
     */
    public function show(string $id)
    {
        $cargo = cargos::with('empleados')->find($id);
        if (!$cargo) {
            return response()->json(['message' => 'Cargo no encontrado'], 404);
        }
        return response()->json($cargo);
    }

    /**
     * @OA\Put(
     *     path="/api/ActualizarCargos/{id}",
     *     summary="Actualizar un cargo",
     *     tags={"Cargos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del cargo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="NombreCargo", type="string", example="Operador Senior"),
     *             @OA\Property(property="Descripcion", type="string", example="Operador con experiencia en maquinaria pesada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cargo actualizado correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Cargo")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cargo no encontrado"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $cargo = cargos::find($id);
        if (!$cargo) {
            return response()->json(['message' => 'Cargo no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'NombreCargo' => 'sometimes|unique:cargos,NombreCargo,' . $id,
            'Descripcion' => 'sometimes|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cargo->update($validator->validated());
        return response()->json($cargo);
    }

    /**
     * @OA\Delete(
     *     path="/api/EliminarCargos/{id}",
     *     summary="Eliminar un cargo",
     *     tags={"Cargos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del cargo",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cargo eliminado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cargo eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cargo no encontrado"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $cargo = cargos::find($id);
        if (!$cargo) {
            return response()->json(['message' => 'Cargo no encontrado'], 404);
        }
        $cargo->delete();
        return response()->json(['message' => 'Cargo eliminado correctamente']);
    }
}

/**
 * @OA\Schema(
 *     schema="Cargo",
 *     type="object",
 *     title="Cargo",
 *     required={"id", "NombreCargo", "Descripcion"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="NombreCargo", type="string", example="Operador de Maquinaria"),
 *     @OA\Property(property="Descripcion", type="string", example="Encargado de operar maquinaria pesada"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */