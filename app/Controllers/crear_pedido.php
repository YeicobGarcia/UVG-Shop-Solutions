<?php
// Incluir el modelo

require_once __DIR__ . '/../Models/ConexionDB.php';
require_once __DIR__ . '/../Models/pedidosModel.php';
require '../../vendor/autoload.php'; // Incluir el SDK de AWS

require_once __DIR__ . '/../Controllers/MySQLSessionHandler.php';

    // Configurar el manejador de sesiones
    $handler = new MySQLSessionHandler();
    session_set_save_handler($handler, true);
    session_start();

use Aws\Sqs\SqsClient;

// Configurar el cliente de SQS
$sqs = new SqsClient([
    'region'  => 'us-east-1', // Cambia esto a tu región
    'version' => 'latest',
]);

$queueUrl = 'https://sqs.us-east-1.amazonaws.com/010526258458/ColaPedidosPrueba.fifo'; // Cambia esto a tu URL de la cola

// Obtener el carrito desde la petición POST (lo estamos recibiendo como JSON)
$input = file_get_contents('php://input');  // Lee el cuerpo de la solicitud
$data = json_decode($input, true);          // Decodifica el JSON

$carrito = isset($data['carrito']) ? $data['carrito'] : [];

$model = new pedidosModel();

// Verificar si el carrito tiene productos
if(!empty($carrito)){
    $user_id = $_SESSION['user_id']; // Asegúrate de que esto esté configurado correctamente
    $nombre = $_SESSION['usuario'];
    // Crear un array que incluya el carrito y el id del usuario
    $pedido = [
        'user_id' => $user_id,
        'Nombre' => $nombre, // Ajusta según tu implementación
        'carrito' => $carrito,
    ];

    // Convertir el array a JSON para enviar como mensaje en la cola
    $pedidoJson = json_encode($pedido);

    // Enviar el mensaje a la cola SQS
    $result = $sqs->sendMessage([
        'QueueUrl'    => $queueUrl,
        'MessageBody' => $pedidoJson,
        'MessageGroupId' => 'PedidosGroup', // Si es una cola FIFO, necesitas un MessageGroupId
    ]);

    // Verificar si el mensaje se envió con éxito
    if ($result->get('MessageId')) {
        echo "Pedido enviado para confirmación.";
    } else {
        echo "Error al enviar el pedido.";
    }
}else{
    echo "El carrito está vacío.";
}







/*
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
}*/
?>
