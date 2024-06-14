<?php
require 'config/config.php';

require 'clases/clienteFunciones.php';

$db = new Database();
$con = $db->conectar();

$errors = [];
if (!empty($_POST)) {
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    if (esNulo([$nombres, $apellidos, $email, $telefono, $direccion, $usuario, $password, $repassword])) {
        $errors[] = "Debe llenar todos los campos";
    }
    if (!esEmail($email)) {
        $errors[] = "El email no es valido";
    }
    if (!validarPassword($password, $repassword)) {
        $errors[] = "Las contraseñas no coiciden";
    }
    if (validarUsuario($usuario, $con)) {
        $errors[] = "El usuario, $usuario ya existe";
    }
    if (validarEmail($email, $con)) {
        $errors[] = "El correo electronico, $email ya existe";
    }
    if (count($errors) == 0) {


        $id = registraCliente([$nombres, $apellidos, $email, $telefono, $direccion], $con);

        if ($id > 0) {

            require 'clases/Mailer.php';
            $mailer = new Mailer();
            $token = generarToken();
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $idUsuario = registraUsuario([$usuario, $pass_hash, $token, $id], $con);


            if ($idUsuario > 0) {
                $url = SITE_URL . '/activa_cliente.php?id=' . $id . '&token=' . $token;
                $asunto = "Activar Cuenta Tienda Online";
                $cuerpo = "
                <html>
                <head>
                    <title>Activar Cuenta Tienda Online</title>
                    <style>
                        .container {
                            width: 100%;
                            max-width: 600px;
                            margin: 0 auto;
                            padding: 20px;
                            font-family: Arial, sans-serif;
                        }
                        .header {
                            text-align: center;
                            padding: 10px 0;
                            background-color: #f4f4f4;
                            border-bottom: 1px solid #ddd;
                        }
                        .content {
                            padding: 20px;
                            text-align: center;
                        }
                        .button {
                            display: inline-block;
                            padding: 10px 20px;
                            margin-top: 20px;
                            font-size: 16px;
                            color: #fff;
                            background-color: #007bff;
                            border-radius: 5px;
                            text-decoration: none;
                        }
                        .footer {
                            padding: 10px 0;
                            background-color: #f4f4f4;
                            border-top: 1px solid #ddd;
                            text-align: center;
                            font-size: 12px;
                            color: #777;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>Tienda Online</h1>
                        </div>
                        <div class='content'>
                            <h2>¡Hola, $nombres!</h2>
                            <p>Gracias por registrarte en Tienda Online. Para completar tu registro y activar tu cuenta, por favor haz clic en el siguiente enlace:</p>
                            <a href='$url' class='button'>Activar Cuenta</a>
                            <p>Si no puedes hacer clic en el enlace, copia y pega la siguiente URL en tu navegador:</p>
                            <p><a href='$url'>$url</a></p>
                        </div>
                        <div class='footer'>
                            <p>&copy; 2024 Tienda Online. Todos los derechos reservados.</p>
                        </div>
                    </div>
                </body>
                </html>
                ";

                if ($mailer->enviarEmail($email, $asunto, $cuerpo)) {
                    header("Location: confirmacion.php?email=" . $email);
                    exit;
                }                
            } else {
                $errors[] = 'Error al registrar un usuario';
            }
        } else {
            $errors[] = 'Error al registrar un cliente';
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
    <header>
        <div class="navbar navbar-dark bg-dark navbar-expand-lg">
            <div class="container">
                <a href="#" class="navbar-brand">

                    <strong>Tienda Online</strong>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarHeader">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a href="#" class="nav-link active">Catalogo</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link ">Contacto</a>
                        </li>
                    </ul>
                    <a href="checkout.php" class="btn btn-primary">Carrito <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart ?></span></a>
                </div>
            </div>
        </div>
    </header>
    <main>
        <div class="container">
            <h2>Datos del cliente</h2>

            <?php mostrarMensajes($errors); ?>
            <form class="row g-3" action="registro.php" method="post" autocomplete="off">
                <div class="col-md-6">
                    <label for="nombres"><span class="text-danger">*</span>Nombres:</label>
                    <input type="text" class="form-control" id="nombres" name="nombres" requireda>
                </div>
                <div class="col-md-6">
                    <label for="apellidos"><span class="text-danger">*</span>Apellido:</label>
                    <input type="text" class="form-control" id="apellidos" name="apellidos" requireda>
                </div>
                <div class="col-md-6">
                    <label for="email"><span class="text-danger">*</span>Email:</label>
                    <input type="email" class="form-control" id="email" name="email" requireda>
                    <span id="validaEmail" class="text-danger"></span>
                </div>
                <div class="col-md-6">
                    <label for="telefono"><span class="text-danger">*</span>Telefono:</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" requireda>
                </div>
                <div class="col-md-6">
                    <label for="direccion"><span class="text-danger">*</span>Direccion:</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" requireda>
                </div>
                <div class="col-md-6">
                    <label for="usuario"><span class="text-danger">*</span>Usuario:</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" requireda>
                    <span id="validaUsuario" class="text-danger"></span>
                </div>
                <div class="col-md-6">
                    <label for="password"><span class="text-danger">*</span>Contraseña:</label>
                    <input type="password" class="form-control" id="password" name="password" requireda>
                </div>
                <div class="col-md-6">
                    <label for="repassword"><span class="text-danger">*</span>Repetir Contraseña:</label>
                    <input type="password" class="form-control" id="repassword" name="repassword" requireda>
                </div>
                <i><b>Nota:</b> Los campos con <span class="text-danger">*</span> son obligatorios</i>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        let txtusuario = document.getElementById('usuario')
        txtusuario.addEventListener("blur", function() {
            existeUsuario(txtusuario.value)
        }, false)

        let txtemail = document.getElementById('email')
        txtemail.addEventListener("blur", function() {
            existeEmail(txtemail.value)
        }, false)

        function existeUsuario(usuario) {
            let url = "clases/clienteAjax.php"
            let formData = new FormData()
            formData.append("action", "existeUsuario")
            formData.append("usuario", usuario)

            fetch(url, {
                    method: "POST",
                    body: formData,
                }).then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        document.getElementById('usuario').value = ''
                        document.getElementById('validaUsuario').innerHTML = 'Usuario en uso'
                    } else {
                        document.getElementById('validaUsuario').innerHTML = ''

                    }

                })
        }

        function existeEmail(email) {
            let url = "clases/clienteAjax.php"
            let formData = new FormData()
            formData.append("action", "existeEmail")
            formData.append("email", email)

            fetch(url, {
                    method: "POST",
                    body: formData,
                }).then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        document.getElementById('email').value = ''
                        document.getElementById('validaEmail').innerHTML = 'Email en uso'
                    } else {
                        document.getElementById('validaEmail').innerHTML = ''

                    }

                })
        }
    </script>
</body>


</html>