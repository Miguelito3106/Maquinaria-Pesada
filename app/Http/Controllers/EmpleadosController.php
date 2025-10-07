<?php

namespace App\Http\Controllers;

use App\Models\empleados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para gestión de empleados
 * 
 * Maneja todas las operaciones CRUD para los empleados del sistema,
 * incluyendo consultas especiales para empleados ordenados.
 */
class EmpleadosController extends Controller
{
    /**
     * Obtener lista de todos los empleados
     * 
     * Retorna una lista completa de todos los empleados registrados en el sistema
     * con sus cargos asociados.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Obtener todos los empleados con sus cargos
        $empleados = empleados::with('cargo')->get();
        return response()->json($empleados);
    }

    /**
     * Crear un nuevo empleado
     * 
     * Registra un nuevo empleado en el sistema con toda su información personal
     * y cargo asignado.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validar datos de entrada
        $validator = Validator::make($request->all(), [
            'Documento' => 'required|string|max:20|unique:empleados,Documento',
            'Nombre' => 'required|string|max:255',
            'Apellido' => 'required|string|max:255',
            'Telefono' => 'required|string|max:20',
            'Email' => 'nullable|email|max:255',
            'cargos_id' => 'required|exists:cargos,id'
        ]);

        // Retornar errores de validación si existen
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Crear nuevo empleado
        $empleado = empleados::create($validator->validated());
        return response()->json($empleado, 201);
    }

    /**
     * Obtener un empleado específico
     * 
     * Retorna la información detallada de un empleado específico por su ID.
     * 
     * @param string $id ID del empleado
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        // Buscar empleado por ID con su cargo
        $empleado = empleados::with('cargo')->find($id);
        
        // Verificar si el empleado existe
        if (!$empleado) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }
        
        return response()->json($empleado);
    }

    /**
     * Actualizar un empleado existente
     * 
     * Permite actualizar la información de un empleado existente.
     * 
     * @param Request $request
     * @param string $id ID del empleado
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        // Buscar empleado por ID
        $empleado = empleados::find($id);
        if (!$empleado) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }

        // Validar datos de entrada (campos opcionales)
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

        // Actualizar empleado
        $empleado->update($validator->validated());
        return response()->json($empleado);
    }

    /**
     * Eliminar un empleado
     * 
     * Elimina permanentemente un empleado del sistema.
     * 
     * @param string $id ID del empleado
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        // Buscar empleado por ID
        $empleado = empleados::find($id);
        if (!$empleado) {
            return response()->json(['message' => 'Empleado no encontrado'], 404);
        }
        
        // Eliminar empleado
        $empleado->delete();
        return response()->json(['message' => 'Empleado eliminado correctamente']);
    }

    /**
     * Listar empleados ordenados por apellido y nombre
     *
     * Consulta especial que retorna empleados ordenados alfabéticamente por apellido y nombre.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listarEmpleadosOrdenados()
    {
        // Consulta para empleados ordenados
        $empleados = empleados::with('cargo')
            ->orderBy('Apellido') // Ordenar por apellido
            ->orderBy('Nombre')   // Luego por nombre
            ->get(['id', 'Documento', 'Nombre', 'Apellido', 'Telefono', 'Email', 'cargos_id']);

        return response()->json($empleados);
    }
}