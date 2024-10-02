
<?php

        require_once __DIR__ . '/ConexionDB.php';

        class registrarModel {

            function registrar($nombre, $email, $telefono, $password){
                $connClass = new ConexionDB();
                $conexion = $connClass->conectar();

                $sql = "INSERT INTO
                            Usuarios
                        (
                            Nombre,
                            correo,
                            telefono,
                            Contraseña,
                            id_role
                        )
                        VALUES
                        (
                            '$nombre',
                            '$email',
                            '$telefono',
                            '$password',
                            2
                        )";

                $resultado = mysqli_query($conexion, $sql);
                $connClass->desconectar($conexion);
                
                return $resultado;
            }

        }


?>