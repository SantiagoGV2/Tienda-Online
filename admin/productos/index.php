<?php
require '../config/database.php';
require '../config/config.php';
require '../header2.php';

if (!isset($_SESSION['user_type'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$db = new Database();
$con = $db->conectar();



$sql = ("SELECT pro_id, pro_nombre, pro_descripcion, pro_precio, pro_stock, cat_id FROM productos WHERE pro_activo = 1");
$resultado = $con->query($sql);
$productos = $resultado->fetchAll(PDO::FETCH_ASSOC);


?>
<main>
    <div class="container-fluid px-4">
        <h1 class="mt-3">Productos</h1>
        <a href="nuevo.php" class=" btn btn-primary">Nuevo</a>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Stock</th>
                        <th scope="col"></th>
                        <th scope="col"></th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['pro_nombre'], ENT_QUOTES); ?></td>
                            <td><?php echo $producto['pro_precio']; ?></td>
                            <td><?php echo $producto['pro_stock']; ?></td>
                            <td>
                                <a href="edita.php?id=<?php echo $producto['pro_id']; ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalElimina" data-bs-id="<?php echo $producto['pro_id']; ?>">
                                    <i class="fa-solid fa-trash"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</main>



<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div class="modal fade" id="modalElimina" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Confirmar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">¿Desea eliminar el registro?</div>
            <div class="modal-footer">
                <form action="eliminar.php" method="post">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Optional: Place to the bottom of scripts -->
<script>
    let eliminaModal = document.getElementById('modalElimina');
    eliminaModal.addEventListener('show.bs.modal', function(event) {
        let button = event.relatedTarget;
        let id = button.getAttribute('data-bs-id');

        let modalInput = eliminaModal.querySelector('.modal-footer input');
        modalInput.value = id;
    });
</script>

<?php
require '../footer.php';
?>