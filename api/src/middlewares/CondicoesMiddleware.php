<?php
require_once 'api/src/http/Response.php';
require_once 'api/src/DAO/CondicoesDAO.php';

    class CondicoesMiddleware 
    {
        public function stringJsonToStdClass($requestBody): stdClass{
            $stdCondicao = json_decode(json: $requestBody);
            if (json_last_error() !== JSON_ERROR_NONE){
                (new Response(
                    success: false,
                    message: "Condição inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Json inválido.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if (!isset($stdCondicao->Condicao->IdPlanta)){
                (new Response(
                    success: false,
                    message: "Condição inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o ID da planta.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdCondicao->Condicao->Umidade)){
                (new Response(
                    success: false,
                    message: "Condição inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviada a umidade da planta.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
    
            return $stdCondicao;
        }

        public function isValidId(int $id): self
        {
            if (!isset($id) || !is_numeric($id) || ((int)$id) <= 0) {
                (new Response(
                    success: false,
                    message: "ID inválido",
                    error: [
                        "code" => 'validation_error',
                        "message" => 'O ID deve ser um número positivo.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            return $this;
        }

        public function isValidIdPlanta($idPlanta): self
        {
            if(!isset($idPlanta))
            {
               (new Response(
                success: false,
                message: "ID da planta não foi informado.",
                error:[
                    "code" => 'planta_validation_error',
                    "message" => 'O ID da planta deve ser informado para a operação.'
                ],
                httpCode: 400
                ))->send();
                exit();
            }else if(!is_numeric($idPlanta) || ((int)$idPlanta) <= 0)
            {
                (new Response(
                    success: false,
                    message: "ID da planta inválido.",
                    error:[
                        "code" => 'planta_validation_error',
                        "message" => 'O ID da planta deve ser um número positivo.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }else{
               return $this; 
            } 
        }

        public function isValidUmidade($umidade): self
        {
            if (!isset($umidade) || !is_numeric($umidade)) {
                (new Response(
                    success: false,
                    message: "Umidade inválida.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A umidade da planta deve ser um número.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($umidade < 0 || $umidade > 100) {
                (new Response(
                    success: false,
                    message: "Umidade fora do intervalo.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A umidade da planta deve estar entre 0 e 100.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            return $this;
        }
    }
?>