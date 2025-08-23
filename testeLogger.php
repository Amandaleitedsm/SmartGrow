<?php 
// vai reconhecer o erro quando tentar dividir por zero
// vai gravar o erro no arquivo de log
    require_once "api_2bim/utils/Logger.php";
    require_once "api_2bim/http/Response.php";
    try {
        $x = 1/0;
    } catch (Throwable $exception){
        Logger::log($exception);
    }
?>