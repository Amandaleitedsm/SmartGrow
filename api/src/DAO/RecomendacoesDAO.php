<?php
require_once 'api/src/db/Database.php';
require_once 'api/src/models/Recomendacoes.php';
require_once "api/src/utils/Logger.php";

    class RecomendacoesDAO{
       public function readAll(){
            $resultados = [];
            $query = 'SELECT *
                FROM recomendacoes ORDER BY ID ASC';

            $statement =  Database::getConnection()->query(query: $query); // impedir sql injection
            $statement->execute();

            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);

            return $resultados;
        }

        public function readByID($idRecomendacao){
            $query = 'SELECT *
                FROM recomendacoes 
                WHERE ID = :idRecomendacao;';

            $statement = Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute([':idRecomendacao' => $idRecomendacao]);

            $resultado = $statement->fetchAll(mode: PDO::FETCH_ASSOC);

            return $resultado;
        }

        public function readByTitulo($titulo){
            $query = 'SELECT *
                FROM recomendacoes 
                WHERE Titulo = :titulo;';

            $statement = Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute([':titulo' => $titulo]);

            $resultado = $statement->fetchAll(mode: PDO::FETCH_ASSOC);

            return $resultado;
        }

        public function create(Recomendacoes $recomendacoes): Recomendacoes|false{

            $query = 'INSERT INTO 
                    recomendacoes (
                        titulo,
                        descricao
                    ) VALUES (
                        :titulo,
                        :descricao
                    );';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $success = $statement->execute([
                ':titulo' => $recomendacoes->getTitulo(),
                ':descricao' => $recomendacoes->getDescricao()
            ]);

            if (!$success) {
                return false;
            }
            $recomendacoes->setID((int) Database::getConnection()->lastInsertId());

            return $recomendacoes;
        }

        public function delete (int $id): bool {
            $query = 'DELETE FROM recomendacoes WHERE ID = :id';
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':id' => $id]);
            return $statement->rowCount() > 0;
        }
        
    }                      