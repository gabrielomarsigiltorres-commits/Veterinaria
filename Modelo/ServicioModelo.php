<?php
require_once "conexion.php";

class ServicioModelo {
    private $pdo;

    public function __construct() {
        // ✅ Usa la variable $conexion del archivo incluido
        global $conexion;  
        $this->pdo = $conexion;
    }

    // ✅ Obtener todos los servicios
    public function obtenerServicios() {
        $sql = "SELECT * FROM servicios ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Agregar un nuevo servicio
    public function agregarServicio($nombre, $descripcion, $precio, $categoria, $estado, $fecha_inicio) {
        $sql = "INSERT INTO servicios (nombre, descripcion, precio, categoria, estado, fecha_inicio)
                VALUES (:nombre, :descripcion, :precio, :categoria, :estado, :fecha_inicio)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":precio", $precio);
        $stmt->bindParam(":categoria", $categoria);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":fecha_inicio", $fecha_inicio);
        return $stmt->execute();
    }

    // ✅ Actualizar servicio
    public function actualizarServicio($id, $nombre, $descripcion, $precio, $categoria, $estado, $fecha_inicio) {
        $sql = "UPDATE servicios SET 
                    nombre = :nombre,
                    descripcion = :descripcion,
                    precio = :precio,
                    categoria = :categoria,
                    estado = :estado,
                    fecha_inicio = :fecha_inicio
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":precio", $precio);
        $stmt->bindParam(":categoria", $categoria);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":fecha_inicio", $fecha_inicio);
        return $stmt->execute();
    }

    // ✅ Eliminar servicio
    public function eliminarServicio($id) {
        $sql = "DELETE FROM servicios WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // ✅ Obtener un solo servicio por ID
    public function obtenerServicioPorId($id) {
        $sql = "SELECT * FROM servicios WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
   
   public function listarPublico(){

    try{
        $query = "SELECT * FROM servicios WHERE estado = 'Activo' ORDER BY categoria ASC"; 

        $stml =$this->pdo->prepare($query);
        $stml->execute(); 

        return $stml->fetchAll(PDO::FETCH_ASSOC); 
    }catch (PDOException $e){
      die("Error al listar servicios publicos:". $e->getMessage()); 
    }
   }


}
?>
