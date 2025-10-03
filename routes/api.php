<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CargosController;
use App\Http\Controllers\CategoriasMaquinariasController;
use App\Http\Controllers\EmpleadosController;
use App\Http\Controllers\EmpresasController;
use App\Http\Controllers\MaquinasController;
use App\Http\Controllers\MantenimientosController;
use App\Http\Controllers\PagosController;
use App\Http\Controllers\RepresentantesController;
use App\Http\Controllers\SolicitudesController;
use Illuminate\Support\Facades\Route;

// Rutas públicas de autenticación
Route::post('login', [AuthController::class, 'login']);
Route::post('registrar', [AuthController::class, 'register']);

// === AGREGAR DESDE AQUÍ PARA SWAGGER ===

// Ruta de salud de la API para documentación
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'API Maquinaria Pesada',
        'version' => '1.0.0',
        'timestamp' => now()
    ]);
});

// Ruta de test para documentación
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Maquinaria Pesada funcionando correctamente',
        'data' => [
            'service' => 'Maquinaria Pesada API',
            'version' => '1.0.0',
            'status' => 'active'
        ]
    ]);
});

// Rutas para Maquinarias (corregido el namespace del controlador)
if (!Route::has('maquinarias.index')) {
    Route::apiResource('maquinarias', MaquinasController::class);
}

// Rutas protegidas con autenticación (SIN ROLES temporalmente)
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'userProfile']);

    // Rutas de empresas
    Route::get('ListarEmpresas', [EmpresasController::class, 'index']);
    Route::post('CrearEmpresas', [EmpresasController::class, 'store']);
    Route::get('ObtenerEmpresa/{id}', [EmpresasController::class, 'show']);
    Route::put('ActualizarEmpresas/{id}', [EmpresasController::class, 'update']);
    Route::delete('EliminarEmpresas/{id}', [EmpresasController::class, 'destroy']);

    // Rutas de representantes
    Route::get('ListarRepresentantes', [RepresentantesController::class, 'index']);
    Route::post('CrearRepresentantes', [RepresentantesController::class, 'store']);
    Route::get('ObtenerRepresentante/{id}', [RepresentantesController::class, 'show']);
    Route::put('ActualizarRepresentantes/{id}', [RepresentantesController::class, 'update']);
    Route::delete('EliminarRepresentantes/{id}', [RepresentantesController::class, 'destroy']);

    // Rutas de categorías maquinarias
    Route::get('ListarCategoriasMaquinarias', [CategoriasMaquinariasController::class, 'index']);
    Route::post('CrearCategoriasMaquinarias', [CategoriasMaquinariasController::class, 'store']);
    Route::get('ObtenerCategoriaMaquinaria/{id}', [CategoriasMaquinariasController::class, 'show']);
    Route::put('ActualizarCategoriasMaquinarias/{id}', [CategoriasMaquinariasController::class, 'update']);
    Route::delete('EliminarCategoriasMaquinarias/{id}', [CategoriasMaquinariasController::class, 'destroy']);

    // Rutas de máquinas
    Route::get('ListarMaquinas', [MaquinasController::class, 'index']);
    Route::post('CrearMaquinas', [MaquinasController::class, 'store']);
    Route::get('ObtenerMaquina/{id}', [MaquinasController::class, 'show']);
    Route::put('ActualizarMaquinas/{id}', [MaquinasController::class, 'update']);
    Route::delete('EliminarMaquinas/{id}', [MaquinasController::class, 'destroy']);

    // Rutas de cargos
    Route::get('ListarCargos', [CargosController::class, 'index']);
    Route::post('CrearCargos', [CargosController::class, 'store']);
    Route::get('ObtenerCargo/{id}', [CargosController::class, 'show']);
    Route::put('ActualizarCargos/{id}', [CargosController::class, 'update']);
    Route::delete('EliminarCargos/{id}', [CargosController::class, 'destroy']);

    // Rutas de empleados
    Route::get('ListarEmpleados', [EmpleadosController::class, 'index']);
    Route::post('CrearEmpleados', [EmpleadosController::class, 'store']);
    Route::get('ObtenerEmpleado/{id}', [EmpleadosController::class, 'show']);
    Route::put('ActualizarEmpleados/{id}', [EmpleadosController::class, 'update']);
    Route::delete('EliminarEmpleados/{id}', [EmpleadosController::class, 'destroy']);

    // Rutas de solicitudes
    Route::get('ListarSolicitudes', [SolicitudesController::class, 'index']);
    Route::post('CrearSolicitudes', [SolicitudesController::class, 'store']);
    Route::get('ObtenerSolicitud/{id}', [SolicitudesController::class, 'show']);
    Route::put('ActualizarSolicitudes/{id}', [SolicitudesController::class, 'update']);
    Route::delete('EliminarSolicitudes/{id}', [SolicitudesController::class, 'destroy']);

    // Rutas de mantenimientos
    Route::get('ListarMantenimientos', [MantenimientosController::class, 'index']);
    Route::post('CrearMantenimientos', [MantenimientosController::class, 'store']);
    Route::get('ObtenerMantenimiento/{id}', [MantenimientosController::class, 'show']);
    Route::put('ActualizarMantenimientos/{id}', [MantenimientosController::class, 'update']);
    Route::delete('EliminarMantenimientos/{id}', [MantenimientosController::class, 'destroy']);

    // Rutas de pagos
    Route::get('ListarPagos', [PagosController::class, 'index']);
    Route::post('CrearPagos', [PagosController::class, 'store']);
    Route::get('ObtenerPago/{id}', [PagosController::class, 'show']);
    Route::put('ActualizarPagos/{id}', [PagosController::class, 'update']);
    Route::delete('EliminarPagos/{id}', [PagosController::class, 'destroy']);

    // CONSULTAS EXTRAS
    Route::get('EmpleadosOrdenados', [EmpleadosController::class, 'listarEmpleadosOrdenados']);
    Route::get('MaquinasPesadasCostosas', [MaquinasController::class, 'maquinasPesadasCostosas']);
    Route::get('EmpresaMasSolicitudes', [EmpresasController::class, 'empresaMasSolicitudes']);
    Route::get('EmpresasSinSolicitudes', [EmpresasController::class, 'empresasSinSolicitudes']);
    Route::get('TotalMaquinasEmpresa/{nombre}', [SolicitudesController::class, 'totalMaquinasEmpresa']);
    Route::get('SolicitudesEmpleado/{documento}', [SolicitudesController::class, 'solicitudesPorDocumentoEmpleado']);
    Route::get('RepresentantesSinSolicitudes', [RepresentantesController::class, 'representantesEmpresasSinSolicitudes']);
    Route::get('ReporteSolicitudes', [SolicitudesController::class, 'reporteSolicitudesDetallado']);
    Route::get('BuscarSolicitud/{codigo}', [SolicitudesController::class, 'buscarSolicitudConEmpleados']);
    Route::get('MantenimientosRetroexcavadoras', [MantenimientosController::class, 'contarMantenimientosRetroexcavadoras']);
    Route::get('ReporteOctubre2023', [SolicitudesController::class, 'reporteOctubre2023']);
});