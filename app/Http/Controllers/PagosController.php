<?php

namespace App\Http\Controllers;

use App\Models\pagos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Pagos",
 *     description="Endpoints para gestión de pagos"
 * )
 */
class PagosController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ListarPagos",
     *     summary="Obtener lista de pagos",
     *     tags={"Pagos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de pagos obtenida correctamente",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Pago"))
     *     )
     * )
     */
    public function index()
    {
        $pagos = pagos::with('mantenimiento.maquina', 'empresa')->get();
        return response()->json($pagos);
    }

    /**
     * @OA\Post(
     *     path="/api/CrearPagos",
     *     summary="Crear un nuevo pago",
     *     tags={"Pagos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"codigoPago","fechaPago","monto","metodoPago","estado","mantenimientos_id","empresas_id"},
     *             @OA\Property(property="codigoPago", type="string", example="PAGO-001"),
     *             @OA\Property(property="fechaPago", type="string", format="date", example="2024-01-10"),
     *             @OA\Property(property="monto", type="number", format="float", example=1500000.00),
     *             @OA\Property(property="metodoPago", type="string", enum={"efectivo", "tarjeta", "transferencia"}, example="transferencia"),
     *             @OA\Property(property="referencia", type="string", example="TRF-123456"),
     *             @OA\Property(property="estado", type="string", enum={"pendiente", "completado", "rechazado"}, example="completado"),
     *             @OA\Property(property="observaciones", type="string", example="Pago realizado por transferencia bancaria"),
     *             @OA\Property(property="mantenimientos_id", type="integer", example=1),
     *             @OA\Property(property="empresas_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Pago creado correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Pago")
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
            'codigoPago' => 'required|unique:pagos,codigoPago',
            'fechaPago' => 'required|date',
            'monto' => 'required|numeric|min:0',
            'metodoPago' => 'required|in:efectivo,tarjeta,transferencia',
            'referencia' => 'nullable|string|max:255',
            'estado' => 'required|in:pendiente,completado,rechazado',
            'observaciones' => 'nullable|string|max:1000',
            'mantenimientos_id' => 'required|exists:mantenimientos,id',
            'empresas_id' => 'required|exists:empresas,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pago = pagos::create($validator->validated());
        return response()->json($pago, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/ObtenerPago/{id}",
     *     summary="Obtener un pago específico",
     *     tags={"Pagos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del pago",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pago encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Pago")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pago no encontrado"
     *     )
     * )
     */
    public function show(string $id)
    {
        $pago = pagos::with('mantenimiento.maquina', 'empresa')->find($id);
        if (!$pago) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }
        return response()->json($pago);
    }

    /**
     * @OA\Put(
     *     path="/api/ActualizarPagos/{id}",
     *     summary="Actualizar un pago",
     *     tags={"Pagos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del pago",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="codigoPago", type="string", example="PAGO-001-UPDATED"),
     *             @OA\Property(property="fechaPago", type="string", format="date", example="2024-01-11"),
     *             @OA\Property(property="monto", type="number", format="float", example=1600000.00),
     *             @OA\Property(property="metodoPago", type="string", enum={"efectivo", "tarjeta", "transferencia"}, example="tarjeta"),
     *             @OA\Property(property="estado", type="string", enum={"pendiente", "completado", "rechazado"}, example="pendiente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pago actualizado correctamente",
     *         @OA\JsonContent(ref="#/components/schemas/Pago")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pago no encontrado"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $pago = pagos::find($id);
        if (!$pago) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'codigoPago' => 'sometimes|unique:pagos,codigoPago,' . $id,
            'fechaPago' => 'sometimes|date',
            'monto' => 'sometimes|numeric|min:0',
            'metodoPago' => 'sometimes|in:efectivo,tarjeta,transferencia',
            'referencia' => 'nullable|string|max:255',
            'estado' => 'sometimes|in:pendiente,completado,rechazado',
            'observaciones' => 'nullable|string|max:1000',
            'mantenimientos_id' => 'sometimes|exists:mantenimientos,id',
            'empresas_id' => 'sometimes|exists:empresas,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pago->update($validator->validated());
        return response()->json($pago);
    }

    /**
     * @OA\Delete(
     *     path="/api/EliminarPagos/{id}",
     *     summary="Eliminar un pago",
     *     tags={"Pagos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del pago",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pago eliminado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Pago eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pago no encontrado"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $pago = pagos::find($id);
        if (!$pago) {
            return response()->json(['message' => 'Pago no encontrado'], 404);
        }
        $pago->delete();
        return response()->json(['message' => 'Pago eliminado correctamente']);
    }
}

/**
 * @OA\Schema(
 *     schema="Pago",
 *     type="object",
 *     title="Pago",
 *     required={"id", "codigoPago", "fechaPago", "monto", "metodoPago", "estado", "mantenimientos_id", "empresas_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="codigoPago", type="string", example="PAGO-001"),
 *     @OA\Property(property="fechaPago", type="string", format="date", example="2024-01-10"),
 *     @OA\Property(property="monto", type="number", format="float", example=1500000.00),
 *     @OA\Property(property="metodoPago", type="string", enum={"efectivo", "tarjeta", "transferencia"}, example="transferencia"),
 *     @OA\Property(property="referencia", type="string", example="TRF-123456"),
 *     @OA\Property(property="estado", type="string", enum={"pendiente", "completado", "rechazado"}, example="completado"),
 *     @OA\Property(property="observaciones", type="string", example="Pago realizado por transferencia bancaria"),
 *     @OA\Property(property="mantenimientos_id", type="integer", example=1),
 *     @OA\Property(property="empresas_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */