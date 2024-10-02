<?php


require_once __DIR__ . '/../Models/registrarModel.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

header('Content-Type: application/json');

try {

   
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = '502' . $_POST['telefono'];
    $password = $_POST['password'];

 
    $registrarModel = new registrarModel();

    // Verificar si el correo ya está registrado
    if ($registrarModel->verificarCorreo($email)) {
        throw new Exception("El correo ya está registrado.");
    }

    $resultado = $registrarModel->registrar($nombre, $email, $telefono, $password);

    if ($resultado) {
        // Si el registro fue exitoso, enviamos el correo de bienvenida con SES
        enviarCorreoBienvenida($email, $nombre);
        echo json_encode(["success" => true, "message" => "Registro exitoso. Confirma tu correo para iniciar sesión"]);
    } else {
        throw new Exception("Error al registrar el usuario.");
    }

} catch (Exception $e) {
    // En caso de error, devolver un JSON con el mensaje de error
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

// Función para enviar el correo de bienvenida usando Amazon SES
function enviarCorreoBienvenida($destinatarioEmail, $nombreUsuario) {
    try {
      
        $SesClient = new SesClient([
            'region' => 'us-east-1', 
            'version' => '2010-12-01',
        ]);

        // Configuración del correo
        $sender_email = 'uvgdevs@uvgaimingshop.me'; 
        $subject = '¡Bienvenido a UVG-Shop-Solutions!';
        $body_html = '<h1>Hola ' . htmlspecialchars($nombreUsuario) . '!</h1><p>Gracias por registrarte en nuestra aplicación. ¡Esperamos que disfrutes la experiencia!</p>';
        $body_text = 'Hola ' . $nombreUsuario . '!, Gracias por registrarte en nuestra aplicación.';

        // Enviar el correo
        $result = $SesClient->sendEmail([
            'Destination' => ['ToAddresses' => [$destinatarioEmail]],
            'Message' => [
                'Body' => [
                    'Html' => ['Charset' => 'UTF-8', 'Data' => $body_html],
                    'Text' => ['Charset' => 'UTF-8', 'Data' => $body_text],
                ],
                'Subject' => ['Charset' => 'UTF-8', 'Data' => $subject],
            ],
            'Source' => $sender_email,
        ]);
    } catch (AwsException $e) {
       
        error_log("Error al enviar el correo con SES: " . $e->getAwsErrorMessage());
    }
}

?>
