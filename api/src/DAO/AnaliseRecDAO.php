<?php
require_once 'api/src/db/Database.php';
require_once 'api/src/models/AnaliseRecomendacao.php';
require_once "api/src/utils/Logger.php";

    class AnaliseRecDAO{
       public function readAll(){
            $resultados = [];
            $query = 'SELECT *
                FROM recomendacoes ORDER BY ID ASC';

            $statement =  Database::getConnection()->query(query: $query); // impedir sql injection
            $statement->execute();

            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);

            return $resultados;
        }

        public function readById (int $idAnalise): array | null {
            $query = 'SELECT ID_recomendacao
                    FROM analiseXrecomendacao
                    WHERE ID_analise = :idAnalise;';
            
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':idAnalise' => $idAnalise]);

            $resultado = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (!$resultado) {
                return null;
            } else {
                return $resultado;
            }
            
        }

        /*public function readByIdsAnalise(array $idsAnalise): array | null {
            $resultados = [];

            if (empty($idsAnalise)) {
                return null;
            }

            $placeholders = [];
            $params = [];

            foreach ($idsAnalise as $index => $id) {
                $placeholder = ":id$index";
                $placeholders[] = $placeholder;
                $params[$placeholder] = $id;
            }

            $inClause = implode(', ', $placeholders);

            $query = "SELECT ID_recomendacao
                    FROM analiseXrecomendacao
                    WHERE ID_analise IN ($inClause);";
            
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute($params);

            $resultados = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                return null;
            } else {
                return $resultados;
            }
        }


        public function readPlantaUsuarioByID (int $idUsuario): array|null {
            $resultado = [];
            $query = 'SELECT ID
                    FROM planta_usuario
                    WHERE IdUsuario = :idUsuario;';
            
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':idUsuario' => $idAnaidUsuariolise]);

            $resultado = $statement->fetch(PDO::FETCH_OBJ);
            if (empty($resultado)) {
                return null;
            }
            return $resultado;
        }

        public function readAnaliseByPlantaUsuario(array $Ids): array |null
        {
            $resultados = [];

            if (empty($Ids)) {
                return null;
            }

            $placeholders = [];
            $params = [];

            foreach ($Ids as $index => $id) {
                $placeholder = ":id$index";
                $placeholders[] = $placeholder;
                $params[$placeholder] = $id;
            }

            $inClause = implode(', ', $placeholders);

            $query = "SELECT ID
                    FROM analise_planta
                    WHERE ID_planta_usuario IN ($inClause);";
            
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute($params);

            $resultados = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados)) {
                return null;
            } else {
                return $resultados;
            }
        }*/


        public function readAnaliseById(int $idAnalise): AnalisePlanta |null
        {
            $query = 'SELECT ID_planta_usuario
                    FROM analise_planta
                    WHERE ID = :idAnalise;';
            
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':idAnalise' => $idAnalise]);

            $resultado = $statement->fetch(PDO::FETCH_OBJ);
            if (!$resultado) {
                // evita tentar acessar propriedade de booleano
                return null; // ou trate como preferir
            }
            return (new AnalisePlanta())
                        ->setIdPlantaUsuario((int)$resultado->ID_planta_usuario);
        }

        public function readByPlantaUsuario(int $idPesquisa): PlantaUsuario|null
        {
            $query = 'SELECT IdUsuario
                FROM planta_usuario
                WHERE ID = :id';

            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':id' => $idPesquisa]);

            $resultado = $statement->fetch(PDO::FETCH_OBJ);
            if (!$resultado) {
                // evita tentar acessar propriedade de booleano
                return null; // ou trate como preferir
            }
            return (new PlantaUsuario())->setIdUsuario((int)$resultado->IdUsuario);
        }

        public function create($ar):bool{
            
            $query = 'INSERT INTO 
                    analiseXrecomendacao (
                        ID_analise,
                        ID_recomendacao
                    ) 
                    VALUES (
                        :ID_analise,
                        :ID_recomendacao
                    );';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $success = $statement->execute([
                ':ID_analise' => $ar->getIDAnalise(),
                ':ID_recomendacao' => $ar->getIDRecomendacao()
            ]);

            if (!$success) {
                return false;
            }

            return true;
        }
    }
?>