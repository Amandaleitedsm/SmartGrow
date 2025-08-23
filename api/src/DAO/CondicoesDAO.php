<?php
require_once 'api/src/db/Database.php';
require_once 'api/src/models/CondicoesPlanta.php';
require_once "api/src/utils/Logger.php";

    class CondicoesDAO{
       public function readAll(){
            $resultados = [];
            $query = 'SELECT *
                FROM condicoes_planta ORDER BY ID ASC';

            $statement =  Database::getConnection()->query(query: $query); // impedir sql injection
            $statement->execute();

            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);

            return $resultados;
        }


        public function readById (int $idPlanta): array {
            $query = 'SELECT * 
                FROM condicoes_planta
                WHERE ID_planta = :idPlanta
                ORDER BY data_registro ASC;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute([':idPlanta' => $idPlanta]);
            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $resultados;
        }



        public function create(CondicoesPlanta $condicoesPlanta): CondicoesPlanta|false{
            $agora = new DateTime();
            $condicoesPlanta->setDataRegistro($agora);
            $query = 'INSERT INTO 
                    condicoes_planta (
                        ID_planta,
                        data_registro,
                        umidade_atual
                    ) VALUES (
                        :idPlanta,
                        :dataRegistro,
                        :umidadeAtual
                    );';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $success = $statement->execute([
                ':idPlanta' => $condicoesPlanta->getIDPlanta(),
                ':dataRegistro' => $condicoesPlanta->getDataRegistro()->format('Y-m-d H:i:s'),
                ':umidadeAtual' => $condicoesPlanta->getUmidadeAtual()
            ]);

            if (!$success) {
                return false;
            }
            $condicoesPlanta->setID((int) Database::getConnection()->lastInsertId());

            return $condicoesPlanta;
        }

        public function delete(int $id): bool
        {
            $query = 'DELETE FROM condicoes_panta WHERE ID = :id';
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':id' => $id]);
            return $statement->rowCount() > 0;
        }

    }
?>