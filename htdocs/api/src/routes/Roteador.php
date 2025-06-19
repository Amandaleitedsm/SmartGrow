<?php
require_once "api/src/routes/Router.php";
require_once "api/src/utils/Logger.php";
require_once "api/src/http/Response.php";

require_once "api/src/models/AnalisePlanta.php";
require_once "api/src/models/AnaliseRecomendacao.php";
require_once "api/src/models/CadastroUsuario.php";
require_once "api/src/models/CondicoesPlanta.php";
require_once "api/src/models/Plantas.php";
require_once "api/src/models/PlantaUsuario.php";
require_once "api/src/models/Recomendacoes.php";
require_once "api/src/models/UsuarioPlanta.php";


    class Roteador {
        public function __construct(Router $router = null)
        {
            $this->router = $router ?? new Router();
            $this->setUpHeaders();
            $this->setUpAutenticacao();
            $this->setUpUsuarios();
            $this->setUpPlantas();
            $this->setUpPlantasUsuarios();
            $this->setUpAnalisePlantas();
            $this->setUpRecomendacoes();
            $this->setUpCondicoesAtuais();
        }
        private function setUpHeaders(): void {
            // Set up CORS headers
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
        }
        private function setUpAutenticacao(): void {
            $this->router->post('/auth/register', function() {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error during registration");
                }
                exit();
            });
            $this->router->post('/auth/login', function() {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error during login");
                }
                exit();
            });
            $this->router->post('/auth/logout', function() {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error during logout");
                }
                exit();
            });
        }
        private function setUpUsuarios(): void {
            $this->router->get('/usuarios', function() {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error fetching users");
                }
                exit();
            });
            $this->router->get('/usuarios/:id', function($id) {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error fetching user");
                }
                exit();
            });
            $this->router->put('/usuarios/:id', function($id) {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error updating user");
                }
                exit();
            });
            $this->router->delete('/usuarios/:id', function($id) {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error deleting user");
                }
                exit();
            });
        }
        private function setUpPlantas(): void {
            $this->router->get('/plantas', function() {
                try{
                   $plantasMiddleware = new Planta();   
                }
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error fetching plants");
                }
                exit();
            });
            $this->router->get('/plantas/:id', function($id) {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error fetching plant");
                }
                exit();
            });
            $this->router->post('/plantas', function() {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error creating plant");
                }
                exit();
            });
            $this->router->put('/plantas/:id', function($id) {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error updating plant");
                }
                exit();
            });
            $this->router->delete('/plantas/:id', function($id) {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error deleting plant");
                }
                exit();
            });
        }
        private function setUpPlantasUsuarios(): void {
            $this->router->get('/plantas-usuarios', function() {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error fetching plant-user relationships");
                }
                exit();
            });
            $this->router->get('/plantas-usuarios/:id', function($id) {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error fetching plant-user relationship");
                }
                exit();
            });
            $this->router->post('/plantas-usuarios', function() {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error creating plant-user relationship");
                }
                exit();
            });
            $this->router->put('/plantas-usuarios/:id', function($id) {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error updating plant-user relationship");
                }
                exit();
            });
            $this->router->delete('/plantas-usuarios/:id', function($id) {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error deleting plant-user relationship");
                }
                exit();
            });
        }
        private function setUpAnalisePlantas(): void {
            $this->router->get('/analises/:id', function($id) {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error fetching analysis");
                }
                exit();
            });
            $this->router->get('/minhas-plantas/:id/analises', function($id) {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error fetching user plant analyses");
                }
                exit();
            });
            $this->router->post('/minhas-plantas/:id/analises', function() {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error creating user plant analysis");
                }
                exit();
            });
            $this->router->delete('/analises/:id', function($id) {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error deleting user plant analysis");
                }
                exit();
            });
        }
        private function setUpRecomendacoes(): void {
            $this->router->get('/recomendacoes', function($id) {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error fetching recommendations");
                }
                exit();
            });
            $this->router->get('/analises/:id/recomendacoes', function($id) {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error fetching analysis recommendations");
                }
                exit();
            });
        }
        private function setUpCondicoesAtuais(): void {
            $this->router->get('/minhas-plantas/:id/condicoes', function() {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error fetching current conditions");
                }
                exit();
            });
            $this->router->post('/minhas-plantas/:id/condicoes', function() {
                try{}
                catch (Throwable $exception) {
                    $this->handleError(exception: $exception, message: "Error creating current conditions");
                }
                exit();
            });
        }

        private function handleError(Throwable $exception, $message): void {
            // Log the error
            Logger::log(exception: $exception);
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
