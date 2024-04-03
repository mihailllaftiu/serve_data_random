<?php
namespace App\DB;

use App\DB\DataDB;

class DataQueries extends DataDB {
    /**
     * Connects to the specified database driver using PDO.
     *
     * This method uses the PDO extension to connect to a database
     * using a PDO driver. The driver name must be a string, and the
     * method will throw an exception if it is not.
     *
     * @param string $driver The name of the driver to connect to.
     * @return \PDO The database connection.
     * @throws \InvalidArgumentException If the given driver is not a string.
     * @throws \PDOException If the connection fails.
     */
    public function connect($driver): \PDO
    {
        return $this->connectViaPDO($driver);
    }

    /**
     * Save currency rates in the database.
     *
     * This method checks if the currency rate for the given date and symbol
     * already exists in the database and acts accordingly: if it does not
     * exist, it inserts a new record, otherwise it updates the existing one.
     *
     * @param string $date Date of the currency rates.
     * @param string $symbol Currency symbol.
     * @param string $rate Currency rate.
     * @return void
     */
    public function saveCurrencyRatesInDB(string $date, string $symbol, string $rate): void
    {
        $formattedDate = \DateTimeImmutable::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        $query = "INSERT INTO currency_rates (currency_date, currency_symbol, currency_rate) VALUES (:date, :symbol, :rate) 
                    ON DUPLICATE KEY UPDATE currency_rate = VALUES(currency_rate)";
        
        $pdo = $this->connectViaPDO('mysql');
        $stmt = $pdo->prepare($query);

        $stmt->bindParam(':date', $formattedDate);
        $stmt->bindParam(':symbol', $symbol);
        $stmt->bindParam(':rate', $rate);

        $stmt->execute();
    }

}
