<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ClienteService
{
    public function getCliente(array $data)
    {
        $correo    = $data['correo']    ?? null;
        $passwordd = $data['passwordd'] ?? null;

        $rows = DB::select('CALL sp_getCliente(?, ?)', [
            $correo,
            $passwordd,
        ]);

        return $rows; // array de stdClass
    }

    public function setCliente(array $data)
    {
        $id        = (int)($data['id'] ?? 0);
        $nombres   = $data['nombres']   ?? null;
        $correo    = $data['correo']    ?? null;
        $passwordd = $data['passwordd'] ?? null;

        $rows = DB::select('CALL sp_setCliente(?, ?, ?, ?)', [
            $id,
            $nombres,
            $correo,
            $passwordd,
        ]);

        // Si es nuevo (id = 0) y tu SP devuelve insertID
        if ($id === 0 && !empty($rows) && isset($rows[0]->insertID)) {
            return [
                'id'        => (int)$rows[0]->insertID,
                'nombres'   => $nombres,
                'correo'    => $correo,
                'passwordd' => $passwordd,
            ];
        }

        // Si el SP devolvió error
        if (!empty($rows) && isset($rows[0]->error)) {
            return [
                'error' => $rows[0]->error,
            ];
        }

        // Si no devolvió nada, asumimos UPDATE correcto
        return [
            'update' => true,
        ];
    }

    public function getClienteCodigo(array $data)
    {
        $correo = $data['correo'] ?? null;

        try {
            $rows = DB::select('CALL sp_getClienteCodigo(?)', [
                $correo,
            ]);
        } catch (\Throwable $e) {
            return [
                'error' => 'El servidor no está disponible',
            ];
        }

        if (!empty($rows) && isset($rows[0]->codigo)) {
            return [
                'codigo' => $rows[0]->codigo,
            ];
        }

        if (!empty($rows) && isset($rows[0]->error)) {
            return [
                'error' => $rows[0]->error,
            ];
        }

        return [
            'error' => 'No se pudo generar el código',
        ];
    }
}
