<?php
require 'config/config.php';

require 'clases/clienteFunciones.php';
date_default_timezone_set('America/Bogota');
$token_sesion = $_SESSION['token'];
$orden = $_GET['orden'] ?? null;
$token = $_GET['token'] ?? null;

if ($orden == null || $token == null || $token != $token_sesion) {
    header('Location: compras.php');
    exit();
}

$db = new Database();
$con = $db->conectar();

$sqlCompra = $con->prepare("SELECT com_id, com_fecha, com_total, tran_id FROM compras WHERE tran_id=? LIMIT 1");
$sqlCompra->execute([$orden]);
$rowCompra = $sqlCompra->fetch(PDO::FETCH_ASSOC);
$idCompra = $rowCompra['com_id'];

$fecha = new DateTime($rowCompra['com_fecha']); // Create DateTime object from the string
$fecha->setTimezone(new DateTimeZone('America/Bogota')); // Convert to 'America/Bogota' timezone
$formattedFecha = $fecha->format('Y-m-d H:i:s');

$sqlDetalle = $con->prepare("SELECT det_id,det_nombre,det_precio,det_cantidad FROM detallecompras WHERE com_id=?");
$sqlDetalle->execute([$idCompra]);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <?php include 'menu.php';    ?>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Detalle de la compra</strong>
                        </div>
                        <div class="card-body">
                            <p><strong>Fecha:</strong><?php echo $formattedFecha ; ?></p>
                            <p><strong>Orden:</strong><?php echo $rowCompra['tran_id']; ?></p>
                            <p><strong>Total:</strong><?php echo MONEDA . ' ' . number_format($rowCompra['com_total'], 2, '.', ','); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $sqlDetalle->fetch(PDO::FETCH_ASSOC)){
                                    $precio = $row['det_precio'];
                                    $cantidad = $row['det_cantidad'];
                                    $subtotal = $precio * $cantidad
                                 ?>
                                    <tr>
                                        <td><?php echo $row['det_nombre'] ?></td>
                                        <td><?php echo MONEDA . ' ' . number_format($precio, 2, '.', ',') ?></td>
                                        <td><?php echo $cantidad ?></td>
                                        <td><?php echo MONEDA . ' ' . number_format( $subtotal, 2, '.', ',') ?></td>
                                    </tr>
                                 <?php } ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>


</html>