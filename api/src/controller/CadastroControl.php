<?php
require_once 'api/src/DAO/CadastroDAO.php';
require_once 'api/src/http/Response.php';

    class CadastroControl{
        public function index(): never{
            $cadastroDAO = new CadastroDAO();
            $resposta = $cadastroDAO->readAll();

            (new Response(
                success: true,
                message: 'Cadastros selecionados com sucesso.',
                data: ['cadastros' => $resposta],
                httpCode: 200
            ))->send();

            exit();
        }
        public function show(int $idCadastro): never
        {
            $cadastroDAO = new CadastroDAO();
            $resposta = $cadastroDAO->readById(usuarioID: $idCadastro);

            if ($resposta === null) {
                (new Response(
                    success: false,
                    message: 'Cadastro não encontrado.',
                    httpCode: 404
                ))->send();
            } else {
                (new Response(
                    success: true,
                    message: 'Cadastro selecionado com sucesso.',
                    data: ['cadastro' => $resposta],
                    httpCode: 200
                ))->send();
            }
            
            exit();
        }

        public function edit(stdClass $stdCadastro): never
        {
            $cadastroDAO = new CadastroDAO();
            $atual = $cadastroDAO->readById($stdCadastro->usuario->ID_usuario);

            $cadastro = new CadastroUsuario();
            $cadastro
                ->setId($stdCadastro->usuario->ID_usuario)
                ->setNome(isset($stdCadastro->usuario->Nome) ? $stdCadastro->usuario->Nome : $atual->getNome())
                ->setEmail(isset($stdCadastro->usuario->Email) ? $stdCadastro->usuario->Email : $atual->getEmail())
                ->setSenha(isset($stdCadastro->usuario->Senha) ? $stdCadastro->usuario->Senha : $atual->getSenha())
                ->setSenhaHash(isset($stdCadastro->usuario->Senha) ? password_hash($stdCadastro->usuario->Senha, PASSWORD_DEFAULT) : $atual->getSenhaHash())
                ->setRoleUsuario(isset($stdCadastro->usuario->RoleUsuario) ? $stdCadastro->usuario->RoleUsuario : $atual->getRoleUsuario())
                ->setDataCadastro($atual->getDataCadastro())
                ->setAtivo(isset($stdCadastro->usuario->Ativo) ? $stdCadastro->usuario->Ativo : $atual->getAtivo());

            $atualizado = $cadastroDAO->update($cadastro);
            if ($atualizado !== false) {
                (new Response(
                    success: true,
                    message: 'Cadastro atualizado com sucesso.',
                    data: ['Cadastro' => $atualizado],
                    httpCode: 200
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'Cadastro não atualizado.',
                    error: [
                        "code" => 'update_error',
                        "message" => 'Não foi possível atualizar o Cadastro.'
                    ],
                    httpCode: 400
                ))->send();
            }
            exit();
        }


        public function store(stdClass $stdCadastro): never
        {
            $cadastro = new CadastroUsuario();
            $cadastro
                ->setNome(nome: $stdCadastro->usuario->Nome)
                ->setEmail(email: $stdCadastro->usuario->Email)
                ->setSenha(senha: $stdCadastro->usuario->Senha)
                ->setSenhaHash(senhaHash: password_hash($stdCadastro->usuario->Senha, PASSWORD_DEFAULT)); // Armazena a senha como hash

            if (property_exists($stdCadastro->usuario, 'Ativo')) {
                $cadastro->setAtivo($stdCadastro->usuario->Ativo);
            }
            if (property_exists($stdCadastro->usuario, 'RoleUsuario')) {
                $cadastro->setRoleUsuario($stdCadastro->usuario->RoleUsuario);
            } else {
                $cadastro->setRoleUsuario('usuario'); // Default role
            }
            $cadastroDAO = new CadastroDAO();
            $nomeCadastro = $cadastroDAO->create($cadastro);
            (new Response(
                success: true,
                message: 'Cadastro criado com sucesso.',
                data: ['cadastro' => $nomeCadastro],
                httpCode: 200
            ))->send();
            exit();  
        }

        public function delete(int $idCadastro): never
        {
            $cadastroDAO = new CadastroDAO();
            $success = $cadastroDAO->delete($idCadastro);

            if ($success) {
                (new Response(
                    success: true,
                    message: 'Cadastro deletado com sucesso.',
                    data: null,
                    httpCode: 200
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'falha ao deletar cadastro.',
                    data: null,
                    httpCode: 404
                ))->send();
            }

            exit();
        }



        public function IsActive (int $idCadastro): never
        {
            $cadastroDAO = new CadastroDAO();
            $cadastro = $cadastroDAO->readById($idCadastro);
            
            if ($cadastro === null) {
                (new Response(
                    success: false,
                    message: 'Cadastro não encontrado.',
                    httpCode: 404
                ))->send();
                exit();
            }    
            if ($cadastro->getAtivo()) {
                $cadastro->setAtivo(false);
            } else {
                $cadastro->setAtivo(true); 
            }  
            $cadastroAtualizado= $cadastroDAO->update($cadastro);
                
            if (!$cadastroAtualizado){
                (new Response(
                    success: false,
                    message: 'Não houve sucesso na atualização de dados.',
                    httpCode: 200
                ))->send();
                exit();
            } else if ($cadastroAtualizado->getAtivo()){
                
                (new Response(
                    success: true,
                    message: 'Cadastro ativado com sucesso.',
                    data: ['Cadastro' => $cadastro],
                    httpCode: 200
                ))->send();
                exit();
            } else {
                (new Response(
                    success: true,
                    message: 'Cadastro desativado com sucesso.',
                    data: ['Cadastro' => $cadastro],
                    httpCode: 200
                ))->send();
            }
        }   
    }