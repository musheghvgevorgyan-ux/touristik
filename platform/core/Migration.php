<?php

namespace Core;

class Migration
{
    private Database $db;
    private string $migrationsPath;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->migrationsPath = BASE_PATH . '/database/migrations';
        $this->createMigrationsTable();
    }

    private function createMigrationsTable(): void
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                ran_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    /**
     * Run all pending migrations
     */
    public function migrate(): array
    {
        $ran = $this->getRanMigrations();
        $files = $this->getMigrationFiles();
        $pending = array_diff($files, $ran);
        $results = [];

        sort($pending);

        foreach ($pending as $migration) {
            $filePath = $this->migrationsPath . '/' . $migration;
            $sql = require $filePath;

            // Extract the 'up' portion if migration uses up/down format
            if (is_array($sql) && isset($sql['up'])) {
                $sql = $sql['up'];
            }

            if (is_string($sql)) {
                $this->db->query($sql);
            } elseif (is_array($sql)) {
                foreach ($sql as $statement) {
                    $this->db->query($statement);
                }
            }

            $this->db->query(
                "INSERT INTO migrations (migration) VALUES (?)",
                [$migration]
            );

            $results[] = $migration;
        }

        return $results;
    }

    /**
     * Rollback the last batch of migrations
     */
    public function rollback(int $steps = 1): array
    {
        $ran = $this->getRanMigrations();
        $toRollback = array_slice(array_reverse($ran), 0, $steps);
        $results = [];

        foreach ($toRollback as $migration) {
            $filePath = $this->migrationsPath . '/' . $migration;
            $data = require $filePath;

            // If migration returns ['up' => ..., 'down' => ...] format
            if (is_array($data) && isset($data['down'])) {
                $down = $data['down'];
                if (is_string($down)) {
                    $this->db->query($down);
                } elseif (is_array($down)) {
                    foreach ($down as $statement) {
                        $this->db->query($statement);
                    }
                }
            }

            $this->db->query(
                "DELETE FROM migrations WHERE migration = ?",
                [$migration]
            );

            $results[] = $migration;
        }

        return $results;
    }

    /**
     * Get list of already-ran migrations
     */
    private function getRanMigrations(): array
    {
        $stmt = $this->db->query("SELECT migration FROM migrations ORDER BY id ASC");
        return array_column($stmt->fetchAll(), 'migration');
    }

    /**
     * Get all migration files from disk
     */
    private function getMigrationFiles(): array
    {
        $files = glob($this->migrationsPath . '/*.php');
        return array_map('basename', $files);
    }

    /**
     * Get migration status
     */
    public function status(): array
    {
        $ran = $this->getRanMigrations();
        $files = $this->getMigrationFiles();
        sort($files);

        $status = [];
        foreach ($files as $file) {
            $status[] = [
                'migration' => $file,
                'status'    => in_array($file, $ran) ? 'ran' : 'pending',
            ];
        }

        return $status;
    }
}
