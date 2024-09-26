<?php 

class ConexionDB{

    function conectar(){
        $servername = "db-app-store.cn44qw2ounhe.us-east-1.rds.amazonaws.com";
        $username = "root";
        $password = "admin123*";
        $dbname = "app_web_store";

        // Crear la conexión
        $conexion = mysqli_connect($servername, $username, $password, $dbname);

        if($conexion){
            mysqli_query($conexion, "SET NAMES 'utf8'");
            mysqli_set_charset($conexion, "utf8");            
        }else{
            echo "Error de conexion debido a: <br> ".mysqli_connect_error();
        }

        return $conexion;
    }
    
    function desconectar($conexion){
        $close = mysqli_close($conexion);

        if(!$close){
            echo "Ocurrio un error al cerrar la conexion debido a: <br> ".mysqli_connect_error();
        }

        return $close;
    }
}
?>