<?php
require 'config/config.php';
require 'config/database.php';
require 'clases/adminFunciones.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_GET['id'] ?? $_POST['id'] ?? '';

if($user_id == '' || $user_id != $_SESSION['user_id']){
    header("Location: index.php");
    exit;
}

$db = new Database();
$con = $db->conectar();

$errors = [];

if (!empty($_POST)) {
    
    $password = trim($_POST['password']);
    $repassword = trim($_POST['repassword']);

    if (esNulo([$user_id,$password, $repassword])) {
        $errors[] = "Debe llenar todos los campos";
    }
    if (!validarPassword($password, $repassword)) {
        $errors[] = "Las contraseñas no coiciden";
    }
    if(empty($errors)){
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        if(actualizaPasswordAdmin($user_id, $pass_hash, $con)){
            $errors[]="Contraseña modificada";
        }else{
            $errors[]="Error al modicar la contraseña. Intentalo Nuevamente";
        }
    }
}

$sql ="SELECT adm_id, adm_usuario FROM admins WHERE adm_id = ?";
$sql = $con->prepare($sql);
$sql->execute([$user_id]);
$usuario = $sql->fetch(PDO::FETCH_ASSOC);

require 'header.php';
?>

    <main class="form-login m-auto pt-4">
        <h3 class="text-center">Cambiar contraseña</h3>
        <?php mostrarMensajes($errors); ?>

        <form action="cambiar_password.php" method="post" class="row g-3" autocomplete="off">
            <input type="hidden" name="id" value="<?php echo $usuario['adm_id'] ?>">

            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="password" value="<?php echo $usuario['adm_usuario'] ?>" disabled>
                <label for="usuario">Usuario</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" class="form-control" name="password" id="password" placeholder="Nueva Contraseña" required>
                <label for="email">Nueva Contraseña</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" name="repassword" id="repassword" placeholder="Confirmar Contraseña" required>
                <label for="email">Confirmar Contraseña</label>
            </div>
            <div class="d-grid gap-3 col-12">
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </main>

<?php include 'footer.php' ?>