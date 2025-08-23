<?php
require_once 'api/src/DAO/PlantaDAO.php';
require_once 'api/src/models/Plantas.php';
require_once 'api/src/http/Response.php';

    class PlantaControl{
        public function index(): never{
            $plantaDAO = new PlantaDAO();
            $resposta = $plantaDAO->readAll();

            (new Response(
                success: true,
                message: 'Plantas selecionadas com sucesso.',
                data: ['plantas' => $resposta],
                httpCode: 200
            ))->send();

            exit();
        }




        public function show(int $idPlanta): never
        {
            $plantaDAO = new PlantaDAO();
            $resposta = $plantaDAO->readById(plantaID: $idPlanta);

            if ($resposta === null) {
                (new Response(
                    success: false,
                    message: 'Planta não encontrada.',
                    httpCode: 404
                ))->send();
            } else {
                (new Response(
                    success: true,
                    message: 'Planta selecionada com sucesso.',
                    data: ['planta' => $resposta],
                    httpCode: 200
                ))->send();
            }
            
            exit();
        }




        public function edit(stdClass $stdPlanta): never
        {
            $plantaDAO = new PlantaDAO();
            $atual = $plantaDAO->readById($stdPlanta->Dados->IdPlanta);

            $planta = new Plantas();
            $planta
                ->setId($stdPlanta->Dados->IdPlanta)
                ->setNomeComum(isset($stdPlanta->Dados->Nome_comum) ? $stdPlanta->Dados->Nome_comum : $atual->getNomeComum())
                ->setNomeCientifico(isset($stdPlanta->Dados->Nome_cientifico) ? $stdPlanta->Dados->Nome_cientifico : $atual->getNomeCientifico())
                ->setTipo(isset($stdPlanta->Dados->Tipo) ? $stdPlanta->Dados->Tipo : $atual->getTipo())
                ->setClima(isset($stdPlanta->Dados->Clima) ? $stdPlanta->Dados->Clima : $atual->getClima())
                ->setRegiaoOrigem(isset($stdPlanta->Dados->Regiao_origem) ? $stdPlanta->Dados->Regiao_origem : $atual->getRegiaoOrigem())
                ->setLuminosidade(isset($stdPlanta->Dados->Luminosidade) ? $stdPlanta->Dados->Luminosidade : $atual->getLuminosidade())
                ->setFrequenciaRega(isset($stdPlanta->Dados->Frequencia_rega) ? $stdPlanta->Dados->Frequencia_rega : $atual->getFrequenciaRega())
                ->setUmidadeMin(isset($stdPlanta->Dados->Umidade_min) ? $stdPlanta->Dados->Umidade_min : $atual->getUmidadeMin())
                ->setUmidadeMax(isset($stdPlanta->Dados->Umidade_max) ? $stdPlanta->Dados->Umidade_max : $atual->getUmidadeMax())
                ->setDescricao(isset($stdPlanta->Dados->Descricao) ? $stdPlanta->Dados->Descricao : $atual->getDescricao());

            $atualizado = $plantaDAO->update($planta);
            if ($atualizado !== false) {
                (new Response(
                    success: true,
                    message: 'Planta atualizada com sucesso.',
                    data: ['Planta' => $atualizado],
                    httpCode: 200
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'Planta não atualizada.',
                    error: [
                        "code" => 'update_error',
                        "message" => 'Não foi possível atualizar a Planta.'
                    ],
                    httpCode: 400
                ))->send();
            }
            exit();
        }




        public function store(stdClass $stdPlanta): never
        {
            $planta = new Plantas();
            $planta
                ->setNomeCientifico(nomeCientifico: $stdPlanta->Dados->Nome_cientifico)
                ->setUmidadeMin(umidadeMin: $stdPlanta->Dados->Umidade_min)
                ->setUmidadeMax(umidadeMax: $stdPlanta->Dados->Umidade_max);
            if (isset($stdPlanta->Dados->Nome_comum)) {
                $planta->setNomeComum(nomeComum: $stdPlanta->Dados->Nome_comum);
            }
            if (isset($stdPlanta->Dados->Tipo)) {
                $planta->setTipo(tipo: $stdPlanta->Dados->Tipo);
            }
            if (isset($stdPlanta->Dados->Clima)) {
                $planta->setClima(clima: $stdPlanta->Dados->Clima);
            }
            if (isset($stdPlanta->Dados->Regiao_origem)) {
                $planta->setRegiaoOrigem(regiaoOrigem: $stdPlanta->Dados->Regiao_origem);
            }
            if (isset($stdPlanta->Dados->Luminosidade)) {
                $planta->setLuminosidade(luminosidade: $stdPlanta->Dados->Luminosidade);
            }
            if (isset($stdPlanta->Dados->Frequencia_rega)) {
                $planta->setFrequenciaRega(frequenciaRega: $stdPlanta->Dados->Frequencia_rega);
            }
            if (isset($stdPlanta->Dados->Descricao)) {
                $planta->setDescricao(descricao: $stdPlanta->Dados->Descricao);
            }

            $plantaDAO = new PlantaDAO();
            $nomePlanta = $plantaDAO->create($planta);
            (new Response(
                success: true,
                message: 'Planta criada com sucesso.',
                data: ['planta' => $nomePlanta],
                httpCode: 200
            ))->send();
            exit();
        }




        public function delete(int $idPlanta): never
        {
            $plantaDAO = new PlantaDAO();
            $success = $plantaDAO->delete($idPlanta);

            if ($success) {
                (new Response(
                    success: true,
                    message: 'Planta deletada com sucesso.',
                    data: null,
                    httpCode: 200
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'falha ao deletar planta.',
                    data: null,
                    httpCode: 404
                ))->send();
            }

            exit();
        }




        public function import(array $respostas): never   {
            
            $plantaDAO = new PlantaDAO();

            $plantasValidas = $respostas['plantasValidas'];

            $plantasCriadas = [];
            $plantasNaoCriadas = $respostas['plantasInvalidas'];

            foreach ($plantasValidas as $item) {
                $planta = new Plantas();
                $planta->setNomeComum($item['nomeComum'])
                        ->setNomeCientifico($item['nomeCientifico'])
                        ->setTipo($item['tipo'])
                        ->setClima($item['clima'])
                        ->setRegiaoOrigem($item['regiaoOrigem'])
                        ->setLuminosidade($item['luminosidade'])
                        ->setFrequenciaRega($item['frequenciaRega'])
                        ->setUmidadeMin($item['umidadeMin'])
                        ->setUmidadeMax($item['umidadeMax'])
                        ->setDescricao($item['descricao']);

                $criado = $plantaDAO->create($planta);

                if ($criado == false) {
                    $plantasNaoCriadas[] = $item;
                } else {
                    $plantasCriadas[] = ['ID_planta' => $criado->getID(),
                                            'nomeComum' => $criado->getNomeComum(),
                                            'nomeCientifico' => $criado->getNomeCientifico(),
                                            'tipo' => $criado->getTipo(),
                                            'clima' => $criado->getClima(),
                                            'regiaoOrigem' => $criado->getRegiaoOrigem(),
                                            'luminosidade' => $criado->getLuminosidade(),
                                            'frequenciaRega' => $criado->getFrequenciaRega(),
                                            'umidadeMin' => $criado->getUmidadeMin(),
                                            'umidadeMax' => $criado->getUmidadeMax(),
                                            'descricao' => $criado->getDescricao()];
                }
            }

            (new Response(
                success: true,
                message: 'Importação executada com sucesso.',
                data: [
                    'plantasCriadas' => $plantasCriadas,
                    'plantasNaoCriadas' => $plantasNaoCriadas,
                    'plantasDuplicadas' => $respostas['plantasDuplicadas']
                ],
                httpCode: 200
            ))->send();

            exit();

        } 




         public function exportCSV(): never {
            if (ob_get_length()) {
                ob_clean(); // limpa o que já foi enviado
            }
            ob_start(); //
            $plantaDAO = new PlantaDAO();
            $plantas = $plantaDAO->readAll();

            header(header: 'Content-Type: text/csv; charset=utf-8');
            header(header: 'Content-Disposition: attachment; filename="plantas.csv"');
            
            $saida = fopen(filename: 'php://output', mode: 'w'); //cria um ponteiro para escrita de um arquivo
            

            fputcsv(stream: $saida, fields: [
                'ID_planta',
                'nome_comum',
                'nome_cientifico',
                'tipo',
                'clima',
                'regiao_origem',
                'luminosidade',
                'frequencia_rega',
                'umidade_min',
                'umidade_max',
                'descricao'
            ]); 
            foreach ($plantas as $planta) {
                fputcsv(stream: $saida, fields: [
                    $planta["ID_planta"],
                    $planta["nome_comum"],
                    $planta["nome_cientifico"],
                    $planta["tipo"],
                    $planta["clima"],
                    $planta["regiao_origem"],
                    $planta["luminosidade"],
                    $planta["frequencia_rega"],
                    $planta["umidade_min"],
                    $planta["umidade_max"],
                    $planta["descricao"]
                ]);
            }
            fclose(stream: $saida); // fecha o ponteiro do arquivo.
            exit();
        }



        
        public function exportJSON(): never {
            if (ob_get_length()) {
                ob_clean(); // limpa o que já foi enviado
            }
            ob_start(); //
            $plantaDAO = new PlantaDAO();
            $plantas = $plantaDAO->readAll();

            header(header: 'Content-Type: application/json; charset=utf-8');
            header(header: 'Content-Disposition: attachment; filename="plantas.json"');

            $resposta = [
                'Dados' => $plantas
            ];
            
            echo json_encode(value: $resposta);
            exit();
        }
 



        public function exportXML(): never  {
            if (ob_get_length()) {
                ob_clean(); // limpa o que já foi enviado
            }
            ob_start(); //
            $plantaDAO = new PlantaDAO();
            $plantas = $plantaDAO->readAll();
            header(header: 'Content-Type: application/xml; charset=utf-8');
            header(header: 'Content-Disposition: attachment; filename="plantas.xml"');

            $xml = new SimpleXMLElement('<Dados/>');

            foreach ($plantas as $planta) {
                $plantaXML = $xml->addChild('Dado');
                $plantaXML->addChild('ID_planta', $planta['ID_planta']);
                $plantaXML->addChild('Nome_comum', $planta['nome_comum']);
                $plantaXML->addChild('Nome_cientifico', $planta['nome_cientifico']);
                $plantaXML->addChild('Tipo', $planta['tipo']);
                $plantaXML->addChild('Clima', $planta['clima']);
                $plantaXML->addChild('Regiao_origem', $planta['regiao_origem']);
                $plantaXML->addChild('Luminosidade', $planta['luminosidade']);
                $plantaXML->addChild('Frequencia_rega', $planta['frequencia_rega']);
                $plantaXML->addChild('Umidade_min', $planta['umidade_min']);
                $plantaXML->addChild('Umidade_max', $planta['umidade_max']);
                $plantaXML->addChild('Descricao', $planta['descricao']);
            }
            echo $xml->asXML();
        
            exit();
        }
    }