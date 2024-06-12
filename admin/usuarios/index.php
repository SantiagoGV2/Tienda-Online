<?php
require '../config/config.php';
require '../config/database.php';



if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$db = new Database();
$con = $db->conectar();



$sql = "SELECT usuarios.usu_id, CONCAT(clientes.cli_nombre,' ',clientes.cli_apellidos) AS cliente, usuarios.usu_usuario, clientes.cli_direccion, usuarios.usu_activacion,
CASE 
WHEN usuarios.usu_activacion = 1 THEN 'activo'
WHEN usuarios.usu_activacion = 0 THEN 'No activo'
ELSE 'Deshabilitado'
END AS estatus 
FROM usuarios 
INNER JOIN clientes ON usuarios.cli_id = clientes.cli_id";
$resultado = $con->query($sql);

require '../header.php';
?>

<main class="flex-shrink-0">
    <div class="container">
        <h4>Usuarios</h4>

        <hr>

        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Usuario</th>
                    <th>Dirección</th>
                    <th>Estatus</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>

                <?php while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td><?php echo $row['cliente']; ?></td>
                        <td><?php echo $row['usu_usuario']; ?></td>
                        <td><?php echo $row['cli_direccion']; ?></td>
                        <td><?php echo $row['estatus']; ?></td>
                        <td>

                            <a href="cambiar_password.php?user_id=<?php echo $row['usu_id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fa-solid fa-lock"></i> Cambiar Password
                            </a>

                            <?php if ($row['usu_activacion'] == 1) : ?>

                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminaModal" 
                                data-bs-user="<?php echo $row['usu_id']; ?>"><i class="fa-solid fa-circle-xmark"></i> Baja</button>
                            <?php else :  ?>
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activaModal" 
                                data-bs-user="<?php echo $row['usu_id']; ?>"><i class="fa-solid fa-circle-check"></i> Activa</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php } ?>

            </tbody>
        </table>
    </div>
</main>
<div class="modal fade" id="eliminaModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="detalleModalLabel">Alerta</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Desea deshabilitar el usuario?
            </div>
            <div class="modal-footer">
                <form action="deshabilita.php" method="post">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-danger">Deshabilitar</button>
                </form>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="activaModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="detalleModalLabel">Alerta</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Desea activar el usuario?
            </div>
            <div class="modal-footer">
                <form action="activa.php" method="post">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success">Activar</button>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    const eliminaModal = document.getElementById('eliminaModal')
    eliminaModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        const user = button.getAttribute('data-bs-user')
        const inputId = eliminaModal.querySelector('.modal-footer input')

        inputId.value = user

    })

    const activaModal = document.getElementById('activaModal')
    activaModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget
        const user = button.getAttribute('data-bs-user')
        const inputId = activaModal.querySelector('.modal-footer input')

        inputId.value = user

    })
</script>

<?php include '../footer.php'; ?>