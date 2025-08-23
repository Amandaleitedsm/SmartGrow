<?php
require_once 'api/src/db/Database.php';
require_once 'api/src/models/Plantas.php';
require_once "api/src/utils/Logger.php";

    class PlantaDAO{
       public function readAll(){
            $resultados = [];
            $query = 'SELECT 
                ID_planta,
                nome_comum,
                nome_cientifico,
                tipo,
                clima,
                regiao_origem,
                luminosidade,
                frequencia_rega,
                umidade_min,
                umidade_max,
                descricao
                FROM plantas ORDER BY ID_planta ASC';

            $statement =  Database::getConnection()->query(query: $query); // impedir sql injection
            $statement->execute();
            
            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);
            
            return $resultados;
        }
        public function readById(int $plantaID): Plantas|null
        {
            $query = 'SELECT 
                        ID_planta,
                        nome_comum,
                        nome_cientifico,
                        tipo,
                        clima,
                        regiao_origem,
                        luminosidade,
                        frequencia_rega,
                        umidade_min,
                        umidade_max,
                        descricao
                    FROM plantas
                    WHERE ID_planta = :plantaID;';

            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':plantaID' => $plantaID]);

            $linha = $statement->fetch(PDO::FETCH_OBJ);

            if (!$linha) {
                return null;
            }

            return (new Plantas())
                ->setId((int)$linha->ID_planta)
                ->setNomeComum($linha->nome_comum)
                ->setNomeCientifico($linha->nome_cientifico)
                ->setTipo($linha->tipo)
                ->setClima($linha->clima)
                ->setRegiaoOrigem($linha->regiao_origem)
                ->setLuminosidade($linha->luminosidade)
                ->setFrequenciaRega($linha->frequencia_rega)
                ->setUmidadeMin($linha->umidade_min)
                ->setUmidadeMax($linha->umidade_max)
                ->setDescricao($linha->descricao);
        }

        public function readByName(string $nomePlanta): array {
            $query = 'SELECT * 
                FROM plantas
                WHERE nome_comum = :nomePlanta
                OR nome_cientifico = :nomePlanta
                ORDER BY nome_comum ASC;';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $statement->execute([':nomePlanta' => $nomePlanta]);
            $resultados = $statement->fetchAll(mode: PDO::FETCH_ASSOC);
            return $resultados;
        }



        public function create(Plantas $planta): Plantas|false{

            $query = 'INSERT INTO 
                    plantas (
                        nome_comum,
                        nome_cientifico,
                        tipo,
                        clima,
                        regiao_origem,
                        luminosidade,
                        frequencia_rega,
                        umidade_min,
                        umidade_max,
                        descricao
                    ) VALUES (
                        :nomeComum,
                        :nomeCientifico,
                        :tipo,
                        :clima,
                        :regiaoOrigem,
                        :luminosidade,
                        :frequenciaRega,
                        :umidadeMin,
                        :umidadeMax,
                        :descricao
                    );';

            $statement =  Database::getConnection()->prepare(query: $query); // impedir sql injection
            $success = $statement->execute([
                ':nomeComum' => $planta->getNomeComum(),
                ':nomeCientifico' => $planta->getNomeCientifico(),
                ':tipo' => $planta->getTipo(),
                ':clima' => $planta->getClima(),
                ':regiaoOrigem' => $planta->getRegiaoOrigem(),
                ':luminosidade' => $planta->getLuminosidade(),
                ':frequenciaRega' => $planta->getFrequenciaRega(),
                ':umidadeMin' => $planta->getUmidadeMin(),
                ':umidadeMax' => $planta->getUmidadeMax(),
                ':descricao' => $planta->getDescricao()
            ]);

            if (!$success) {
                return false;
            }
            $planta->setId((int) Database::getConnection()->lastInsertId());

            return $planta;
        }

        public function delete(int $idPlanta): bool
        {
            $query = 'DELETE FROM plantas WHERE ID_planta = :idPlanta';
            $statement = Database::getConnection()->prepare(query: $query);
            $statement->execute([':idPlanta' => $idPlanta]);
            return $statement->rowCount() > 0;
        }

        public function update(Plantas $planta): Plantas|false {

            $query = 'UPDATE plantas SET 
                        nome_comum = :nomeComum, 
                        nome_cientifico = :nomeCientifico,
                        tipo = :tipo,
                        clima = :clima,
                        regiao_origem = :regiaoOrigem,
                        luminosidade = :luminosidade,
                        frequencia_rega = :frequenciaRega,
                        umidade_min = :umidadeMin,
                        umidade_max = :umidadeMax,
                        descricao = :descricao
                    WHERE ID_planta = :idPlanta;';

            $statement = Database::getConnection()->prepare(query: $query);
            $success = $statement->execute([
                ':idPlanta' => $planta->getId(),
                ':nomeComum' => $planta->getNomeComum(),
                ':nomeCientifico' => $planta->getNomeCientifico(),
                ':tipo' => $planta->getTipo(),
                ':clima' => $planta->getClima(),
                ':regiaoOrigem' => $planta->getRegiaoOrigem(),
                ':luminosidade' => $planta->getLuminosidade(),
                ':frequenciaRega' => $planta->getFrequenciaRega(),
                ':umidadeMin' => $planta->getUmidadeMin(),
                ':umidadeMax' => $planta->getUmidadeMax(),
                ':descricao' => $planta->getDescricao()
            ]);

            if (!$success) {
                return false;
            }

            return $planta;
        }
    }