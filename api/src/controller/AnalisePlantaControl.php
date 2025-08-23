<?php
require_once 'api/src/DAO/AnalisePlantaDAO.php';
require_once 'api/src/DAO/PlantaUsuarioDAO.php';
require_once 'api/src/controller/PlantaUsuarioControl.php';
require_once 'api/src/http/Response.php';

    class AnalisePlantaControl{
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
        public function showByUser(int $idUsuario): never{
            $analisePlantaDAO = new AnalisePlantaDAO();
            $resposta = $analisePlantaDAO->readByIdUsuario(idUsuario);

            (new Response(
                success: true,
                message: 'análises da planta selecionadas com sucesso.',
                data: ['análises' => $resposta],
                httpCode: 200
            ))->send();

            exit();
        }


        public function showByPlantaUsuario($idPlantaUsuario, int $idUsuario): never {
            $plantaUsuarioDAO = new PlantaUsuarioDAO();
            $resposta = $plantaUsuarioDAO->readByPlantaUsuario($idPlantaUsuario);

            if ($resposta === null){
                (new Response(
                    success: false,
                    message: 'Relação não encontrada.',
                    httpCode: 404
                ))->send();
                exit();
            }
            if ($resposta->getIdUsuario() !== $idUsuario){
                (new Response(
                    success: false,
                    message: 'Você não possui autorização para executar a operação',
                    error: ['codigoError' => 'validation_error', 'message' => 'Credencial de acesso inválida', ],
                    httpCode: 401
                ))->send();
                exit();
            }
            $analisePlantaDAO = new AnalisePlantaDAO();
            $analises = $analisePlantaDAO->readByPlantaUsuario($idPlantaUsuario);
            if ($analises === null) {
                (new Response(
                    success: false,
                    message: 'Analises não encontradas.',
                    httpCode: 404
                ))->send();
            } else {
                (new Response(
                    success: true,
                    message: 'Analises selecionadas com sucesso.',
                    data: ['analises' => $analises],
                    httpCode: 200
                ))->send();
            }

        }


        public function show(int $idAnalise, int $idUsuario): never
        {
            $analisePlantaDAO = new AnalisePlantaDAO();
            $resposta = $analisePlantaDAO->readById($idAnalise);
            $plantaControl = new PlantaUsuarioControl();
            if ($resposta === null) {
                (new Response(
                    success: false,
                    message: 'Análise de planta não encontrada.',
                    httpCode: 404
                ))->send();
            } else if (!$plantaControl->verificarPlantaPertenceAoUsuario($resposta->getIdPlantaUsuario(), $idUsuario)) {
                (new Response(
                    success: false,
                    message: 'Usuário não autorizado a acessar esta planta.',
                    error: [
                        "code" => 'authorization_error',
                        "message" => 'Você não tem permissão para acessar esta planta.'
                    ],
                    httpCode: 403
                ))->send();
            } else {
                (new Response(
                    success: true,
                    message: 'análise de planta selecionada com sucesso.',
                    data: ['análise' => $resposta],
                    httpCode: 200
                ))->send();
            }
            
            exit();
        }
        public function showWithoutVerificacao(int $idAnalise): never
        {
            $analisePlantaDAO = new AnalisePlantaDAO();
            $resposta = $analisePlantaDAO->readById($idAnalise);
            if ($resposta === null) {
                (new Response(
                    success: false,
                    message: 'Análise de planta não encontrada.',
                    httpCode: 404
                ))->send();
            } else {
                (new Response(
                    success: true,
                    message: 'análise de planta selecionada com sucesso.',
                    data: ['análise' => $resposta],
                    httpCode: 200
                ))->send();
            }
            
            exit();
        }

        public function store(stdClass $stdAnalise): never
        {
            $analise = new AnalisePlanta();
            $analise
                ->setIDPlantaUsuario($stdAnalise->analise->ID_planta_usuario)
                ->setStatusSaude($stdAnalise->analise->status_saude)
                ->setStatusUmidade(status_umidade: $stdAnalise->analise->status_umidade);
            $analisePlantaDAO = new AnalisePlantaDAO();

            $verificacao = $analisePlantaDAO->readByIdAndUser($analise, $stdAnalise->analise->IdUsuario);
            if ($verificacao == false) {
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
            } else {
                $nomeAnalise = $analisePlantaDAO->create($analise, stdAnalise->analise->IdUsuario);
                if ($nomeAnalise === false) {
                    (new Response(
                        success: false,
                        message: 'falha ao criar análise de planta.',
                        data: null,
                        httpCode: 400
                    ))->send();
                    exit();
                }
                (new Response(
                    success: true,
                    message: 'análise de planta criada com sucesso.',
                    data: ['análise' => $nomeAnalise],
                    httpCode: 200
                ))->send();
                exit();  
            }
        }

        public function delete(int $idAnalise, int $idUsuario): never
        {
            $analisePlantaDAO = new AnalisePlantaDAO();
            $select = $analisePlantaDAO->readById($idAnalise);
            if ($select == null){
                (new Response(
                    success: false,
                    message: 'Não há análise para o ID informado',
                    data: null,
                    httpCode: 404
                ))->send();
                exit();
            }
            $verificacao = $analisePlantaDAO->readByIdAndUser($select, $idUsuario);
            if ($verificacao == false) {
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
            $success = $analisePlantaDAO->delete($idAnalise);

            if ($success) {
                (new Response(
                    success: true,
                    message: 'análise de planta deletada com sucesso.',
                    data: null,
                    httpCode: 200
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'falha ao deletar análise de planta.',
                    data: null,
                    httpCode: 404
                ))->send();
            }

            exit();
        }

    }