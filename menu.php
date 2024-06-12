<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <header>
        <div class="navbar navbar-dark bg-dark navbar-expand-lg">
        <img src="admin/img/logo.jpeg" alt="" class="rounded"  style="width: 5%;" >
            <div class="container">
                <a href="index.php" class="navbar-brand">
                    <strong>Tienda Fibra Optica</strong>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarHeader">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    </ul>
                    <form action="index.php" method="get" autocomplete="off">
                        <div class="input-group pe-3">
                            <input type="text" name="q" id="q" class="form-control form-control-sm" placeholder="Buscar..." aria-describedby="icon-buscar">
                            <button ty
                            pe="submit" id="icon-buscar" class="btn btn-outline-info btn-sm">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>
                    </form>
                    <a href="checkout.php" class="btn btn-primary me-2 btn-sm"><i class="fas fa-shopping-cart"></i> Carrito <span id="num_cart" class="badge bg-secondary"><?php echo $num_cart ?></span></a>
                    <?php if (isset($_SESSION['user_id'])) { ?>

                        <div class="dropdown">
                            <button class="btn btn-success dropdown-toggle btn-sm" id="btn_session" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> &nbsp; <?php echo $_SESSION['user_usuario']; ?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btn_session">
                                <li><a class="dropdown-item" href="actualizarDatos.php">Actualizar Cuenta</a></li>
                                <li><a class="dropdown-item" href="compras.php">Mis Compras</a></li>
                                <li><a class="dropdown-item" href="logout.php">Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    <?php } else { ?>
                        <a href="login.php" class="btn btn-success"><i class="fas fa-user-circle"></i>Ingresar</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </header>
    <script src="https://kit.fontawesome.com/1acde824b3.js" crossorigin="anonymous"></script>
</body>

</html>