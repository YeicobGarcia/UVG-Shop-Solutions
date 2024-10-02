<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login.css">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="container">
        <div class="container-form">
            <form action="../Controllers/loginController.php" class="sing-in" method="post">
                <!---<form class="sing-in"> --->
                <h2>Iniciar Sesión</h2>
                <div class="social-networks">
                    <ion-icon name="logo-twitch"></ion-icon>
                    <ion-icon name="logo-twitter"></ion-icon>
                    <ion-icon name="logo-instagram"></ion-icon>
                    <ion-icon name="logo-tiktok"></ion-icon>
                </div>
                <span>Use su correo y contraseña</span>
                
                    <div class="container-input">
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="text" placeholder="Email" id="inNombre" name="inNombre" required>
                    </div>
                    <div class="container-input">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" placeholder="Password" id="inPassword" name="inPassword" required>
                    </div>
                    <a href="#">¿Olvidaste tu contraseña?</a>
                    <button class="button" type="submit">INICIAR SESIÓN</button>
            </form>
            <!-----</form> --->
        </div>

        <div class="container-form">
            <!-- form para registrar -->
            <form id="registerForm" action="../Controllers/registrarController.php" class="sing-up" method="post">
                <h2>Registrarse</h2>
                <div class="social-networks">
                    <ion-icon name="logo-twitch"></ion-icon>
                    <ion-icon name="logo-twitter"></ion-icon>
                    <ion-icon name="logo-instagram"></ion-icon>
                    <ion-icon name="logo-tiktok"></ion-icon>
                </div>
                <span>Use su correo y contraseña</span>
                <div class="container-input">
                    <ion-icon name="person-outline"></ion-icon>
                    <input type="text" placeholder="Nombre" id="nombre" name="nombre" required>
                </div>
                <div class="container-input">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="text" placeholder="Email" id="email" name="email" required>
                </div>
                <div class="container-input">
                    <ion-icon name="call-outline"></ion-icon>
                    <input type="text" placeholder="Telefono" id="telefono" name="telefono" required>
                </div>
                <div class="container-input">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" placeholder="Password" id="password" name="password" required>
                </div>
                <button class="button" type="submit">REGISTRARSE</button>
            </form>
        </div>

        <div class="container-welcome">
            <div class="welcome-sing-up welcome">
                <h3>¡Bienvenido!</h3>
                <p>Ingrese sus datos personales para usar las funciones del sitio</p>
                <button class="button" id="btn-sing-up">Registrarse</button>
            </div>
            <div class="welcome-sing-in welcome">
                <h3>¡Hola!</h3>
                <p>Regístrese con sus datos personales para todas las funciones del sitio</p>
                <button class="button" id="btn-sing-in">Iniciar sesión</button>
            </div>
        </div>
    </div>


    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../assets/js/login.js"></script>
    <script>
        const registerForm = document.getElementById('registerForm');

        registerForm.addEventListener('submit', async function (event) {
            event.preventDefault(); // Evita que el formulario se envíe de manera tradicional

            const formData = new FormData(registerForm);
            const response = await fetch(registerForm.action, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    html: result.message,
                    confirmButtonText: 'Ok'
                }).then (() => {
                    window.location.href = '../views/index.php';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: result.message,
                    confirmButtonText: 'Ok'
                });
            }
        });
    </script>
</body>

</html>