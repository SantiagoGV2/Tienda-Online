<?php
require '../config/database.php';
require '../config/config.php';
require '../header.php';


if (!isset($_SESSION['user_type'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../../index.php");
    exit();
}

$db = new Database();
$con = $db->conectar();


?>
<main>
    <div class="container-fluid px-4">
        <h2 class="mt-3">Nueva Categoria</h2>

        <form action="guarda.php" method="post" autocomplete="off">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" id="nombre" required autofocus />
                <button type="submit" class="btn btn-primary">
                    Guardar
                </button>
        </form>
    </div>
</main>
<?php
require '../footer.php';
?>