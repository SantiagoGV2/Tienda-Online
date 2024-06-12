<?php
require '../config/config.php';
date_default_timezone_set('America/Bogota');
$db = new Database();
$con = $db->conectar();

$json = file_get_contents('php://input');
$datos = json_decode($json,true);

if(is_array($datos)){
    $id_cliente = $_SESSION['user_cli_id'];
    $sql = $con->prepare("SELECT cli_email FROM clientes WHERE cli_id=? AND cli_status=1");
    $sql->execute([$id_cliente]);
    $row_cliente = $sql->fetch(PDO::FETCH_ASSOC);

    $id_transaccion = $datos['detalles']['id'];
    $total = $datos['detalles']['purchase_units'][0]['amount']['value'];
    $status = $datos['detalles']['status'];
    $fecha = $datos['detalles']['update_time'];
    $fecha = new DateTime("now", new DateTimeZone('America/Bogota'));
    $formattedFecha = $fecha->format('Y-m-d H:i:s');
    $email = $row_cliente['cli_email'];
    //$email = $datos['detalles']['payer']['email_address'];
    //$id_cliente = $datos['detalles']['payer']['payer_id'];

    $sql = $con -> prepare("INSERT INTO compras(com_fecha,com_status,com_email,com_total,com_mediopago,tran_id,cli_id) 
    VALUES(?,?,?,?,?,?,?)");
    $sql->execute([$formattedFecha,$status,$email,$total,'paypal',$id_transaccion,$id_cliente]);
    $id =$con->lastInsertId();

    if($id>0){
        $productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

        if ($productos != null) {
            foreach ($productos as $clave => $cantidad) {
                $sql = $con->prepare("SELECT pro_id, pro_nombre, pro_precio FROM productos WHERE pro_id=? AND pro_activo=1");
                $sql->execute([$clave]);
                $row_prod = $sql->fetch(PDO::FETCH_ASSOC);

                $precio = $row_prod['pro_precio'];

                $sql_insert = $con->prepare("INSERT INTO detallecompras(det_nombre,det_precio,det_cantidad,com_id,pro_id) 
                VALUES(?,?,?,?,?)");
                if($sql_insert->execute([$row_prod['pro_nombre'],$precio,$cantidad,$id,$clave])){
                    restarStock($row_prod['pro_id'],$cantidad, $con);
                }
            }
            require 'Mailer.php';

            $asunto = 'Detalle de su compra';
            $cuerpo ='<h4>Gracias por su compra</h4><p>El ID de su compra es <b>' . $id_transaccion . '</b></p>';
            $mailer = new Mailer();
            $mailer->enviarEmail($email,$asunto,$cuerpo);

        }
        unset($_SESSION['carrito']);
    }
}

function restarStock($id, $cantidad,$con){
    $sql = $con->prepare("UPDATE productos SET pro_stock= pro_stock - ? WHERE pro_id=?");
    $sql->execute([$cantidad, $id]);
}