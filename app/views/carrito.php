<?php
session_start();
if (!$_SESSION['user_id']) {
    header("location: ../views/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1 {
            text-align: center;
            font-size: 2.5em;
            color: #333;
            margin-bottom: 30px;
        }
        .step-container {
            display: none;
            width: 90%;
            margin: 0 auto;
        }
        .active-step {
            display: block;
        }
        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #fff;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            position: relative;
        }
        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 20px;
        }
        .cart-item-details {
            flex: 1;
            margin-left: 20px;
        }
        .cart-item h3 {
            font-size: 1.2em;
            color: #333;
            margin-bottom: 5px;
        }
        .cart-item p {
            margin: 5px 0;
            font-size: 0.9em;
            color: #777;
        }
        .cart-summary {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .cart-summary .total {
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
        }
        .cart-summary .total span {
            color: #009879;
        }
        .btn {
            display: inline-block;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
            cursor: pointer;
            background-color: #009879;
            color: white;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #007d64;
        }
        .remove-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }
        .remove-btn:hover {
            background-color: #c0392b;
        }
        /* Estilos para la sección de método de pago */
        .payment-method {
            margin-top: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }
        .payment-method label {
            font-size: 1em;
            color: #333;
            margin-right: 10px;
        }
        .step-nav {
            margin-top: 20px;
            text-align: center;
        }
        .step-nav .btn {
            margin: 10px;
        }
        .order-summary {
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }
    </style>
    <script>
        let currentStep = 1;
        let selectedMetodoPago = 'efectivo';  // Inicializar con el valor por defecto

        // Función para obtener el carrito de LocalStorage vinculado al usuario
        function obtenerCarrito() {
            let userId = <?php echo $_SESSION['user_id']; ?>;
            let carritoKey = 'carrito_' + userId;
            let carrito = JSON.parse(localStorage.getItem(carritoKey)) || [];
            return carrito;
        }

        // Función para guardar el carrito actualizado en LocalStorage
        function guardarCarrito(carrito) {
            let userId = <?php echo $_SESSION['user_id']; ?>;
            let carritoKey = 'carrito_' + userId;
            localStorage.setItem(carritoKey, JSON.stringify(carrito));
        }

        // Función para mostrar los productos en el carrito (Paso 1)
        function mostrarCarrito() {
            let carrito = obtenerCarrito();
            let carritoContainer = document.getElementById('carritoContainer');
            let totalCarrito = 0;

            if (carrito.length > 0) {
                carritoContainer.innerHTML = ''; // Limpiar el contenido previo del carrito
                carrito.forEach((producto, index) => {
                    let cartItem = document.createElement('div');
                    cartItem.classList.add('cart-item');

                    let img = document.createElement('img');
                    img.src = 'https://via.placeholder.com/100';  // Puedes reemplazar con la URL de la imagen del producto
                    cartItem.appendChild(img);

                    let details = document.createElement('div');
                    details.classList.add('cart-item-details');

                    let nombreProducto = document.createElement('h3');
                    nombreProducto.textContent = producto.nombre;
                    details.appendChild(nombreProducto);

                    let descripcionProducto = document.createElement('p');
                    descripcionProducto.textContent = producto.descripcion;
                    details.appendChild(descripcionProducto);

                    let cantidadProducto = document.createElement('p');
                    cantidadProducto.textContent = `Cantidad: ${producto.cantidad}`;
                    details.appendChild(cantidadProducto);

                    let precioProducto = document.createElement('p');
                    precioProducto.innerHTML = `Precio: $${(producto.precio * producto.cantidad).toFixed(2)}`;
                    details.appendChild(precioProducto);

                    // Botón para eliminar producto
                    let removeBtn = document.createElement('button');
                    removeBtn.classList.add('remove-btn');
                    removeBtn.textContent = 'Eliminar';
                    removeBtn.onclick = () => eliminarProducto(index);
                    cartItem.appendChild(removeBtn);

                    cartItem.appendChild(details);
                    carritoContainer.appendChild(cartItem);

                    totalCarrito += producto.precio * producto.cantidad;
                });

                // Mostrar el total del carrito
                document.getElementById('totalCarrito').textContent = "Total: $" + totalCarrito.toFixed(2);
            } else {
                carritoContainer.innerHTML = "<p>Tu carrito está vacío.</p>";
            }
        }

        // Función para eliminar un producto del carrito
        function eliminarProducto(index) {
            let carrito = obtenerCarrito();
            carrito.splice(index, 1); // Eliminar producto del carrito
            guardarCarrito(carrito); // Guardar el carrito actualizado
            mostrarCarrito(); // Volver a mostrar el carrito actualizado
        }

        // Función para cambiar de paso
        function showStep(step) {
            document.querySelectorAll('.step-container').forEach((stepContainer, index) => {
                stepContainer.classList.remove('active-step');
                if (index === step - 1) {
                    stepContainer.classList.add('active-step');
                }
            });
            currentStep = step;
        }

        // Función para capturar el método de pago seleccionado
        function capturarMetodoPago() {
            const metodo = document.querySelector('input[name="metodo-pago"]:checked');
            selectedMetodoPago = metodo ? metodo.value : 'efectivo';
        }

        // Función para mostrar el resumen del pedido (Paso 3)
        function mostrarResumen() {
            let carrito = obtenerCarrito();
            let resumenContainer = document.getElementById('resumenContainer');
            resumenContainer.innerHTML = '';  // Limpiar contenido previo
            let totalCarrito = 0;

            if (carrito.length > 0) {
                carrito.forEach(producto => {
                    let resumenItem = document.createElement('div');
                    resumenItem.classList.add('cart-item');

                    let nombreProducto = document.createElement('h3');
                    nombreProducto.textContent = producto.nombre;
                    resumenItem.appendChild(nombreProducto);

                    let cantidadProducto = document.createElement('p');
                    cantidadProducto.textContent = `Cantidad: ${producto.cantidad}`;
                    resumenItem.appendChild(cantidadProducto);

                    let precioProducto = document.createElement('p');
                    precioProducto.textContent = `Precio total: $${(producto.precio * producto.cantidad).toFixed(2)}`;
                    resumenItem.appendChild(precioProducto);

                    resumenContainer.appendChild(resumenItem);

                    totalCarrito += producto.precio * producto.cantidad;
                });

                // Mostrar el método de pago seleccionado
                let metodoPagoResumen = document.createElement('p');
                metodoPagoResumen.textContent = `Método de pago: ${selectedMetodoPago}`;
                resumenContainer.appendChild(metodoPagoResumen);

                let totalResumen = document.createElement('p');
                totalResumen.textContent = `Total: $${totalCarrito.toFixed(2)}`;
                resumenContainer.appendChild(totalResumen);
            } else {
                resumenContainer.innerHTML = "<p>No hay productos en el carrito.</p>";
            }
        }

// Función para confirmar el pedido
function confirmarPedido() {
    let carrito = obtenerCarrito(); // Obtener el carrito de LocalStorage

    // Verificar el contenido del carrito antes de enviarlo
    console.log('Carrito actual:', carrito);  // Esto mostrará el contenido del carrito en la consola del navegador
    console.log(JSON.parse(localStorage.getItem('carrito_' + <?php echo $_SESSION['user_id']; ?>)));  // Verificar el carrito específico del usuario

    if (carrito.length === 0) {
        alert('El carrito está vacío.');
        return;
    }

    // Hacer una solicitud AJAX para enviar el carrito al servidor
    fetch('../Controllers/crear_pedido.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ carrito: carrito })  // Enviar el carrito como JSON
    })
    .then(response => response.text())
    .then(result => {
        alert(result);
        // Limpiar el carrito de LocalStorage si el pedido fue exitoso
        if (result.includes("exitosamente")) {
            localStorage.removeItem('carrito_' + <?php echo $_SESSION['user_id']; ?>);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}




        // Inicializar la página cuando se cargue
        window.onload = function() {
            mostrarCarrito();
            showStep(currentStep);
        }
    </script>
</head>
<body>
    <h1>Proceso de Compra</h1>

    <!-- Paso 1: Mostrar productos en el carrito -->
    <div class="step-container active-step" id="step1">
        <div class="cart-container" id="carritoContainer">
            <!-- Los productos del carrito se mostrarán aquí -->
        </div>
        <div class="cart-summary">
            <p class="total" id="totalCarrito">Total: $0.00</p>
        </div>
        <div class="step-nav">
            <a href="home.php" class="btn">Continuar Comprando</a>
        </div>
    </div>

    <!-- Paso 2: Selección de método de pago -->
    <div class="step-container" id="step2">
        <div class="payment-method">
            <h3>Método de Pago</h3>
            <label>
                <input type="radio" name="metodo-pago" value="efectivo" checked> Efectivo contra entrega
            </label><br>
            <label>
                <input type="radio" name="metodo-pago" value="deposito"> Depósito bancario
            </label><br>
            <label>
                <input type="radio" name="metodo-pago" value="tarjeta"> Tarjeta de débito/crédito
            </label>
        </div>
    </div>

    <!-- Paso 3: Confirmación del pedido -->
    <div class="step-container" id="step3">
        <div class="order-summary">
            <h3>Resumen del Pedido</h3>
            <div id="resumenContainer">
                <!-- El resumen del pedido se mostrará aquí -->
            </div>
        </div>
        <button class="btn" onclick="confirmarPedido()">Confirmar Pedido</button>
    </div>

    <!-- Navegación entre pasos -->
    <div class="step-nav">
        <button class="btn" onclick="showStep(currentStep - 1)" id="prevBtn">Anterior</button>
        <button class="btn" onclick="if(currentStep === 2){capturarMetodoPago();} if(currentStep === 3){mostrarResumen();} showStep(currentStep + 1);" id="nextBtn">Siguiente</button>
    </div>
</body>
</html>
