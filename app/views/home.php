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
        function addToCart() {
            $.ajax({
                type: "POST",
                url: "../Controllers/crear_pedido.php",  // Ruta al archivo PHP que manejará la inserción
                success: function(response) {
                    alert(response);  // Muestra la respuesta del servidor
                },
                error: function() {
                    alert("Error al crear el pedido");
                }
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
            <li>
              <a href="">US</a>
            </li>
            <li>
              <a href="">Login</a>
            </li>
            <li>
              <a href="">Support</a>
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
            <div class="col-md-2">
              <div class="card product-item" onclick="addToCart()">
                product 1
              </div>
            </div>
            <div class="col-md-2">
              <div class="card product-item">
                product
              </div>
            </div>
            <div class="col-md-2">
              <div class="card product-item">
                product
              </div>
            </div>
            <div class="col-md-2">
              <div class="card product-item">
                product
              </div>
            </div>
            <div class="col-md-2">
              <div class="card product-item">
                product
              </div>
            </div>
            <div class="col-md-2">
              <div class="card product-item">
                product
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</body>
        <script src="https://unpkg.co/gsap@3/dist/gsap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="../assets/js/index.js"></script>
</html>
