<?php
require_once 'api/src/db/Database.php';
require_once 'api/src/models/CadastroUsuario.php';
require_once "api/src/utils/Logger.php";

    class CadastroDAO{
       public function readAll(){
            $resultados = [];
            $query = 'SELECT 
                ID_usuario,
                Nome,
                Email,
                Senha_hash,
                RoleUsuario,
                Data_criacao,
                Data_atualizacao,
                ativo
                FROM cadastro_usuario ORDER BY ID_usuario ASC';

            $statement =  Database::getConnection()->query(query: $query); // impedir sql injection
            $statement->execute();

            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);
            
            return $resultados;
        }
        public function readById(int $usuarioID): CadastroUsuario|null
        {
            $query = 'SELECT 
                        ID_usuario,
                        Nome,
                        Email,
                        Senha_hash,
                        RoleUsuario,
                        Data_criacao,
                        Data_atualizacao,
                        ativo
                    FROM cadastro_usuario
                    WHERE ID_usuario = :usuarioID;';

            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':usuarioID' => $usuarioID]);

            $linha = $statement->fetch(PDO::FETCH_OBJ);

            if (!$linha) {
                return null;
            }

            return (new CadastroUsuario())
                ->setId((int)$linha->ID_usuario)
                ->setNome($linha->Nome)
                ->setEmail($linha->Email)
                ->setSenhaHash($linha->Senha_hash)
                ->setRoleUsuario($linha->RoleUsuario)
                ->setDataCadastro(new DateTime($linha->Data_criacao))
                ->setDataAtualizacao(new DateTime($linha->Data_atualizacao))
                ->setAtivo($linha->ativo);
        }

        public function readByName(string $name): array {
            $query = 'SELECT * 
                FROM analise_planta 
                WHERE Nome = :name 
                ORDER BY Nome ASC;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute([':name' => $name]);
            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $resultados;
        }

        public function create(CadastroUsuario $usuario): CadastroUsuario|false{
            $agora = new DateTime();
            $usuario->setDataCadastro($agora);
            $usuario->setDataAtualizacao($agora);

            $query = 'INSERT INTO 
                    cadastro_usuario (
                        Nome, 
                        Email,
                        Senha_hash,
                        RoleUsuario,
                        Data_criacao,
                        Data_atualizacao,
                        ativo
                    ) 
                    VALUES (
                        :nome, 
                        :email,
                        :senhaHash,
                        :roleUsuario,
                        :dataCriacao,
                        :dataAtualizacao,
                        :ativo
                    );';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $success = $statement->execute([
                ':nome' => $usuario->getNome(),
                ':email' => $usuario->getEmail(),
                ':senhaHash' => $usuario->getSenhaHash(),
                ':roleUsuario' => $usuario->getRoleUsuario(),
                ':dataCriacao' => $usuario->getDataCadastro()->format('Y-m-d H:i:s'),
                ':dataAtualizacao' => $usuario->getDataAtualizacao()->format('Y-m-d H:i:s'),
                ':ativo' => $usuario->getAtivo()
            ]);

            if (!$success) {
                return false;
            }
            $usuario->setId((int) Database::getConnection()->lastInsertId());

            return $usuario;
        }

        public function delete(int $idUsuario): bool
        {
            $query = 'DELETE FROM cadastro_usuario WHERE ID_usuario = :idUsuario';
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':idUsuario' => $idUsuario]);
            return $statement->rowCount() > 0;
        }

        public function update(CadastroUsuario $usuario): CadastroUsuario|false {
            $agora = new DateTime();
            $usuario->setDataAtualizacao($agora);

            $query = 'UPDATE cadastro_usuario SET 
                        Nome = :nome, 
                        Email = :email,
                        Senha_hash = :senhaHash,
                        RoleUsuario = :roleUsuario,
                        Data_atualizacao = :dataAtualizacao,
                        ativo = :ativo
                    WHERE ID_usuario = :idUsuario;';

            $statement = Database::getConnection()->prepare(query: $query);
            $success = $statement->execute([
                ':idUsuario' => $usuario->getId(),
                ':nome' => $usuario->getNome(),
                ':email' => $usuario->getEmail(),
                ':senhaHash' => $usuario->getSenhaHash(),
                ':roleUsuario' => $usuario->getRoleUsuario(),
                ':dataAtualizacao' => $usuario->getDataAtualizacao()->format('Y-m-d H:i:s'),
                ':ativo' => $usuario->getAtivo()
            ]);

            if (!$success) {
                return false;
            }

            return $usuario;
        }


        


        
            

    }