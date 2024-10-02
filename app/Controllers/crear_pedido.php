<?php
// Incluir el modelo
require_once __DIR__ . '/../Models/ConexionDB.php';
require_once __DIR__ . '/../Models/pedidosModel.php';
require_once __DIR__ . '/../Models/loginModel.php'; // Para obtener el correo del usuario
require '../../vendor/autoload.php'; // Incluir el SDK de AWS

require_once __DIR__ . '/../Controllers/MySQLSessionHandler.php';

// Configurar el manejador de sesiones
$handler = new MySQLSessionHandler();
session_set_save_handler($handler, true);
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    echo "Usuario no autenticado.";
    exit();
}

use Aws\Sqs\SqsClient;
use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

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
$userModel = new loginModel();  // Modelo para obtener el contacto del usuario

// Verificar si el carrito tiene productos
if (!empty($carrito)) {
    $user_id = $_SESSION['user_id']; // Asegúrate de que esto esté configurado correctamente
    $nombre = $_SESSION['usuario'];

    // Obtener el contacto del usuario logueado (correo y teléfono)
    $userContactInfo = $userModel->getUserContactInfo($user_id);  // Método para obtener el contacto por ID

    // Verificar que el correo exista en la respuesta
    if (!$userContactInfo || !isset($userContactInfo['email'])) {
        echo "Error: No se pudo obtener el correo del usuario.";
        exit();
    }

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
        // Enviar el correo de confirmación del pedido usando SES
        enviarCorreoSES($userContactInfo['email'], $result->get('MessageId')); // Usamos el correo del array
        echo "Pedido enviado para confirmación. Se ha enviado un correo a " . $userContactInfo['email'];
    } else {
        echo "Error al enviar el pedido.";
    }
} else {
    echo "El carrito está vacío.";
}

// Función para enviar el correo usando Amazon SES
function enviarCorreoSES($toEmail, $orderId) {
    // Configurar el cliente de SES
    $sesClient = new SesClient([
        'region'  => 'us-east-1', // Cambia a tu región
        'version' => '2010-12-01',
        'credentials' => [
            'key'    => 'AKIAQE43J6UNNFOGVWVL',
            'secret' => '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',
        ],
    ]);

    // Mensaje del correo
    $subject = "En espera de confirmación";
    $message = "Tu pedido con ID#{$orderId} está en espera de confirmación por parte de nuestros colaboradores.";

    try {
        // Enviar el correo
        $result = $sesClient->sendEmail([
            'Destination' => [
                'ToAddresses' => [$toEmail], // Correo del usuario
            ],
            'ReplyToAddresses' => ['noreply@tu-dominio.com'],
            'Source' => 'uvgdevs@uvgaimingshop.me', // Dirección de correo del remitente
            'Message' => [
                'Body' => [
                    'Text' => [
                        'Charset' => 'UTF-8',
                        'Data' => $message,
                    ],
                ],
                'Subject' => [
                    'Charset' => 'UTF-8',
                    'Data' => $subject,
                ],
            ],
        ]);

        return true;
    } catch (AwsException $e) {
        echo "Error al enviar el correo: " . $e->getAwsErrorMessage();
        return false;
    }
}
