<?php
require_once 'api/src/DAO/AnaliseRecDAO.php';
require_once 'api/src/DAO/RecomendacoesDAO.php';
require_once 'api/src/http/Response.php';

class AnaliseRecControl{
    public function index(): never{
        $analisePlantaDAO = new AnalisePlantaDAO();
        $resposta = $analisePlantaDAO->readAll();

        (new Response(
            success: true,
            message: 'análises da planta selecionadas com sucesso.',
            data: ['análises' => $resposta],
            httpCode: 200
        ))->send();

        exit();
    }

    public function show(int $idAnalise, int $idUsuario): never
    {
        $analiseRecDAO = new AnaliseRecDAO();
        $idPlantaUsuario = $analiseRecDAO->readAnaliseById($idAnalise);

        if ($idPlantaUsuario === null) {
            (new Response(
                success: false,
                message: 'Análise de planta não encontrada.',
                httpCode: 404
            ))->send();
            exit();
        }

        $idUsuarioPesquisa = $analiseRecDAO->readByPlantaUsuario($idPlantaUsuario->getIDPlantaUsuario());

        if ($idUsuarioPesquisa->getIdUsuario() === $idUsuario){
            $idRecomendacao = $analiseRecDAO->readById($idAnalise);
            if ($idRecomendacao === null) {
                (new Response(
                    success: false,
                    message: 'Associação entre análise e recomendação não encontrada.',
                    httpCode: 404
                ))->send();
                exit();
            }
            $recomendacaoDAO = new RecomendacoesDAO();
            $ids = [];
            if ($idRecomendacao !== null) {
                foreach ($idRecomendacao as $item) {
                    $ids[] = (int)$item['ID_recomendacao']; // pega só os números
                }
            }

            $recomendacao = $recomendacaoDAO->readByID($ids);
            if (!empty($recomendacao)){
                (new Response(
                    success: true,
                    message: 'Recomendação para análise selecionada com sucesso.',
                    data: ['Recomendações' => $recomendacao],
                    httpCode: 200
                ))->send();
                exit();
            } else {
                (new Response(
                    success: false,
                    message: 'Recomendação para análise não encontrada.',
                    data: null,
                    httpCode: 200
                ))->send();
                exit();
            }
            
        } else {
            (new Response(
                success: false,
                message: 'Usuário não autorizado a acessar esta planta.',
                error: [
                    "code" => 'authorization_error',
                    "message" => 'Você não tem permissão para acessar esta planta.'
                ],
                httpCode: 403
            ))->send();
            exit();
        }
    }

    public function store(stdClass $stdAnalise): never
    {
        $ar = new AnaliseRecomendacao();
        $ar
            ->setIDAnalise($stdAnalise->Dados->IdAnalise)
            ->setIDRecomendacao($stdAnalise->Dados->IdRecomendacao);

        $analiseRecDAO = new AnaliseRecDAO();
        $analise = $analiseRecDAO->create($ar);
        (new Response(
            success: true,
            message: 'Associação criada com sucesso.',
            data: ['planta' => $ar],
            httpCode: 200
        ))->send();
        exit();
    }

}