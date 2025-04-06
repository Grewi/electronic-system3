<?php

declare(strict_types=1);

namespace system\core\database;

use PDO;
use PDOException;
use PDOStatement;

class sqlite
{
    private ?PDO $connection = null;
    
    public function __construct(
        private string $databasePath,
        private array $options = []
    ) {
        $this->connect();
    }
    
    /**
     * Устанавливает соединение с SQLite базой данных
     * 
     * @throws PDOException Если соединение не удалось
     */
    private function connect(): void
    {
        // Проверяем, существует ли файл базы данных (если не in-memory)
        if ($this->databasePath !== ':memory:' && !file_exists(dirname($this->databasePath))) {
            throw new PDOException("Database directory does not exist");
        }
        
        // Оптимальные настройки для SQLite
        $defaultOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::ATTR_PERSISTENT => false, // Для SQLite лучше не использовать постоянные соединения
        ];
        
        $options = array_replace($defaultOptions, $this->options);
        
        try {
            $this->connection = new PDO("sqlite:{$this->databasePath}", null, null, $options);
            
            // Включаем поддержку внешних ключей (по умолчанию выключены для обратной совместимости)
            $this->connection->exec("PRAGMA foreign_keys = ON");
            // Включаем строгий режим для типов данных (доступно с SQLite 3.37.0+)
            $this->connection->exec("PRAGMA strict = ON");
            // Устанавливаем режим журналирования WAL для лучшей производительности
            $this->connection->exec("PRAGMA journal_mode = WAL");
            
        } catch (PDOException $e) {
            throw new PDOException("SQLite connection failed: " . $e->getMessage(), (int)$e->getCode());
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
     */
    public function insert(string $sql, array $params = []): string
    {
        $this->query($sql, $params);
        return $this->lastInsertId();
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
            "SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = ?",
            [$tableName]
        );
        
        return (bool)$result;
    }
    
    /**
     * Получает список таблиц в базе данных
     */
    public function getTables(): array
    {
        return $this->fetchColumnAll(
            "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'"
        );
    }
    
    /**
     * Получает схему таблицы (структуру)
     */
    public function getTableSchema(string $tableName): array
    {
        return $this->fetchAll(
            "PRAGMA table_info({$this->quoteIdentifier($tableName)})"
        );
    }
    
    /**
     * Экранирует идентификатор (имя таблицы/столбца)
     */
    public function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
    
    /**
     * Создает резервную копию базы данных
     */
    public function backup(string $backupPath): bool
    {
        if ($this->databasePath === ':memory:') {
            throw new PDOException("Cannot backup in-memory database");
        }
        
        // Закрываем текущее соединение
        $this->close();
        
        try {
            // Копируем файл базы данных
            if (!copy($this->databasePath, $backupPath)) {
                throw new PDOException("Failed to copy database file");
            }
            
            // Восстанавливаем соединение
            $this->connect();
            
            return true;
        } catch (PDOException $e) {
            // Пытаемся восстановить соединение в случае ошибки
            $this->connect();
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