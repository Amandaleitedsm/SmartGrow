<?php
require_once 'api/src/db/Database.php';
require_once 'api/src/models/PlantaUsuario.php';
require_once "api/src/utils/Logger.php";

    class PlantaUsuarioDAO{
       public function readAll(){
            $resultados = [];
            $query = 'SELECT 
                ID,
                IdUsuario,
                IdPlanta,
                apelido,
                localizacao
                FROM planta_usuario ORDER BY ID ASC';

            $statement =  Database::getConnection()->query(query: $query); // impedir sql injection
            $statement->execute();

            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);

            return $resultados;
        }
        public function readById(int $idPesquisa): PlantaUsuario|false
        {
            $query = 'SELECT 
                ID,
                IdUsuario,
                IdPlanta,
                apelido,
                localizacao
                FROM planta_usuario
                WHERE ID = :id';

            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':id' => $idPesquisa]);

            $linha = $statement->fetch(PDO::FETCH_OBJ);

            if (!$linha) {
                return false;
            }

            return (new PlantaUsuario())
                ->setId((int)$linha->ID)
                ->setIdUsuario((int)$linha->IdUsuario)
                ->setIdPlanta((int)$linha->IdPlanta)
                ->setApelido($linha->apelido)
                ->setLocalizacao($linha->localizacao);
        }

        public function readByIdUsuario(int $idUsuario): Array|null {
            $query = 'SELECT * 
                FROM planta_usuario
                WHERE IdUsuario = :idUsuario
                ORDER BY IdUsuario ASC;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute([':idUsuario' => $idUsuario]);
            $linha = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (!$linha) {
                return null;
            }

            return $linha;
        }

        public function readByIdPlanta(int $idPlanta, int $idUsuario): PlantaUsuario|null {
            $query = 'SELECT 
                ID,
                IdUsuario,
                IdPlanta,
                apelido,
                localizacao 
                FROM planta_usuario
                WHERE IdPlanta = :idPlanta AND IdUsuario = :idUsuario
                ORDER BY IdPlanta ASC;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute([':idPlanta' => $idPlanta, ':idUsuario' => $idUsuario]);
            $linha = $statement->fetch(PDO::FETCH_OBJ);

            if (!$linha) {
                return null;
            }

            return (new PlantaUsuario())
                ->setId((int)$linha->ID)
                ->setIdUsuario((int)$linha->IdUsuario)
                ->setIdPlanta((int)$linha->IdPlanta)
                ->setApelido($linha->apelido)
                ->setLocalizacao($linha->localizacao);
        }

        public function readByApelido(string $apelido, int $idUsuario): array {
            $query = 'SELECT * 
                FROM planta_usuario
                WHERE apelido = :apelido AND IdUsuario = :idUsuario
                ORDER BY apelido ASC;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute([':apelido' => $apelido, ':idUsuario' => $idUsuario]);
            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $resultados;
        }

        public function readByPlantaUsuario(int $idPlantaUsuario): PlantaUsuario|null {
            $query = 'SELECT IdUsuario
                    FROM planta_usuario
                    WHERE ID = :idPlantaUsuario;';

            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':idPlantaUsuario' => $idPlantaUsuario]);
            $resposta = $statement->fetch(PDO::FETCH_OBJ);

            if (!$resposta){
                return null;
            }
            return (new PlantaUsuario())->setIdUsuario((int)$resposta->IdUsuario);

        }

        public function readByLocalizacao(string $localizacao, int $idUsuario): array {
            $query = 'SELECT * 
                FROM planta_usuario
                WHERE localizacao = :localizacao AND IdUsuario = :idUsuario
                ORDER BY localizacao ASC;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute([':localizacao' => $localizacao, ':idUsuario' => $idUsuario]);
            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $resultados;
        }

        public function create(PlantaUsuario $plantaUsuario): PlantaUsuario|false{

            $query = 'INSERT INTO 
                    planta_usuario (
                        IdUsuario,
                        IdPlanta,
                        apelido,
                        localizacao
                    ) VALUES (
                        :idUsuario,
                        :idPlanta,
                        :apelido,
                        :localizacao
                    );';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $success = $statement->execute([
                ':idUsuario' => $plantaUsuario->getIdUsuario(),
                ':idPlanta' => $plantaUsuario->getIdPlanta(),
                ':apelido' => $plantaUsuario->getApelido(),
                ':localizacao' => $plantaUsuario->getLocalizacao()
            ]);

            if (!$success) {
                return false;
            }
            $plantaUsuario->setId((int) Database::getConnection()->lastInsertId());

            return $plantaUsuario;
        }

        public function delete(int $id): bool
        {

            $query = 'DELETE FROM planta_usuario WHERE ID = :id';
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':id' => $id]);
            return $statement->rowCount() > 0;
        }

        public function update(PlantaUsuario $plantaUsuario): PlantaUsuario|false {

            $query = 'UPDATE planta_usuario SET 
                        IdUsuario = :idUsuario, 
                        IdPlanta = :idPlanta,
                        apelido = :apelido,
                        localizacao = :localizacao
                        WHERE ID = :id;';

            $statement = Database::getConnection()->prepare(query: $query);
            $success = $statement->execute([
                ':id' => $plantaUsuario->getId(),
                ':idUsuario' => $plantaUsuario->getIdUsuario(),
                ':idPlanta' => $plantaUsuario->getIdPlanta(),
                ':apelido' => $plantaUsuario->getApelido(),
                ':localizacao' => $plantaUsuario->getLocalizacao()
            ]);

            if (!$success) {
                return false;
            }

            return $plantaUsuario;
        }
    }
?>
                        