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

$id = $_POST['id'];
$nombre = $_POST['nombre'];

$sql = $con->prepare("UPDATE categorias SET cat_nombre = ? WHERE cat_id = ?");

$sql->execute([$nombre, $id]);

header('Location: index.php');

