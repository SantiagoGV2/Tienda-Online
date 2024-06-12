<?php
require '../config/config.php';
require '../config/database.php';



if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$db = new Database();
$con = $db->conectar();

$id = $_POST['id'];

$sql = $con->prepare("UPDATE usuarios SET usu_activacion= 2 WHERE usu_id = ?");
$sql->execute([$id]);

header("Location: index.php");