<?php

namespace Core;

abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';

    protected static function db(): Database
    {
        return Database::getInstance();
    }

    /**
     * Find a record by primary key
     */
    public static function find(int $id): ?array
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        $stmt = static::db()->query(
            "SELECT * FROM {$table} WHERE {$pk} = ? LIMIT 1",
            [$id]
        );
        return $stmt->fetch() ?: null;
    }

    /**
     * Find a record by a specific column
     */
    public static function findBy(string $column, mixed $value): ?array
    {
        $table = static::$table;
        $stmt = static::db()->query(
            "SELECT * FROM {$table} WHERE {$column} = ? LIMIT 1",
            [$value]
        );
        return $stmt->fetch() ?: null;
    }

    /**
     * Get all records (with optional limit/offset)
     */
    public static function all(int $limit = 100, int $offset = 0): array
    {
        $table = static::$table;
        $stmt = static::db()->query(
            "SELECT * FROM {$table} ORDER BY {$table}." . static::$primaryKey . " DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
        return $stmt->fetchAll();
    }

    /**
     * Get records matching conditions
     */
    public static function where(array $conditions, string $orderBy = '', int $limit = 0): array
    {
        $table = static::$table;
        $clauses = [];
        $values = [];

        foreach ($conditions as $column => $value) {
            if ($value === null) {
                $clauses[] = "{$column} IS NULL";
            } else {
                $clauses[] = "{$column} = ?";
                $values[] = $value;
            }
        }

        $sql = "SELECT * FROM {$table} WHERE " . implode(' AND ', $clauses);

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        if ($limit > 0) {
            $sql .= " LIMIT ?";
            $values[] = $limit;
        }

        $stmt = static::db()->query($sql, $values);
        return $stmt->fetchAll();
    }

    /**
     * Count records matching conditions
     */
    public static function count(array $conditions = []): int
    {
        $table = static::$table;

        if (empty($conditions)) {
            $stmt = static::db()->query("SELECT COUNT(*) as cnt FROM {$table}");
        } else {
            $clauses = [];
            $values = [];
            foreach ($conditions as $column => $value) {
                $clauses[] = "{$column} = ?";
                $values[] = $value;
            }
            $stmt = static::db()->query(
                "SELECT COUNT(*) as cnt FROM {$table} WHERE " . implode(' AND ', $clauses),
                $values
            );
        }

        return (int) $stmt->fetch()['cnt'];
    }

    /**
     * Insert a new record
     */
    public static function create(array $data): int
    {
        $table = static::$table;
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        static::db()->query(
            "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})",
            array_values($data)
        );

        return (int) static::db()->lastInsertId();
    }

    /**
     * Update a record by primary key
     */
    public static function update(int $id, array $data): bool
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        $sets = [];
        $values = [];

        foreach ($data as $column => $value) {
            $sets[] = "{$column} = ?";
            $values[] = $value;
        }

        $values[] = $id;

        $stmt = static::db()->query(
            "UPDATE {$table} SET " . implode(', ', $sets) . " WHERE {$pk} = ?",
            $values
        );

        return $stmt->rowCount() > 0;
    }

    /**
     * Delete a record by primary key
     */
    public static function delete(int $id): bool
    {
        $table = static::$table;
        $pk = static::$primaryKey;
        $stmt = static::db()->query(
            "DELETE FROM {$table} WHERE {$pk} = ?",
            [$id]
        );
        return $stmt->rowCount() > 0;
    }

    /**
     * Execute a raw query
     */
    public static function raw(string $sql, array $params = []): \PDOStatement
    {
        return static::db()->query($sql, $params);
    }
}
