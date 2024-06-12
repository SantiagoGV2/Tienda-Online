<?php
require 'config/config.php';

require 'clases/clienteFunciones.php';

$db = new Database();
$con = $db->conectar();

$token = generarToken();
$_SESSION['token'] = $token;


$idCliente = $_SESSION['user_cli_id'];

$sql = $con->prepare("SELECT tran_id, com_fecha, com_status, com_total FROM compras WHERE cli_id=? ORDER BY DATE(com_fecha) DESC");
$sql->execute([$idCliente]);
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

<body>
    <?php include 'menu.php';    ?>
    <main>
        <div class="container">
            <h4>Mis compras</h4>
            <hr>
            <?php while($row = $sql->fetch(PDO::FETCH_ASSOC)){?>
            <div class="card mb-3 bg-secondary border-1 border-black">
                <div class="card-header text-dark">
                    <?php echo $row['com_fecha']?>
                </div>
                <div class="card-body">
                    <h5 class="card-title text-dark">Folio de la compra: <?php echo $row['tran_id']?></h5>
                    <p class="card-text text-dark">Total: <?php echo MONEDA . number_format($row['com_total'], 2, '.', ','); ?></p>
                    <a href="compra_detalle.php?orden=<?php echo $row['tran_id'] ?>&token=<?php echo $token ?>" class="btn btn-primary text-ligth">Ver compra</a>
                </div>
            </div>
            <?php }?>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>


</html>