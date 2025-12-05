<?php
// Modelo/ClienteModelo.php

require_once "conexion.php"; // Asume que incluye la conexión PDO $conexion

class ClienteModelo {
    private $pdo;

    public function __construct() {
        // Usa la variable $conexion del archivo incluido (PDO instance)
        global $conexion;  
        $this->pdo = $conexion;
    }

    /**
     * Obtiene todos los datos del perfil (usuarios + cliente_perfil) usando LEFT JOIN.
     * Alias: correo_electronico -> correo, contrasena -> password
     */
    public function obtenerPerfil($id_usuario) {
        $sql = "SELECT 
                    u.id, u.nombres_completos, u.correo_electronico AS correo, u.contrasena AS password, u.tipo_usuario,
                    cp.telefono, cp.dni, cp.distrito, cp.provincia, cp.direccion_av, cp.foto_perfil
                FROM usuarios u
                LEFT JOIN cliente_perfil cp ON u.id = cp.id_usuario
                WHERE u.id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza los datos del perfil del cliente (usuarios y cliente_perfil) en una transacción.
     */
    public function actualizarPerfil($id_usuario, $datos) {
        $this->pdo->beginTransaction(); // Inicia una transacción

        try {
            // --- 1. Actualizar campos de la tabla USUARIOS (Nombre y Contraseña) ---
            $updates_usuario = [];
            $params_usuario = [':id' => $id_usuario];

            if (isset($datos['nombres_completos'])) {
                $updates_usuario[] = "nombres_completos = :nombres_completos";
                $params_usuario[':nombres_completos'] = $datos['nombres_completos'];
            }

            if (!empty($datos['password'])) {
                $updates_usuario[] = "contrasena = :password"; // Campo 'contrasena' de la tabla original
                $params_usuario[':password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
            }

            if (!empty($updates_usuario)) {
                $sql_u = "UPDATE usuarios SET " . implode(', ', $updates_usuario) . " WHERE id = :id";
                $stmt_u = $this->pdo->prepare($sql_u);
                $stmt_u->execute($params_usuario);
            }

            // --- 2. Actualizar campos de la tabla CLIENTE_PERFIL (Todos los demás campos) ---
            $updates_perfil = [];
            $params_perfil = [':id_usuario' => $id_usuario];

            $campos_perfil = ['telefono', 'dni', 'distrito', 'provincia', 'direccion_av', 'foto_perfil'];

            foreach ($campos_perfil as $campo) {
                if (array_key_exists($campo, $datos)) { 
                    $updates_perfil[] = "{$campo} = :{$campo}";
                    $params_perfil[":{$campo}"] = $datos[$campo];
                }
            }
            
            // Si hay campos que actualizar en cliente_perfil
            if (!empty($updates_perfil)) {
                $sql_cp = "UPDATE cliente_perfil SET " . implode(', ', $updates_perfil) . " WHERE id_usuario = :id_usuario";
                $stmt_cp = $this->pdo->prepare($sql_cp);
                $stmt_cp->execute($params_perfil);
            }

            $this->pdo->commit(); // Confirma la transacción
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack(); // Revierte si hay error
            throw $e;
        }
    }
}
?>