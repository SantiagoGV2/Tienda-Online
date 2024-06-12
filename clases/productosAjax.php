<?php


require_once '../config/database.php';

$datos = [];

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    $db = new Database();
    $con = $db->conectar();

    if ($action == 'buscarColoresPorTalla') {
        $datos['colores'] = buscarColoresPorTalla($con);
    }elseif($action = 'buscaIdVariante'){
        $datos['variante'] = buscaIdVariante($con);
    }
}

function buscarColoresPorTalla($con){

    $idProducto = $_POST['pro_id'] ?? 0;
    $idTalla = $_POST['tal_id'] ?? 0;

    $sqlColores = $con->prepare("SELECT DISTINCT c.col_id, c.col_nombre FROM pro_variantes AS pv 
    INNER JOIN colores AS c ON pv.col_id = c.col_id 
    WHERE pv.pro_id = ? AND pv.tal_id=?");
    $sqlColores->execute([$idProducto,$idTalla]);
    $colores = $sqlColores->fetchAll(PDO::FETCH_ASSOC);

    $html = '';

    foreach ($colores as $color){
        $html.= '<option value="'.$color['col_id'].'">'.$color['col_nombre'].'</option>';
    }
    return $html;
}

function buscaIdVariante($con){

    $idProducto = $_POST['pro_id'] ?? 0;
    $idTalla = $_POST['tal_id'] ?? 0;
    $idColor = $_POST['col_id'] ?? 0;

    $sql = $con->prepare("SELECT vari_id, vari_precio, vari_stock FROM pro_variantes
    WHERE pro_id = ? AND tal_id=? AND col_id=? LIMIT 1");
    $sql->execute([$idProducto,$idTalla,$idColor]);
    return $sql->fetch(PDO::FETCH_ASSOC);
}
echo json_encode($datos);
