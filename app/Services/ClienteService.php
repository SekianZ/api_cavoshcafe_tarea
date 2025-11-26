<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\CodigoVerificacion;
use Carbon\Carbon;

class ClienteService
{
    public function getCliente(array $data)
    {
        $correo = $data['correo'] ?? null;
        $passwordd = $data['passwordd'] ?? null;

        // Validar que lleguen los datos
        if (!$correo || !$passwordd) {
            return [];
        }

        // Buscar cliente con Eloquent
        $cliente = Cliente::where('Correo', $correo)
            ->where('Passwordd', $passwordd)
            ->first();

        // Si no existe, devolver array vacío
        if (!$cliente) {
            return [];
        }

        // Devolver como array (compatible con tu controller)
        return [
            [
                'id' => $cliente->id,
                'Nombres' => $cliente->Nombres,
                'Correo' => $cliente->Correo,
            ]
        ];
    }

    public function setCliente(array $data)
    {
        $id = (int)($data['id'] ?? 0);
        $nombres = $data['nombres'] ?? null;
        $correo = $data['correo'] ?? null;
        $passwordd = $data['passwordd'] ?? null;

        // Validar campos requeridos
        if (!$nombres || !$correo || !$passwordd) {
            return ['error' => 'Faltan campos obligatorios'];
        }

        if ($id === 0) {
            // Verificar si el correo ya existe
            $existe = Cliente::where('Correo', $correo)->exists();

            if ($existe) {
                return ['error' => 'Correo ya está registrado'];
            }

            // Crear el cliente
            $cliente = Cliente::create([
                'Nombres' => $nombres,
                'Correo' => $correo,
                'Passwordd' => $passwordd
            ]);

            // Devolver datos del cliente nuevo
            return [
                'id' => $cliente->id,
                'nombres' => $cliente->Nombres,
                'correo' => $cliente->Correo,
                'passwordd' => $cliente->Passwordd
            ];
        }

        $cliente = Cliente::find($id);

        if (!$cliente) {
            return ['error' => 'Cliente no está registrado'];
        }

        // Si cambió el correo, verificar que no esté en uso
        if ($cliente->Correo !== $correo) {
            $correoEnUso = Cliente::where('Correo', $correo)
                ->where('id', '!=', $id)
                ->exists();

            if ($correoEnUso) {
                return ['error' => 'Correo ya está registrado'];
            }
        }

        // Actualizar datos
        $cliente->update([
            'Nombres' => $nombres,
            'Correo' => $correo,
            'Passwordd' => $passwordd
        ]);

        return ['update' => true];
    }

    public function getClienteCodigo(array $data)
    {
        $correo = $data['correo'] ?? null;

        if (!$correo) {
            return ['error' => 'Falta el correo'];
        }

        // Buscar el cliente
        $cliente = Cliente::where('Correo', $correo)->first();

        if (!$cliente) {
            return ['error' => 'Correo no está registrado'];
        }

        // Generar código aleatorio de 4 dígitos
        $codigo = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);

        // Calcular fecha de caducidad (5 minutos desde ahora)
        $fechaCaducidad = Carbon::now()->addMinutes(5);

        // Guardar el código en la base de datos
        CodigoVerificacion::create([
            'idCliente' => $cliente->id,
            'Codigo' => $codigo,
            'FechaCaducidad' => $fechaCaducidad
        ]);

        // Devolver el código
        return [
            'codigo' => $codigo
        ];
    }

    public function validarCodigo(array $data)
    {
        $correo = $data['correo'] ?? null;
        $codigo = $data['codigo'] ?? null;

        if (!$correo || !$codigo) {
            return ['error' => 'Faltan campos: correo y codigo'];
        }

        // Buscar el cliente
        $cliente = Cliente::where('Correo', $correo)->first();

        if (!$cliente) {
            return ['error' => 'Correo no está registrado'];
        }

        // Buscar el código más reciente
        $codigoDb = CodigoVerificacion::where('idCliente', $cliente->id)
            ->where('Codigo', $codigo)
            ->orderBy('FechaCaducidad', 'desc')
            ->first();

        if (!$codigoDb) {
            return ['error' => 'Código incorrecto'];
        }

        // Verificar si está vigente
        if (!$codigoDb->estaVigente()) {
            return ['error' => 'Código caducado'];
        }

        return [
            'valido' => true,
            'cliente' => [
                'id' => $cliente->id,
                'correo' => $cliente->Correo
            ]
        ];
    }
}
