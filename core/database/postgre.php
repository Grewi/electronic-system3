<?php

declare(strict_types=1);

namespace system\core\database;

use PDO;
use PDOException;
use PDOStatement;

class postgre
{
    private ?PDO $connection = null;
    
    public function __construct(
        private string $host,
        private string $dbname,
        private string $username,
        private string $password,
        private string $port = '5432',
        private array $options = []
    ) {
        $this->connect();
    }
    
    /**
     * Устанавливает соединение с PostgreSQL
     * 
     * @throws PDOException Если соединение не удалось
     */
    private function connect(): void
    {
        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->dbname}";
        
        // Оптимальные настройки для PostgreSQL
        $defaultOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::ATTR_PERSISTENT => false,
            PDO::PGSQL_ATTR_DISABLE_PREPARES => false,
        ];
        
        $options = array_replace($defaultOptions, $this->options);
        
        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
            // Устанавливаем важные параметры PostgreSQL
            $this->connection->exec("SET TIME ZONE 'UTC'");
            $this->connection->exec("SET NAMES 'UTF8'");
            $this->connection->exec("SET client_encoding TO 'UTF8'");
            
        } catch (PDOException $e) {
            throw new PDOException("PostgreSQL connection failed: " . $e->getMessage(), (int)$e->getCode());
        }
    }
    
    /**
     * Выполняет SQL запрос с параметрами
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Выполняет запрос и возвращает одну строку результата
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        return $this->query($sql, $params)->fetch() ?: null;
    }
    
    /**
     * Выполняет запрос и возвращает все строки результата
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
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
            $data[$row[$keyColumn]] = $row;
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
     * 
     * @param string $sequenceName Имя последовательности (обычно 'table_name_id_seq')
     */
    public function insert(string $sql, array $params = [], ?string $sequenceName = null): string
    {
        $this->query($sql, $params);
        return $this->lastInsertId($sequenceName);
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
            "SELECT COUNT(*) FROM information_schema.tables 
             WHERE table_schema = 'public' AND table_name = ?",
            [$tableName]
        );
        
        return (bool)$result;
    }
    
    /**
     * Получает список таблиц в текущей базе данных
     */
    public function getTables(): array
    {
        return $this->fetchColumnAll(
            "SELECT table_name FROM information_schema.tables 
             WHERE table_schema = 'public'"
        );
    }
    
    /**
     * Получает структуру таблицы
     */
    public function getTableSchema(string $tableName): array
    {
        return $this->fetchAll(
            "SELECT column_name, data_type, is_nullable, column_default 
             FROM information_schema.columns 
             WHERE table_name = ? 
             ORDER BY ordinal_position",
            [$tableName]
        );
    }
    
    /**
     * Экранирует идентификатор (имя таблицы/столбца)
     */
    public function quoteIdentifier(string $identifier): string
    {
        return '"' . str_replace('"', '""', $identifier) . '"';
    }
    
    /**
     * Выполняет несколько запросов в одной транзакции
     */
    public function executeTransaction(callable $callback): void
    {
        $this->beginTransaction();
        try {
            $callback($this);
            $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            throw $e;
        }
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
        return $this->connection->rollBack();
    }
    
    /**
     * Возвращает ID последней вставленной записи
     * 
     * @param string|null $sequenceName Имя последовательности (для PostgreSQL)
     */
    public function lastInsertId(?string $sequenceName = null): string
    {
        return $this->connection->lastInsertId($sequenceName);
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