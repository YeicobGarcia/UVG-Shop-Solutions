<?php

require_once __DIR__ . '/ConexionDB.php';

class registrarModel {

    public function verificarCorreo($email){
        $connClass = new ConexionDB();
        $conexion = $connClass->conectar();

        
        $sql = "SELECT * FROM Usuarios WHERE correo = ?";

        
        $stmt = $conexion->prepare($sql);

        
        $stmt->bind_param("s", $email);

        
        $stmt->execute();

        
        $resultado = $stmt->get_result();

        
        $stmt->close();
        $connClass->desconectar($conexion);

        
        return $resultado->num_rows > 0;
    }


    function registrar($nombre, $email, $telefono, $password){
        $connClass = new ConexionDB();
        $conexion = $connClass->conectar();

        
        $sql = "INSERT INTO Usuarios (Nombre, correo, telefono, Contraseña, id_role) VALUES (?, ?, ?, ?, 2)";
       

        $stmt = $conexion->prepare($sql);

        
        $stmt->bind_param("ssss", $nombre, $email, $telefono, $password);

        
        $resultado = $stmt->execute();

       
        $stmt->close();
        $connClass->desconectar($conexion);
        
        return $resultado;
    }
}
?>
