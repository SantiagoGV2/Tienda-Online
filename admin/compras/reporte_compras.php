<?php 

require '../config/config.php';
require '../config/database.php';
require '../fpdf/plantilla_reporte_compras.php';


if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$db = new Database();
$con = $db->conectar();

$fechaIni = $_POST['fecha_ini'] ?? '2024-01-01';
$fechaFin = $_POST['fecha_fin'] ?? '2025-01-01';

$query="SELECT date_format(c.com_fecha,'%d/%m/%Y %H:%i') AS fechaHora, c.com_status, cli.cli_direccion, c.com_total, c.com_mediopago, CONCAT(cli.cli_nombre,' ',cli.cli_apellidos) AS cliente 
FROM compras AS c 
INNER JOIN clientes AS cli ON c.cli_id = cli.cli_id
WHERE DATE(c.com_fecha) BETWEEN ? AND ?
ORDER BY DATE(com_fecha)ASC";

$resultado = $con->prepare($query);
$resultado->execute([$fechaIni,$fechaFin]);

$datos=[
    'fechaIni'=>$fechaIni,
    'fechaFin'=>$fechaFin,
];

$pdf = new PDF('L','mm', 'Letter',$datos);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

while($row = $resultado->fetch(PDO::FETCH_ASSOC)){
    $pdf->Cell(40,6,$row['fechaHora'],1,0);
    $pdf->Cell(40,6,$row['com_status'],1,0);
    $pdf->Cell(60,6,mb_convert_encoding($row['cliente'],'ISO-8859-1','UTF-8'),1,0);
    $pdf->Cell(40,6,$row['cli_direccion'],1,0);
    $pdf->Cell(40,6,$row['com_total'],1,0);
    $pdf->Cell(40,6,$row['com_mediopago'],1,1);
}
$pdf->Output();