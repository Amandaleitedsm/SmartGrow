<?php
require_once 'api/src/http/Response.php';
require_once 'api/src/DAO/CadastroDAO.php';

    class CadastroMiddleware 
    {
        public function stringJsonToStdClass($requestBody): stdClass{
            $stdCadastro = json_decode(json: $requestBody);
            if (json_last_error() !== JSON_ERROR_NONE){
                (new Response(
                    success: false,
                    message: "Cadastro inválido",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Json inválido.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!isset($stdCadastro->usuario->Nome)){
                    (new Response(
                        success: false,
                        message: "Usuário inválido",
                        error:[
                            "code" => 'validation_error',
                            "message" => 'Não foi enviado o nome do usuário.'
                        ],
                        httpCode: 400
                    ))->send();
                    exit();
                }
                else if (!isset($stdCadastro->usuario->Email)){
                    (new Response(
                        success: false,
                        message: "Usuário inválido",
                        error:[
                            "code" => 'validation_error',
                            "message" => 'Não foi enviado o email do usuário.'
                        ],
                        httpCode: 400
                    ))->send();
                    exit();
                }
                else if (!isset($stdCadastro->usuario->Senha)){
                    (new Response(
                        success: false,
                        message: "Usuário inválido",
                        error:[
                            "code" => 'validation_error',
                            "message" => 'Não foi enviada a senha do usuário.'
                        ],
                        httpCode: 400
                    ))->send();
                    exit();
                }
            } else if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $_SERVER['REQUEST_URI'] === '/usuarios/(\d+)') {
                $temCamposAtualizaveis = isset($stdCadastro->usuario->Nome) ||
                             isset($stdCadastro->usuario->Email) ||
                             isset($stdCadastro->usuario->Senha) ||
                             isset($stdCadastro->usuario->Ativo) ||
                             isset($stdCadastro->usuario->RoleUsuario);

                if (!$temCamposAtualizaveis) {
                    (new Response(
                        success: false,
                        message: "Requisição inválida",
                        error: [
                            "code" => 'validation_error',
                            "message" => 'Nenhum campo de atualização foi enviado (Nome, Email, Senha_hash ou Ativo).'
                        ],
                        httpCode: 400
                    ))->send();
                    exit();
                } 
            } else if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $_SERVER['REQUEST_URI'] === '/auth/change-password') {
                if (!isset($stdCadastro->Email)){
                    (new Response(
                        success: false,
                        message: "Usuário inválido",
                        error:[
                            "code" => 'validation_error',
                            "message" => 'Não foi enviado o email do usuário.'
                        ],
                        httpCode: 400
                    ))->send();
                    exit();
                }
                else if (!isset($stdCadastro->Senha)){
                    (new Response(
                        success: false,
                        message: "Usuário inválido",
                        error:[
                            "code" => 'validation_error',
                            "message" => 'Não foi enviada a nova senha do usuário.'
                        ],
                        httpCode: 400
                    ))->send();
                    exit();
                }
            }

            return $stdCadastro;
        }

        public function isValidID($idUsuario): self
        {
            if(!isset($idUsuario))
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
            }else if(!is_numeric($idUsuario) || ((int)$idUsuario) <= 0)
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

        public function isValidNome($nome): self
        {
            if (!isset($nome)){
                (new Response(
                    success: false,
                    message: "Nome inválido",
                    error: [
                        "code" => 'validation_error',
                        "message" => 'O nome do usuário deve ser informado.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            } else if (strlen($nome) < 3) {
                (new Response(
                    success: false,
                    message: "Nome inválido",
                    error: [
                        "code" => 'validation_error',
                        "message" => 'O nome do usuário deve ter no mínimo 3 letras.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }else{
                return $this;
            }
        }
        public function isValidEmail($email): self
        {
            if (!isset($email)){
                (new Response(
                    success: false,
                    message: "Email inválido",
                    error: [
                        "code" => 'validation_error',
                        "message" => 'O email do usuário deve ser informado.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                (new Response(
                    success: false,
                    message: "Email inválido",
                    error: [
                        "code" => 'validation_error',
                        "message" => 'O email do usuário deve ser um email válido.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }else {
                return $this;
            }
        }
        public function isValidSenha($senha): self
        {
            if (!isset($senha)){
                (new Response(
                    success: false,
                    message: "Senha inválida",
                    error: [
                        "code" => 'validation_error',
                        "message" => 'A senha do usuário deve ser informada.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            } else if (strlen($senha) < 6) {
                (new Response(
                    success: false,
                    message: "Senha inválida",
                    error: [
                        "code" => 'validation_error',
                        "message" => 'A senha do usuário deve conter no mínimo 6 caracteres.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            } else{
                return $this;
            }
        }
    }
?>