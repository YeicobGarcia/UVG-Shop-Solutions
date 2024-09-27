<?php 

    // Incluir la clase de manejo de sesiones
    require_once __DIR__ . '/../Controllers/MySQLSessionHandler.php';

    // Configurar el manejador de sesiones
    $handler = new MySQLSessionHandler();
    session_set_save_handler($handler, true);
    session_start();

    session_start();    
    include_once('../Models/adminModel.php');    
    if (!$_SESSION['user_id']) {
      header("location: ../views/index.php");
      exit();
    }
    $admClass = new adminModel();
    $result = array();
    $result = $admClass->getPedidos();
// Obtener las órdenes activas y canceladas
$pedidosActivos = $admClass->getPedidosActivos();
$pedidosCancelados = $admClass->getPedidosCancelados();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <title>UVGAMING-SHOP</title>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>UVGAMING-SHOP</h2>
            <ul>
                <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            </ul>
        </div>
        <div class="main">
            <div class="header">
                <h1>Dashboard</h1>
                <div class="user">
                    <img src="https://via.placeholder.com/30" alt="User Avatar">
                    <span>Admin</span>
                    <a href="../Controllers/logoutController.php" class="logout">Cerrar sesión</a>
                </div>
            </div>

            <!-- Órdenes Activas -->
            <div class="orders">
                <h2>Órdenes Activas</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Fecha de Creación</th>
                            <th>Cliente</th>
                            <th>Cantidad de Productos</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($pedidosActivos->num_rows > 0) {
                            // Mostrar las órdenes activas
                            while($row = $pedidosActivos->fetch_assoc()) {
                                echo "<tr id='pedido-{$row["id_pedido"]}'>";
                                echo "<td>" . $row["id_pedido"] . "</td>";
                                echo "<td>" . $row["fecha_creacion"] . "</td>";
                                echo "<td>" . $row["Nombre"] . "</td>";
                                echo "<td>" . $row["cantidad_productos"] . "</td>";
                                echo "<td>Q." . number_format($row["total_pedido"], 2) . "</td>";
                                
                                // Select para cambiar el estado
                                echo "<td class='status'>
                                        <select class='estado' data-id='" . $row["id_pedido"] . "'>
                                            <option value='Preparandose' " . ($row['estado'] == 'Preparandose' ? 'selected' : '') . ">Preparándose</option>
                                            <option value='Enviado' " . ($row['estado'] == 'Enviado' ? 'selected' : '') . ">Enviado</option>
                                            <option value='Entregado' " . ($row['estado'] == 'Entregado' ? 'selected' : '') . ">Entregado</option>
                                            <option value='Cancelado' " . ($row['estado'] == 'Cancelado' ? 'selected' : '') . ">Cancelado</option>
                                        </select>
                                      </td>";

                                // Botón de eliminar
                                echo "<td class='action'>
                                        <button class='delete-order' data-id='" . $row["id_pedido"] . "'>Eliminar</button>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No hay órdenes activas</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Órdenes Canceladas -->
            <div class="orders">
                <h2>Órdenes Canceladas</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Fecha de Creación</th>
                            <th>Cliente</th>
                            <th>Cantidad de Productos</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($pedidosCancelados->num_rows > 0) {
                            // Mostrar las órdenes canceladas
                            while($row = $pedidosCancelados->fetch_assoc()) {
                                echo "<tr id='pedido-{$row["id_pedido"]}'>";
                                echo "<td>" . $row["id_pedido"] . "</td>";
                                echo "<td>" . $row["fecha_creacion"] . "</td>";
                                echo "<td>" . $row["Nombre"] . "</td>";
                                echo "<td>" . $row["cantidad_productos"] . "</td>";
                                echo "<td>Q." . number_format($row["total_pedido"], 2) . "</td>";
                                echo "<td class='status'>" . $row["estado"] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No hay órdenes canceladas</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Manejar cambio de estado
        document.querySelectorAll('.estado').forEach(function(select) {
            select.addEventListener('change', function() {
                const orderId = this.getAttribute('data-id');
                const nuevoEstado = this.value;
                
                fetch('../Controllers/adminController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'updateStatus', id_pedido: orderId, estado: nuevoEstado })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('Error al actualizar el estado');
                    }
                });
            });
        });

        // Manejar eliminación de pedidos
        document.querySelectorAll('.delete-order').forEach(function(button) {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                if (confirm('¿Estás seguro de que deseas eliminar este pedido?')) {
                    fetch('../Controllers/adminController.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'deleteOrder', id_pedido: orderId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const row = document.getElementById('pedido-' + orderId);
                            row.remove();  // Eliminar la fila del pedido
                        } else {
                            alert('Error al eliminar el pedido');
                        }
                    });
                }
            });
        });
    });
</script>
</html>
