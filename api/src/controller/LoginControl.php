<?php
require_once 'api/src/models/CadastroUsuario.php';
require_once 'api/src/models/MeuTokenJWT.php';
require_once 'api/src/DAO/LoginDAO.php';
require_once 'api/src/http/Response.php';
class LoginControl {
    public function autenticar(stdClass $stdLogin): never {
        // Cria uma instância do DAO para acessar os dados no banco
        $loginDAO = new LoginDAO();
        $usuario = new CadastroUsuario();
        $usuario->setEmail($stdLogin->usuario->Email);
        $usuario->setSenha($stdLogin->usuario->Senha);
        
        $usuarioLogado = $loginDAO->verificarLogin($usuario);
        
        if (empty($usuarioLogado)) {
        // Envia a resposta JSON com os dados encontrados
        (new Response(
            success: false,
            message: 'Usuário e senha inválidos',
            httpCode: 401
        ))->send();
        } else {
            $claims = new stdClass();
            $claims->Nome = $usuarioLogado->getNome();
            $claims->Email = $usuarioLogado->getEmail();
            $claims->Role = $usuarioLogado->getRoleUsuario();
            $claims->IdUsuario = $usuarioLogado->getId();
            $claims->Ativo = $usuarioLogado->getAtivo();

            $meuToken = new MeuTokenJWT();
            $token = $meuToken->gerarToken($claims);
            (new Response(
                success: true,
                message: 'Usuário e senha validados',
                data: [
                'token' => $token,
                'usuario' => $usuarioLogado
                ],
                httpCode: 200
            ))->send();
            }
            exit();
        }
    }