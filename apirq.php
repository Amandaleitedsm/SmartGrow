    <?php 
        date_default_timezone_set('America/Sao_Paulo');
        require_once "api/src/routes/Roteador.php";
        (new Roteador())->start(); // Start the router
    ?>