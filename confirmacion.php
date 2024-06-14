<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card text-center" style="width: 24rem;">
            <div class="card-body">
                <img src="img/pngegg.png" class="card-img-top" alt="Check" style="width: 5rem; margin: 0 auto;">
                <h5 class="card-title mt-3">¡Registro Exitoso!</h5>
                <p class="card-text">Hemos enviado un correo de activación a <strong><?php echo htmlspecialchars($_GET['email']); ?></strong>. Por favor, revisa tu bandeja de entrada y sigue las instrucciones para activar tu cuenta.</p>
                <a href="login.php" class="btn btn-primary">Volver al inicio</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
