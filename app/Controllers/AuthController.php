<?php

class AuthController {
    protected $cognito;

    public function __construct($cognito) {
        $this->cognito = $cognito;
    }

    // Función para registrar un nuevo usuario
    public function register($name, $email, $password) {
        try {
            $result = $this->cognito->signUp([
                'ClientId' => 'YOUR_COGNITO_CLIENT_ID',
                'Username' => $email,
                'Password' => $password,
                'UserAttributes' => [
                    [
                        'Name' => 'name',
                        'Value' => $name,
                    ],
                    [
                        'Name' => 'email',
                        'Value' => $email,
                    ],
                ],
            ]);

            return ['status' => 'success', 'message' => 'User registered successfully'];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Función para iniciar sesión
    public function login($email, $password) {
        try {
            $result = $this->cognito->adminInitiateAuth([
                'AuthFlow' => 'ADMIN_NO_SRP_AUTH',
                'ClientId' => 'YOUR_COGNITO_CLIENT_ID',
                'UserPoolId' => 'YOUR_COGNITO_USER_POOL_ID',
                'AuthParameters' => [
                    'USERNAME' => $email,
                    'PASSWORD' => $password,
                ],
            ]);

            return ['status' => 'success', 'data' => $result['AuthenticationResult']];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    // Función para confirmar la cuenta con el código recibido por email
    public function confirmSignUp($email, $code) {
        try {
            $result = $this->cognito->confirmSignUp([
                'ClientId' => 'YOUR_COGNITO_CLIENT_ID',
                'Username' => $email,
                'ConfirmationCode' => $code,
            ]);

            return ['status' => 'success', 'message' => 'User confirmed successfully'];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
