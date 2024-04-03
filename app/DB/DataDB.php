<?php
namespace App\DB;

abstract class DataDB
{
    protected $driver = 'mysql';

    abstract protected function connect($driver): \PDO;

    abstract protected function saveCurrencyRatesInDB(string $date, string $symbol, string $rate);

    /**
     * Executes the given query and returns the result.
     *
     * @param string $query The SQL query to be executed
     * @return array<array<string,string>> The result of the query
     * @throws PDOException If there is a connection error
     */
    public function runQueries(string $query, mixed $params = []): void // return object
    {
        try {
            // Connect to the database
            $connection = $this->connectViaPDO($this->driver);

            // Execute the query with parameters, if any
            $stmt = $connection->prepare($query);
            $stmt->execute($params);
        } catch (\PDOException $e) {
            // Print the error message
            echo "Connection error: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Connects to the database using PDO.
     *
     * This method creates a new PDO instance for the specified database driver.
     * The method requires the database credentials (host, username, password, database name)
     * to be present in the configuration file.
     *
     * @param string $driver The database driver. Possible values are 'mysql' or 'pgsql'.
     * @return \PDO The PDO instance for the connection to the database.
     * @throws \PDOException If the connection fails.
     * @throws \Exception If an unsupported database driver is specified.
     */
    protected function connectViaPDO(string $driver): \PDO
    {
        // Retrieve the database credentials
        [$host, $username, $password, $database] = $this->dbCredentials($driver);

        // Construct the DSN string
        if ($driver === 'mysql') {
            $dsn = "mysql:host=$host;dbname=$database";
        } elseif ($driver === 'pgsql') {
            $dsn = "pgsql:host=$host;dbname=$database";
        } else {
            throw new \Exception("Unsupported database driver.");
        }

        // Create a new PDO instance
        $pdo = new \PDO($dsn, $username, $password);

        // Set the error mode to throw exceptions when there is an error
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    /**
     * Retrieves the database credentials based on the specified driver.
     *
     * This method returns the database credentials in the form of an associative array with the
     * following keys:
     * - host: The host of the database.
     * - username: The username for accessing the database.
     * - password: The password for accessing the database.
     * - dbname: The name of the database.
     *
     * @param string $driver The driver for the database. Possible values are 'mysql' or 'pgsql'.
     * @return array The database credentials.
     * @throws Exception If an unsupported database driver is specified.
     */
    private function dbCredentials(string $driver): array
    {
        if ($driver === 'mysql') {
            // Database credentials for MySQL
            $host = "127.0.0.1";
            $username = "root";
            $password = "";
            $dbname = "exchange_db";
        } elseif ($driver === 'pgsql') {
            // Database credentials for PostgreSQL
            $host = "127.0.0.1";
            $username = "postgres";
            $password = "";
            $dbname = "exchange_db";
        } else {
            error_log("Unsupported database driver: " . $driver, 0);
            throw new \Exception("Unsupported database driver.");
        }

        return array($host, $username, $password, $dbname);
    }
}

