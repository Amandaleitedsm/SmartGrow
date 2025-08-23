<?php
require_once 'api/src/http/Response.php';
require_once 'api/src/DAO/AnalisePlantaDAO.php';

    class AnalisePlantaMiddleware 
    {
        public function stringJsonToStdClass($requestBody): stdClass{
            $stdAnalise = json_decode(json: $requestBody);
            if (json_last_error() !== JSON_ERROR_NONE){
                (new Response(
                    success: false,
                    message: "Análise inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Json inválido.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdAnalise->analise->status_saude)){
                (new Response(
                    success: false,
                    message: "Análise inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o status de saúde da planta.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdAnalise->analise->status_umidade)){
                (new Response(
                    success: false,
                    message: "Análise inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o status de umidade da planta.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }   

            return $stdAnalise;
        }

        public function isValidStatusSaude(string $status): self
        {
            $statusPermitidos = ['Boa', 'Regular', 'Ruim', 'Doente'];

            if (!in_array($status, $statusPermitidos)) {
                http_response_code(400);
                echo json_encode([
                    "erro" => "O status de saúde precisa ser: " . implode(', ', $statusPermitidos)
                ]);
                exit();
            }

            return $this;
        }
        public function isValidStatusUmidade(string $status): self
        {
            $statusPermitidos = ['Baixa', 'Alta', 'Regular'];

            if (!in_array($status, $statusPermitidos)) {
                http_response_code(400);
                echo json_encode([
                    "erro" => "O status de umidade precisa ser: " . implode(', ', $statusPermitidos)
                ]);
                exit();
            }

            return $this;
        }
        public function isValidID($idAnalise): self
        {
            if(!isset($idAnalise))
            {
               (new Response(
                success: false,
                message: "ID da planta do usuário não foi informado.",
                error:[
                    "code" => 'planta_usuario_validation_error',
                    "message" => 'O ID da planta do usuário deve ser informado para a operação.'
                ],
                httpCode: 400
            ))->send();
            exit();
            }else if(!is_numeric($idAnalise) || ((int)$idAnalise) <= 0)
            {
                (new Response(
                    success: false,
                    message: "ID da planta do usuário inválido.",
                    error:[
                        "code" => 'planta_usuario_validation_error',
                        "message" => 'O ID da planta do usuário deve ser um número positivo.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }else{
               return $this; 
            } 
        }
        public function isValidIdUsuario($id): self
        {
            if(!isset($id))
            {
               (new Response(
                success: false,
                message: "ID do usuário não foi informado.",
                error:[
                    "code" => 'usuario_validation_error',
                    "message" => 'O ID do usuário deve ser informado para a operação.'
                ],
                httpCode: 400
            ))->send();
            exit();
            }else if(!is_numeric($id) || ((int)$id) <= 0)
            {
                (new Response(
                    success: false,
                    message: "ID do usuário inválido.",
                    error:[
                        "code" => 'usuario_validation_error',
                        "message" => 'O ID do usuário deve ser um número positivo.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }else{
               return $this; 
            } 
        }
    }
?>