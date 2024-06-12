<?php
require '../config/database.php';
require '../config/config.php';
require '../clases/adminFunciones.php';

if (!isset($_SESSION['user_type'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$db = new Database();
$con = $db->conectar();



$id = $_POST['id'];
$nombre = $_POST['nombre'];
$slug = crearUrl($nombre);
$descripcion = $_POST['descripcion'];
$precio = $_POST['precio'];
$stock = $_POST['stock'];
$categoria = $_POST['categoria'];


$sql = "UPDATE productos SET pro_slug=?, pro_nombre = ?, pro_descripcion = ?, pro_precio = ?, pro_stock = ?, cat_id = ? WHERE pro_id = ?";
$stm = $con->prepare($sql);

if ($stm->execute([$slug, $nombre, $descripcion, $precio, $stock, $categoria, $id])) {

    if ($_FILES['imagen_principal']['error'] == UPLOAD_ERR_OK) {
        $dir = '../../img/productos/' . $id . '/';
        $permitidos = ['jpeg', 'jpg'];

        $arregloImagen = explode('.', $_FILES['imagen_principal']['name']);
        $extension = strtolower(end($arregloImagen)); //el ultimo indece que tenga este arreglo va ser la extencion y pasalo a minisculas(strtolower)

        //aseguramos que exista toda la estructura de la direccion de las imagenes
        if (in_array($extension, $permitidos)) {
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $ruta_img = $dir . 'principal2.' . $extension;
            if (move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $ruta_img)) {
                echo 'El archivo se cargo correctamente';
            } else {
                echo 'El archivo no se cargo correctamente';
            }
        } else {
            echo 'Archivo no permitido';
        }
    } else {
        echo 'No se cargo ninguna imagen';
    }

    $idVariante = $_POST['id_variante'] ?? [];
    $talla = $_POST['talla'] ?? [];
    $color = $_POST['color'] ?? [];
    $precioVariante = $_POST['precio_variante'] ?? [];
    $stockVariante = $_POST['stock_variante'] ?? [];

    $sizeTalla = count($talla);

    if ($sizeTalla == count($color) && $sizeTalla == count($precioVariante) && $sizeTalla == count($stockVariante)) {
        $sql = "INSERT INTO pro_variantes (pro_id, tal_id , col_id, vari_precio, vari_stock) VALUES (?,?,?,?,?) ";
        $stm = $con->prepare($sql);

        $sqlUpdate = "UPDATE pro_variantes SET tal_id=? , col_id =?, vari_precio =?, vari_stock=? WHERE vari_id=?";
        $stmUpdate = $con->prepare($sqlUpdate);

        for ($i = 0; $i < $sizeTalla; $i++) {
            $idTalla = (int)$talla[$i];
            $idColor = (int)$color[$i];
            $precio = $precioVariante[$i];
            $stock = $stockVariante[$i];

            if(isset($idVariante[$i])){
                $stmUpdate->execute([$idTalla,$idColor,$precio,$stock,$idVariante[$i]]);
            }else{
                $stm->execute([$id, $idTalla, $idColor, $precio, $stock]);
            }
        }
    }
}
header('Location: index.php');
