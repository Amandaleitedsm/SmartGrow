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
                $recomendacao =  $recomendacaoDAO->readByID($idRecomendacao->getIDRecomendacao());
                if ($recomendacao !== null){
                    (new Response(
                        success: true,
                        message: 'Recomendação para análise selecionada com sucesso.',
                        data: ['Recomendação' => $recomendacao],
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


        /*public function showAnalisesRec ($idUsuario): never {
            $analiseRecDAO = new AnaliseRecDAO();
            $Ids = $analiseRecDAO->readPlantaUsuarioByID($idUsuario);

            if ($Ids == null){
                (new Response(
                    success: false,
                    message: 'Não há plantas para este usuário.',
                    httpCode: 404
                ))->send();
                exit();
            }

            $analises = $analiseRecDAO->readAnaliseByPlantaUsuario($Ids);

            if ($analises == null){
                (new Response(
                    success: false,
                    message: 'Não há analises para as plantas deste usuário.',
                    httpCode: 404
                ))->send();
                exit();
            }

            $IdsRecomendacoes = $analiseRecDAO->readByIdsAnalise($analises);

            if ($IdsRecomendacoes == null){
                (new Response(
                    success: false,
                    message: 'Não há recomendações para as plantas deste usuário.',
                    httpCode: 404
                ))->send();
                exit();
            }

            $recomendacoes = $analiseRecDAO->
            if ($recomendacoes !== null){
                    (new Response(
                        success: true,
                        message: 'Recomendação para análise selecionada com sucesso.',
                        data: ['Recomendação' => $recomendacao],
                        httpCode: 200
                    ))->send();
                    exit();
                }

        }*/
    }