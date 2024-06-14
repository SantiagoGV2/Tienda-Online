<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activación de Cuenta</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        .card {
            width: 300px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .check-icon {
            width: 64px;
            height: 64px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="card">
        <?php
        if(isset($_GET['msg'])) {
            $msg = htmlspecialchars($_GET['msg']);
            echo '<img src="img/pngegg.png" alt="Check" class="check-icon">';
            echo '<p>' . $msg . '</p>';
        } else {
            echo '<p>Error: No se ha proporcionado ningún mensaje.</p>';
        }
        ?>
        <a href="login.php" class="btn btn-primary">Iniciar Sesion</a>
    </div>
</body>
</html>
