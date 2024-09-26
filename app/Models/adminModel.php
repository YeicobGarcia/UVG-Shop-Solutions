<?php
require_once __DIR__ . '/ConexionDB.php';
class adminModel{

    function getPedidos(){
        $connClass = new ConexionDB();
        $conexion = $connClass->conectar();

        $sql = "SELECT
                    p.id_pedido, p.fecha_creacion, p.estado, p.id_usuario, u.Nombre, u.ID
                FROM
                    Pedidos p
                JOIN
                    Usuarios u on
                    u.ID = p.id_usuario";                

        $resultado = mysqli_query($conexion, $sql);
        $connClass->desconectar($conexion);
        
        return $resultado;
    }
}