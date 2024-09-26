<?php
require 'config/aws.php';
require 'app/Controllers/AuthController.php';

$data = json_decode(file_get_contents('php://input'), true);
$auth = new AuthController($cognito);

$result = $auth->confirmSignUp($data['email'], $data['code']);
echo json_encode($result);
