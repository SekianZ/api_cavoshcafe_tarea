<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ClienteService;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    protected $service;

    public function __construct(ClienteService $service)
    {
        $this->service = $service;
    }

    // ========================================
    // POST /api/login
    // ========================================
    public function getCliente(Request $request)
    {
        $rows = $this->service->getCliente($request->all());

        $success = count($rows) > 0;
        $data    = $success ? $rows[0] : null;
        $message = $success ? 'Cliente registrado' : 'Cliente no registrado';

        return response()->json([
            'success' => $success,
            'data'    => $data,
            'message' => $message,
        ]);
    }

    // ========================================
    // POST /api/registrar
    // ========================================
    public function setCliente(Request $request)
    {
        $rows = $this->service->setCliente($request->all());

        $hasId     = isset($rows['id']);
        $isUpdate  = isset($rows['update']) && $rows['update'] === true;
        $hasError  = isset($rows['error']);

        $success = $hasId || $isUpdate;
        $data    = $hasId ? $rows : null;

        if ($hasId) {
            $message = 'Cliente registrado';
        } elseif ($isUpdate) {
            $message = 'Cliente actualizado';
        } elseif ($hasError) {
            $message = $rows['error'];
        } else {
            $message = 'No se pudo registrar el cliente';
        }

        return response()->json([
            'success' => $success,
            'data'    => $data,
            'message' => $message,
        ]);
    }

    // ========================================
    // POST /api/codigo
    // ========================================
    public function getClienteCodigo(Request $request)
    {
        $rows = $this->service->getClienteCodigo($request->all());

        $hasCodigo = isset($rows['codigo']);
        $hasError  = isset($rows['error']);

        $success = $hasCodigo;
        $data    = $hasCodigo ? $rows : null;

        if ($hasCodigo) {
            $message = 'Código generado';
        } elseif ($hasError) {
            $message = $rows['error'];
        } else {
            $message = 'No se pudo generar el código';
        }

        return response()->json([
            'success' => $success,
            'data'    => $data,
            'message' => $message,
        ]);
    }

    // ========================================
    // POST /api/validar-codigo (NUEVO)
    // ========================================
    public function validarCodigo(Request $request)
    {
        $result = $this->service->validarCodigo($request->all());

        $hasError = isset($result['error']);
        $success  = isset($result['valido']) && $result['valido'] === true;

        if ($success) {
            $message = 'Código válido';
            $data = $result['cliente'];
        } elseif ($hasError) {
            $message = $result['error'];
            $data = null;
        } else {
            $message = 'No se pudo validar el código';
            $data = null;
        }

        return response()->json([
            'success' => $success,
            'data'    => $data,
            'message' => $message,
        ]);
    }
}
