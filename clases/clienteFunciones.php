<?php

function esNulo(array $parametros){
    foreach($parametros as $parametro){
        if(strlen(trim($parametro))<1){
            return true;
        }
    }
    return false;
}
function esEmail($email){
    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        return true;
    }
    return false;
}

function validarPassword($password, $repassword){
    if(strcmp($password, $repassword) === 0 ){
        return true;
    }   
    return false;
}

function generarToken(){
    return md5(uniqid(mt_rand(),false));
}
function registraCliente(array $datos, $con){
    $sql = $con->prepare( "INSERT INTO clientes(cli_nombre,cli_apellidos,cli_email,cli_telefono,cli_direccion,cli_status,cli_fecha_alta) 
    VALUES (?,?,?,?,?,1,now())");
    if($sql->execute($datos)){
        return $con-> lastInsertId();
    }
    return 0;
}
function actualizarCliente($id, array $datos, $con){
    $sql = $con->prepare("UPDATE clientes SET cli_nombre = ?, cli_apellidos = ?, cli_email = ?, cli_telefono = ?, cli_direccion = ? WHERE cli_id = ?");
    if($sql->execute([$datos[0], $datos[1], $datos[2], $datos[3],$datos[4], $id])){
        return $sql->rowCount();
    }
    return 0;
}
function obtenerCliente($id, $con) {
    $sql = $con->prepare("SELECT cli_nombre, cli_apellidos, cli_email, cli_telefono, cli_direccion FROM clientes WHERE cli_id = ?");
    $sql->execute([$id]);
    return $sql->fetch(PDO::FETCH_ASSOC);
}


function registraUsuario(array $datos, $con){
    $sql = $con->prepare("INSERT INTO usuarios (usu_usuario,usu_password,usu_token,cli_id) VALUES (?,?,?,?)");
    if($sql->execute($datos)){
        return $con-> lastInsertId();
    }
    return 0;
}

function validarUsuario($usuario, $con){
    $sql = $con->prepare( "SELECT usu_id FROM usuarios WHERE usu_usuario LIKE ? LIMIT 1");
    $sql->execute([$usuario]);
    if($sql->fetchColumn() > 0){
        return true;
    }
    return false;

}
function validarEmail($email, $con){
    $sql = $con->prepare( "SELECT cli_id FROM clientes WHERE cli_email LIKE ? LIMIT 1");
    $sql->execute([$email]);
    if($sql->fetchColumn() > 0){
        return true;
    }
    return false;

}

function mostrarMensajes(array $errors){
    if(count($errors)>0){
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert"><ul>';
        foreach($errors as $error){
            echo '<li>'. $error .'</li>';
        }
        echo '<ul>';
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}

function validarToken($id, $token, $con){
    $msg = "";
    $sql = $con->prepare("SELECT usu_id FROM usuarios WHERE usu_id = ? AND usu_token LIKE ? LIMIT 1");
    $sql->execute([$id, $token]);
    if($sql->fetchColumn() > 0){
        if(activarUsuario($id, $con)){
            $msg = "Cuenta Activada Exitosamente";
        }else{
            $msg = "Error al Activar Cuenta";
        }
    }else{
        $msg = "No existe el registro del cliente";
    }

    // Redireccionar a activacion.php con el mensaje como parámetro
    header("Location: activacionUser.php?msg=" . $msg);
    exit(); // Asegurarse de que el script se detenga después de redireccionar
}

function activarUsuario($id,$con){
    $sql = $con->prepare("UPDATE usuarios SET usu_activacion = 1, usu_token = '' WHERE usu_id = ?");
    return $sql->execute([$id]);

}

function login($usuario, $password, $con, $proceso){

    $sql = $con->prepare("SELECT usu_id, usu_usuario, usu_password, cli_id FROM usuarios WHERE usu_usuario LIKE ? LIMIT 1");
    $sql->execute([$usuario]);

    if($row = $sql->fetch(PDO::FETCH_ASSOC)){
        if(esActivo($usuario,$con)){
            if(password_verify($password, $row['usu_password'])){
                $_SESSION['user_id'] = $row['usu_id'];
                $_SESSION['user_usuario'] = $row['usu_usuario'];
                $_SESSION['user_cli_id'] = $row['cli_id'];
                if($proceso == 'pago'){
                    header("Location: checkout.php");
                }else{
                    header("Location: index.php");
                }
                exit;
            }
        }else{
            return 'El usuario no ha sido activado.';
        }
    }
    return 'El usuario y/o contraseña son incorrectos.';   
}

function esActivo($usuario, $con){
    $sql = $con->prepare("SELECT usu_activacion FROM usuarios WHERE usu_usuario LIKE ? LIMIT 1");
    $sql->execute([$usuario]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    if($row['usu_activacion'] == 1){
        return true;
    }else{
        return false;
    }
}

function solicitaPassword($user_id, $con){
    $token = generarToken();

    $sql = $con->prepare("UPDATE usuarios SET usu_token_password=?, usu_password_request=1 
    WHERE usu_id = ?");

    if($sql->execute([$token, $user_id])){
        return $token;
    }
    return null;
}

function verificaTokenRequest($user_id, $token, $con){
    $sql = $con->prepare("SELECT usu_id FROM usuarios WHERE usu_id = ? AND usu_token_password LIKE ? AND usu_password_request=1 LIMIT 1");
    $sql->execute([$user_id, $token]);
    if($sql->fetchColumn() > 0){
        return true;
    }
    return false;
}

function actualizaPassword($user_id, $password, $con){
    $sql = $con-> prepare("UPDATE usuarios SET usu_password=?, usu_token_password ='', usu_password_request=0 WHERE usu_id=?");
    if($sql->execute([$password,$user_id])){
        return true;
    }
    return false;
}