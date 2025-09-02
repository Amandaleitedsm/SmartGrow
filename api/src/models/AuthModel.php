<?php
require_once 'api/src/DAO/EmailDAO.php';
require_once 'api/vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthModel {
    private EmailDAO $emailDAO;

    public function __construct() {
        $this->emailDAO = new EmailDAO();
    }

    public function sendCode(string $email): array {
        
        if (!$email) {
            return ["success" => false, "message" => "Email não fornecido"];
        }

        // Gera código aleatório
        $codigo = rand(100000, 999999);

        // Salva no banco
        $saved = $this->emailDAO->saveCode($email, $codigo);
        if (!$saved) {
            return ["success" => false, "message" => "Erro ao salvar código no banco"];
        }

        // Envia e-mail
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.mail.yahoo.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'lu39cemachado@yahoo.com.br';
            $mail->Password = 'gzkzdultotidoclj';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('lu39cemachado@yahoo.com.br', 'SmartGrow');
            $mail->addAddress($email);
            $mail->Subject = 'Seu código de verificação';
            $mail->Body = "<h1> Bem vindo de volta ao SmartGrow</h1><br>
            <p> Como você esqueceu sua senha, estamos te enviando um código para que possa criar uma nova senha. 
            Digite-a no campos disponível no site. </p><br>
            <h3>CÓDIGO DE VERIFICAÇÃO: </h3><br><br><h1>$codigo</h1>";

            $mail->CharSet = "UTF-8";   // força UTF-8 nos headers
            $mail->isHTML(true);        // garante suporte a acentuação e formatação

            $mail->SMTPDebug = 3; // ou 3 para ainda mais detalhado
            $mail->SMTPDebug = 0;


            $mail->send();
            return ["success" => true, "codigo" => $codigo, "email" => $email];
        } catch (Exception $e) {
            return ["success" => false, "message" => "Erro ao enviar e-mail: " . $mail->ErrorInfo];
        }
    }
}
