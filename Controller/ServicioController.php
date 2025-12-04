<?php
require_once "../Modelo/ServicioModelo.php";

class ServicioController {
    private $modelo;
    private $pdo;

    public function __construct() {
        $this->modelo = new ServicioModelo();
        $this->pdo = $GLOBALS['conexion'];   // trae la conexión PDO del archivo conexion.php

    }

    public function listar() {
        return $this->modelo->obtenerServicios();
    }

    public function guardar($data) {
        return $this->modelo->agregarServicio(
            $data["nombre"],
            $data["descripcion"],
            $data["precio"],
            $data["categoria"],
            $data["estado"],
            $data["fecha_inicio"]
        );
    }

    public function actualizar($data) {
        return $this->modelo->actualizarServicio(
            $data["id"],
            $data["nombre"],
            $data["descripcion"],
            $data["precio"],
            $data["categoria"],
            $data["estado"],
            $data["fecha_inicio"]
        );
    }

    public function eliminar($id) {
        return $this->modelo->eliminarServicio($id);
    }

    public function obtener($id) {
        return $this->modelo->obtenerServicioPorId($id);
    }
 public function listarPublico() {
        try {
            $sql = "SELECT * FROM servicios ORDER BY id DESC";
            $query = $this->pdo->prepare($sql);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Error al listar servicios: " . $e->getMessage());
        }
    }
}



// ✅ Controlador como punto de entrada para AJAX
if (isset($_POST["accion"])) {
    $controller = new ServicioController();

    switch ($_POST["accion"]) {
        case "listar":
            echo json_encode($controller->listar());
            break;

        case "guardar":
            echo json_encode($controller->guardar($_POST));
            break;

        case "actualizar":
            echo json_encode($controller->actualizar($_POST));
            break;

        case "eliminar":
            echo json_encode($controller->eliminar($_POST["id"]));
            break;

        case "obtener":
            echo json_encode($controller->obtener($_POST["id"]));
            break;
    
   
}}

?>