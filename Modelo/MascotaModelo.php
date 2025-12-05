<?php
// Modelo/MascotaModelo.php
require_once "conexion.php"; // Asume que incluye la conexión PDO $conexion

class MascotaModelo {
    private $pdo;

    public function __construct() {
        // Usa la variable $conexion del archivo incluido (PDO instance)
        global $conexion;  
        $this->pdo = $conexion;
    }

    /**
     * Registra una nueva mascota en la tabla mascotas_cliente.
     * Utiliza PDO para consultas seguras y preparadas.
     */
    public function registrarMascotaCliente($id_usuario, $nombre, $especie, $raza, $sexo, $fecha_nacimiento, $edad, $direccion, $contacto, $correo, $alergias, $imagen) {
        $sql = "INSERT INTO mascotas_cliente 
                (id_usuario, nombre, especie, raza, sexo, fecha_nacimiento, edad, direccion, contacto, correo, alergias, imagen)
                VALUES 
                (:id_usuario, :nombre, :especie, :raza, :sexo, :fecha_nacimiento, :edad, :direccion, :contacto, :correo, :alergias, :imagen)";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':nombre' => $nombre,
            ':especie' => $especie,
            ':raza' => $raza,
            ':sexo' => $sexo,
            ':fecha_nacimiento' => $fecha_nacimiento,
            ':edad' => $edad,
            ':direccion' => $direccion,
            ':contacto' => $contacto,
            ':correo' => $correo,
            ':alergias' => $alergias,
            ':imagen' => $imagen
        ]);
    }
}
?>