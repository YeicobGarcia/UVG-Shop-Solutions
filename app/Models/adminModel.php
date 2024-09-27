<?php
require_once __DIR__ . '/ConexionDB.php';

class adminModel {

    // Obtener todas las órdenes que no estén canceladas
    function getPedidosActivos() {
        $connClass = new ConexionDB();
        $conexion = $connClass->conectar();

        $sql = "SELECT
                    p.id_pedido,
                    p.fecha_creacion,
                    p.estado,
                    p.id_usuario,
                    u.Nombre,
                    u.ID,
                    SUM(dp.cantidad) AS cantidad_productos,  
                    SUM(dp.cantidad * dp.precio) AS total_pedido  
                FROM
                    Pedidos p
                JOIN
                    Usuarios u ON u.ID = p.id_usuario
                JOIN
                    DETALLEPEDIDO dp ON dp.id_pedido = p.id_pedido
                WHERE
                    p.estado != 'Cancelado'  -- Filtrar las órdenes que no estén canceladas
                GROUP BY
                    p.id_pedido, p.fecha_creacion, p.estado, p.id_usuario, u.Nombre, u.ID";
                
        $resultado = mysqli_query($conexion, $sql);
        $connClass->desconectar($conexion);
        
        return $resultado;
    }

    // Obtener todas las órdenes que ya fueron canceladas
    function getPedidosCancelados() {
        $connClass = new ConexionDB();
        $conexion = $connClass->conectar();

        $sql = "SELECT
                    p.id_pedido,
                    p.fecha_creacion,
                    p.estado,
                    p.id_usuario,
                    u.Nombre,
                    u.ID,
                    SUM(dp.cantidad) AS cantidad_productos,  
                    SUM(dp.cantidad * dp.precio) AS total_pedido  
                FROM
                    Pedidos p
                JOIN
                    Usuarios u ON u.ID = p.id_usuario
                JOIN
                    DETALLEPEDIDO dp ON dp.id_pedido = p.id_pedido
                WHERE
                    p.estado = 'Cancelado'  -- Filtrar solo las órdenes canceladas
                GROUP BY
                    p.id_pedido, p.fecha_creacion, p.estado, p.id_usuario, u.Nombre, u.ID";
                
        $resultado = mysqli_query($conexion, $sql);
        $connClass->desconectar($conexion);
        
        return $resultado;
    }
}
