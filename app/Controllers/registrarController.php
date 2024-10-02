
<?php

    require_once __DIR__ . '/../Models/registrarModel.php';
    
    
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $password = $_POST['password'];

    $registrarModel = new registrarModel();
    $resultado = $registrarModel->registrar($nombre, $email, $telefono, $password);

    header('Content-Type: application/json'); 

    if($resultado){

    echo json_encode(["success" => true, "message" => "Registro exitoso <br> Confirma tu correo para iniciar sesión"]);

    }else{

    echo json_encode(["success" => false, "message" => "Error al registrar"]);
    }
    
    


?>