<?php
require 'config/config.php';
date_default_timezone_set('America/Bogota');
$db = new Database();
$con = $db->conectar();

$id_transaccion = isset($_GET['key']) ? $_GET['key'] : '0';         
$error = '';

if($id_transaccion == '') {
    $error = 'Error al procesar la peticion';
} else {
    $sql = $con->prepare("SELECT count(com_id) FROM compras WHERE tran_id=? AND com_status=?");
    $sql->execute([$id_transaccion, 'COMPLETED']);

    if ($sql->fetchColumn() > 0) {
        $sql = $con->prepare("SELECT com_id, com_fecha, com_email, com_total FROM compras WHERE tran_id=? AND com_status=? LIMIT 1");
        $sql->execute([$id_transaccion, 'COMPLETED']);
        $row = $sql->fetch(PDO::FETCH_ASSOC);

        $idCompra = $row['com_id'];
        $totalCompra = $row['com_total'];
        $fecha = new DateTime($row['com_fecha']); // Create DateTime object from the string
        $fecha->setTimezone(new DateTimeZone('America/Bogota')); // Convert to 'America/Bogota' timezone
        $formattedFecha = $fecha->format('Y-m-d H:i:s');
    
        $sqlDet = $con->prepare("SELECT det_nombre, det_precio, det_cantidad FROM detallecompras WHERE com_id=?");
        $sqlDet->execute([$idCompra]);
    } else {
        $error = 'Error al comprobar la compra';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php include 'menu.php';?>
    <main>
        <div class="container">
            <?php if (strlen($error) > 0) { ?>
                <div class="row">
                    <div class="col">
                        <h3><?php echo $error; ?></h3>
                    </div>
                </div>
            <?php } else { ?>
                <div class="row">
                    <div class="col">
                        <b>Folio de la compra: </b><?php echo $id_transaccion; ?><br>
                        <b>Fecha de compra: </b><?php echo $formattedFecha; ?><br>
                        <b>Total de la compra: </b><?php echo MONEDA . number_format($totalCompra, 2, '.', ','); ?><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Cantidad</th>
                                    <th>Producto</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row_det = $sqlDet->fetch(PDO::FETCH_ASSOC)) {
                                    $importe = $row_det['det_precio'] * $row_det['det_cantidad']; ?>
                                    <tr>
                                        <td><?php echo $row_det['det_cantidad']; ?></td>
                                        <td><?php echo $row_det['det_nombre']; ?></td>
                                        <td><?php echo MONEDA . number_format($importe * EXCHANGE_RATE, 2, '.', ','); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>
    </main>
</body>
</html>
