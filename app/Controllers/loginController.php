<?php
session_start();
require_once __DIR__ . '/../Models/loginModel.php';    

    $loginModel = new loginModel();
    $username = $_POST['inNombre'];
    $password = $_POST['inPassword'];
    

    $result = array();

    $result = $loginModel->autenticar($username, $password);        

    if ($row = mysqli_fetch_array($result)) {
        $_SESSION['user_id'] = $row['ID'];
        $_SESSION['usuario'] = $row['Nombre'];
        $_SESSION['id_role'] = $row['id_role'];

        if ($_SESSION['id_role'] == 1) {
            // Redirigir a la página de administrador
            header("Location: ../views/Admin.php");
        } elseif ($_SESSION['id_role'] == 2) {
            // Redirigir a la página de usuario regular
            header("Location: ../views/home.php");
        }
        
        exit();
    }else{
        echo "Contraseña incorrecta";
    }

?>
