<?php

abstract class DataDB
{
    protected $driver = 'mysql';

    abstract protected function connect($driver);

    abstract protected function saveCurrencyRatesInDB($date, string $symbol, $rate);

    public function runQueries($query)
    {
        try {
            $connection = $this->connectViaPDO($this->driver);
        
            // Execute the query
            $stmt = $connection->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $result;
        } catch (PDOException $e) {
            echo $query;
            echo ">> Connection error: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    protected function connectViaPDO($driver)
    {
        [$host, $username, $password, $database] = $this->dbCredentials($driver);

        if ($driver === 'mysql') {
            $dsn = "mysql:host=$host;dbname=$database";
        } elseif ($driver === 'pgsql') {
            $dsn = "pgsql:host=$host;dbname=$database";
        }

        // Create a new PDO instance
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    protected function dbCredentials($driver): array
    {
        if ($driver === 'mysql') {
            $host = "127.0.0.1";
            $username = "root";
            $password = "";
            $dbname = "exchange_db";
        } elseif ($driver === 'pgsql') {
            $host = "127.0.0.1";
            $username = "postgres";
            $password = "";
            $dbname = "exchange_db";
        } else {
            throw new Exception("Unsupported database driver.");
        }

        return [$host, $username, $password, $dbname];
    }
}

