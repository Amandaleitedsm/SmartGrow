<?php

    class Roteaor {
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
                catch{}
                exit();
            });
            $this->router->post('/auth/login', function() {
                try{}
                catch{}
                exit();
            });
            $this->router->post('/auth/logout', function() {
                try{}
                catch{}
                exit();
            });
        }
        private function setUpUsuarios(): void {
            $this->router->get('/usuarios', function() {
                try{}
                catch{}
                exit();
            });
            $this->router->get('/usuarios/:id', function($id) {
                try{}
                catch{}
                exit();
            });
            $this->router->put('/usuarios/:id', function($id) {
                try{}
                catch{}
                exit();
            });
            $this->router->delete('/usuarios/:id', function($id) {
                try{}
                catch{}
                exit();
            });
        }
        private function setUpPlantas(): void {
            $this->router->get('/plantas', function() {
                try{}
                catch{}
                exit();
            });
            $this->router->get('/plantas/:id', function($id) {
                try{}
                catch{}
                exit();
            });
            $this->router->post('/plantas', function() {
                try{}
                catch{}
                exit();
            });
            $this->router->put('/plantas/:id', function($id) {
                try{}
                catch{}
                exit();
            });
            $this->router->delete('/plantas/:id', function($id) {
                try{}
                catch{}
                exit();
            });
        }
        private function setUpPlantasUsuarios(): void {
            $this->router->get('/plantas-usuarios', function() {
                try{}
                catch{}
                exit();
            });
            $this->router->get('/plantas-usuarios/:id', function($id) {
                try{}
                catch{}
                exit();
            });
            $this->router->post('/plantas-usuarios', function() {
                try{}
                catch{}
                exit();
            });
            $this->router->put('/plantas-usuarios/:id', function($id) {
                try{}
                catch{}
                exit();
            });
            $this->router->delete('/plantas-usuarios/:id', function($id) {
                try{}
                catch{}
                exit();
            });
        }
        private function setUpAnalisePlantas(): void {
            $this->router->get('/analises/:id', function($id) {
                try{}
                catch{}
                exit();
            });
            $this->router->get('/minhas-plantas/:id/analises', function($id) {
                try{}
                catch{}
                exit();
            });
            $this->router->post('/minhas-plantas/:id/analises', function() {
                try{}
                catch{}
                exit();
            });
            $this->router->delete('/analises/:id', function($id) {
                try{}
                catch{}
                exit();
            });
        }
        private function setUpRecomendacoes(): void {
            $this->router->get('/recomendacoes', function($id) {
                try{}
                catch{}
                exit();
            });
            $this->router->get('/analises/:id/recomendacoes', function($id) {
                try{}
                catch{}
                exit();
            });
        }
        private function setUpCondicoesAtuais(): void {
            $this->router->get('/minhas-plantas/:id/condicoes', function() {
                try{}
                catch{}
                exit();
            });
            $this->router->post('/minhas-plantas/:id/condicoes', function() {
                try{}
                catch{}
                exit();
            });
        }
    }