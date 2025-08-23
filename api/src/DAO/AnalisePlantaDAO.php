<?php
require_once 'api/src/db/Database.php';
require_once 'api/src/models/AnalisePlanta.php';
require_once "api/src/utils/Logger.php";

    class AnalisePlantaDAO{
       public function readAll(){
            $resultados = [];
            $query = 'SELECT 
                ID,
                ID_planta_usuario,
                data_analise,
                status_saude,
                status_umidade
                FROM analise_planta ORDER BY ID ASC';

            $statement =  Database::getConnection()->query(query: $query); // impedir sql injection
            $statement->execute();

            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);

            return $resultados;
        }
        public function readById(int $analiseID): AnalisePlanta|null
        {
            $query = 'SELECT 
                        ID,
                        ID_planta_usuario,
                        data_analise,
                        status_saude,
                        status_umidade
                    FROM analise_planta
                    WHERE ID = :analiseID;';
            
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':analiseID' => $analiseID]);

            $linha = $statement->fetch(PDO::FETCH_OBJ);

            if (!$linha) {
                return null;
            }

            return (new AnalisePlanta())
                ->setId((int)$linha->ID)
                ->setIdPlantaUsuario((int)$linha->ID_planta_usuario)
                ->setDataAnalise($linha->data_analise)
                ->setStatusSaude($linha->status_saude)
                ->setStatusUmidade($linha->status_umidade);
        }


        public function readByPlantaUsuario (int $idPlantaUsuario): array
        {
            $query = 'SELECT *
                    FROM analise_planta
                    WHERE ID_planta_usuario = :idPlantaUsuario;';
            
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':idPlantaUsuario' => $idPlantaUsuario]);

            $respostas = $statement->fetchAll(mode: PDO::FETCH_ASSOC);

            return $respostas;
        }


        public function readByIdUsuario(int $idUsuario): array {
            $query = 'SELECT * 
                FROM analise_planta 
                WHERE ID_planta_usuario IN (SELECT ID FROM planta_usuario WHERE IdUsuario = :idUsuario)
                ORDER BY data_analise asc;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute([':idUsuario' => (int)$idUsuario]);
            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $resultados;
        }

        public function readByIdAndUser (AnalisePlanta $analise, int $idUsuario): bool
        {
            $query = 'SELECT ID FROM planta_usuario WHERE ID = :idPlantaUsuario AND IdUsuario = :idUsuario';
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([
                ':idPlantaUsuario' => $analise->getIdPlantaUsuario(),
                ':idUsuario' => $idUsuario
            ]);
            if ($statement->rowCount() === 0) {
                return false;
            }
            return true;
        }
        public function create(AnalisePlanta $analise, int $idUsuario): AnalisePlanta|false{
            
            $query = 'INSERT INTO 
                    analise_planta (
                        ID_planta_usuario,
                        status_saude, 
                        status_umidade
                    ) 
                    VALUES (
                        :idPlantaUsuario,
                        :statusSaude, 
                        :statusUmidade
                    );';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $success = $statement->execute([
                ':idPlantaUsuario' => $analise->getIdPlantaUsuario(),
                ':statusSaude' => $analise->getStatusSaude(),
                ':statusUmidade' => $analise->getStatusUmidade()
            ]);

            if (!$success) {
                return false;
            }
            $analise->setId((int) Database::getConnection()->lastInsertId());

            return $analise;
        }

        public function delete(int $idAnalise): bool
        {
            $query = 'DELETE FROM analise_planta WHERE ID = :idAnalise';
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':idAnalise' => $idAnalise]);
            return $statement->rowCount() > 0;
        }

    }
?>