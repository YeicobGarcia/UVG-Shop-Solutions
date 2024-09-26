<?php 
    session_start();    
    include_once('../Models/adminModel.php');    
    if (!$_SESSION['user_id']) {
      header("location: ../views/index.php");
    }
    $admClass = new adminModel();
    $result = array();
    $result = $admClass->getPedidos();
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
                    <a href="#" class="logout">Cerrar sesión</a>
                </div>
            </div>
            <div class="dashboard">
                <div class="dashboard-item">
                    <h2>Orden en Proceso</h2>
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M48 0C21.5 0 0 21.5 0 48L0 368c0 26.5 21.5 48 48 48l16 0c0 53 43 96 96 96s96-43 96-96l128 0c0 53 43 96 96 96s96-43 96-96l32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-64 0-32 0-18.7c0-17-6.7-33.3-18.7-45.3L512 114.7c-12-12-28.3-18.7-45.3-18.7L416 96l0-48c0-26.5-21.5-48-48-48L48 0zM416 160l50.7 0L544 237.3l0 18.7-128 0 0-96zM112 416a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm368-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/></svg>                    </div>
                    <h2>2</h2>
                </div>
                <div class="dashboard-item">
                    <h2>Ordenes Completadas</h2>
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>
                    </div>
                    <h2>0</h2>
                </div>
                <div class="dashboard-item">
                    <h2>Total de Productos</h2>
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M36.8 192l566.3 0c20.3 0 36.8-16.5 36.8-36.8c0-7.3-2.2-14.4-6.2-20.4L558.2 21.4C549.3 8 534.4 0 518.3 0L121.7 0c-16 0-31 8-39.9 21.4L6.2 134.7c-4 6.1-6.2 13.2-6.2 20.4C0 175.5 16.5 192 36.8 192zM64 224l0 160 0 80c0 26.5 21.5 48 48 48l224 0c26.5 0 48-21.5 48-48l0-80 0-160-64 0 0 160-192 0 0-160-64 0zm448 0l0 256c0 17.7 14.3 32 32 32s32-14.3 32-32l0-256-64 0z"/></svg>
                    </div>
                    <h2>5</h2>
                </div>
            </div>
            <div class="orders">
                <h2>Ordenes Recientes</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>METODO DE PAGO</th>
                            <th>DATO DE LA ORDEN</th>
                            <th>INFORMACIÓN DEL CLIENTE</th>
                            <th>ESTADO</th>
                            <th>Total</th>
                            <th>ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if ($result->num_rows > 0) {
                            // Salida de datos para cada fila
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row["id_pedido"] . "</td>";
                                    echo "<td>TARJETA</td>";
                                    echo "<td>" . $row["fecha_creacion"] . "</td>";
                                    echo "<td>" . $row["Nombre"] . "</td>";
                                    echo "<td class='status'><span class='processing'>" . $row["estado"] . "</span></td>";
                                    echo "<td>Q.750</td>";
                                    echo "<td class='action'>";
                                    echo "<td class='action'>";
                                    if ($row["estado"] !== "listo") {
                                        echo "<button class='mark-ready edit' data-id='" . $row["id_pedido"] . "'>Marcar como Listo</button> ";
                                    } else {
                                        echo "<span>Pedido Listo</span> ";
                                    }
                                    echo "<button class='delete-order view' data-id='" . $row["id_pedido"] . "'>Eliminar</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No hay órdenes recientes</td></tr>";
                            }
                        ?>
                        <!---<tr>
                            <td>965488907439</td>
                            <td>TARJETA</td>
                            <td>September 9th 2020</td>
                            <td></td>
                            <td class="status"><span class="processing">processing</span></td>
                            <td>Q.750</td>
                            <td class="action"><a href="#" class="view">View</a> <a href="#" class="edit">Edit</a></td>
                        </tr>--->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Manejar cambio de estado
        document.querySelectorAll('.mark-ready').forEach(function(button) {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                fetch('../Controllers/adminController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'markReady', id_pedido: orderId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const row = document.getElementById('pedido-' + orderId);
                        row.querySelector('.status').textContent = 'listo';
                        this.remove();  // Eliminar el botón de "Marcar como Listo"
                    } else {
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