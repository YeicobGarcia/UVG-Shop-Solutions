<?php

    //cerrar sesion y redirigir a la pagina de inicio
    require_once __DIR__ . '/../Controllers/MySQLSessionHandler.php';

    // Configurar el manejador de sesiones
    $handler = new MySQLSessionHandler();
    session_set_save_handler($handler, true);
    session_start();
    session_destroy();
    
?>

<script>
    localStorage.removeItem('carrito_' + <?php echo $_SESSION['user_id']; ?>);
    window.location.href = '../views/index.php';
</script>