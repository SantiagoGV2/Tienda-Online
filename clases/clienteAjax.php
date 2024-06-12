<?php


require_once 'clienteFunciones.php';

$datos = [];

if(isset($_POST['action'])){
    $action = $_POST['action'];

    $db = new Database();
    $con = $db-> conectar();

    if($action == 'existeUsuario'){
        $datos['ok'] = validarUsuario($_POST['usuario'], $con);
    }elseif($action = 'existeEmail'){
        $datos['ok'] = validarEmail($_POST['email'],$con);
    }
}
echo json_encode($datos);