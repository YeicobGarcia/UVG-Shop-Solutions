<?php

    //cerrar sesion y redirigir a la pagina de inicio
    session_start();
    session_destroy();
    
?>

<script>
    localStorage.removeItem('carrito_' + <?php echo $_SESSION['user_id']; ?>);
    window.location.href = '../views/index.php';
</script>