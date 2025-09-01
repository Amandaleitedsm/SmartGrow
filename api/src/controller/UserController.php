<?php
require_once 'api/src/models/AuthModel.php';

class AuthController {
    public function sendCode() {
        $data = json_decode(file_get_contents('php://input'));
        $email = $data->Email ?? null;
        $authModel = new AuthModel();
        $response = $authModel->sendCode($email);

        if ($response['success']) {
            (new Response(
                success: true,
                message: 'Código enviado com sucesso.',
                data: ['code' => $response['codigo'], 'email' => $response['email']],
                httpCode: 200
            ))->send();
        } else {
            (new Response(
                success: false,
                message: 'Falha ao enviar código.',
                httpCode: 400
            ))->send();
        }

        exit();
    }
}
