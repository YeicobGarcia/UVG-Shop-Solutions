<?php

    class ProductModel {
        function getProducts(){
            $conexionClass = new ConexionDB();
            $conexion = $conexionClass->conectar();
            $sql = "SELECT ID, NOMBRE, PRECIO, DESCRIPCION FROM PRODUCTOS";
            $resultado = mysqli_query($conexion, $sql);
            $conexionClass->desconectar($conexion);
            return $resultado;
        }


        function getProductById($id){
            $conexionClass = new ConexionDB();
            $conexion = $conexionClass->conectar();
            $sql = "SELECT ID, NOMBRE, PRECIO, DESCRIPCION FROM Productos WHERE Id = $id";
            $resultado = mysqli_query($conexion, $sql);
            $conexionClass->desconectar($conexion);
            return $resultado;
        }

    }


?>