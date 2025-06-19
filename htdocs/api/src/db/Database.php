<?php 
// lidar com conexão ao banco de dados, backup e restauração
// estático, não precisa de instância
require_once "api/src/http/Response.php";
require_once "api/src/utils/Logger.php";

class Database
{
  private const HOST = '127.0.0.1';
  private const USER = 'root';
  private const PASSWORD = '';
  private const DATABASE = 'smartgrow_db';
  private const PORT = 3306;

  // suporta todos os caracteres Unicode (incluindo emojis) 
  private const CHARACTER_SET = 'utf8mb4';

  private static ?PDO $CONNECTION = null; // armazena a conexão PDO
  
  public static function getConnection(): PDO|null
    {
      // Verifica se a conexão já existe
      if (Database::$CONNECTION === null) {
        // Se não existir, estabelece uma nova conexão
        Database::connect();
      }

      // Retorna a conexão existente ou recém-criada
      return Database::$CONNECTION;
    }
  private static function connect(): PDO // retorna uma conexão
  {
    // Formata a string DSN (Data Source Name) com os parâmetros de conexão
    $dsn = sprintf(  // escreve em uma string formatada
      'mysql:host=%s;port=%d;dbname=%s;charset=%s',
      Database::HOST,
      Database::PORT,
      Database::DATABASE,
      Database::CHARACTER_SET
    );

    // Cria a instância PDO com os parâmetros de conexão
    Database::$CONNECTION = new PDO(
      dsn: $dsn,                          // String de conexão formatada
      username: Database::USER,           // Usuário do banco de dados
      password: Database::PASSWORD,       // Senha do banco de dados
      options: [                          // Opções de configuração
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lança exceções em erros, pegar uma try catch
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ // Retorna objetos por padrão, mais intuitivo
      ]
    );

    return Database::$CONNECTION; // Retorna a conexão PDO criada
  }

}
