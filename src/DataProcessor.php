<?php

require_once 'DataProvider.php';
require_once 'DataStore.php';

class DataProcessor
{
    private $dataProvider;
    private $dataStore;

    public function __construct()
    {
        $this->dataProvider = new DataProvider();
        $this->dataStore = new DataStore(); 
    }

    public function processData($startDate, $endDate, $source = "")
    {
        $this->catchProblems($startDate, $endDate, $source);
        $startTime = microtime(true);

        // Run relevant process - provider based on source
        switch ($source) {
            case 'exchangerate':
                $this->processExchangeRateData($startDate, $endDate);
                break;
            case 'empty': // case where source=''
                echo ">> Unknown data source: $source\n";
                break;
            case $source !== '': // case where source='something else that not exists'
                echo ">> Unknown data source: $source\n";
                break;
            default:
                $this->processExchangeRateData($startDate, $endDate);
                break;
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        echo "Execution time: {$executionTime}\n\n";
    }

    private function processExchangeRateData($startDate, $endDate)
    {
        $startDate = ($startDate === null) ? 'latest' : $startDate;

        if ($startDate === 'latest') {
            $data = $this->dataProvider->getExRsLatestDateData($startDate);
        } elseif ($endDate !== null) {
            $data = $this->dataProvider->getExRsRangeOfDatesData($startDate, $endDate);
        } else {
            $data = $this->dataProvider->getExRsSpecificDateData($startDate);
        }

        foreach ($data as $date => $value) {
            if (empty($value['rates'])) break; // in case something goes wrong with specific date data
            foreach ($value['rates'] as $symbol => $rate) {
                $pad_length = 15 - strlen(substr(strrchr($rate, "."), 1));
                $rate = sprintf("%0.{$pad_length}f", $rate); // Full fill with 0s the rest
                $this->dataStore->saveCurrencyRatesInDB(date("Y-m-d", strtotime($date)), $symbol, $rate);
            }
        }
    }
    
    private function catchProblems($startDate, $endDate, $source)
    {
        // if end date is after start date
        if (!empty($startDate) && !empty($endDate)) {
            if (strtotime($endDate) < strtotime($startDate)) {
                echo ">> End date must be after start date\n";
                exit;
            }
        }
        // if end date and start date are Y-m-d format
        if (!empty($startDate)) {
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $startDate)) {
                echo ">> Start date must be in the format YYYY-MM-DD\n";
                exit;
            }
        } else if (!empty($endDate)) {
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $endDate)) {
                echo ">> End date must be in the format YYYY-MM-DD\n";
                exit;
            }
        }
    }
}
