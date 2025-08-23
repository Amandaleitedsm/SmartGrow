<?php
require_once 'api/src/http/Response.php';
require_once 'api/src/DAO/PlantaUsuarioDAO.php';

    class PlantaUsuarioMiddleware 
    {
        public function stringJsonToStdClass($requestBody): stdClass{
            $stdPlanta = json_decode(json: $requestBody);
            if (json_last_error() !== JSON_ERROR_NONE){
                (new Response(
                    success: false,
                    message: "Planta inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Json inválido.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!isset($stdPlanta->Dados->IdPlanta)){
                    (new Response(
                        success: false,
                        message: "Planta inválida",
                        error:[
                            "code" => 'validation_error',
                            "message" => 'Não foi enviado o ID da planta.'
                        ],
                        httpCode: 400
                    ))->send();
                    exit();
                }
                else if (!isset($stdPlanta->Dados->Apelido)){
                    (new Response(
                        success: false,
                        message: "Planta inválida",
                        error:[
                            "code" => 'validation_error',
                            "message" => 'Não foi enviada o apelido da planta.'
                        ],
                        httpCode: 400
                    ))->send();
                    exit();
                }
                else if (!isset($stdPlanta->Dados->Localizacao)){
                    (new Response(
                        success: false,
                        message: "Planta inválida",
                        error:[
                            "code" => 'validation_error',
                            "message" => 'Não foi enviada a localização da planta.'
                        ],
                        httpCode: 400
                    ))->send();
                    exit();
                }
            } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                $temCamposAtualizaveis = 
                            isset($stdPlanta->Dados->Apelido) ||
                            isset($stdPlanta->Dados->Localizacao);

                if (!$temCamposAtualizaveis) {
                    (new Response(
                        success: false,
                        message: "Requisição inválida",
                        error: [
                            "code" => 'validation_error',
                            "message" => 'Nenhum campo de atualização foi enviado (Apelido ou Localizacao).'
                        ],
                        httpCode: 400
                    ))->send();
                    exit();
                } 
            }
            return $stdPlanta;
        }

        public function hasNotPlantaByApelido($apelido, int $idUsuario): self{
            $plantaUsuarioDAO = new PlantaUsuarioDAO();
            $planta = $plantaUsuarioDAO->readByApelido(apelido: $apelido, idUsuario: $idUsuario);
            if (!empty($planta)) {
                if(isset($planta)){
                    (new Response(
                        success: false,
                        message: "Planta já cadastrada",
                        error:[
                            "code" => 'validation_error',
                            "message" => 'Já existe uma planta cadastrada com o apelido informado.'
                        ],
                        httpCode: 400
                    ))->send();
                    exit();
                }
            }
            return $this;
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

        public function isValidIdUsuario ($id): self
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

        public function isValidApelido($apelido): self
        {
            if (!is_string($apelido) || trim($apelido) === '') {
            (new Response(
                success: false,
                message: "Apelido inválido.",
                error: [
                    "code" => 'planta_validation_error',
                    "message" => 'O apelido da planta deve ser uma string não vazia.'
                ],
                httpCode: 400
            ))->send();
            exit();
            } 
            if (mb_strlen($apelido) > 50) {
                (new Response(
                    success: false,
                    message: "Apelido muito longo.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'O apelido da planta deve ter no máximo 50 caracteres.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            return $this;
        }

        public function isValidLocalizacao($localizacao): self
        {
            if (!isset($localizacao) || !is_string($localizacao) || trim($localizacao) === '') {
                (new Response(
                    success: false,
                    message: "Localização inválida.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A localização da planta deve ser uma string não vazia.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if (mb_strlen($localizacao) > 150) {
                (new Response(
                    success: false,
                    message: "Localização muito longa.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A localização da planta deve ter no máximo 150 caracteres.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            return $this;
        }
    }
?>