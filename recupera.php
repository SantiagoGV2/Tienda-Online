<?php
require 'config/config.php';

require 'clases/clienteFunciones.php';

$db = new Database();
$con = $db->conectar();

$errors = [];

if (!empty($_POST)) {

    $email = trim($_POST['email']);


    if (esNulo([$email])) {
        $errors[] = "Debe llenar todos los campos";
    }
    if (!esEmail($email)) {
        $errors[] = "El email no es valido";
    }
    if (count($errors) == 0) {
        if (validarEmail($email, $con)) {
            $sql = $con->prepare("SELECT usuarios.usu_id, clientes.cli_nombre FROM usuarios
            INNER JOIN clientes ON usuarios.usu_id = clientes.cli_id WHERE clientes.cli_email LIKE ? LIMIT 1");

            $sql->execute([$email]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $user_id = $row['usu_id'];
            $nombre = $row['cli_nombre'];

            $token = solicitaPassword($user_id, $con);

            if ($token !== null) {
                require 'clases/Mailer.php';
                $mailer = new Mailer();

                $url = SITE_URL . '/reset_password.php?id=' . $user_id . '&token=' . $token;
                $asunto = "Recuperar Password - Tienda Online";
                $cuerpo = "
                <html>
                <head>
                    <title>Recuperar Password - Tienda Online</title>
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
                            <h2>¡Hola, $nombre!</h2>
                            <p>Si has solicitado el cambio de tu contraseña, por favor haz clic en el siguiente enlace:</p>
                            <a href='$url' class='button'>Recuperar Contraseña</a>
                            <p>Si no puedes hacer clic en el enlace, copia y pega la siguiente URL en tu navegador:</p>
                            <p><a href='$url'>$url</a></p>
                            <p>Si no hiciste esta solicitud, puedes ignorar este correo.</p>
                        </div>
                        <div class='footer'>
                            <p>&copy; 2024 Tienda Online. Todos los derechos reservados.</p>
                        </div>
                    </div>
                </body>
                </html>
                ";

                if ($mailer->enviarEmail($email, $asunto, $cuerpo)) {
                    header("Location: recuperaPass.php?email=" . $email);
                    exit;
                }
            } else {
                $errors[] = "No existe una cuanta asociada a este correo electronico";
            }
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
    <link rel="stylesheet" href="CSS/styles.css">
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
    <main class="form-login m-auto pt-4">
        <h3 class="text-center">Recuperar contraseña</h3>
        <?php mostrarMensajes($errors); ?>

        <form action="recupera.php" method="post" class="row g-3" autocomplete="off">
            <div class="form-floating mb-3">
                <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
                <label for="email">Correo electronico</label>
            </div>
            <div class="d-grid gap-3 col-12">
                <button type="submit" class="btn btn-primary">Continuar</button>
            </div>
            <div class="col-12">
                ¿No tiene cuenta? <a href="registro.php">Registrate aquí</a>
            </div>
        </form>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>


</html>