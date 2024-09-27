<?php
// Incluir el modelo
require_once __DIR__ . '/../Models/ConexionDB.php';
require_once __DIR__ . '/../Models/pedidosModel.php';




$model = new pedidosModel();

// Llama a la función para crear un pedido
$resultado = $model->crearPedido();

if ($resultado) {
    echo "Pedido creado exitosamente.";
} else {
    echo "Error al crear el pedido.";
}
?>
