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

       public function readByID($idsRecomendacao) {
    if (!is_array($idsRecomendacao)) {
        $idsRecomendacao = [$idsRecomendacao]; // garante array
    }

    if (empty($idsRecomendacao)) {
        return [];
    }

    $placeholders = [];
    $params = [];

    // cria placeholders :id0, :id1, :id2...
    foreach ($idsRecomendacao as $index => $id) {
        $placeholder = ":id$index";
        $placeholders[] = $placeholder;
        $params[$placeholder] = $id;
    }

    $in = implode(',', $placeholders);
    $query = "SELECT * FROM recomendacoes WHERE ID IN ($in)";

    $stmt = Database::getConnection()->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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