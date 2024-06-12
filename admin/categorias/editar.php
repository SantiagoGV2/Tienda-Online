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

$id = $_GET['id'];

$sql = $con->prepare("SELECT cat_id, cat_nombre FROM categorias WHERE cat_id = ? LIMIT 1");
$sql->execute([$id]);
$categoria = $sql->fetch(PDO::FETCH_ASSOC);


?>
<main>
    <div class="container-fluid px-4">
        <h2 class="mt-3">Editar Categoria</h2>
        <form action="actualiza.php" method="post" autocomplete="off">
            <input type="hidden" name="id" value="<?php echo $categoria['cat_id'] ?>">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" id="nombre" value="<?php echo $categoria['cat_nombre'] ?>" required autofocus />
                <button type="submit" class="btn btn-primary">
                    Guardar
                </button>
        </form>
    </div>
</main>
<?php
require '../footer.php';
?>