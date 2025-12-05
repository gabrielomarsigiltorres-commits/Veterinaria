<?php
session_start();
if (!isset($_SESSION['logueado']) || trim($_SESSION['tipo_usuario']) !== 'administrador') {
    die("Acceso denegado");
}


require('../fpdf.php');
require('../Modelo/conexion.php');

$mes = $_GET['mes'] ?? date('n');
$anio = $_GET['anio'] ?? date('Y');

// Array de nombres de meses
$nombresMeses = [1=>"Enero", 2=>"Febrero", 3=>"Marzo", 4=>"Abril", 5=>"Mayo", 6=>"Junio", 7=>"Julio", 8=>"Agosto", 9=>"Septiembre", 10=>"Octubre", 11=>"Noviembre", 12=>"Diciembre"];
$nombreMes = $nombresMeses[$mes];

class PDF extends FPDF {
    // Cabecera de página
    function Header() {
        global $nombreMes, $anio;
        // Logo (Asegúrate que la ruta sea correcta o comenta la línea)
        // $this->Image('../img/logo.jpg',10,8,33);
        $this->SetFont('Arial','B',15);
        $this->Cell(80);
        $this->Cell(30,10,'Reporte de Ventas Mensual',0,0,'C');
        $this->Ln(10);
        $this->SetFont('Arial','',12);
        $this->Cell(0,10, mb_convert_encoding("$nombreMes - $anio", 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');
        $this->Ln(20);
        
        // Encabezados de tabla
        $this->SetFillColor(0, 167, 157); // Color #00A79D
        $this->SetTextColor(255);
        $this->SetFont('Arial','B',10);
        
        $this->Cell(30,10,'Fecha',1,0,'C',true);
        $this->Cell(50,10,'Cliente',1,0,'C',true);
        $this->Cell(50,10,'Producto',1,0,'C',true);
        $this->Cell(20,10,'Cant.',1,0,'C',true);
        $this->Cell(40,10,'Total (S/)',1,0,'C',true);
        $this->Ln();
    }

    // Pie de página
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// Consulta a BD
try {
    $sql = "SELECT v.fecha_venta, p.nombre as producto, u.nombres_completos as cliente, v.cantidad, v.total_venta
            FROM ventas v
            JOIN productos p ON v.id_producto = p.id_producto
            LEFT JOIN usuarios u ON v.id_usuario_cliente = u.id
            WHERE MONTH(v.fecha_venta) = ? AND YEAR(v.fecha_venta) = ?
            ORDER BY v.fecha_venta DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$mes, $anio]);
    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error BD: " . $e->getMessage());
}

// Generación PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

$totalMes = 0;

foreach($ventas as $row) {
    $fecha = date('d/m/Y', strtotime($row['fecha_venta']));
    $cliente = !empty($row['cliente']) ? $row['cliente'] : 'Mostrador (Anonimo)';
    
    $pdf->Cell(30,10,$fecha,1);
    // Usamos mb_convert_encoding para tildes
    $pdf->Cell(50,10, mb_convert_encoding(substr($cliente,0,25), 'ISO-8859-1', 'UTF-8'),1);
    $pdf->Cell(50,10, mb_convert_encoding(substr($row['producto'],0,25), 'ISO-8859-1', 'UTF-8'),1);
    $pdf->Cell(20,10,$row['cantidad'],1,0,'C');
    $pdf->Cell(40,10, number_format($row['total_venta'], 2),1,0,'R');
    $pdf->Ln();
    
    $totalMes += $row['total_venta'];
}

// Fila de Total
$pdf->SetFont('Arial','B',10);
$pdf->Cell(150,10,'TOTAL INGRESOS',1,0,'R');
$pdf->Cell(40,10, 'S/ ' . number_format($totalMes, 2),1,0,'R');

$pdf->Output('I', 'Reporte_Ventas.pdf'); // 'I' para mostrar en navegador, 'D' para descargar directo
?>