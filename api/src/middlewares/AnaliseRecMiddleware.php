<?php
require_once 'api/src/http/Response.php';
require_once 'api/src/DAO/AnaliseRecDAO.php';

    class AnaliseRecMiddleware 
    {
        public function stringJsonToStdClass($requestBody): stdClass{
            $stdAnalise = json_decode(json: $requestBody);
            if (json_last_error() !== JSON_ERROR_NONE){
                (new Response(
                    success: false,
                    message: "Análise-recomendacao inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Json inválido.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdAnaliseR->Dados->IdAnalise)){
                (new Response(
                    success: false,
                    message: "Análise-recomendacao inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o id da análise.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdAnaliseR->Dados->IdRecomendacao)){
                (new Response(
                    success: false,
                    message: "Análise inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o o id da recomendação.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }   

            return $stdAnaliseR;
        }

        public function isValidIdAnalise(string $idAnalise): self
        {
            if (!is_numeric($idAnalise)) {
                http_response_code(400);
                echo json_encode([
                    "erro" => "O id da análise precisa ser numérico."
                ]);
                exit();
            } else if ($idAnalise <= 0){
                http_response_code(400);
                echo json_encode([
                    "erro" => "O id da análise precisa ser um número positivo."
                ]);
                exit();
            }

            return $this;
        }
        public function isValidIdRecomendacao(string $idRecomendacao): self
        {
            if (!is_numeric($idRecomendacao)) {
                http_response_code(400);
                echo json_encode([
                    "erro" => "O id da recomendação precisa ser numérico."
                ]);
                exit();
            } else if ($idRecomendacao <= 0){
                http_response_code(400);
                echo json_encode([
                    "erro" => "O id da recomendação precisa ser um número positivo."
                ]);
                exit();
            }

            return $this;
        }
    }
?>