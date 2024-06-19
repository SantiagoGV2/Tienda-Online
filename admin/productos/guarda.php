<?php
require '../config/database.php';
require '../config/config.php';
require '../clases/adminFunciones.php';

if(!isset($_SESSION['user_type'])){
    header("Location: ../index.php");
    exit();
}

if($_SESSION['user_type'] != 'admin'){
    header("Location: ../index.php");
    exit();
}

$db = new Database();
$con = $db->conectar();

$nombre = $_POST['nombre'];
$slug = crearUrl($nombre);
$descripcion = $_POST['descripcion'];
$precio = $_POST['precio'];
$stock = $_POST['stock'];
$categoria = $_POST['categoria'];


$sql= "INSERT INTO productos (pro_slug, pro_nombre, pro_descripcion, pro_precio, pro_stock, pro_activo, cat_id) VALUES(?, ?, ?, ?, ?, 1, ?)";
$stm = $con->prepare($sql);

if($stm->execute([$slug,$nombre,$descripcion,$precio,$stock,$categoria])){
    $id = $con->lastInsertId();

    if($_FILES['imagen_principal']['error'] == UPLOAD_ERR_OK){
        $dir = '../../img/productos/' . $id . '/';
        $permitidos = ['jpeg','jpg'];

        $arregloImagen = explode('.', $_FILES['imagen_principal']['name']);
        $extension = strtolower(end($arregloImagen));//el ultimo indece que tenga este arreglo va ser la extencion y pasalo a minisculas(strtolower)

        //aseguramos que exista toda la estructura de la direccion de las imagenes
        if(in_array($extension,$permitidos)){
            if(!file_exists($dir)){
                mkdir($dir, 0777, true);  
            }
            $ruta_img = $dir. 'principal2.' . $extension;
            if(move_uploaded_file( $_FILES['imagen_principal']['tmp_name'], $ruta_img)){
                echo 'El archivo se cargo correctamente';
            }else{
                echo 'El archivo no se cargo correctamente';
            }
        }else{
            echo 'Archivo no permitido';
        }
    }else{
        echo 'No se cargo ninguna imagen';
    }
}




header('Location: index.php');

