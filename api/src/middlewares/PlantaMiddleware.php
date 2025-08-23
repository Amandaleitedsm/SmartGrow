<?php
require_once 'api/src/http/Response.php';
require_once 'api/src/DAO/PlantaDAO.php';

    class PlantaMiddleware 
    {
        public function stringJsonToStdClass($requestBody): stdClass{
            $stdPlanta = json_decode(json: $requestBody);
            if (json_last_error() !== JSON_ERROR_NONE){
                (new Response(
                    success: false,
                    message: "Planta inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Json inválido.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            } else if (!isset($stdPlanta->Dados->Nome_cientifico)){
                (new Response(
                    success: false,
                    message: "Planta inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o nome científico da planta.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            } else if (!isset($stdPlanta->Dados->Umidade_min)) {
                (new Response(
                    success: false,
                    message: "Planta inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviada a umidade mínima da planta.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdPlanta->Dados->Umidade_max)){
                (new Response(
                    success: false,
                    message: "Planta inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviada a umidade máxima da planta.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            return $stdPlanta;
        }

        public function hasNotPlantaByName($nomePlanta, $modoImport = false): self|bool{
            $plantaDAO = new PlantaDAO();
            $planta = $plantaDAO->readByName(nomePlanta: $nomePlanta);
            if(!empty($planta)){
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Planta já cadastrada",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Já existe uma planta cadastrada com o nome informado.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($modoImport) return true;
        
            return $this;
        }

        public function isValidID($idPlanta): self
        {
            if(!isset($idPlanta))
            {
               (new Response(
                success: false,
                message: "ID da planta não foi informado.",
                error:[
                    "code" => 'planta_validation_error',
                    "message" => 'O ID da planta deve ser informado para a operação.'
                ],
                httpCode: 400
                ))->send();
                exit();
            }else if(!is_numeric($idPlanta) || ((int)$idPlanta) <= 0)
            {
                (new Response(
                    success: false,
                    message: "ID da planta inválido.",
                    error:[
                        "code" => 'planta_validation_error',
                        "message" => 'O ID da planta deve ser um número positivo.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }else{
               return $this; 
            } 
        }

        public function isValidNomeComum($nomeComum, $modoImport = false): self|bool
        {
            if (!is_string($nomeComum) || trim($nomeComum) === '') {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Nome comum inválido.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'O nome comum da planta deve ser uma string não vazia.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            } 
            if (mb_strlen($nomeComum) > 50) {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Nome comum muito longo.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'O nome comum da planta deve ter no máximo 50 caracteres.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($modoImport) return true;
            return $this;
        }

        public function isValidNomeCientifico($nomeCientifico, $modoImport = false): self|bool
        {
            if (!isset($nomeCientifico) || !is_string($nomeCientifico) || trim($nomeCientifico) === '') {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Nome científico inválido.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'O nome científico da planta deve ser uma string não vazia.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if (mb_strlen($nomeCientifico) > 150) {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Nome científico muito longo.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'O nome científico da planta deve ter no máximo 150 caracteres.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($modoImport) return true;
            return $this;
        }

        public function isValidTipo($tipo, $modoImport = false): self|bool
        {
            if (!is_string($tipo) || trim($tipo) === '') {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Tipo inválido.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'O tipo da planta deve ser uma string não vazia.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if (mb_strlen($tipo) > 50) {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Tipo muito longo.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'O tipo da planta deve ter no máximo 50 caracteres.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($modoImport) return true;
            return $this;
        }

        public function isValidClima($clima, $modoImport = false): self|bool
        {
            if (!is_string($clima) || trim($clima) === '') {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Clima inválido.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'O clima da planta deve ser uma string não vazia.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if (mb_strlen($clima) > 50) {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Clima muito longo.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'O clima da planta deve ter no máximo 50 caracteres.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($modoImport) return true;
            return $this;
        }

        public function isValidRegiaoOrigem($regiaoOrigem, $modoImport = false): self|bool
        {
            if (!is_string($regiaoOrigem) || trim($regiaoOrigem) === '') {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Região de origem inválida.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A região de origem da planta deve ser uma string não vazia.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if (mb_strlen($regiaoOrigem) > 100) {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Região de origem muito longa.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A região de origem da planta deve ter no máximo 100 caracteres.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($modoImport) return true;
            return $this;
        }

        public function isValidLuminosidade($luminosidade, $modoImport = false): self|bool
        {
            if (!is_string($luminosidade) || trim($luminosidade) === '') {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Luminosidade inválida.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A luminosidade da planta deve ser uma string não vazia.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if (mb_strlen($luminosidade) > 50) {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Luminosidade muito longa.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A luminosidade da planta deve ter no máximo 50 caracteres.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($modoImport) return true;
            return $this;
        }

        public function isValidFrequenciaRega($frequenciaRega, $modoImport = false): self|bool
        {
            if (!is_string($frequenciaRega) || trim($frequenciaRega) === '') {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Frequência de rega inválida.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A frequência de rega da planta deve ser uma string não vazia.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if (mb_strlen($frequenciaRega) > 50) {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Frequência de rega muito longa.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A frequência de rega da planta deve ter no máximo 50 caracteres.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($modoImport) return true;
            return $this;
        }

        public function isValidUmidadeMin($umidadeMin, $modoImport = false): self|bool
        {
            if (!isset($umidadeMin) || !is_numeric($umidadeMin)) {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Umidade mínima inválida.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A umidade mínima da planta deve ser um número.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($umidadeMin < 0 || $umidadeMin > 100) {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Umidade mínima fora do intervalo.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A umidade mínima da planta deve estar entre 0 e 100.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($modoImport) return true;
            return $this;
        }

        public function isValidUmidadeMax($umidadeMax, $modoImport = false): self|bool
        {
            if (!isset($umidadeMax) || !is_numeric($umidadeMax)) {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Umidade máxima inválida.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A umidade máxima da planta deve ser um número.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($umidadeMax < 0 || $umidadeMax > 100) {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Umidade máxima fora do intervalo.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A umidade máxima da planta deve estar entre 0 e 100.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if (isset($umidadeMin) && $umidadeMax < $umidadeMin) {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Umidade máxima menor que a mínima.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A umidade máxima da planta não pode ser menor que a umidade mínima.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($modoImport) return true;
            return $this;
        }

        public function isValidDescricao($descricao, $modoImport = false): self|bool
        {
            if (!is_string($descricao) || trim($descricao) === '') {
                if ($modoImport) return false;

                (new Response(
                    success: false,
                    message: "Descrição inválida.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A descrição da planta deve ser uma string não vazia.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if (mb_strlen($descricao) > 500) {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Descrição muito longa.",
                    error: [
                        "code" => 'planta_validation_error',
                        "message" => 'A descrição da planta deve ter no máximo 500 caracteres.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($modoImport) return true;
            return $this;
        }




        public function hasNotByNomeCSV(array $csvFile): array {
            $nomeTemporario = $csvFile['tmp_name'];

            $plantasValidas = [];      // Linhas do CSV que podem ser inseridas
            $plantasDuplicadas = [];   // Títulos já existentes no banco

            if (!is_uploaded_file($nomeTemporario)) {
                (new Response(
                    success: false,
                    message: 'Arquivo inválido.', 
                    httpCode: 400
                ))->send();
                exit(); 
            }
            
            $ponteiroArquivo = fopen($nomeTemporario, "r");
            if ($ponteiroArquivo === false) {
                (new Response(
                    success: false,
                    message: 'Não foi possível abrir o arquivo.',
                    httpCode: 500
                ))->send();
                exit(); 
            }

            while (($linhaArquivo = fgetcsv($ponteiroArquivo, 1000, ",")) !== false) {
                foreach ($linhaArquivo as &$campo) {
                    if (!mb_detect_encoding($campo, 'UTF-8', true)) {
                        $campo = mb_convert_encoding($campo, 'UTF-8', 'iSO-8859-1');
                    }
                }

                if (count($linhaArquivo) < 10) {
                    continue; // Ignora linhas mal formatadas
                }

                $nomeComum = (string)trim($linhaArquivo[0]);
                $nomeCientifico = (string)trim($linhaArquivo[1]);
                $tipo = (string)trim($linhaArquivo[2]);
                $clima = (string)trim($linhaArquivo[3]);
                $regiaoOrigem = (string)trim($linhaArquivo[4]);
                $luminosidade = (string)trim($linhaArquivo[5]);
                $frequenciaRega = (string)trim($linhaArquivo[6]);
                $umidadeMin = (float)trim($linhaArquivo[7]);
                $umidadeMax = (float)trim($linhaArquivo[8]);
                $descricao = (string)trim($linhaArquivo[9]);

                if(!($this->isValidClima($clima, true) || $this->isValidDescricao($descricao, true)|| $this->isValidFrequenciaRega($frequenciaRega, true)
                    || $this->isValidLuminosidade($luminosidade, true) || $this->isValidNomeCientifico($nomeCientifico, true)
                    || $this->isValidNomeComum($nomeComum, true) || $this->isValidRegiaoOrigem($regiaoOrigem, true) || $this->isValidTipo($tipo, true))
                    || $this->isValidUmidadeMin($umidadeMin, true) || $this->isValidUmidadeMax($umidadeMax, true)){
                        
                    $plantasInvalidas[] = ['plantas'=>$nomeCientifico,
                                        'motivo'=>"Erro de validação"];
                }
                
                

                $plantaDAO = new PlantaDAO();

                if (!$this->hasNotPlantaByName($nomeComum, true)) {
                    $plantasDuplicadas[] = $nomeComum;
                } else if (!$this->hasNotPlantaByName($nomeCientifico, true)) {
                    $plantasDuplicadas[] = $nomeCientifico;
                } else {
                    $plantasValidas[] = [
                        'nomeComum' => $nomeComum,
                        'nomeCientifico' => $nomeCientifico,
                        'tipo' => $tipo,
                        'clima' => $clima,
                        'regiaoOrigem' => $regiaoOrigem,
                        'luminosidade' => $luminosidade,
                        'frequenciaRega' => $frequenciaRega,
                        'umidadeMin' => $umidadeMin,
                        'umidadeMax' => $umidadeMax,
                        'descricao' => $descricao
                    ];
                }
            }

            if (empty($plantasValidas)){
                (new Response(
                    success: false,
                    message: 'Não há plantas válidas. Itens duplicados e/ou inválidos apenas',
                    data: ['plantasDuplicadas' => $plantasDuplicadas,
                            'plantasInvalidas' => $plantasInvalidas],
                    httpCode: 500
                ))->send();
                exit(); 
            }
            return [
                'plantasValidas' => $plantasValidas,
                'plantasDuplicadas' => $plantasDuplicadas,
                'plantasInvalidas' => $plantasInvalidas
            ];
            
        }




        public function hasNotByNomeJson(array $jsonFile): array {
            $nomeTemporario = $jsonFile['tmp_name'];
            $conteudoArquivo = file_get_contents($nomeTemporario);
            $dadosJson = json_decode($conteudoArquivo);

            if ($dadosJson === null) {
                (new Response(
                    success: false,
                    message: 'Erro ao decodificar o arquivo JSON',
                    httpCode: 400
                ))->send();
                exit();
            }

            if (!isset($dadosJson->Dados) || !is_array($dadosJson->Dados)) {
                (new Response(
                    success: false,
                    message: 'Formato do JSON inválido: "Dados" deve ser uma lista de objetos.',
                    httpCode: 400
                ))->send();
                exit();
            }

            $plantasValidas = [];
            $plantasDuplicadas = [];
            $plantasInvalidas = [];

            foreach ($dadosJson->Dados as $plantaNode) {
                if (!isset($plantaNode->Nome_cientifico) || !isset($plantaNode->Umidade_min) || !isset($plantaNode->Umidade_max)) {
                    continue; // ignora linhas incompletas
                }
                
                $nomeComum = (string)$plantaNode->Nome_comum;
                $nomeCientifico = (string)$plantaNode->Nome_cientifico;
                $tipo = (string)$plantaNode->Tipo;
                $clima = (string)$plantaNode->Clima;
                $regiaoOrigem = (string)$plantaNode->Regiao_origem;
                $luminosidade = (string)$plantaNode->Luminosidade;
                $frequenciaRega = (string)$plantaNode->Frequencia_rega;
                $umidadeMin = (float)$plantaNode->Umidade_min;
                $umidadeMax = (float)$plantaNode->Umidade_max;
                $descricao = (string)$plantaNode->Descricao;

                if(!($this->isValidClima($clima, true) || $this->isValidDescricao($descricao, true)|| $this->isValidFrequenciaRega($frequenciaRega, true)
                    || $this->isValidLuminosidade($luminosidade, true) || $this->isValidNomeCientifico($nomeCientifico, true)
                    || $this->isValidNomeComum($nomeComum, true) || $this->isValidRegiaoOrigem($regiaoOrigem, true) || $this->isValidTipo($tipo, true))
                    || $this->isValidUmidadeMin($umidadeMin, true) || $this->isValidUmidadeMax($umidadeMax, true)){
                        $plantasInvalidas[] = ['plantas'=>$nomeCientifico,
                                            'motivo'=>"Erro de validação"];
                    }

                $plantaDAO = new PlantaDAO();
                
                if (!$this->hasNotPlantaByName($nomeComum, true)) {
                    $plantasDuplicadas[] = $nomeComum;
                } else if (!$this->hasNotPlantaByName($nomeCientifico, true)) {
                    $plantasDuplicadas[] = $nomeCientifico;
                } else {
                    $plantasValidas[] = [
                        'nomeComum' => $nomeComum,
                        'nomeCientifico' => $nomeCientifico,
                        'tipo' => $tipo,
                        'clima' => $clima,
                        'regiaoOrigem' => $regiaoOrigem,
                        'luminosidade' => $luminosidade,
                        'frequenciaRega' => $frequenciaRega,
                        'umidadeMin' => $umidadeMin,
                        'umidadeMax' => $umidadeMax,
                        'descricao' => $descricao
                    ];
                }
            }

            if (empty($plantasValidas)){
                (new Response(
                    success: false,
                    message: 'Não há plantas válidas. Itens duplicados e/ou inválidos apenas',
                    data: ['plantasDuplicadas' => $plantasDuplicadas,
                            'plantasInvalidas' => $plantasInvalidas],
                    httpCode: 500
                ))->send();
                exit(); 
            }
            return [
                'plantasValidas' => $plantasValidas,
                'plantasDuplicadas' => $plantasDuplicadas,
                'plantasInvalidas' => $plantasInvalidas
            ];
            
        }



        public function hasNotByNomeXML(array $xmlFile): array {
            $nomeTemporario = $xmlFile['tmp_name'];
            $xml = simplexml_load_file(filename: $nomeTemporario);
            if (!$xml) {
                (new Response(
                    success: false,
                    message: 'Erro ao carregar o arquivo XML',
                    httpCode: 400
                ))->send();
                exit(); 
            }
            
            $plantasValidas = [];
            $plantasDuplicadas = [];

            foreach ($xml->Dado as $plantaNode) {
                if (!isset($plantaNode->Nome_cientifico) || !isset($plantaNode->Umidade_min) || !isset($plantaNode->Umidade_max)) {
                    continue; // ignora linhas incompletas
                }

                $nomeComum = (string)$plantaNode->Nome_comum;
                $nomeCientifico = (string)$plantaNode->Nome_cientifico;
                $tipo = (string)$plantaNode->Tipo;
                $clima = (string)$plantaNode->Clima;
                $regiaoOrigem = (string)$plantaNode->Regiao_origem;
                $luminosidade = (string)$plantaNode->Luminosidade;
                $frequenciaRega = (string)$plantaNode->Frequencia_rega;
                $umidadeMin = (float)$plantaNode->Umidade_min;
                $umidadeMax = (float)$plantaNode->Umidade_max;
                $descricao = (string)$plantaNode->Descricao;

                if(!($this->isValidClima($clima, true) || $this->isValidDescricao($descricao, true)|| $this->isValidFrequenciaRega($frequenciaRega, true)
                    || $this->isValidLuminosidade($luminosidade, true) || $this->isValidNomeCientifico($nomeCientifico, true)
                    || $this->isValidNomeComum($nomeComum, true) || $this->isValidRegiaoOrigem($regiaoOrigem, true) || $this->isValidTipo($tipo, true))
                    || $this->isValidUmidadeMin($umidadeMin, true) || $this->isValidUmidadeMax($umidadeMax, true)){
                        $plantasInvalidas[] = ['plantas'=>$nomeCientifico,
                                            'motivo'=>"Erro de validação"];
                    }
                      

                $plantaDAO = new PlantaDAO();
                
                if (!$this->hasNotPlantaByName($nomeComum, true)) {
                    $plantasDuplicadas[] = $nomeComum;
                } else if (!$this->hasNotPlantaByName($nomeCientifico, true)) {
                    $plantasDuplicadas[] = $nomeCientifico;
                } else {
                    $plantasValidas[] = [
                        'nomeComum' => $nomeComum,
                        'nomeCientifico' => $nomeCientifico,
                        'tipo' => $tipo,
                        'clima' => $clima,
                        'regiaoOrigem' => $regiaoOrigem,
                        'luminosidade' => $luminosidade,
                        'frequenciaRega' => $frequenciaRega,
                        'umidadeMin' => $umidadeMin,
                        'umidadeMax' => $umidadeMax,
                        'descricao' => $descricao
                    ];
                }
            }

            if (empty($plantasValidas)){
                (new Response(
                    success: false,
                    message: 'Não há plantas válidas. Itens duplicados e/ou inválidos apenas',
                    data: ['plantasDuplicadas' => $plantasDuplicadas,
                            'plantasInvalidas' => $plantasInvalidas],
                    httpCode: 500
                ))->send();
                exit(); 
            }
            return [
                'plantasValidas' => $plantasValidas,
                'plantasDuplicadas' => $plantasDuplicadas,
                'plantasInvalidas' => $plantasInvalidas
            ];
            
        }
    }
?>