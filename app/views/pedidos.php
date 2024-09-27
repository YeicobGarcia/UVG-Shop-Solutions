<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
require_once __DIR__ . '/../Controllers/MySQLSessionHandler.php';

// Configurar el manejador de sesiones
$handler = new MySQLSessionHandler();   
session_set_save_handler($handler, true);
session_start();
if (!$_SESSION['user_id']) {
    header("location: ../views/index.php");
    exit();
}

require_once __DIR__ . '/../Models/pedidosModel.php';
require '../../vendor/autoload.php'; // Incluir el SDK de AWS
use Aws\Sqs\SqsClient;

// Configurar el cliente de SQS
$sqs = new SqsClient([
    'region'  => 'us-east-1', // Cambia esto a tu región
    'version' => 'latest',
]);

$queueUrl = 'https://sqs.us-east-1.amazonaws.com/010526258458/ColaPedidosPrueba.fifo'; // Cambia esto a tu URL de la cola
$user_id = $_SESSION['user_id'];

// Obtener los pedidos confirmados del usuario desde la base de datos
$model = new pedidosModel();
$pedidosConfirmados = $model->getPedidosCliente($user_id);

// Obtener los pedidos pendientes del usuario desde la cola SQS
$pedidosPendientes = [];
$pedidosPorConfirmar = $sqs->receiveMessage([
    'QueueUrl'            => $queueUrl,
    'MaxNumberOfMessages' => 10, // Número máximo de mensajes a recibir (máx. 10)
    'WaitTimeSeconds'     => 0,  // Tiempo de espera para recibir mensajes
]);

if (!empty($pedidosPorConfirmar->get('Messages'))) {
    foreach ($pedidosPorConfirmar->get('Messages') as $mensaje) {
        $body = json_decode($mensaje['Body'], true);
        if ($body['user_id'] == $user_id) {
            $pedidosPendientes[] = [
                'id_pedido' => 'Pendiente', // Asignar "Pendiente" como ID para diferenciarlo
                'fecha_creacion' => date('Y-m-d H:i:s'), // Puedes ajustar esto si tienes una fecha específica en el mensaje
                'estado' => 'Pendiente de Confirmación',
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos</title>
    <link rel="stylesheet" href="../assets/css/index.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2.5em;
        }
        .pedidos-container {
            width: 90%;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f4f4f4;
            font-weight: 600;
            color: #333;
        }
        table td {
            font-size: 1em;
            color: #555;
        }
        .status {
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
        }
        /* Colores para los diferentes estados del pedido */
        .status-preparandose {
            background-color: #ffcc00;
            color: #fff;
        }
        .status-enviado {
            background-color: #009879;
            color: #fff;
        }
        .status-entregado {
            background-color: #28a745;
            color: #fff;
        }
        .status-cancelado {
            background-color: #dc3545;
            color: #fff;
        }
        .status-pendiente-de-confirmacion {
            background-color: #ffc107;
            color: #fff;
        }
        a.btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1em;
            text-align: center;
        }
        a.btn:hover {
            background-color: #0056b3;
        }
        @media (max-width: 768px) {
            table th, table td {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <h1>Mis Pedidos</h1>

    <div class="pedidos-container">
        <?php if (($pedidosConfirmados && mysqli_num_rows($pedidosConfirmados) > 0) || count($pedidosPendientes) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Mostrar pedidos confirmados -->
                    <?php while ($pedido = mysqli_fetch_assoc($pedidosConfirmados)): ?>
                    <tr>
                        <td><?php echo $pedido['id_pedido']; ?></td>
                        <td><?php echo $pedido['fecha_creacion']; ?></td>
                        <td>
                            <span class="status <?php echo getStatusClass($pedido['estado']); ?>">
                                <?php echo ucfirst($pedido['estado']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                    <!-- Mostrar pedidos pendientes de confirmación -->
                    <?php foreach ($pedidosPendientes as $pedidoPendiente): ?>
                    <tr>
                        <td><?php echo $pedidoPendiente['id_pedido']; ?></td>
                        <td><?php echo $pedidoPendiente['fecha_creacion']; ?></td>
                        <td>
                            <span class="status <?php echo getStatusClass($pedidoPendiente['estado']); ?>">
                                <?php echo ucfirst($pedidoPendiente['estado']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No has realizado ningún pedido.</p>
        <?php endif; ?>
    </div>

    <div style="text-align: center;">
        <a href="home.php" class="btn">Volver a la tienda</a>
    </div>

    <?php
    // Función para asignar clase CSS según el estado del pedido
    function getStatusClass($estado) {
        switch (strtolower($estado)) {
            case 'preparandose':
                return 'status-preparandose';
            case 'enviado':
                return 'status-enviado';
            case 'entregado':
                return 'status-entregado';
            case 'cancelado':
                return 'status-cancelado';
            case 'pendiente de confirmación':
                return 'status-pendiente-de-confirmacion';
            default:
                return '';
        }
    }
    ?>
</body>
</html>
