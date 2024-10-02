<?php 
    // Incluir la clase de manejo de sesiones
    require_once __DIR__ . '/../Controllers/MySQLSessionHandler.php';

    // Configurar el manejador de sesiones
    $handler = new MySQLSessionHandler();
    session_set_save_handler($handler, true);

    require_once __DIR__ . '/ConexionDB.php';

   class pedidosModel{
    
    function getPedidosCliente(){
        $conexionClass = new ConexionDB();
        $conexion = $conexionClass->conectar();
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT p.id_pedido, p.fecha_creacion, p.estado, p.id_usuario, u.ID  
                FROM Pedidos p 
                JOIN Usuarios u ON p.id_usuario = u.ID
                WHERE u.ID = $user_id";  
                
        $resultado = mysqli_query($conexion, $sql);
        $conexionClass->desconectar($conexion);
        return $resultado;
    }

    function crearPedido($carrito, $user_id) {
        $conexionClass = new ConexionDB();
        $conexion = $conexionClass->conectar();
        //$user_id = $_SESSION['user_id']; // Asegúrate de que esto esté configurado correctamente
    
        // Insertar el pedido en la tabla Pedidos
        $sqlPedido = "INSERT INTO Pedidos(fecha_creacion, estado, id_usuario)
                      VALUES(now(), 'Preparandose', $user_id)";
        $resultadoPedido = mysqli_query($conexion, $sqlPedido);
        
        if($resultadoPedido){
            // Obtener el ID del pedido recién creado
            $id_pedido = mysqli_insert_id($conexion);
    
            // Insertar cada artículo del carrito en la tabla DetallePedido
            foreach ($carrito as $item) {
                $id_producto = $item['id'];
                $cantidad = $item['cantidad'];
                $precio = $item['precio'];
    
                $sqlDetalle = "INSERT INTO DETALLEPEDIDO(id_pedido, id_producto, cantidad, precio)
                               VALUES($id_pedido, $id_producto, $cantidad, $precio)";
                $resultadoDetalle = mysqli_query($conexion, $sqlDetalle);
                
                if(!$resultadoDetalle){
                    // Si falla alguna inserción en DetallePedido, cancelamos todo el pedido
                    mysqli_query($conexion, "DELETE FROM Pedidos WHERE id_pedido = $id_pedido");
                    $conexionClass->desconectar($conexion);
                    return false;
                }
            }
    
            // Desconectar y confirmar éxito
            $conexionClass->desconectar($conexion);
            return true;
        }else{
            // Si falla la inserción del pedido
            $conexionClass->desconectar($conexion);
            return false;
        }
    }
    
}
?>

