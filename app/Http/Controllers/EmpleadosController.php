<?php

namespace App\Http\Controllers;

use App\Models\empleados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Empleados",
 *     description="Endpoints para gestión de empleados"
 * )
 */
class EmpleadosController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ListarEmpleados",
     *     summary="Obtener lista de empleados",
     *     tags={"Empleados"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empleados obtenida correctamente",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Empleado"))
     *     )
     * )
     */
    public function index()
    {
        $empleados = empleados::with('cargo')->get();
        return response()->json($empleados);
    }

    /**
     * @OA\Post(
     *     path="/api/CrearEmpleados",
     *     summary="Crear un nuevo empleado",
     *     tags={"Empleados"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"Documento","Nombre","Apellido","Telefono","cargos_id"},
     *             @OA\Property(property="Documento", type="string", example="12345678"),
     *             @OA\Property(property="Nombre", type="string", example="Juan"),
     *             @OA\Property(property="Apellido", type="string", example="Pérez"),
     *             @OA\Property(property="Telefono", type="string", example="+573001234567"),
     *             @OA\Property(property="Email", type="string", format="email", example="juan@empresa.com"),
     *             @OA\Property(property="cargos_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empleado creado correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Empleado")
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
            'Documento' => 'required|string|max:20|unique:empleados,Documento',
            'Nombre' => 'required|string|max:255',
            'Apellido' => 'required|string|max:255',
            'Telefono' => 'required|string|max:20',
            'Email' => 'nullable|email|max:255',
            'cargos_id' => 'required|exists:cargos,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $empleado = empleados::create($validator->validated());
        return response()->json($empleado, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/ObtenerEmpleado/{id}",
     *     summary="Obtener un empleado específico",
     *     tags={"Empleados"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del empleado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empleado encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Empleado")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empleado no encontrado"
     *     )
     * )
     */
    public function show(string $id)
    {
        $empleado = empleados::with('cargo')->find($id);
        if (!$empleado) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }
        return response()->json($empleado);
    }

    /**
     * @OA\Put(
     *     path="/api/ActualizarEmpleados/{id}",
     *     summary="Actualizar un empleado",
     *     tags={"Empleados"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del empleado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="Documento", type="string", example="12345678"),
     *             @OA\Property(property="Nombre", type="string", example="Juan Carlos"),
     *             @OA\Property(property="Apellido", type="string", example="Pérez García"),
     *             @OA\Property(property="Telefono", type="string", example="+573001234568"),
     *             @OA\Property(property="Email", type="string", format="email", example="juanc@empresa.com"),
     *             @OA\Property(property="cargos_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empleado actualizado correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Empleado")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empleado no encontrado"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $empleado = empleados::find($id);
        if (!$empleado) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'Documento' => 'sometimes|string|max:20|unique:empleados,Documento,' . $id,
            'Nombre' => 'sometimes|string|max:255',
            'Apellido' => 'sometimes|string|max:255',
            'Telefono' => 'sometimes|string|max:20',
            'Email' => 'nullable|email|max:255',
            'cargos_id' => 'sometimes|exists:cargos,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $empleado->update($validator->validated());
        return response()->json($empleado);
    }

    /**
     * @OA\Delete(
     *     path="/api/EliminarEmpleados/{id}",
     *     summary="Eliminar un empleado",
     *     tags={"Empleados"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del empleado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empleado eliminado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Empleado eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empleado no encontrado"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $empleado = empleados::find($id);
        if (!$empleado) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }
        $empleado->delete();
        return response()->json(['message' => 'Empleado eliminado correctamente']);
    }

    /**
     * @OA\Get(
     *     path="/api/EmpleadosOrdenados",
     *     summary="Obtener empleados ordenados por apellido y nombre",
     *     tags={"Empleados"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empleados ordenados",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Empleado"))
     *     )
     * )
     */
    public function listarEmpleadosOrdenados()
    {
        $empleados = empleados::with('cargo')
            ->whereHas('cargo', function($query) {
                $query->where('NombreCargo', 'like', '%empleado%');
            })
            ->orderBy('Apellido')
            ->orderBy('Nombre')
            ->get(['id', 'Documento', 'Nombre', 'Apellido', 'Telefono', 'Email', 'cargos_id']);

        return response()->json($empleados);
    }
}

/**
 * @OA\Schema(
 *     schema="Empleado",
 *     type="object",
 *     title="Empleado",
 *     required={"id", "Documento", "Nombre", "Apellido", "Telefono", "cargos_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="Documento", type="string", example="12345678"),
 *     @OA\Property(property="Nombre", type="string", example="Juan"),
 *     @OA\Property(property="Apellido", type="string", example="Pérez"),
 *     @OA\Property(property="Telefono", type="string", example="+573001234567"),
 *     @OA\Property(property="Email", type="string", format="email", example="juan@empresa.com"),
 *     @OA\Property(property="cargos_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */