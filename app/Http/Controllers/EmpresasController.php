<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\empresas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Empresas",
 *     description="Endpoints para gestión de empresas"
 * )
 */
class EmpresasController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ListarEmpresas",
     *     summary="Obtener lista de empresas",
     *     tags={"Empresas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empresas obtenida correctamente",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Empresa"))
     *     )
     * )
     */
    public function index()
    {
        $empresas = empresas::with('representante')->get();
        return response()->json($empresas);
    }

    /**
     * @OA\Post(
     *     path="/api/CrearEmpresas",
     *     summary="Crear una nueva empresa",
     *     tags={"Empresas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nit","nombreEmpresa","direccion","ciudad","telefono"},
     *             @OA\Property(property="nit", type="string", example="900123456-1"),
     *             @OA\Property(property="nombreEmpresa", type="string", example="Constructora XYZ S.A.S."),
     *             @OA\Property(property="direccion", type="string", example="Calle 123 #45-67"),
     *             @OA\Property(property="ciudad", type="string", example="Bogotá"),
     *             @OA\Property(property="telefono", type="string", example="6012345678")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empresa creada correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Empresa")
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
            'nit' => 'required|unique:empresas,nit',
            'nombreEmpresa' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'telefono' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $empresa = empresas::create($validator->validated());
        return response()->json($empresa, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/ObtenerEmpresa/{id}",
     *     summary="Obtener una empresa específica",
     *     tags={"Empresas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Empresa")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa no encontrada"
     *     )
     * )
     */
    public function show(string $id)
    {
        $empresa = empresas::with('representante')->find($id);
        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }
        return response()->json($empresa);
    }

    /**
     * @OA\Put(
     *     path="/api/ActualizarEmpresas/{id}",
     *     summary="Actualizar una empresa",
     *     tags={"Empresas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nit", type="string", example="900123456-2"),
     *             @OA\Property(property="nombreEmpresa", type="string", example="Constructora XYZ S.A.S. Actualizada"),
     *             @OA\Property(property="direccion", type="string", example="Calle 123 #45-67 Actualizada"),
     *             @OA\Property(property="ciudad", type="string", example="Medellín"),
     *             @OA\Property(property="telefono", type="string", example="6012345679")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa actualizada correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Empresa")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa no encontrada"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $empresa = empresas::find($id);
        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nit' => 'sometimes|unique:empresas,nit,' . $id,
            'nombreEmpresa' => 'sometimes|string|max:255',
            'direccion' => 'sometimes|string|max:255',
            'ciudad' => 'sometimes|string|max:100',
            'telefono' => 'sometimes|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $empresa->update($validator->validated());
        return response()->json($empresa);
    }

    /**
     * @OA\Delete(
     *     path="/api/EliminarEmpresas/{id}",
     *     summary="Eliminar una empresa",
     *     tags={"Empresas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la empresa",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa eliminada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Empresa eliminada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa no encontrada"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $empresa = empresas::find($id);
        if (!$empresa) {
            return response()->json(['message' => 'Empresa no encontrada'], 404);
        }
        $empresa->delete();
        return response()->json(['message' => 'Empresa eliminada correctamente']);
    }

    /**
     * @OA\Get(
     *     path="/api/EmpresaMasSolicitudes",
     *     summary="Obtener la empresa con más solicitudes",
     *     tags={"Empresas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Empresa con más solicitudes encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Empresa")
     *     )
     * )
     */
    public function empresaMasSolicitudes()
    {
        $empresa = empresas::withCount('solicitudes')
            ->orderBy('solicitudes_count', 'desc')
            ->first();

        return response()->json($empresa);
    }

    /**
     * @OA\Get(
     *     path="/api/EmpresasSinSolicitudes",
     *     summary="Obtener empresas sin solicitudes",
     *     tags={"Empresas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empresas sin solicitudes",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Empresa"))
     *     )
     * )
     */
    public function empresasSinSolicitudes()
    {
        $empresas = empresas::doesntHave('solicitudes')
            ->with('representante')
            ->get();

        return response()->json($empresas);
    }
}

/**
 * @OA\Schema(
 *     schema="Empresa",
 *     type="object",
 *     title="Empresa",
 *     required={"id", "nit", "nombreEmpresa", "direccion", "ciudad", "telefono"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nit", type="string", example="900123456-1"),
 *     @OA\Property(property="nombreEmpresa", type="string", example="Constructora XYZ S.A.S."),
 *     @OA\Property(property="direccion", type="string", example="Calle 123 #45-67"),
 *     @OA\Property(property="ciudad", type="string", example="Bogotá"),
 *     @OA\Property(property="telefono", type="string", example="6012345678"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */