<?php

require_once "api/src/routes/Router.php";
require_once "api/src/utils/Logger.php";
require_once "api/src/http/Response.php";
require_once 'api/vendor/autoload.php';

require_once "api/src/DAO/CadastroDAO.php";
require_once "api/src/DAO/TokenDAO.php";

require_once "api/src/controller/AnalisePlantaControl.php";
require_once "api/src/controller/CadastroControl.php";
require_once "api/src/controller/PlantaControl.php";
require_once "api/src/controller/LoginControl.php";
require_once "api/src/controller/PlantaUsuarioControl.php";
require_once "api/src/controller/RecomendacoesControl.php";
require_once "api/src/controller/AnaliseRecControl.php";
require_once "api/src/controller/CondicoesControl.php";
require_once "api/src/controller/UserController.php";

require_once "api/src/middlewares/analisePlantaMiddleware.php";
require_once "api/src/middlewares/PlantaMiddleware.php";
require_once "api/src/middlewares/LoginMiddleware.php";
require_once "api/src/middlewares/JWTmiddleware.php";
require_once "api/src/middlewares/CadastroMiddleware.php";
require_once "api/src/middlewares/PlantaUsuarioMiddleware.php";
require_once "api/src/middlewares/AnaliseRecMiddleware.php";
require_once "api/src/middlewares/RecomendacoesMiddleware.php";
require_once "api/src/middlewares/CondicoesMiddleware.php";




    class Roteador {
        public function __construct(private Router $router = new Router())
        {
            $this->setUpHeaders();
            $this->setUpAutenticacao();
            $this->setUpUsuarios();
            $this->setUpPlantas();
            $this->setUpPlantasUsuarios();
            $this->setUpAnalisePlantas();
            $this->setUpRecomendacoes();
            $this->setUpCondicoesAtuais();
            $this->setUp404Route();
            $this->setUpRelatorios();
        }




        #set up headers
        private function setUpHeaders(): void {
            // Set up CORS headers
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
        }




        #rotas de autenticação
        private function setUpAutenticacao(): void {
            $this->router->post('/auth/register', function() {
                try{
                    $requestBody = file_get_contents('php://input');

                    $cadastroMiddleware = new CadastroMiddleware();
                    $objStd = $cadastroMiddleware->stringJsonToStdClass($requestBody);

                    $cadastroMiddleware
                        ->isValidNome($objStd->usuario->Nome)
                        ->isValidEmail($objStd->usuario->Email)
                        ->isValidSenha($objStd->usuario->Senha);

                    (new CadastroControl())->store(stdCadastro: $objStd);
                    
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error during registration");
                }
                exit();
            });
            $this->router->post('/auth/login', function() {
                try{
                    $requestBody = file_get_contents('php://input');
                    $LoginMiddleware = new LoginMiddleware();
                    $stdLogin = $LoginMiddleware->stringJsonToStdClass($requestBody);

                    $LoginMiddleware
                        ->isValidEmail($stdLogin->usuario->Email)
                        ->isValidSenha($stdLogin->usuario->Senha);

                    $token = (new LoginControl())->autenticar($stdLogin);
                    (new CadastroControl())->IsActive((int)$id);
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error during login");
                }
                exit();
            });
            $this->router->post('/auth/logout', function() {
                try{
                    (new JWTMiddleware())->isValidToken(); 
                    $authorization = getallheaders()['Authorization'] ?? null;
                    $jwt = trim(str_replace('Bearer ', '', $authorization));
                    (new TokenDAO())->invalidarToken($jwt);
                    (new Response(true, "Logout realizado com sucesso."))->send();
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error during logout");
                }
                exit();
            });
            
            $this->router->post('/auth/send-code', function() {
                try {
                    // Chama o controller
                    (new AuthController())->sendCode();
                } catch (Throwable $exception) {
                    $this->handleError($exception, "Erro ao enviar código");
                }
                exit();
            });

            $this->router->put('/auth/change-password', function() {
                try {
                    $requestBody = file_get_contents('php://input');
                    $cadastroMiddleware = new CadastroMiddleware();
                    $stdCadastro = $cadastroMiddleware->stringJsonToStdClass($requestBody);
                    $cadastroMiddleware->isValidEmail($stdCadastro->Email);
                    $cadastroMiddleware->isValidSenha($stdCadastro->Senha); 
                    
                    (new CadastroControl())->changePassword($stdCadastro);
                } catch (Throwable $exception) {
                    $this->handleError($exception, "Erro ao enviar código");
                }
                exit();
            });
        }




        #rotas de usuários
        private function setUpUsuarios(): void {
            $this->router->get('/usuarios', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {    
                        if ($claims->public->Role === "admin") {
                            (new CadastroControl())->index();
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching users");
                }
                exit();
            });
            $this->router->get('/usuarios/(\d+)', function($id) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin" || $claims->private->IdUsuario == (int)$id) {
                            (new CadastroMiddleware())->isValidID((int)$id);
                            (new CadastroControl())->show((int)$id);
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching user");
                }
                exit();
            });
            $this->router->put('/usuarios/(\d+)', function($id) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->private->IdUsuario == (int)$id) {
                            $requestBody = file_get_contents('php://input');
                            $cadastroMiddleware = new CadastroMiddleware();
                            $stdCadastro = $cadastroMiddleware->stringJsonToStdClass($requestBody);
                            $cadastroMiddleware->isValidID((int)$id);

                            if (isset($stdCadastro->usuario->Nome)) {
                                $cadastroMiddleware->isValidNome($stdCadastro->usuario->Nome);
                            }
                            if (isset($stdCadastro->usuario->Email)) {
                                $cadastroMiddleware->isValidEmail($stdCadastro->usuario->Email);
                            }
                            if (isset($stdCadastro->usuario->Senha)) {
                                $cadastroMiddleware->isValidSenha($stdCadastro->usuario->Senha);
                            }
                            $stdCadastro->usuario->ID_usuario = (int)$id;
                            (new CadastroControl())->edit($stdCadastro);
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error updating user");
                }
                exit();
            });
            $this->router->put('/usuarios/active/(\d+)', function($id) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->private->IdUsuario == (int)$id || $claims->public->Role === "admin")  {
                            (new CadastroMiddleware())->isValidID((int)$id);

                            (new CadastroControl())->isActive((int)$id);
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error activating user");
                }
                exit();
            });
            $this->router->delete('/usuarios/(\d+)', function($id) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->private->IdUsuario == (int)$id) {
                            (new CadastroMiddleware())->isValidID((int)$id);

                            (new CadastroControl())->delete((int)$id);
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error deleting user");
                }
                exit();
            });
        }




        #rotas de plantas
        private function setUpPlantas(): void {
            $this->router->get('/plantas', function() {
                try{ 
                    $tokenMiddleware = new JWTMiddleware();
                    $payload= $tokenMiddleware->isValidToken();
                    if ($tokenMiddleware->verificarTokenAtivo()) {
                        (new PlantaControl())->index();
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching plants");
                }
                exit();
            });
            $this->router->get('/plantas/(\d+)', function($id) {
                try{
                    $tokenMiddleware = new JWTMiddleware();
                    $payload= $tokenMiddleware->isValidToken();
                    if ($tokenMiddleware->verificarTokenAtivo()) {
                        (new PlantaMiddleware())->isValidID((int)$id);
                        (new PlantaControl())->show((int)$id);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching plant");
                }
                exit();
            });
            $this->router->post('/plantas', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            $requestBody = file_get_contents('php://input');

                            $plantaMiddleware = new PlantaMiddleware();
                            $stdPlanta = $plantaMiddleware->stringJsonToStdClass($requestBody);

                            $plantaMiddleware
                                ->isValidNomeCientifico($stdPlanta->Dados->Nome_cientifico)
                                ->isValidUmidadeMin($stdPlanta->Dados->Umidade_min)
                                ->isValidUmidadeMax($stdPlanta->Dados->Umidade_max)
                                ->hasNotPlantaByName($stdPlanta->Dados->Nome_cientifico);

                            if (isset($stdPlanta->Dados->Nome_comum)) {
                                $plantaMiddleware->isValidNomeComum($stdPlanta->Dados->Nome_comum);
                                $plantaMiddleware->hasNotPlantaByName($stdPlanta->Dados->Nome_comum);
                            }
                            if (isset($stdPlanta->Dados->Tipo)) {
                                $plantaMiddleware->isValidTipo($stdPlanta->Dados->Tipo);
                            }
                            if (isset($stdPlanta->Dados->Clima)) {
                                $plantaMiddleware->isValidClima($stdPlanta->Dados->Clima);
                            }
                            if (isset($stdPlanta->Dados->Regiao_origem)) {
                                $plantaMiddleware->isValidRegiaoOrigem($stdPlanta->Dados->Regiao_origem);
                            }
                            if (isset($stdPlanta->Dados->Luminosidade)) {
                                $plantaMiddleware->isValidLuminosidade($stdPlanta->Dados->Luminosidade);
                            }
                            if (isset($stdPlanta->Dados->Frequencia_rega)) {
                                $plantaMiddleware->isValidFrequenciaRega($stdPlanta->Dados->Frequencia_rega);
                            }
                            if (isset($stdPlanta->Dados->Descricao)) {
                                $plantaMiddleware->isValidDescricao($stdPlanta->Dados->Descricao);
                            }
                            (new PlantaControl())->store($stdPlanta);
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error creating plant");
                }
                exit();
            });
            $this->router->post('/plantas/import/csv', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            $respostas = (new PlantaMiddleware())->hasNotByNomeCSV(csvFile: $_FILES['csv']);
                            (new PlantaControl())->import($respostas); 
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error importing CSV");
                }
                exit();
            });
            $this->router->post('/plantas/import/json', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            $respostas = (new PlantaMiddleware())->hasNotByNomeJson(jsonFile: $_FILES['json']);
                            (new PlantaControl())->import($respostas);
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error importing JSON");
                }
                exit();
            });
            $this->router->post('/plantas/import/xml', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            $respostas = (new PlantaMiddleware())->hasNotByNomeXML(xmlFile: $_FILES['xml']);
                            (new PlantaControl())->import($respostas);
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error importing XML");
                }
                exit();
            });
            $this->router->get('/plantas/export/csv', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            (new PlantaControl())->exportCSV(); // Processa e exporta os dados para CSV
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error exporting CSV");
                }
                exit();
            });
            $this->router->get('/plantas/export/json', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            (new PlantaControl())->exportJSON(); // Processa e exporta os dados para JSON
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error exporting JSON");
                }
                exit();
            });
            $this->router->get('/plantas/export/xml', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            (new PlantaControl())->exportXML(); // Processa e exporta os dados para XML
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error exporting XML");
                }
                exit();
            });
            $this->router->put('/plantas/(\d+)', function($id) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            $requestBody = file_get_contents('php://input');
                            $plantaMiddleware = new PlantaMiddleware();
                            $stdPlanta = $plantaMiddleware->stringJsonToStdClass($requestBody);

                            $plantaMiddleware->isValidID((int)$id);

                            if (isset($stdPlanta->Dados->Nome_comum)) {
                                $plantaMiddleware
                                    ->isValidNomeComum($stdPlanta->Dados->Nome_comum)
                                    ->hasNotPlantaByName($stdPlanta->Dados->Nome_comum);
                            }
                            if (isset($stdPlanta->Dados->Nome_cientifico)) {
                                $plantaMiddleware
                                    ->isValidNomeCientifico($stdPlanta->Dados->Nome_cientifico)
                                    ->hasNotPlantaByName($stdPlanta->Dados->Nome_cientifico);
                            }
                            if (isset($stdPlanta->Dados->Tipo)) {
                                $plantaMiddleware->isValidTipo($stdPlanta->Dados->Tipo);
                            }
                            if (isset($stdPlanta->Dados->Clima)) {
                                $plantaMiddleware->isValidClima($stdPlanta->Dados->Clima);
                            }
                            if (isset($stdPlanta->Dados->Regiao_origem)) {
                                $plantaMiddleware->isValidRegiaoOrigem($stdPlanta->Dados->Regiao_origem);
                            }
                            if (isset($stdPlanta->Dados->Luminosidade)) {
                                $plantaMiddleware->isValidLuminosidade($stdPlanta->Dados->Luminosidade);
                            }
                            if (isset($stdPlanta->Dados->Frequencia_rega)) {
                                $plantaMiddleware->isValidFrequenciaRega($stdPlanta->Dados->Frequencia_rega);
                            }
                            if (isset($stdPlanta->Dados->Umidade_min)) {
                                $plantaMiddleware->isValidUmidadeMin($stdPlanta->Dados->Umidade_min);
                            }
                            if (isset($stdPlanta->Dados->Umidade_max)) {
                                $plantaMiddleware->isValidUmidadeMax($stdPlanta->Dados->Umidade_max);
                            }
                            if (isset($stdPlanta->Dados->Descricao)) {
                                $plantaMiddleware->isValidDescricao($stdPlanta->Dados->Descricao);
                            }
                            $stdPlanta->Dados->IdPlanta = (int)$id;
                            (new PlantaControl())->edit($stdPlanta);
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error updating plant");
                }
                exit();
            });
            $this->router->delete('/plantas/(\d+)', function($id) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            (new PlantaMiddleware())->isValidID((int)$id);

                            (new PlantaControl())->delete((int)$id);
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error deleting plant");
                }
                exit();
            });
        }




        private function setUpPlantasUsuarios(): void {
            $this->router->get('/plantas-usuarios', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            (new PlantaUsuarioControl())->index();
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching plant-user relationships");
                }
                exit();
            });
            $this->router->get('/plantas-usuarios/(\d+)', function($id) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        $idUsuario = $claims->private->IdUsuario;
                        (new PlantaUsuarioMiddleware())->isValidID((int)$id);
                        (new PlantaUsuarioControl())->show((int)$id, $idUsuario);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching plant-user relationship");
                }
                exit();
            });
            $this->router->get('/plantas-usuarios/user/(\d+)', function($idUsuario) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->private->IdUsuario === (int)$idUsuario){
                            (new PlantaUsuarioMiddleware())->isValidIdUsuario($idUsuario);
                            (new PlantaUsuarioControl())->showByUser($idUsuario);
                        }else {
                            $this->unauthorizedResponse();
                            exit();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching plant-user relationship");
                }
                exit();
            });
            $this->router->get('/plantas-usuarios/plant/(\d+)', function($idPlanta) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        (new PlantaUsuarioMiddleware())->isValidIdPlanta($idPlanta);
                        $idUsuario = $claims->private->IdUsuario;
                        (new PlantaUsuarioControl())->showByPlant((int)$idPlanta, (int)$idUsuario);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching plant-user relationship");
                }
                exit();
            });
            $this->router->get('/plantas-usuarios/apelido/([a-zA-Z0-9_\-\s]+)', function($apelido) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        (new PlantaUsuarioMiddleware())->isValidApelido($apelido);
                        $idUsuario = $claims->private->IdUsuario;
                        (new PlantaUsuarioControl())->showByApelido($apelido, $idUsuario);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching plant-user relationship");
                }
                exit();
            });
            $this->router->get('/plantas-usuarios/location/([a-zA-Z0-9_\-\s]+)', function($localizacao) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        (new PlantaUsuarioMiddleware())->IsValidLocalizacao($localizacao);
                        $idUsuario = $claims->private->IdUsuario;
                        (new PlantaUsuarioControl())->showByLocalizacao($localizacao, $idUsuario);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching plant-user relationship");
                }
                exit();
            });
            $this->router->post('/plantas-usuarios', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        $id = $claims->private->IdUsuario;
                        $requestBody = file_get_contents('php://input');

                        $plantaUsuarioMiddleware = new PlantaUsuarioMiddleware();
                        
                        $stdPlanta = $plantaUsuarioMiddleware->stringJsonToStdClass($requestBody);
                        $stdPlanta->Dados->IdUsuario = (int)$id;

                        $plantaUsuarioMiddleware
                            ->isValidIdUsuario($stdPlanta->Dados->IdUsuario)
                            ->isValidIdPlanta($stdPlanta->Dados->IdPlanta)
                            ->isValidApelido($stdPlanta->Dados->Apelido)
                            ->isValidLocalizacao($stdPlanta->Dados->Localizacao)
                            ->hasNotPlantaByApelido($stdPlanta->Dados->Apelido, $claims->private->IdUsuario);

                        (new PlantaUsuarioControl())->store($stdPlanta);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error creating plant-user relationship");
                }
                exit();
            });
            $this->router->put('/plantas-usuarios/(\d+)', function($id) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        $requestBody = file_get_contents('php://input');
                        $plantaUsuarioMiddleware = new PlantaUsuarioMiddleware();
                        $stdPlanta = $plantaUsuarioMiddleware->stringJsonToStdClass($requestBody);
                        $stdPlanta->Dados->IdUsuario = $claims->private->IdUsuario;
                        $plantaUsuarioMiddleware->isValidId((int)$id);

                        if (isset($stdPlanta->Dados->Apelido)) {
                            $plantaUsuarioMiddleware
                                ->isValidApelido($stdPlanta->Dados->Apelido)
                                ->hasNotPlantaByApelido($stdPlanta->Dados->Apelido);
                        }
                        if (isset($stdPlanta->Dados->Localizacao)) {
                            $plantaUsuarioMiddleware->IsValidLocalizacao($stdPlanta->Dados->Localizacao);
                        }
                        $stdPlanta->Dados->IdPesquisa = (int)$id;

                        (new PlantaUsuarioControl())->edit($stdPlanta);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error updating plant-user relationship");
                }
                exit();
            });
            $this->router->delete('/plantas-usuarios/(\d+)', function($id) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        $idUsuario = $claims->private->IdUsuario;
                        (new PlantaUsuarioMiddleware())->isValidID((int)$id);
                        (new PlantaUsuarioControl())->delete((int)$id, (int)$idUsuario);

                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error deleting plant-user relationship");
                }
                exit();
            });
        }




        #rotas de análises de plantas
        private function setUpAnalisePlantas(): void {
            $this->router->get('/analises', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        $analisePlantaControl = new AnalisePlantaControl();
                        if ($claims->public->Role === "admin"){
                            $analisePlantaControl->index();
                        } else {
                            $idUsuario = $claims->private->IdUsuario;
                            (new AnalisePlantaMiddleware())
                                ->isValidIdUsuario((int)$idUsuario);

                            $analisePlantaControl->showByUser((int)$idUsuario);
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching user plant analyses");
                }
                exit();
            });
            $this->router->get('/analises/(\d+)', function($id) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        $idUsuario = $claims->private->IdUsuario;
                        (new AnalisePlantaMiddleware())->isValidID((int)$id);
                        $analisePlantaControl = new AnalisePlantaControl();

                        if ($claims->public->Role === "admin"){
                            $analisePlantaControl->showWithoutVerificacao((int)$id);
                        } else {
                            $analisePlantaControl->show((int)$id, (int)$idUsuario);
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching analysis");
                }
                exit();
            });
            $this->router->get('/minhas-plantas/analises', function() { #aqui envia o id planta usuario
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        $id = $claims->private->IdUsuario;
                            (new AnalisePlantaMiddleware())
                                ->isValidID((int)$id);

                            (new AnalisePlantaControl())
                                ->showByUser((int)$id);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching user plant analyses");
                }
                exit();
            });
            $this->router->get('/minhas-plantas/analises/(\d+)', function($id) { #aqui envia o id planta usuario
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        $idUsuario = $claims->private->IdUsuario;
                            (new AnalisePlantaMiddleware())
                                ->isValidID((int)$id);

                            (new AnalisePlantaControl())
                                ->showByPlantaUsuario($id, $idUsuario);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching user plant analyses");
                }
                exit();
            });
            $this->router->post('/minhas-plantas/analises/(\d+)', function($id) {
                try{
                    ### Aqui, deve ser alterada a lógica (obs os comments enumerados abaixo)
                    ### 1. Os dados Json não são mais os dados exigidos pela tabela, mas sim um objeto com as plantas que o usuario quer analisar
                    ### 1.1 exemplo: {"analise": { "plantasSelect": [1,2,3,...,n] } } ou {"analise": { "plantasSelect": [1] } } ou {"analise": { "plantasSelect": [2,4,5] } }
                    ### 2. Esse Json recebido será decodificado e enviado para a IA em python (o chat recomendou o uso de flask)
                    ### 2.1 A IA retornará um Json com os dados da análise cada planta analisada e as respectivas recomendações
                    ### 3. Esse Json retornado pela IA será decodificado e salvo no banco de dados (aqui sim, os dados serão os exigidos pela tabela)
                    ### 3.1 o requestBody do php://input é oq vai pra IA, esse pode ser sobrescrito posteriormente para continuar a logica do banco

                    ### ADENDO!!!!!!!! Como a IA se conecta com o código de umidade de eletrônica (para validar os dados de umidade), tentar conectar 
                    ### a IA com a rota de condições atuais (POST), para atualizar os dados de umidade em tempo ""real""
                    ### Se necssário, pode apagar a rota de condições atuais (POST) e deixar só a IA fazer isso, cortando mais linhas de código e evitando rotas não implementadas pelo HTML
                    ### Recomendo ver a lógica de script, evite mudar o css, apenas mexa com a lógica de redirecionamento e requisições
                    ### FIM DO ADENDO!!!!!!!!!
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        $requestBody = file_get_contents('php://input');

                        $analisePlantaMiddleware = new AnalisePlantaMiddleware();
                        $objStd = $analisePlantaMiddleware->stringJsonToStdClass($requestBody);
                        $objStd->analise->ID_planta_usuario = (int)$id;

                        $objStd->analise->IdUsuario = $claims->private->IdUsuario;
                        $analisePlantaMiddleware
                            ->isValidID((int)$objStd->analise->ID_planta_usuario)
                            ->isValidStatusSaude($objStd->analise->status_saude)
                            ->isValidStatusUmidade($objStd->analise->status_umidade);

                        (new AnalisePlantaControl())->store($objStd);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error creating user plant analysis");
                }
                exit();
            });
            $this->router->delete('/analises/(\d+)', function($id) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        (new AnalisePlantaMiddleware())->isValidID($id); 
                        $idUsuario = $claims->private->IdUsuario;
                        (new AnalisePlantaControl())->delete((int)$id, $idUsuario);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error deleting user plant analysis");
                }
                exit();
            });
        }




        private function setUpRecomendacoes(): void {
            $this->router->get('/recomendacoes', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin"){
                            (new RecomendacoesControl())->index();
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching recommendations");
                }
                exit();
            });
            $this->router->get('/analises/recomendacoes/(\d+)', function($id) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        $idUsuario = $claims->private->IdUsuario;
                        (new AnaliseRecMiddleware())
                            ->isValidIdAnalise((int)$id);

                        (new AnaliseRecControl())
                            ->show((int)$id, $idUsuario);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching analysis recommendations");
                }
                exit();
            });
            $this->router->post('/analises/recomendacoes/(\d+)', function($idUsuario) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo() && $claims->private->IdUsuario === (int)$idUsuario) {

                        $requestBody = file_get_contents('php://input');
                        $analiseRecMiddleware = new AnaliseRecMiddleware();
                        $objStd = $analiseRecMiddleware->stringJsonToStdClass($requestBody);

                        $analiseRecMiddleware
                            ->isValidIdAnalise((int)$objStd->Dados->IdAnalise)
                            ->isValidIdRecomendacao((int)$objStd->Dados->IdRecomendacao);

                        (new AnaliseRecControl())
                            ->store($objStd);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching analysis recommendations");
                }
                exit();
            });
            $this->router->post('/recomendacoes/import/csv', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            $respostas = (new RecomendacoesMiddleware())->hasNotByTituloCSV(csvFile: $_FILES['csv']);
                            (new RecomendacoesControl())->import($respostas); // Processa e importa os dados do arquivo CSV
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error importing CSV");
                }
                exit();
            });
            $this->router->post('/recomendacoes/import/json', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            $respostas = (new RecomendacoesMiddleware())->hasNotByTituloJson(jsonFile: $_FILES['json']);
                            (new RecomendacoesControl())->import($respostas); // Processa e importa os dados do arquivo JSON
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error importing JSON");
                }
                exit();
            });
            $this->router->post('/recomendacoes/import/xml', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            $respostas = (new RecomendacoesMiddleware())->hasNotByTituloXML(xmlFile: $_FILES['xml']);
                            (new RecomendacoesControl())->import($respostas); // Processa e importa os dados do arquivo JSON
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }

                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error importing XML");
                }
                exit();
            });
            $this->router->get('/recomendacoes/export/csv', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            (new RecomendacoesControl())->exportCSV(); // Processa e exporta os dados para CSV
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error exporting CSV");
                }
                exit();
            });
            $this->router->get('/recomendacoes/export/json', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            (new RecomendacoesControl())->exportJSON(); // Processa e exporta os dados para JSON
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }

                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error exporting JSON");
                }
                exit();
            });
            $this->router->get('/recomendacoes/export/xml', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin") {
                            (new RecomendacoesControl())->exportXML(); // Processa e exporta os dados para XML
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error exporting XML");
                }
                exit();
            });
            $this->router->post('/recomendacoes', function() {  
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin"){
                            $requestBody = file_get_contents('php://input');

                            $objStd = $recomendacoesMiddleware->stringJsonToStdClass($requestBody);

                            $objStd->Dados->IdUsuario = $claims->private->IdUsuario;
                            $recomendacoesMiddleware
                                ->isValidTitulo($objStd->Dados->Titulo)
                                ->hasNotByTitulo($objStd->Dados->Titulo)
                                ->isValidDescricao($objStd->Dados->Descricao);
                            (new RecomendacoesControl())->store($objStd);
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching analysis recommendations");
                }
                exit();
            });
            $this->router->delete('/recomendacoes/(\d+)', function($id) {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        if ($claims->public->Role === "admin"){
                            (new RecomendacoesMiddleware())->isValidID($id); 

                            (new RecomendacoesControl())->delete((int)$id);
                        } else {
                            $this->unauthorizedResponse();
                        }
                    } else {
                        $this->notValidToken();
                    }  
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error deleting user plant analysis");
                }
                exit();
            });
        }




        private function setUpCondicoesAtuais(): void {
            $this->router->get('/minhas-plantas/(\d+)/condicoes', function($id) { #envia o id planta usuario
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        $idUsuario = $claims->private->IdUsuario;
                        (new CondicoesMiddleware())->isValidIdPlanta((int)$id);
                        (new CondicoesControl())->showByPlantaUsuario($id, $idUsuario);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching current conditions");
                }
                exit();
            });
            $this->router->post('/minhas-plantas/condicoes', function() {
                try{
                    $jwtMiddleware = new JWTMiddleware();
                    $claims = $jwtMiddleware->isValidToken();
                    if ($jwtMiddleware->verificarTokenAtivo()) {
                        $requestBody = file_get_contents('php://input');
                        $idUsuario = $claims->private->IdUsuario;
                        $condicoesMiddleware = new CondicoesMiddleware();
                        $objStd = $condicoesMiddleware->stringJsonToStdClass($requestBody);

                        $condicoesMiddleware->isValidIdPlanta($objStd->Condicao->IdPlanta)
                                            ->isValidUmidade($objStd->Condicao->Umidade);
                        
                        (new CondicoesControl())->store($objStd, $idUsuario);
                    } else {
                        $this->notValidToken();
                    }
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error creating current conditions");
                }
                exit();
            });
        }




        private function setUpRelatorios(): void {
            $this->router->post('/relatorios', function($idUsuario) {
                try{
                    ### Tem que receber em JSON: Data de inicio, Data de fim, Array de plantas
                    ### Exemplo: { "dataInicio": "2023-01-01", "dataFim": "2023-12-31", "plantas": [1, 2, 3] }
                    ### Validar token e idUsuario
                    ### Alterar script se necessário (arquivo relatorios.php) para poder pegar IDS (não lembro como está a lógica)
                    ### Verificar necessidade de middleware para validar dados recebidos
                    ### Gerar relatório em PDF e enviar como resposta para download
                    ### O relatório deve buscar de análises, analisesXrecomendações e condicoes_planta (utilizando das demais tabelas para relacionar)
                    ### Existe a rota condicoes atuais (GET) para consulta, podendo, se necessário, ser apagada a rota de GET e incluída a lógica dentro dessa rota AQUI
                    ### O GET de condicoes não está sendo utilizado, por isso sua implementação para cortar código pode ser util
                    ### Não se deve chamar rota dentro de rota. Multiplos redirecionamentos, pode gerar problemas, não sei se é ideal.
                    ### Tentar, se possível, já fazer o HTML, similar ao de analises.php, porém, ao invés de "mais detalhes >", um botão retangular para download
                }
                catch (Throwable $exception) {
                    $this->handleError($exception, "Error fetching analysis recommendations");
                }
                exit();
            });
        }




        public function unauthorizedResponse(){
            (new Response(
                success: false,
                message: 'Você não possui autorização para executar a operação',
                error: ['codigoError' => 'validation_error', 'message' => 'Credencial de acesso inválida', ],
                httpCode: 401
            ))->send();
        }




        public function notValidToken(){
            (new Response(
                success: false,
                message: 'O token enviado está invalidado.',
                error: ['codigoError' => 'validation_error', 'message' => 'Credencial de acesso inválida', ],
                httpCode: 401
            ))->send();
        }




        private function handleError(Throwable $exception, $message): void {
            // Log the error
            Logger::log($exception);
            (new Response(
                success: false,
                message: $message,
                error: [
                    'problemCode' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ],
                httpCode: 500
            ))->send();
            exit();
        }



        
        private function setUp404Route(): void {
            $this->router->set404(match_fn: function(): void {
                (new Response(
                    success: false,
                    message: "Rota não encontrada.",
                    error: [
                        'problemCode' => 'routing_error',
                        'message' => "A rota solicitada não foi mapeada."
                    ],
                    httpCode: 404
                ))->send();
                exit();
            });
        }


        

        public function start(): void {
            // Start the router
            $this->router->run();
        }
    }