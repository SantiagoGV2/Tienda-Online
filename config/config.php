<?php

$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;

require_once $path . 'database.php';
require_once $path . '../admin/clases/cifrado.php';

$db = new Database();
$con = $db->conectar();

$sql = "SELECT con_nombre, con_valor FROM configs";
$resultado = $con->query($sql);
$datosConfig = $resultado->fetchAll(PDO::FETCH_ASSOC);

$config = [];
foreach ($datosConfig as $datoConfig) {
    $config[$datoConfig['con_nombre']] = $datoConfig['con_valor'];
}


define("CLIENT_ID", "AR209tlvtz32KMZQwNhucVqQWDIDrY4qXnOnMFOc9FDPHiLTCDg_l2Q5akkY6kYVWfh4GBYw0kZiQ7AI");
define("CURRENCY", "USD");
define("KEY_TOKEN", "SJR-02.4*rf");
define("MONEDA", "$");
define("SITE_URL", "http://localhost/Tienda-2.1/");

define("TOKEN", "TEST-4548260847395468-051607-89c1dd079a744e2a99e2264508063ee3-1770793831");    

define("MAIL_HOST", $config['correo_smtp']);

define("MAIL_USER", $config['correo_email']);

define("MAIL_PASS", descifrar($config['correo_password']));

define("MAIL_PORT", $config['correo_puerto']);
define('EXCHANGE_RATE', 0.00027);

session_start();

$num_cart = 0;
if (isset($_SESSION['carrito']['productos'])) {
    $num_cart = count($_SESSION['carrito']['productos']);
}
