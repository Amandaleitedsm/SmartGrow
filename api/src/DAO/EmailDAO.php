<?php
require_once 'api/src/db/Database.php';

class EmailDAO {
    public function saveCode(string $email, int $codigo): bool {
        $expiraEm = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        $sql = "INSERT INTO codigos_verificacao (email, codigo, expira_em) VALUES (:email, :codigo, :expira_em)";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':expira_em', $expiraEm);
        return $stmt->execute();
    }
}
