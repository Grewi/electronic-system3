<?php

declare(strict_types=1);

namespace system\core\database;

use PDO;
use PDOException;
use PDOStatement;

class maryadb
{
    private ?PDO $connection = null;
    
    public function __construct(
        private string $host,
        private string $dbname,
        private string $username,
        private string $password,
        private string $charset = 'utf8mb4',
        private array $options = []
    ) {
        $this->connect();
    }
    
    /**
     * Устанавливает соединение с MariaDB
     * 
     * @throws PDOException Если соединение не удалось
     */
    private function connect(): void
    {
        // Используем префикс "mysql:" так как MariaDB совместима с MySQL
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=' . $this->charset;
        
        // Оптимальные настройки для MariaDB
        $defaultOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            // Особые настройки для MariaDB
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET time_zone = "+00:00", sql_mode = "STRICT_TRANS_TABLES"',
        ];
        
        $options = array_replace($defaultOptions, $this->options);
        
        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
            // Установка дополнительных параметров после подключения
            $this->connection->exec('SET NAMES "' . $this->charset . '"');
            $this->connection->exec('SET CHARACTER SET "' . $this->charset . '"');
            
        } catch (PDOException $e) {
            throw new PDOException('MariaDB connection failed: ' . $e->getMessage(), (int)$e->getCode());
        }
    }
    
    /**
     * Выполняет SQL запрос с параметрами
     */
    public function query(string $sql, array $params = [], string $className = 'stdClass'): PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->setFetchMode($this->connection::FETCH_CLASS, $className);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Выполняет запрос и возвращает одну строку результата
     */
    public function fetch(string $sql, array $params = [], string $className = 'stdClass'): ?object
    {
        return $this->query($sql, $params, $className)->fetch() ?: null;
    }
    
    /**
     * Выполняет запрос и возвращает все строки результата
     */
    public function fetchAll(string $sql, array $params = [], string $className = 'stdClass'): array
    {
        return $this->query($sql, $params, $className)->fetchAll();
    }
    
    /**
     * Выполняет запрос и возвращает значение первого столбца первой строки
     */
    public function fetchColumn(string $sql, array $params = [], int $column = 0): mixed
    {
        return $this->query($sql, $params)->fetchColumn($column);
    }
    
    /**
     * Выполняет запрос и возвращает результат в виде массива,
     * где ключом является значение указанного столбца
     */
    public function fetchAllKeyed(string $sql, array $params, string $keyColumn): array
    {
        $data = [];
        $stmt = $this->query($sql, $params);
        
        while ($row = $stmt->fetch()) {
            $data[$row->{$keyColumn}] = $row;
        }
        
        return $data;
    }
    
    /**
     * Выполняет запрос и возвращает массив значений одного столбца
     */
    public function fetchColumnAll(string $sql, array $params = [], int $column = 0): array
    {
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_COLUMN, $column);
    }
    
    /**
     * Выполняет запрос и возвращает результат в виде пар ключ-значение
     */
    public function fetchPairs(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    /**
     * Выполняет INSERT запрос и возвращает ID последней вставленной записи
     */
    public function insert(string $sql, array $params = []): ?int
    {
        $this->query($sql, $params);
        $id = $this->lastInsertId();
        return $id ? (int) $id : null;
    }
    
    /**
     * Выполняет UPDATE запрос и возвращает количество изменённых строк
     */
    public function update(string $sql, array $params = []): int
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Выполняет DELETE запрос и возвращает количество удалённых строк
     */
    public function delete(string $sql, array $params = []): int
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Проверяет существует ли таблица в базе данных
     */
    public function tableExists(string $tableName): bool
    {
        $result = $this->fetchColumn(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?', 
            [$this->dbname, $tableName]
        );
        
        return (bool)$result;
    }
    
    /**
     * Получает список таблиц в текущей базе данных
     */
    public function getTables(): array
    {
        return $this->fetchColumnAll(
            'SELECT table_name FROM information_schema.tables WHERE table_schema = ?',
            [$this->dbname]
        );
    }
    
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }
    
    public function commit(): bool
    {
        return $this->connection->commit();
    }
    
    public function rollBack(): bool
    {
        if($this->connection->inTransaction()){
            return $this->connection->rollBack();
        }
        return false;
    }
    
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }
    
    public function close(): void
    {
        $this->connection = null;
    }
    
    public function __destruct()
    {
        $this->close();
    }
}