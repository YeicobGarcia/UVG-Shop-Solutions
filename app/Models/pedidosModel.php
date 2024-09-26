<?php 
    session_start();
    class pedidosModel{
        function getPedidosCliente(){
            $conexionClass = new ConexionDB();
            $conexion = $conexionClass->conectar();
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT p.id_pedido, p.fecha_creacion, p.estado, p.id_usuario, u.ID  
            FROM 
                Pedidos p 
            join 
                Usuarios u on 
                p.id_usuario = u.ID
            where
                u.ID = $user_id";  
                
            $resultado = mysqli_query($conexion, $sql);
            $conexionClass->desconectar($conexion);
            return $resultado;
        }

        function crearPedido(){
            $conexionClass = new ConexionDB();
            $conexion = $conexionClass->conectar();
            $user_id = $_SESSION['user_id'];
            $sql = "INSERT INTO Pedidos(
                        fecha_creacion,
                        estado,
                        id_usuario)
                    VALUES(
                        now(),
                        'Preparandose',
                        $user_id)";
                        
            $resultado = mysqli_query($conexion, $sql);
            if($resultado){
                $conexionClass->desconectar($conexion);
                echo "Insercion correcta";
                return true;
            }else{
                $conexionClass->desconectar($conexion);
                return false;
            }
        }
    }
?>