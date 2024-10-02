LOGINMODEL.PHP

<?php
require_once __DIR__ . '/ConexionDB.php';

class loginModel {

    /**
     * Método de autenticación
     */
    function autenticar($user, $pass){
        $connClass = new ConexionDB();
        $conexion = $connClass->conectar();

        $sql = "SELECT ID, Nombre, Contraseña, id_role
                FROM Usuarios
                WHERE UPPER(Nombre) = UPPER('$user')
                  AND Contraseña = '$pass'";

        $resultado = mysqli_query($conexion, $sql);
        $connClass->desconectar($conexion);
        
        return $resultado;
    }

    /**
     * Método para obtener el correo y telefono del usuario por su ID
     */
    public function getUserContactInfo($user_id) {
        $connClass = new ConexionDB();
        $conexion = $connClass->conectar();

        // Asumiendo que la tabla "Usuarios" tiene una columna "Telefono"
        $sql = "SELECT correo, telefono FROM Usuarios WHERE ID = ? LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($email, $telefono);
        $stmt->fetch();
        $stmt->close();
        $connClass->desconectar($conexion);

        // Retornar un array con el correo y el teléfono, o false si no se encontró
        return ($email && $telefono) ? ['email' => $email, 'telefono' => $telefono] : false;
    }
}
?>
