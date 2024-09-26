<?php
require 'config/aws.php';
require 'app/Controllers/AuthController.php';

$data = json_decode(file_get_contents('php://input'), true);
$auth = new AuthController($cognito);

$result = $auth->login($data['email'], $data['password']);
echo json_encode($result);
