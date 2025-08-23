<?php
require_once 'api/src/DAO/RecomendacoesDAO.php';
require_once 'api/src/models/Recomendacoes.php';
require_once 'api/src/http/Response.php';

    class RecomendacoesControl{
        public function index(): never{
            $recomendacoesDAO = new RecomendacoesDAO();
            $resposta = $recomendacoesDAO->readAll();

            (new Response(
                success: true,
                message: 'Recomendações selecionadas com sucesso.',
                data: ['plantas' => $resposta],
                httpCode: 200
            ))->send();

            exit();
        }




        public function store(stdClass $stdRecomendacao): never
        {
            $recomendacaoModel = new Recomendacoes();
            $recomendacaoModel
                ->setTitulo($stdRecomendacao->Dados->Titulo)
                ->setDescricao($stdRecomendacao->Dados->Descricao);

            $recomendacoesDAO = new RecomendacoesDAO();
            $recomendacao = $recomendacoesDAO->create($recomendacaoModel);
            (new Response(
                success: true,
                message: 'Recomendação criada com sucesso.',
                data: ['recomendação' => $recomendacao],
                httpCode: 200
            ))->send();
            exit();
        }




        public function delete(int $id): never
        {
            $recomendacoesDAO = new RecomendacoesDAO();
            $success = $recomendacoesDAO->delete($id);

            if ($success) {
                (new Response(
                    success: true,
                    message: 'Recomendação deletada com sucesso.',
                    data: null,
                    httpCode: 200
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'falha ao deletar recomendação.',
                    data: null,
                    httpCode: 404
                ))->send();
            }

            exit();
        }





        public function import(array $respostas): never   {
            
            $recomendacoesDAO = new RecomendacoesDAO();

            $recomendacoesValidas = $respostas['recomendacoesValidas'];

            $recomendacoesCriadas = [];
            $recomendacoesNaoCriadas = [];

            foreach ($recomendacoesValidas as $item) {
                $recomendacao = new Recomendacoes();
                $recomendacao->setTitulo($item['titulo'])
                            ->setDescricao($item['descricao']);

                $criado = $recomendacoesDAO->create($recomendacao);

                if ($criado == false) {
                    $recomendacoesNaoCriadas[] = $item;
                } else {
                    $recomendacoesCriadas[] = ['ID' => $criado->getID(),
                                            'titulo' => $criado->getTitulo(),
                                            'descricao' => $criado->getDescricao()];
                }
            }

            (new Response(
                success: true,
                message: 'Importação executada com sucesso.',
                data: [
                    'recomendacoesCriadas' => $recomendacoesCriadas,
                    'recomendacoesNaoCriadas' => $recomendacoesNaoCriadas,
                    'recomendacoesDuplicadas' => $respostas['recomendacoesDuplicadas']
                ],
                httpCode: 200
            ))->send();

            exit();

        } 




        public function exportCSV(): never {
            if (ob_get_length()) {
                ob_clean(); // limpa o que já foi enviado
            }
            ob_start(); // começa novo buffer

            header(header: 'Content-Type: text/csv; charset=utf-8');
            header(header: 'Content-Disposition: attachment; filename="recomendacoes.csv"');
            $recomendacoesDAO = new RecomendacoesDAO();
            $recomendacao = $recomendacoesDAO->readAll();
            $saida = fopen(filename: 'php://output', mode: 'w'); //cria um ponteiro para escrita de um arquivo
            fputcsv(stream: $saida, fields: [
                'ID',
                'Titulo',
                'Descricao'
            ]); //escreve os cabeçalhos no arquivo 
            foreach ($recomendacao as $rec) {
                fputcsv(stream: $saida, fields: [
                    $rec['ID'],
                    $rec['titulo'],
                    $rec['descricao']
                ]);
            }
            fclose(stream: $saida); // fecha o ponteiro do arquivo.
            exit();
        }




        public function exportJSON(): never {
            $recomendacoesDAO = new RecomendacoesDAO();
            $recomendacao = $recomendacoesDAO->readAll();

            header(header: 'Content-Type: application/json; charset=utf-8');
            header(header: 'Content-Disposition: attachment; filename="recomendacoes.json"');

            $resposta = [
                'Dados' => $recomendacao
            ];
            
            echo json_encode(value: $resposta);
            exit();
        }

        
        
        
        public function exportXML(): never  {
            $recomendacoesDAO = new RecomendacoesDAO();
            $recomendacao = $recomendacoesDAO->readAll();

            header(header: 'Content-Type: application/xml; charset=utf-8');
            header(header: 'Content-Disposition: attachment; filename="recomendacoes.xml"');

            $xml = new SimpleXMLElement('<Dados/>');

            foreach ($recomendacao as $rec) {
                $recomendacaoXML = $xml->addChild('Dado');
                $recomendacaoXML->addChild('ID', $rec['ID']);
                $recomendacaoXML->addChild('Titulo', $rec['titulo']);
                $recomendacaoXML->addChild('Descricao', $rec['descricao']);;
            }
            echo $xml->asXML();
        
            exit();
        }
    }