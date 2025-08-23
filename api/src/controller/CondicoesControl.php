<?php
require_once 'api/src/DAO/CondicoesDAO.php';
require_once 'api/src/DAO/PlantaUsuarioDAO.php';
require_once 'api/src/http/Response.php';

    class CondicoesControl{
        public function index(): never{
            $condicoesDAO = new CondicoesDAO();
            $resposta = $condicoesDAO->readAll();

            (new Response(
                success: true,
                message: 'Condicoes selecionadas com sucesso.',
                data: ['condicoes' => $resposta],
                httpCode: 200
            ))->send();

            exit();
        }
        public function show(int $id): never
        {
            $condicoesDAO = new CondicoesDAO();
            $resposta = $condicoesDAO->readById($id);

            if ($resposta === null) {
                (new Response(
                    success: false,
                    message: 'Condição não encontrada.',
                    httpCode: 404
                ))->send();
            } else {
                (new Response(
                    success: true,
                    message: 'Condição selecionada com sucesso.',
                    data: ['condicao' => $resposta],
                    httpCode: 200
                ))->send();
            }
            
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
            $condicoesDAO = new CondicoesDAO();
            $condicoes = $condicoesDAO->readById($idPlantaUsuario);
            if ($condicoes === null) {
                (new Response(
                    success: false,
                    message: 'Condições não encontradas.',
                    httpCode: 404
                ))->send();
            } else {
                (new Response(
                    success: true,
                    message: 'Condições selecionadas com sucesso.',
                    data: ['condicoes' => $condicoes],
                    httpCode: 200
                ))->send();
            }

        }

        public function store(stdClass $stdCondicao, int $idUsuario): never
        {
            $plantaUsuarioDAO = new PlantaUsuarioDAO();
            $resposta = $plantaUsuarioDAO->readByPlantaUsuario($stdCondicao->Condicao->IdPlanta);

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
            $condicao = new CondicoesPlanta();
            $condicao
                ->setIDPlanta($stdCondicao->Condicao->IdPlanta)
                ->setUmidadeAtual($stdCondicao->Condicao->Umidade);

            $condicoesDAO = new CondicoesDAO();
            $dados = $condicoesDAO->create($condicao);
            (new Response(
                success: true,
                message: 'Condição criado com sucesso.',
                data: ['condicao' => $dados],
                httpCode: 200
            ))->send();
            exit();  
        }

        public function delete(int $id): never
        {
            $condicoesDAO = new CondicoesDAO();
            $success = $condicoesDAO->delete($id);

            if ($success) {
                (new Response(
                    success: true,
                    message: 'Condição deletada com sucesso.',
                    data: null,
                    httpCode: 200
                ))->send();
            } else {
                (new Response(
                    success: false,
                    message: 'falha ao deletar condição.',
                    data: null,
                    httpCode: 404
                ))->send();
            }

            exit();
        }
        
    }
?>