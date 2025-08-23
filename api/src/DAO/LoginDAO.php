<?php
require_once 'api/src/db/Database.php';
require_once 'api/src/models/CadastroUsuario.php';
require_once "api/src/utils/Logger.php";

class LoginDAO{

    public function verificarLogin(CadastroUsuario $usuario): ?CadastroUsuario {
        
        $query = ' SELECT ID_usuario, nome, senha_hash, email, RoleUsuario, Data_criacao, Data_atualizacao, Ativo FROM cadastro_usuario
            WHERE 
                email = :email
            ORDER BY nome ASC ';
        // Prepara a instrução SQL, protegendo contra SQL Injection
        $statement = Database::getConnection()->prepare(query: $query);
        $statement->bindValue(
            param: ':email',
            value: $usuario->getEmail(),
            type: PDO::PARAM_STR
        );
        // Busca a única linha esperada da consulta como um objeto genérico (stdClass)
        $statement->execute();
        
        
        // Busca a única linha esperada da consulta como um objeto genérico (stdClass)
        $linha = $statement->fetch(mode: PDO::FETCH_OBJ);

        if (!$linha) {
            return null; // Retorna array vazio caso não encontre nenhum funcionário com esse idFuncionario
        }
    

        if (!password_verify($usuario->getSenha(), $linha->senha_hash)) {
            return null;
        }
        // Preenche os dados básicos do funcionário no objeto Funcionario
        $usuario
            ->setId(ID_usuario: $linha->ID_usuario)         // ID do usuário
            ->setNome(nome: $linha->nome)      // Nome do usuário
            ->setEmail(email: $linha->email)
            ->setRoleUsuario(roleUsuario: $linha->RoleUsuario)
            ->setSenhaHash(senhaHash: $linha->senha_hash);     // ID do cargo associado

        return $usuario;
    }
}