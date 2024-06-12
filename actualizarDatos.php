<?php
require 'config/config.php';
require 'clases/clienteFunciones.php';

$db = new Database();
$con = $db->conectar();

$errors = [];

// Obtener el ID del cliente desde la sesión (o desde algún otro lugar)
$id_usuario = $_SESSION['user_id'];

// Obtener los datos del cliente desde la base de datos
$datos_cliente = obtenerCliente($id_usuario, $con);

// Verificar si se enviaron datos por POST (cuando se envía el formulario)
if (!empty($_POST)) {
    // Recopilar los datos del formulario
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
 
    $direccion = trim($_POST['direccion']);

    // Validaciones de datos, similar a como lo tienes en registro.php

    // Si no hay errores, actualiza los datos del cliente
    if (count($errors) == 0) {
        $actualizado = actualizarCliente($id_usuario, [$nombres, $apellidos, $email, $telefono, $direccion], $con);

        if ($actualizado) {
            // Establecer el mensaje de éxito
            $mensaje = "Datos actualizados correctamente";
        } else {
            $errors[] = 'Error al actualizar los datos del cliente';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <?php include 'menu.php';?>
    <main>
        <div class="container">
            <h2>Datos del cliente</h2>

            <?php mostrarMensajes($errors);
            // Mostrar mensaje de éxito si está establecido
            if (!empty($mensaje)) {
                echo '<div class="alert alert-success" role="alert">' . $mensaje . '</div>';
            }
            ?>
            <form class="row g-3" action="actualizarDatos.php" method="post" autocomplete="off">
                <div class="col-md-6">
                    <label for="nombres"><span class="text-danger">*</span>Nombres:</label>
                    <input type="text" class="form-control" id="nombres" name="nombres" value="<?php echo $datos_cliente['cli_nombre']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="apellidos"><span class="text-danger">*</span>Apellido:</label>
                    <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo $datos_cliente['cli_apellidos']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="email"><span class="text-danger">*</span>Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $datos_cliente['cli_email']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="telefono"><span class="text-danger">*</span>Telefono:</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo $datos_cliente['cli_telefono']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="direccion"><span class="text-danger">*</span>Direccion:</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $datos_cliente['cli_direccion']; ?>" required>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </main>

    <!-- Tus scripts y cierre de HTML aquí -->
</body>

</html>