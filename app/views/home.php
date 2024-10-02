<?php 
    // Incluir la clase de manejo de sesiones
  require_once __DIR__ . '/../Controllers/MySQLSessionHandler.php';

  // Configurar el manejador de sesiones
  $handler = new MySQLSessionHandler();
  session_set_save_handler($handler, true);
  session_start();

    if (!$_SESSION['user_id']) {
      header("location: ../views/index.php");
      exit();
    }
    //include_once('../Controllers/crear_pedido.php');    
    //$resultado = $model->crearPedido();

    include_once('../Controllers/productoController.php');
    include_once('../Controllers/carritoController.php');

    $productController = new ProductController();
    $productos = $productController->showProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../assets/css/index.css">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=PT+Sans:wght@700&family=Poppins&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flexboxgrid/6.3.1/flexboxgrid.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <title>UVGaming-Shop</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
    // Función para agregar productos al carrito usando LocalStorage vinculado al usuario
function agregarAlCarrito(id, nombre, descripcion, precio) {
    // Obtener el ID del usuario de la sesión
    let userId = <?php echo $_SESSION['user_id']; ?>;
    
    // Crear una clave única para el carrito del usuario
    let carritoKey = 'carrito_' + userId;

    // Obtener el carrito de LocalStorage (si existe) usando la clave del usuario
    let carrito = JSON.parse(localStorage.getItem(carritoKey)) || [];

    // Verificar si el producto ya está en el carrito
    let productoExistente = carrito.find(producto => producto.id === id);

    if (productoExistente) {
        // Si el producto ya está en el carrito, aumentar la cantidad
        productoExistente.cantidad += 1;
    } else {
        // Si el producto no está en el carrito, agregarlo con cantidad = 1
        carrito.push({ id, nombre, descripcion, precio, cantidad: 1 });
    }

    // Guardar el carrito actualizado en LocalStorage con la clave del usuario
    localStorage.setItem(carritoKey, JSON.stringify(carrito));

    // Mostrar mensaje de éxito usando SweetAlert2
    Swal.fire({
        title: '¡Éxito!',
        text: 'Producto agregado al carrito.',
        icon: 'success',
        confirmButtonText: 'OK',
        timer: 2000,
        showConfirmButton: false
    });
}

    </script>
</head>
<body>
    <div id="wrapper">
    <a href="" class="logo"></a>
    <header>
        
      <div class="row">
        <div class="col-md-3">
          <a href="" class="burger"><span class="icon"></span> UVGaming-Shop</a>
        </div>
        <div class="col-md-6">
  
        </div>
        <div class="col-md-3">
          <ul class="top-menu">

          <!-- Cuenta de usuario -->
            <li>
              <a href="../views/pedidos.php"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-person-lines-fill" viewBox="0 0 16 16">
  <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1z"/>
</svg></a> 
            </li>
           
           <!-- Carrito de compras -->
            <li>
            
            <a href="../views/carrito.php" data-toggle="modal" data-target="#cart"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
  <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
</svg>(<span class="total-count"></span>)</a>

            </li>
            
            <li>
              <a href="../Controllers/logoutController.php" ><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-box-arrow-left" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z"/>
  <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z"/>
</svg></a>
            </li>
          </ul>
        </div>
      </div>
    </header>
    <section>
      <div class="row">
        <div class="col-md-9">
          <ul class="brand-list">
            <li>
              <a href="" class="active">All brands</a>
            </li>
            <li>
              <a href="">Federal Bikes</a>
            </li>
            <li>
              <a href="">Wethepeople</a>
            </li>
            <li>
              <a href="">Fiend</a>
            </li>
            <li>
              <a href="">Cult</a>
            </li>
            <li>
              <a href="">Suprosa</a>
            </li>
            <li>
              <a href="">Odyssey</a>
            </li>
          </ul>
        </div>
        <div class="col-md-3 search-top">
          <div class="input-wrapper">
            <input type="text" placeholder="Search">
            <button><svg viewBox="0 0 512 512" width="100" title="search">
                <path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z" />
              </svg></button>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-2">
          <h3 class="side-title">Products</h3>
          <ul class="products-category">
            <li>
              <a href="" class="active">All</a>
            </li>
            <li><a href="">Bikes</a></li>
            <li><a href="">Frames</a></li>
            <li><a href="">Wheels</a></li>
            <li><a href="">Steering</a></li>
            <li><a href="">Clothing</a></li>
            <li><a href="">Drivechain</a></li>
            <li><a href="">Misc</a></li>
            <li><a href="">Seating</a></li>
            <li><a href="">Sale</a></li>
          </ul>
        </div>
        <div class="col-md-10">
          <div class="row">
            <div class="col-md-8">
              <div class="main-hero-slider">
                <h2>Garret Reynolds'<br>New Bike.</h2>
                <img class="dude" src="https://trumasex.com/tom/i-removebg-preview.png" alt="">
                <div class="content-cta">
                  <button>Buy now</button>
                  <button class="dark">See review</button>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card">
                <div class="card-image example"></div>
                <div class="card-content">
                  <h5>XGAMES 2020</h5>
                  <p>New DVD, USA 2020 + Flybikes and Red Bull</p>
                  <ul class="users">
                    <li><img src="https://i.pravatar.cc/150?img=13" alt=""></li>
                    <li><img src="https://i.pravatar.cc/150?img=14" alt=""></li>
                    <li><img src="https://i.pravatar.cc/150?img=15" alt=""></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
                <div class="col-md-10">
                    <div class="row">
                    <?php
            if ($productos) {
                // Iterar sobre los productos y mostrarlos
                while ($producto = $productos->fetch_assoc()) {
                    echo "
                        <div class='col-md-3'>
                            <div class='card product-item'>
                                <h3>" . $producto['NOMBRE'] . "</h3>
                                <p>Descripción: " . $producto['DESCRIPCION'] . "</p>
                                <p>Precio: $" . number_format($producto['PRECIO'], 2) . "</p>
                                <button onclick=\"agregarAlCarrito(" . $producto['ID'] . ", '" . $producto['NOMBRE'] . "', '" . $producto['DESCRIPCION'] . "', " . $producto['PRECIO'] . ")\">Agregar al carrito</button>
                            </div>
                        </div>
                    ";
                }
            } else {
                echo "<p>No hay productos disponibles en este momento.</p>";
            }
            ?>


                    </div>
                </div>
            </div>
    </section>
  </div>
</body>
        <script src="https://unpkg.co/gsap@3/dist/gsap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="../assets/js/index.js"></script>
        <script src="../assets/js/carrito.js"></script>
</html>

