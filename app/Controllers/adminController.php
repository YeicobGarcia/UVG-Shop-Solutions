<?php
require_once __DIR__ . '/../Models/ConexionDB.php';

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

            // Depuración de los datos que llegan al servidor
            error_log("Actualizando pedido $id_pedido a estado $nuevo_estado");

            // Consulta para actualizar el estado del pedido
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

        $connClass->desconectar($conexion);
    }
}

echo json_encode($response);
