<?php

class Database
{
    private static $db = null;
    private static $driver = null;

    public static function connexionDB()
    {
        if (self::$db === null) {
            try {
                $user = 'postgres';
                $password = 'passer123';
                $host = 'localhost';
                $port = '5432';
                $dbname = 'storemanager';

                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
                
                self::$db = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
                self::$driver = 'pgsql';

            } catch (PDOException $e) {
                try {
                    $rootPath = dirname(__DIR__, 2);
                    $sqliteFile = $rootPath . '/erp.db';
                    $dsn = "sqlite:{$sqliteFile}";

                    self::$db = new PDO($dsn, null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]);

                    self::$db->exec('PRAGMA foreign_keys = ON;');
                    self::$driver = 'sqlite';

                    self::initSqliteSchema();

                } catch (PDOException $e) {
                    throw new Exception("Erreur de connexion à la base de données");
                }
            }
        }

        return self::$db;
    }

    private static function initSqliteSchema()
    {
        $rootPath = dirname(__DIR__, 2);
        $schemaFile = $rootPath . '/database/schema_sqlite.sql';
        if (file_exists($schemaFile)) {
            $sql = file_get_contents($schemaFile);
            self::$db->exec($sql);
        }
    }

    public static function getDriver()
    {
        return self::$driver;
    }

    public static function query($pdo, $sql, $single = true)
    {
        $query = $pdo->query($sql);
        return $single ? $query->fetch() : $query->fetchAll();
    }

    public static function prepare($pdo, $sql, $datas)
    {
        $prepare = $pdo->prepare($sql);
        $prepare->execute($datas);
        return $prepare;
    }

    public static function executeQuery($pdo, $sql, $datas, $single = true)
    {
        $statement = self::prepare($pdo, $sql, $datas);
        return $single ? $statement->fetch() : $statement->fetchAll();
    }

    public static function executeUpdate($pdo, $sql, $datas)
    {
        $statement = self::prepare($pdo, $sql, $datas);
        
        $sqlUpper = strtoupper(trim($sql));
        if (str_starts_with($sqlUpper, 'INSERT')) {
            return (int)$pdo->lastInsertId();
        }
        
        return $statement->rowCount();
    }

    public static function getAllTable($table)
    {
        $pdo = self::connexionDB();
        $sql = "SELECT * FROM $table";
        return self::query($pdo, $sql, false);
    }

    public static function beginTransaction($pdo)
    {
        return $pdo->beginTransaction();
    }

    public static function commit($pdo)
    {
        return $pdo->commit();
    }

    public static function rollBack($pdo)
    {
        return $pdo->rollBack();
    }
}