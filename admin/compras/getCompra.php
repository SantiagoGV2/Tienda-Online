<?php

require '../config/config.php';
require '../config/database.php';



if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$orden = $_POST['orden'] ?? null;

if ($orden == null) {
    exit;
}

$db = new Database();
$con = $db->conectar();


$sqlCompra = $con->prepare("SELECT compras.com_id, com_fecha, com_total,tran_id, CONCAT(cli_nombre,' ',cli_apellidos) AS cliente FROM compras INNER JOIN clientes ON compras.cli_id = clientes.cli_id WHERE tran_id=? LIMIT 1");
$sqlCompra->execute([$orden]);
$rowCompra = $sqlCompra->fetch(PDO::FETCH_ASSOC);

if (!$rowCompra) {
    exit;
}

$idCompra = $rowCompra['com_id'];

$fecha = new datetime($rowCompra['com_fecha']);
$fecha = $fecha->format('d-m-Y H:i');

$sqlDetalle = $con->prepare("SELECT det_id,det_nombre,det_precio,det_cantidad FROM detallecompras WHERE com_id=?");
$sqlDetalle->execute([$idCompra]);

$html = '<p><strong>Fecha: </strong>' . $fecha . '</p>';

$html .= '<p><strong>Orden: </strong>' . $rowCompra['tran_id'] . '</p>';

$html .= '<p><strong>Total: </strong>' . number_format($rowCompra['com_total'], 2, '.', ',') . '</p>';


$html .='<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th>Producto</th>
<th>Precio</th>
<th>Cantidad</th>
<th>Subtotal</th>
</tr>
</thead>';
$html .= '<tbody>';
        
while ($row = $sqlDetalle->fetch(PDO::FETCH_ASSOC)) {
            $precio = $row['det_precio'];
            $cantidad = $row['det_cantidad'];
            $subtotal = $precio * $cantidad;
            
            $html .= '<tr>';         
            $html .= '<td>'.$row['det_nombre']. '</td>';
            $html .= '<td>'. MONEDA . ' ' . number_format($precio, 2, '.', ','). '</td>';
            $html .= '<td>'. $cantidad.'</td>';
            $html .= '<td>'. MONEDA . ' ' . number_format($subtotal, 2, '.', ',').'</td>';
            $html .='</tr>';
} 

$html .='</tbody></table>';

echo json_encode($html, JSON_UNESCAPED_UNICODE);