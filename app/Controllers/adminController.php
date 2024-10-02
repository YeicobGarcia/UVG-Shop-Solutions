ADMINCONTROLLER.PHP
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../Models/ConexionDB.php';
require_once __DIR__ . '/../Models/pedidosModel.php';
require_once __DIR__ . '/../Models/loginModel.php'; // Modelo para obtener el correo del cliente
require '../../vendor/autoload.php'; // Asegúrate de que el SDK de AWS esté incluido

use Aws\Sqs\SqsClient;
use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use Aws\Sns\SnsClient;


$sqs = new SqsClient([
    'region'  => 'us-east-1', // Cambia esto a tu región
    'version' => 'latest',
]);

$queueUrl = 'https://sqs.us-east-1.amazonaws.com/010526258458/ColaPedidosPrueba.fifo'; // Cambia esto a tu URL de la cola

$response = ['success' => false];

// Función para enviar un SMS usando Amazon SNS
function enviarSMSSNS($telefono, $estado) {
    // Configurar el cliente de SNS
    $snsClient = new SnsClient([
        'region'  => 'us-east-1', // Cambia a tu región
        'version' => 'latest',        
    ]);

    // Mensaje del SMS
    $message = "Actualización de estado de tu pedido de UVG SHOP GAMING: Estado \"$estado\"";

    try {
        // Enviar el SMS
        $result = $snsClient->publish([
            'Message' => $message,
            'PhoneNumber' => $telefono, // Número de teléfono del usuario
            'MessageAttributes' => [
                'AWS.SNS.SMS.SMSType' => [
                    'DataType' => 'String',
                    'StringValue' => 'Transactional', // Mensaje de tipo transaccional
                ],
            ],
        ]);

        return true;
    } catch (AwsException $e) {
        echo "Error al enviar el SMS: " . $e->getAwsErrorMessage();
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action'])) {
        $connClass = new ConexionDB();
        $conexion = $connClass->conectar();

        // Manejar actualización del estado del pedido
        if ($data['action'] === 'updateStatus' && isset($data['id_pedido']) && isset($data['estado'])) {
            $id_pedido = $data['id_pedido'];
            $nuevo_estado = $data['estado'];

            error_log("Actualizando pedido $id_pedido a estado $nuevo_estado");

            $sql = "UPDATE Pedidos SET estado = ? WHERE id_pedido = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("si", $nuevo_estado, $id_pedido);

            if ($stmt->execute()) {
                // Obtener el contacto del cliente (correo y teléfono)
                $pedidoModel = new pedidosModel();
                $user_id = $pedidoModel->getUserIdByPedido($id_pedido); // Suponiendo que tienes un método que devuelve el user_id según el pedido

                $loginModel = new loginModel();
                $contactInfo = $loginModel->getUserContactInfo($user_id); // Obtener contacto (correo y teléfono) del cliente

                if ($contactInfo && $contactInfo['telefono']) {
                    // Enviar el SMS al teléfono del cliente
                    enviarSMSSNS($contactInfo['telefono'], $nuevo_estado);
                }

                $response['success'] = true;
            } else {
                error_log("Error al ejecutar la consulta: " . $stmt->error);
            }
            $stmt->close();
        }

         // Mensaje del SMS
          $message = "Actualización de estado de tu pedido de UVG SHOP GAMING: Estado \"$nuevo_estado\"";


        // Manejar eliminación de pedidos
        if ($data['action'] === 'deleteOrder' && isset($data['id_pedido'])) {
            $id_pedido = $data['id_pedido'];
            $sql = "DELETE FROM Pedidos WHERE id_pedido = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_pedido);
            if ($stmt->execute()) {
                $response['success'] = true;
            }
            $stmt->close();
        }

        // Manejar confirmación de pedidos desde la cola
        if ($data['action'] === 'confirmOrder' && isset($data['orderData']) && isset($data['receiptHandle'])) {
            $orderData = $data['orderData']; // Datos del pedido desde la cola
            $receiptHandle = $data['receiptHandle'];

            $pedidoModel = new pedidosModel();
            $pedidoCreado = $pedidoModel->crearPedido($orderData['carrito'], $orderData['user_id']); // Pasar el carrito extraído del mensaje

            if ($pedidoCreado) {
                // Obtener el correo y el teléfono del cliente usando el user_id
                $loginModel = new loginModel();
                $contactInfo = $loginModel->getUserContactInfo($orderData['user_id']); // Obtener correo y teléfono por el ID de usuario
            
                // Si se encontró el correo y teléfono
                if ($contactInfo) {
                    $userEmail = $contactInfo['email'];
                    $userTelefono = $contactInfo['telefono'];
            
                    // Enviar correo de confirmación usando SES con el desglose del carrito
                    enviarCorreoSES($userEmail, $orderData['order_id'], $orderData['carrito']);

                }
            
                // Eliminar el mensaje de la cola si la inserción fue exitosa
                $sqs->deleteMessage([
                    'QueueUrl' => $queueUrl,
                    'ReceiptHandle' => $receiptHandle,
                ]);
            
                $response['success'] = true;
            }
            else {
                error_log("Error al insertar el pedido");
            }
        }

        // Manejar rechazo de pedidos desde la cola
        if ($data['action'] === 'rejectOrder' && isset($data['receiptHandle'])) {
            $receiptHandle = $data['receiptHandle'];

            // Simplemente elimina el mensaje de la cola
            $sqs->deleteMessage([
                'QueueUrl' => $queueUrl,
                'ReceiptHandle' => $receiptHandle,
            ]);

            $response['success'] = true;
        }

        $connClass->desconectar($conexion);
    }
}

echo json_encode($response);

// Función para enviar el correo usando Amazon SES
function enviarCorreoSES($toEmail, $orderId, $carrito) {
    // Configurar el cliente de SES
    $sesClient = new SesClient([
        'region'  => 'us-east-1', // Cambia a tu región
        'version' => '2010-12-01',        
    ]);

    // Mensaje del correo
    $subject = "Confirmación de Pedido #{$orderId}";

    // Construir el desglose de los artículos del carrito
    $detalleProductos = "";
    $totalPedido = 0;

    foreach ($carrito as $item) {
        $subtotal = $item['cantidad'] * $item['precio'];
        $totalPedido += $subtotal;
        $detalleProductos .= "{$item['cantidad']}x {$item['nombre']} - Q." . number_format($item['precio'], 2) . " (Subtotal: Q." . number_format($subtotal, 2) . ")\n";
    }

    // Construir el cuerpo del mensaje
    $message = "Su pedido con ID#{$orderId} ha sido confirmado por nuestros colaboradores.\n\n";
    $message .= "Detalles del Pedido:\n";
    $message .= $detalleProductos;
    $message .= "\nTotal del Pedido: Q." . number_format($totalPedido, 2);

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
                        'Data' => $message, // El cuerpo del mensaje con los productos
                    ],
                ],
                'Subject' => [
                    'Charset' => 'UTF-8',
                    'Data' => $subject, // Asunto del correo
                ],
            ],
        ]);

        return true;
    } catch (AwsException $e) {
        echo "Error al enviar el correo: " . $e->getAwsErrorMessage();
        return false;
    }
}
