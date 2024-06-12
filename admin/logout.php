<?php 

require 'config/config.php';

    unset($_SESSION['user_id']);
    unset($_SESSION['user_usuario']);
    unset($_SESSION['user_type']);

    session_destroy();
    
    header("Location: index.php");
?>