<?php
require '../config/config.php';
date_default_timezone_set('America/Bogota');
$db = new Database();
$con = $db->conectar();

$id_transaccion = isset($_GET['payment_id']) ? $_GET['payment_id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Mapear estados de Mercado Pago a un estado unificado
if ($status == 'approved') {
    $status = 'COMPLETED';
}

if ($id_transaccion != '') {
    $fecha = new DateTime("now", new DateTimeZone('America/Bogota'));
    $formattedFecha = $fecha->format('Y-m-d H:i:s');
    $total = isset($_SESSION['carrito']['total']) ? $_SESSION['carrito']['total'] : 0;
    $id_cliente = $_SESSION['user_cli_id'];
    $sql = $con->prepare("SELECT cli_email FROM clientes WHERE cli_id=? AND cli_status=1");
    $sql->execute([$id_cliente]);
    $row_cliente = $sql->fetch(PDO::FETCH_ASSOC);
    $email = $row_cliente['cli_email'];

    $sql = $con->prepare("INSERT INTO compras(com_fecha, com_status, com_email, com_total, com_mediopago, tran_id, cli_id) 
                          VALUES(?,?,?,?,?,?,?)");
    $sql->execute([$formattedFecha, $status, $email, $total, 'MP', $id_transaccion, $id_cliente]);
    $id = $con->lastInsertId();

    if ($id > 0) {
        $productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

        if ($productos != null) {
            foreach ($productos as $clave => $cantidad) {
                $sql = $con->prepare("SELECT pro_id, pro_nombre, pro_precio FROM productos WHERE pro_id=? AND pro_activo=1");
                $sql->execute([$clave]);
                $row_prod = $sql->fetch(PDO::FETCH_ASSOC);

                $precio = $row_prod['pro_precio'];
                

                $sql_insert = $con->prepare("INSERT INTO detallecompras(det_nombre, det_precio, det_cantidad, com_id, pro_id) 
                                             VALUES(?,?,?,?,?)");
                if ($sql_insert->execute([$row_prod['pro_nombre'], $precio, $cantidad, $id, $clave])) {
                    restarStock($row_prod['pro_id'], $cantidad, $con);
                }
            }
            require 'Mailer.php';

            $asunto = 'Confirmación de Compra - Detalle de Transacción';
            $cuerpo = '<h3>Estimado/a cliente,</h3>'
                    . '<p>Gracias por realizar su compra en nuestra tienda en línea. A continuación, encontrará los detalles de su transacción:</p>'
                    . '<p>Identificador de Transacción: <b>' . $id_transaccion . '</b></p>'
                    . '<p>Para cualquier consulta adicional, no dude en contactarnos.</p>'
                    . '<p>Atentamente,</p>'
                    . '<p>El equipo de nuestra tienda</p>';
            $mailer = new Mailer();
            $mailer->enviarEmail($email, $asunto, $cuerpo);
        }
        unset($_SESSION['carrito']);
        header("Location: " . SITE_URL . "/completado_mp.php?key=" . $id_transaccion);
    }
}

function restarStock($id, $cantidad, $con)
{
    $sql = $con->prepare("UPDATE productos SET pro_stock= pro_stock - ? WHERE pro_id=?");
    $sql->execute([$cantidad, $id]);
}
