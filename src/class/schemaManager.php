<?php
namespace App\class;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Comparator;
use Dotenv\Dotenv;
use Exception;

class schemaManager
{
    private $conn;
    private $toSchema;
    private $fromSchema;

    public function __construct()
    {
        // Ensure database exists and get connection
        DatabaseConnection::ensureDatabaseExists();
        $this->conn = DatabaseConnection::getConnection();

        // Target schema
        $this->toSchema = new Schema();
        $this->defineSchema();
    }

    private function defineSchema()
    {
        // Users table
        $users = $this->toSchema->createTable('users');
        $users->addColumn('id', 'integer', ['autoincrement' => true]);
        $users->addColumn('firstName', 'string', ['length' => 100]);
        $users->addColumn('lastName', 'string', ['length' => 100]);
        $users->addColumn('email', 'string', ['length' => 255]);
        $users->addColumn('password', 'string', ['length' => 255]);
        $users->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);
        $users->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'notnull' => false]);
        $users->setPrimaryKey(['id']);
        $users->addUniqueIndex(['email']);
    }

    public function migrate()
    {
        $platform = $this->conn->getDatabasePlatform();
        $fromSchema = $this->conn->createSchemaManager()->introspectSchema();

        $comparator = new Comparator();
        if (method_exists($comparator, 'compareSchemas')) {
            $diff = $comparator->compareSchemas($fromSchema, $this->toSchema);
        } else {
            $diff = $comparator->compare($fromSchema, $this->toSchema);
        }

        if (method_exists($platform, 'getAlterSchemaSQL')) {
            $queries = $platform->getAlterSchemaSQL($diff);
        } else {
            $queries = $diff->toSql($platform);
        }

        if (empty($queries)) {
            echo "✅ No changes needed.\n";
            return;
        }

        foreach ($queries as $sql) {
            echo "Running: $sql\n";
            $this->conn->executeStatement($sql);
        }

        echo "✅ Migration complete.\n";
    }
}


