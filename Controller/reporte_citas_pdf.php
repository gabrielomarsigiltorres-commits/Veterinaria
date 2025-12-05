<?php
session_start();
if (!isset($_SESSION['logueado']) || trim($_SESSION['tipo_usuario']) !== 'administrador') {
    die("Acceso denegado");
}

// Los ".." significan subir un nivel hacia atrás
require('../fpdf.php');
require('../Modelo/conexion.php');

$mes = $_GET['mes'] ?? date('n');
$anio = $_GET['anio'] ?? date('Y');

$nombresMeses = [1=>"Enero", 2=>"Febrero", 3=>"Marzo", 4=>"Abril", 5=>"Mayo", 6=>"Junio", 7=>"Julio", 8=>"Agosto", 9=>"Septiembre", 10=>"Octubre", 11=>"Noviembre", 12=>"Diciembre"];
$nombreMes = $nombresMeses[$mes];

class PDF extends FPDF {
    function Header() {
        global $nombreMes, $anio;
        $this->SetFont('Arial','B',15);
        $this->Cell(80);
        $this->Cell(30,10, mb_convert_encoding('Reporte de Citas Médicas', 'ISO-8859-1', 'UTF-8'),0,0,'C');
        $this->Ln(10);
        $this->SetFont('Arial','',12);
        $this->Cell(0,10, mb_convert_encoding("$nombreMes - $anio", 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');
        $this->Ln(20);
        
        $this->SetFillColor(0, 167, 157);
        $this->SetTextColor(255);
        $this->SetFont('Arial','B',9);
        
        $this->Cell(25,10,'Fecha',1,0,'C',true);
        $this->Cell(20,10,'Hora',1,0,'C',true);
        $this->Cell(40,10,'Paciente',1,0,'C',true);
        $this->Cell(45,10,'Dueno',1,0,'C',true);
        $this->Cell(40,10,'Servicio',1,0,'C',true);
        $this->Cell(20,10,'Estado',1,0,'C',true);
        $this->Ln();
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

try {
    $sql = "SELECT c.fecha_cita, c.hora_cita, u.nombres_completos as dueno, m.nombre as mascota, c.servicio, c.estado
            FROM citas c
            JOIN usuarios u ON c.id_usuario = u.id
            JOIN mascotas_cliente m ON c.id_mascota = m.id
            WHERE MONTH(c.fecha_cita) = ? AND YEAR(c.fecha_cita) = ?
            ORDER BY c.fecha_cita ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$mes, $anio]);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error BD: " . $e->getMessage());
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',9);

foreach($citas as $row) {
    $fecha = date('d/m/Y', strtotime($row['fecha_cita']));
    $hora = date('H:i', strtotime($row['hora_cita']));
    
    $pdf->Cell(25,10,$fecha,1,0,'C');
    $pdf->Cell(20,10,$hora,1,0,'C');
    $pdf->Cell(40,10, mb_convert_encoding(substr($row['mascota'],0,20), 'ISO-8859-1', 'UTF-8'),1);
    $pdf->Cell(45,10, mb_convert_encoding(substr($row['dueno'],0,23), 'ISO-8859-1', 'UTF-8'),1);
    $pdf->Cell(40,10, mb_convert_encoding(substr($row['servicio'],0,20), 'ISO-8859-1', 'UTF-8'),1);
    
    // Si el estado es "Confirmada" (que en tu sistema es Atendida), mostramos "Atendida"
    $estadoTexto = $row['estado'];
    if($estadoTexto == 'Confirmada') $estadoTexto = 'Atendida';
    
    $pdf->Cell(20,10, $estadoTexto, 1, 0, 'C');
    $pdf->Ln();
}

$pdf->Output('I', 'Reporte_Citas.pdf');
?>