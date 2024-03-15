<?php
require_once 'DataDB.php';

class DataStore extends DataDB {

    public function connect($driver)
    {
        return $this->connectViaPDO($driver);
    }

    public function saveCurrencyRatesInDB($date, string $symbol, $rate)
    {
        $query = "SELECT COUNT(*) AS count FROM currency_rates WHERE currency_date = '$date' AND currency_symbol = '$symbol';";
        $result = $this->runQueries($query);
        
        if ($result[0]['count'] == 0) {
            $query = "INSERT INTO currency_rates (currency_date, currency_symbol, currency_rate) VALUES ('$date', '$symbol', '$rate') ON DUPLICATE KEY UPDATE currency_rate = VALUES(currency_rate);";
            $this->runQueries($query);
        } else {
            $query = "UPDATE currency_rates SET currency_rate = '$rate' WHERE currency_date = '$date' AND currency_symbol = '$symbol';";
            $this->runQueries($query);
        }
    }
}
