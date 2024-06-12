<?php
require '../config/database.php';
require '../config/config.php';

if(!isset($_SESSION['user_type'])){
    header("Location: ../index.php");
    exit();
}

if($_SESSION['user_type'] != 'admin'){
    header("Location: ../../index.php");
    exit();
}

$db = new Database();
$con = $db->conectar();

$nombre = $_POST['nombre'];

$sql = $con->prepare("INSERT INTO categorias (cat_nombre, cat_activo) VALUES (?, 1)");

$sql->execute([$nombre]);

header('Location: index.php');

