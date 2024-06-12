<?php
require '../config/config.php';
require '../config/database.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$db = new Database();
$con = $db->conectar();

$sql = "SELECT tran_id, com_fecha, com_status, clientes.cli_direccion, com_total, com_mediopago, CONCAT(cli_nombre,' ',cli_apellidos) AS cliente 
FROM compras INNER JOIN clientes ON compras.cli_id = clientes.cli_id ORDER BY DATE(com_fecha)DESC";
$resultado = $con->query($sql);

require '../header.php';
?>

<main class="flex-shrink-0">
    <div class="container mt-3">
        <h4>Compras</h4>
        <a href="genera_reporte_compras.php" class="btn btn-success btn-sm">Reporte de compras</a>
        <hr>

        <table id="tablaCompras" class="table">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Cliente</th>
                    <th>Dirección</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>

                <?php while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $row['tran_id']; ?></td>
                        <td><?php echo $row['cliente']; ?></td>
                        <td><?php echo $row['cli_direccion']; ?></td>
                        <td><?php echo $row['com_total']; ?></td>
                        <td><?php echo $row['com_fecha']; ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detalleModal" data-bs-orden="<?php echo $row['tran_id']; ?>"><i class="fa-solid fa-eye"></i> Ver</button>
                        </td>
                    </tr>
                <?php } ?>

            </tbody>
        </table>
    </div>
</main>
<div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="detalleModalLabel">Detalles de compra</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const detalleModal = document.getElementById('detalleModal')
    detalleModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        const orden = button.getAttribute('data-bs-orden')
        const modalBody = detalleModal.querySelector('.modal-body')

        const url = '<?php echo ADMIN_URL; ?>compras/getCompra.php'

        let formData = new FormData()
        formData.append('orden', orden)

        fetch(url, {
                method: 'post',
                body: formData,
        })
            .then(response => response.json())
            .then(function(data) {
                modalBody.innerHTML = data

        })
    })

    detalleModal.addEventListener('hide.bs.modal', event => {
        const modalBody = detalleModal.querySelector('.modal-body')
        modalBody.innerHTML = ''
    })

    $(document).ready(function() {
        $('#tablaCompras').DataTable();
    });
</script>

<?php include '../footer.php'; ?>
