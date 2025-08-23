<?php
require_once 'api/src/DAO/PlantaUsuarioDAO.php';
require_once 'api/src/http/Response.php';

    class PlantaUsuarioControl{
        public function index(): never{
            $plantaUsuarioDAO = new PlantaUsuarioDAO();
            $resposta = $plantaUsuarioDAO->readAll();

            (new Response(
                success: true,
                message: 'Plantas selecionadas com sucesso.',
                data: ['plantas' => $resposta],
                httpCode: 200
            ))->send();

            exit();
        }

        public function show(int $id, int $idUsuario): never
        {
            $plantaUsuarioDAO = new PlantaUsuarioDAO();
            $resposta = $plantaUsuarioDAO->readById(idPesquisa: $id);
            
            if (!$resposta) {
                (new Response(
                    success: false,
                    message: 'Planta não encontrada.',
                    httpCode: 404
                ))->send();
            } else if ($resposta->getIdUsuario() !== $idUsuario) {
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
             else {
                (new Response(
                    success: true,
                    message: 'Planta selecionada com sucesso.',
                    data: ['planta' => $resposta],
                    httpCode: 200
                ))->send();
            }
            
            exit();
        }

        public function showByUser(int $idUsuario): never
        {
            $plantaUsuarioDAO = new PlantaUsuarioDAO();
            $resposta = $plantaUsuarioDAO->readByIdUsuario(idUsuario: $idUsuario);

            if ($resposta === null) {
                (new Response(
                    success: false,
                    message: 'Plantas não encontradas.',
                    httpCode: 404
                ))->send();
            } else {
                (new Response(
                    success: true,
                    message: 'Plantas selecionadas com sucesso.',
                    data: ['plantas' => $resposta],
                    httpCode: 200
                ))->send();
            }
            
            exit();
        }

        public function showByPlant(int $idPlanta, int $idUsuario): never
        {
            $plantaUsuarioDAO = new PlantaUsuarioDAO();
            $resposta = $plantaUsuarioDAO->readByIdPlanta(idPlanta: $idPlanta, idUsuario: $idUsuario);

            if ($resposta === null) {
                (new Response(
                    success: false,
                    message: 'Planta não encontrada para este usuário.',
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

        public function showByApelido(string $apelido, int $idUsuario): never
        {
            $plantaUsuarioDAO = new PlantaUsuarioDAO();
            $resposta = $plantaUsuarioDAO->readByApelido(apelido: $apelido, idUsuario: $idUsuario);

            if (empty($resposta)) {
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

        public function showByLocalizacao(string $localizacao, int $idUsuario): never
        {
            $plantaUsuarioDAO = new PlantaUsuarioDAO();
            $resposta = $plantaUsuarioDAO->readByLocalizacao(localizacao: $localizacao, idUsuario: $idUsuario);

            if (empty($resposta)) {
                (new Response(
                    success: false,
                    message: 'Plantas não encontradas.',
                    httpCode: 404
                ))->send();
            } else {
                (new Response(
                    success: true,
                    message: 'Plantas selecionadas com sucesso.',
                    data: ['plantas' => $resposta],
                    httpCode: 200
                ))->send();
            }
            
            exit();
        }
        public function edit(stdClass $stdPlanta): never
        {
            $plantaUsuarioDAO = new PlantaUsuarioDAO();
            $atual = $plantaUsuarioDAO->readById($stdPlanta->Dados->IdPesquisa);
            if (!$atual){
                (new Response(
                    success: false,
                    message: 'Não há relações para o id informado',
                    error: [
                        "code" => 'search_error',
                        "message" => 'Não foi possível localizar a relação.'
                    ],
                    httpCode: 403
                ))->send();
                exit();
            }
            if ($atual->getIdUsuario() !== $stdPlanta->Dados->IdUsuario) {
                (new Response(
                    success: false,
                    message: 'Usuário não autorizado a editar esta planta.',
                    error: [
                        "code" => 'authorization_error',
                        "message" => 'Você não tem permissão para editar esta planta.'
                    ],
                    httpCode: 403
                ))->send();
                exit();
            }
            $planta = new PlantaUsuario();
            $planta
                ->setId($stdPlanta->Dados->IdPesquisa)
                ->setIdUsuario(isset($stdPlanta->Dados->IdUsuario) ? $stdPlanta->Dados->IdUsuario : $atual->getIdUsuario())
                ->setIdPlanta(isset($stdPlanta->Dados->IdPlanta) ? $stdPlanta->Dados->IdPlanta : $atual->getIdPlanta())
                ->setApelido(isset($stdPlanta->Dados->Apelido) ? $stdPlanta->Dados->Apelido : $atual->getApelido())
                ->setLocalizacao(isset($stdPlanta->Dados->Localizacao) ? $stdPlanta->Dados->Localizacao : $atual->getLocalizacao());

            $atualizado = $plantaUsuarioDAO->update($planta);
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
            $planta = new PlantaUsuario();
            $planta
                ->setIdUsuario($stdPlanta->Dados->IdUsuario)
                ->setIdPlanta($stdPlanta->Dados->IdPlanta)
                ->setApelido($stdPlanta->Dados->Apelido)
                ->setLocalizacao($stdPlanta->Dados->Localizacao);

            $plantaUsuarioDAO = new PlantaUsuarioDAO();
            $nomePlanta = $plantaUsuarioDAO->create($planta);
            (new Response(
                success: true,
                message: 'Planta criada com sucesso.',
                data: ['planta' => $nomePlanta],
                httpCode: 200
            ))->send();
            exit();
        }



        public function delete(int $id, int $idUsuario): never
        {
            $plantaUsuarioDAO = new PlantaUsuarioDAO();
            $atual = $plantaUsuarioDAO->readById(idPesquisa: $id);
            if (!$atual){
                (new Response(
                    success: false,
                    message: 'Não há relações para o id informado',
                    error: [
                        "code" => 'search_error',
                        "message" => 'Não foi possível localizar a relação.'
                    ],
                    httpCode: 403
                ))->send();
                exit();
            }
            if ($atual->getIdUsuario() !== $idUsuario) {
                (new Response(
                    success: false,
                    message: 'Usuário não autorizado a deletar esta planta.',
                    error: [
                        "code" => 'authorization_error',
                        "message" => 'Você não tem permissão para deletar esta planta.'
                    ],
                    httpCode: 403
                ))->send();
                exit();
            }
            $success = $plantaUsuarioDAO->delete($id);

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

        public function verificarPlantaPertenceAoUsuario(int $idPlantaUsuario, int $idUsuario): bool
        {
            $plantaDAO = new PlantaUsuarioDAO();
            $planta = $plantaDAO->readById($idPlantaUsuario);
            return $planta !== false && $planta->getIDUsuario() === $idUsuario;
        }

    }
?>