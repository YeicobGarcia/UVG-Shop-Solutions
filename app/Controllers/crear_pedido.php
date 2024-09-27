<?php
// Incluir el modelo
require_once __DIR__ . '/../Models/ConexionDB.php';
require_once __DIR__ . '/../Models/pedidosModel.php';


// Obtener el carrito desde la petición POST (lo estamos recibiendo como JSON)
$input = file_get_contents('php://input');  // Lee el cuerpo de la solicitud
$data = json_decode($input, true);          // Decodifica el JSON

$carrito = isset($data['carrito']) ? $data['carrito'] : [];

$model = new pedidosModel();

// Verificar si el carrito tiene productos
if(!empty($carrito)){
    // Llama a la función para crear un pedido con los productos del carrito
    $resultado = $model->crearPedido($carrito);

    if ($resultado) {
        echo "Pedido creado exitosamente.";
    } else {
        echo "Error al crear el pedido.";
    }
}else{
    echo "El carrito está vacío.";
}
?>
