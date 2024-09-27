<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../Models/ConexionDB.php';
require_once __DIR__ . '/../Models/pedidosModel.php';
require '../../vendor/autoload.php'; // Asegúrate de que el SDK de AWS esté incluido
use Aws\Sqs\SqsClient;

$sqs = new SqsClient([
    'region'  => 'us-east-1', // Cambia esto a tu región
    'version' => 'latest',
]);

$queueUrl = 'https://sqs.us-east-1.amazonaws.com/010526258458/ColaPedidosPrueba.fifo'; // Cambia esto a tu URL de la cola

$response = ['success' => false];

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
                $response['success'] = true;
            } else {
                error_log("Error al ejecutar la consulta: " . $stmt->error);
            }
            $stmt->close();
        }

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
                // Eliminar el mensaje de la cola si la inserción fue exitosa
                $sqs->deleteMessage([
                    'QueueUrl' => $queueUrl,
                    'ReceiptHandle' => $receiptHandle,
                ]);
                $response['success'] = true;
            } else {
                $response['success'] = true;
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

