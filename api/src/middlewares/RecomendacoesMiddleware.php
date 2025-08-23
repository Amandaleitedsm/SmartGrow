<?php
require_once 'api/src/http/Response.php';
require_once 'api/src/DAO/RecomendacoesDAO.php';

    class RecomendacoesMiddleware 
    {
        public function stringJsonToStdClass($requestBody): stdClass{
            $stdRecomendacao = json_decode(json: $requestBody);
            if (json_last_error() !== JSON_ERROR_NONE){
                (new Response(
                    success: false,
                    message: "Recomendação inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Json inválido.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            
            if (!isset($stdRecomendacao->Dados->Titulo)){
                (new Response(
                    success: false,
                    message: "Recomendação inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviado o título da recomendação.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            else if (!isset($stdRecomendacao->Dados->Descricao)){
                (new Response(
                    success: false,
                    message: "Recomendação inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'Não foi enviada a descrição da recomendação.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            
            return $stdRecomendacao;
        }




        public function hasNotByTitulo($titulo, $modoImport = false): self|bool {
            $recomendacaoDAO = new RecomendacoesDAO();
            $recomendacao = $recomendacaoDAO->readByTitulo(titulo: $titulo);

            if (!empty($recomendacao)) {
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Recomendação já cadastrada",
                    error: [
                        "code" => 'validation_error',
                        "message" => 'Já existe uma recomendação cadastrada com o título informado.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($modoImport) return true;
            return $this;
        }




        public function hasNotByTituloCSV(array $csvFile): array {
            $nomeTemporario = $csvFile['tmp_name'];
            $recomendacoesInvalidas = [];
            $recomendacoesValidas = [];      // Linhas do CSV que podem ser inseridas
            $recomendacoesDuplicadas = [];   // Títulos já existentes no banco

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
                        $campo = mb_convert_encoding($campo, 'UTF-8', 'ISO-8859-1');
                    }
                }

                if (count($linhaArquivo) !== 2) {
                    continue; // Ignora linhas mal formatadas
                }

                $titulo = (string)trim($linhaArquivo[0]);
                $descricao = (string)trim($linhaArquivo[1]);

                if (!($this->isValidTitulo($titulo, true) || $this->isValidDescricao($descricao, true))){
                    $recomendacoesInvalidas[] = ['recomendacao'=>$nomeCientifico,
                                        'motivo'=>"Erro de validação"];
                }
                $recomendacaoDAO = new RecomendacoesDAO();

                if (!($this->hasNotByTitulo($titulo, true))) {
                    $recomendacoesDuplicadas[] = $titulo;
                } else {
                    $recomendacoesValidas[] = [
                        'titulo' => $titulo,
                        'descricao' => $descricao
                    ];
                }
            }

            if (empty($recomendacoesValidas)) {
                (new Response(
                    success: false,
                    message: 'Não há recomendações válidas. Itens duplicados e/ou inválidos apenas.',
                    data: ['recomendacoesDuplicadas' => $recomendacoesDuplicadas,
                        'recomendacoesInvalidas' => $recomendacoesInvalidas],
                    httpCode: 500
                ))->send();
                exit(); 
            }

            return [
                'recomendacoesValidas' => $recomendacoesValidas,
                'recomendacoesDuplicadas' => $recomendacoesDuplicadas,
                'recomendacoesInvalidas' => $recomendacoesInvalidas
            ];
        }




        public function hasNotByTituloJson(array $jsonFile): array {
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
            $recomendacoesInvalidas = [];
            $recomendacoesValidas = [];
            $recomendacoesDuplicadas = [];

            foreach ($dadosJson->Dados as $plantaNode) {
                if (!isset($plantaNode->Titulo) || !isset($plantaNode->Descricao)) {
                    continue; // ignora linhas incompletas
                }

                $titulo = (string)$plantaNode->Titulo;
                $descricao = (string)$plantaNode->Descricao;

                if (!($this->isValidTitulo($titulo, true) || $this->isValidDescricao($descricao, true))){
                    $recomendacoesInvalidas[] = ['recomendacao'=>$nomeCientifico,
                                        'motivo'=>"Erro de validação"];
                }
                $recomendacaoDAO = new RecomendacoesDAO();

                if (!($this->hasNotByTitulo($titulo, true))) {
                    $recomendacoesDuplicadas[] = $titulo;
                } else {
                    $recomendacoesValidas[] = [
                        'titulo' => $titulo,
                        'descricao' => $descricao
                    ];
                }
            }

            if (empty($recomendacoesValidas)) {
                (new Response(
                    success: false,
                    message: 'Não há recomendações válidas. Itens duplicados e/ou inválidos apenas.',
                    data: ['recomendacoesDuplicadas' => $recomendacoesDuplicadas,
                        'recomendacoesInvalidas' => $recomendacoesInvalidas],
                    httpCode: 500
                ))->send();
                exit(); 
            }

            return [
                'recomendacoesValidas' => $recomendacoesValidas,
                'recomendacoesDuplicadas' => $recomendacoesDuplicadas,
                'recomendacoesInvalidas' => $recomendacoesInvalidas
            ];
        }



        public function hasNotByTituloXML(array $xmlFile): array {
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
            
            $recomendacoesInvalidas = [];
            $recomendacoesValidas = [];
            $recomendacoesDuplicadas = []; 

            foreach ($xml->Dado as $plantaNode) {
                if (!isset($plantaNode->Titulo) || !isset($plantaNode->Descricao)) {
                    continue; // ignora linhas incompletas
                }

                $titulo = (string) $plantaNode->Titulo;
                $descricao = (string) $plantaNode->Descricao;

                if (!($this->isValidTitulo($titulo, true) || $this->isValidDescricao($descricao, true))){
                    $recomendacoesInvalidas[] = ['recomendacao'=>$nomeCientifico,
                                        'motivo'=>"Erro de validação"];
                }
                $recomendacaoDAO = new RecomendacoesDAO();

                if (!($this->hasNotByTitulo($titulo, true))) {
                    $recomendacoesDuplicadas[] = $titulo;
                } else {
                    $recomendacoesValidas[] = [
                        'titulo' => $titulo,
                        'descricao' => $descricao
                    ];
                }
            }

            if (empty($recomendacoesValidas)) {
                (new Response(
                    success: false,
                    message: 'Não há recomendações válidas. Itens duplicados e/ou inválidos apenas.',
                    data: ['recomendacoesDuplicadas' => $recomendacoesDuplicadas,
                        'recomendacoesInvalidas' => $recomendacoesInvalidas],
                    httpCode: 500
                ))->send();
                exit(); 
            }

            return [
                'recomendacoesValidas' => $recomendacoesValidas,
                'recomendacoesDuplicadas' => $recomendacoesDuplicadas,
                'recomendacoesInvalidas' => $recomendacoesInvalidas
            ];
        }




        public function isValidID ($id): self {
            if (!is_numeric($id)){
                (new Response(
                    success: false,
                    message: "Recomendação inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'O id deve ser numérico.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            } else if ($id <= 0){
                (new Response(
                    success: false,
                    message: "Recomendação inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'O id deve ser um número maior que zero.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            return $this;
        }

        public function isValidTitulo ($titulo, $modoImport = false): self|bool {
            if (strlen($titulo) < 5 || strlen($titulo) > 100){
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Recomendação inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'O título deve possuir mais de 5 caracteres e menos que 100.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($modoImport) return true;
            return $this;
        }

        public function isValidDescricao ($descricao): self|bool {
            if (strlen($descricao) < 20 || strlen($descricao) > 500){
                if ($modoImport) return false;
                (new Response(
                    success: false,
                    message: "Recomendação inválida",
                    error:[
                        "code" => 'validation_error',
                        "message" => 'A descricao deve possuir mais de 20 caracteres e menos que 500.'
                    ],
                    httpCode: 400
                ))->send();
                exit();
            }
            if ($modoImport) return true;
            return $this;
        }

        
    }
?>