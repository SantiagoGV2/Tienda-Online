<?php

function esNulo(array $parametros){
    foreach($parametros as $parametro){
        if(strlen(trim($parametro))<1){
            return true;
        }
    }
    return false;
}

function validarPassword($password, $repassword){
    if(strcmp($password, $repassword) === 0 ){
        return true;
    }   
    return false;
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
    $sql = $con->prepare( "SELECT usu_id FROM usuarios WHERE usu_id = ? AND usu_token LIKE ? LIMIT 1");
    $sql->execute([$id, $token]);
    if($sql->fetchColumn() > 0){
        if(activarUsuario($id,$con)){
            $msg= "Cuenta Activada Exitosamente";
        }else{
            $msg= "Error al Activar Cuenta";
        }
    }else{
        $msg = "No existe el registro del cliente";
    }
    return $msg;

}

function activarUsuario($id,$con){
    $sql = $con->prepare("UPDATE usuarios SET usu_activacion = 1, usu_token = '' WHERE usu_id = ?");
    return $sql->execute([$id]);

}

function login($usuario, $password, $con){
    $sql = $con->prepare("SELECT adm_id, adm_usuario, adm_password, adm_nombre FROM admins WHERE adm_usuario LIKE ? AND adm_activo =1 LIMIT 1");
    $sql->execute([$usuario]);

    if($row = $sql->fetch(PDO::FETCH_ASSOC)){
       
            if(password_verify($password, $row['adm_password'])){
                $_SESSION['user_id'] = $row['adm_id'];
                $_SESSION['user_usuario'] = $row['adm_usuario'];
                $_SESSION['user_type'] = 'admin';
                header('Location: inicio.php');
                exit;
            }
    }
    return 'El usuario y/o contraseña son incorrectos.';   
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

function actualizaPasswordAdmin($user_id, $password, $con){
    $sql = $con-> prepare("UPDATE admins SET adm_password=? WHERE adm_id=?");
    if($sql->execute([$password,$user_id])){
        return true;
    }
    return false;
}

function crearUrl($cadena){
    $slug = strtolower($cadena);
    $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);//patron para url amigable
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');

    return $slug;
}   