<?php
require_once 'api/src/db/Database.php';
require_once "api/src/utils/Logger.php";

class TokenDAO {
    public function salvarToken($idUsuario, $token, $expiraEm) {
        
        $query = "INSERT INTO tokens_ativos 
                (ID_usuario, Token, Expira_em) 
                VALUES (:id, :token, :expira);";

        $statement = Database::getConnection()->prepare($query); // impedir sql injection
        $statement->execute([':id' => $idUsuario, ':token' => $token, ':expira' => $expiraEm]);
    }




    public function invalidarToken($token) {
        $query = "UPDATE tokens_ativos SET Valido = FALSE WHERE Token = :token";
        $statement =  Database::getConnection()->prepare($query); // impedir sql injection
        $statement->execute([':token' => $token]);
    }

}