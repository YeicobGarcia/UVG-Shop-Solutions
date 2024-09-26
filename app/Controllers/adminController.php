<?php
require_once __DIR__ . '/../Models/ConexionDB.php';

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        $connClass = new ConexionDB();
        $conexion = $connClass->conectar();

        if ($data['action'] === 'markReady' && isset($data['id_pedido'])) {
            $id_pedido = $data['id_pedido'];
            $sql = "UPDATE Pedidos SET estado = 'listo' WHERE id_pedido = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id_pedido);
            if ($stmt->execute()) {
                $response['success'] = true;
            }
            $stmt->close();
        }

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
?>
