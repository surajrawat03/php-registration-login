<?php
namespace App\class;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Dotenv\Dotenv;

class databaseConnection
{
    private static ?Connection $serverConnection = null;
    private static ?Connection $dbConnection = null;

    /**
     * Get connection to MySQL server (without specific database)
     */
    public static function getServerConnection(): Connection
    {
        if (self::$serverConnection === null) {
            // Load env
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->safeLoad();

            $dbUser = $_ENV['DB_USER'] ?? null;
            $dbPass = $_ENV['DB_PASS'] ?? null;
            $dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $dbPort = $_ENV['DB_PORT'] ?? 3306;

            // Connect to the server (without a database)
            $serverConnectionParams = [
                'user'     => $dbUser,
                'password' => $dbPass,
                'host'     => $dbHost,
                'port'     => $dbPort,
                'driver'   => 'pdo_mysql',
                'charset'  => 'utf8mb4'
            ];

            self::$serverConnection = DriverManager::getConnection($serverConnectionParams);
        }

        return self::$serverConnection;
    }

    /**
     * Get connection to specific database
     */
    public static function getConnection(): Connection
    {
        if (self::$dbConnection === null) {
            // Load env
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->safeLoad();

            $dbName = $_ENV['DB_NAME'] ?? null;
            $dbUser = $_ENV['DB_USER'] ?? null;
            $dbPass = $_ENV['DB_PASS'] ?? null;
            $dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $dbPort = $_ENV['DB_PORT'] ?? 3306;

            // Connect to specific database
            $dbConnectionParams = [
                'user'     => $dbUser,
                'password' => $dbPass,
                'host'     => $dbHost,
                'port'     => $dbPort,
                'dbname'   => $dbName,
                'driver'   => 'pdo_mysql',
                'charset'  => 'utf8mb4'
            ];

            self::$dbConnection = DriverManager::getConnection($dbConnectionParams);
        }

        return self::$dbConnection;
    }

    /**
     * Ensure database exists, create if it doesn't
     */
    public static function ensureDatabaseExists(): void
    {
        $dbName = $_ENV['DB_NAME'] ?? null;
        
        if (!$dbName) {
            throw new \Exception('DB_NAME not set in environment variables');
        }

        $serverConn = self::getServerConnection();
        $schemaManager = $serverConn->createSchemaManager();
        $databases = $schemaManager->listDatabases();

        if (!in_array($dbName, $databases, true)) {
            $serverConn->executeStatement("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "Database `$dbName` created.\n";
        } else {
            echo "Database `$dbName` already exists.\n";
        }
    }

    /**
     * Reset connections (useful for testing)
     */
    public static function resetConnections(): void
    {
        if (self::$serverConnection) {
            self::$serverConnection->close();
            self::$serverConnection = null;
        }
        if (self::$dbConnection) {
            self::$dbConnection->close();
            self::$dbConnection = null;
        }
    }
}
