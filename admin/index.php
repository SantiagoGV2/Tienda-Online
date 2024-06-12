<?php

require 'config/database.php';
require 'config/config.php';
require 'clases/adminFunciones.php';

$db = new Database();
$con = $db->conectar();


/*$password = password_hash('admin', PASSWORD_DEFAULT);
$sql = "INSERT INTO admins (adm_usuario,adm_password,adm_nombre,adm_email,adm_activo,adm_fecha_alta) VALUES ('admin','$password','Administrador','santigarciavel33@gmail.com','1', NOW())";
$con-> query($sql);*/

$errors = [];

if(!empty($_POST)){//Validar si el post no esta vacio
    $usuario = trim($_POST['usuario']);//TRIM elina los espacios al inicio y al final
    $password = trim($_POST['password']);

    if(esNulo([$usuario, $password])){
        $errors [] = "Debe llenar todos los campos";
    }

    if(count($errors) == 0){
       $errors[] = login($usuario, $password, $con);
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <link rel="stylesheet" href="CSS/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="bg-primary container">
    <main class="form-login pt-4 m-auto row justify-content-center">
        <form class="row g-3 bg-light rounded p-4" action="index.php" method="post" autocomplete="off">
            <div class="card-header">
                <h2 class="text-center font-weight-light my-4">Iniciar sesión</h2>
            </div>

            <input type="hidden" name="proceso" value="">

            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="usuario" id="usuario" placeholder="Usuario">
                <label for="usuario">Usuario</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña">
                <label for="password">Contraseña</label>
            </div>
            <?php echo mostrarMensajes($errors); ?>
            <div class="col-12">
                <a href="recupera.php" class="text-dark">¿Olvidaste tu contraseña?</a>
            </div>
            <div class="d-grid gap-3 col-12">
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </div>
            <hr>
        </form>
    </main>
    <script src="https://kit.fontawesome.com/1acde824b3.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>