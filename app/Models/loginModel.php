<?php
require_once __DIR__ . '/ConexionDB.php';
class loginModel {

    /**
     * METODO DE AUTENTICACION
     */

    function autenticar($user, $pass){
        $connClass = new ConexionDB();
        $conexion = $connClass->conectar();

        $sql = "SELECT
                    ID, Nombre, Contraseña, id_role
                FROM
                    Usuarios
                WHERE                    
                    UPPER(Nombre) = UPPER('$user')
                    and Contraseña = '$pass'";

        $resultado = mysqli_query($conexion, $sql);
        $connClass->desconectar($conexion);
        
        return $resultado;
    }

    


}

?>