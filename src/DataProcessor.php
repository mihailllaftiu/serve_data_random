<?php

require_once 'DataProvider.php';
require_once 'DataStore.php';

class DataProcessor
{
    private $dataProvider;
    private $dataStore;

    /**
     * Constructor for the class.
     *
     * Initializes the dataProvider and dataStore objects.
     */
    public function __construct()
    {
        $this->dataProvider = new DataProvider();
        $this->dataStore = new DataStore(); 
    }

    /**
     * Processes data within a specified date range and source.
     *
     * This method retrieves data from the external provider API and
     * stores it in the database based on the given start and end dates
     * and source.
     *
     * @param string|null $startDate The start date of the data range (format YYYY-MM-DD) or null for today.
     * @param string|null $endDate The end date of the data range (format YYYY-MM-DD) or null for $startDate.
     * @param string|null $source The source of the data (optional) or an empty string.
     * @return void
     */
    public function processData(?string $startDate = null, ?string $endDate = null, ?string $source) : void
    {
        $source = $source ?? 'exchangerate';

        // Catch any problems with the given start date, end date, and source
        $this->catchProblems($startDate, $endDate, $source);

        // Start timer for execution time
        $startTime = microtime(true);

        // Run relevant process - provider based on source
        switch ($source) {
            case 'exchangerate':
                $this->processExchangeRateData($startDate, $endDate);
                break;
            case '': // case where source=''
                echo ">> Unknown data source: $source\n";
                break;
            default: // case where source='something else that not exists'
                echo ">> Unknown data source: $source\n";
                break;
        }

        // End timer for execution time
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        echo "Execution time: {$executionTime}\n\n";
    }

    /**
     * Process exchange rate data based on the start and end dates.
     *
     * This method retrieves exchange rate data from the external
     * provider API and stores it in the database.
     *
     * @param string|null $startDate The start date for processing exchange rate data or null for today.
     * @param string|null $endDate The end date for processing exchange rate data or null for $startDate.
     * @return void
     */
    private function processExchangeRateData(string $startDate = null, ?string $endDate = null): void
    {
        if ($endDate !== null) {
            // If $endDate is not null, retrieve data between $startDate and $endDate
            $endDate = (DateTimeImmutable::createFromFormat('Y-m-d', $endDate) > DateTimeImmutable::createFromFormat('Y-m-d', $startDate)) ? 
                            $endDate : clone $startDate;
            $data = $this->dataProvider->getExRsRangeOfDatesData($startDate, $endDate);
        } else {
            // If $endDate is null, retrieve only the data for $startDate
            $data = $this->dataProvider->getExRsSpecificDateData($startDate);
        }

        // Iterate through the received data and store it in the database
        foreach ($data as $date => $value) {
            // Handle the case when rates are empty
            if (empty($value['rates'])) {
                continue;
            }

            // Iterate through each currency and its rate
            foreach ($value['rates'] as $symbol => $rate) {
                // Pad the rate with 0s to the right of the decimal point
                $pad_length = 15 - strlen(substr(strrchr((string) $rate, "."), 1));
                $rate = (string) sprintf("%0.{$pad_length}f", $rate);

                // Save the currency and its rate in the database
                $this->dataStore->saveCurrencyRatesInDB($startDate, $symbol, $rate);
            }
        }
    }

    
    /**
     * Checks for any problems with the given start date, end date, and source.
     *
     * This method checks if there are any problems with the start date,
     * end date, and source given to the class. It checks if the end date
     * is after the start date, if the dates are in the correct format
     * (YYYY-MM-DD), and if the source is a valid source of exchange rate
     * data.
     *
     * @param string|null $startDate The start date in the format YYYY-MM-DD.
     * @param string|null $endDate The end date in the format YYYY-MM-DD or null.
     * @param string|null $source The source of the data.
     * @return void
     */
    private function catchProblems(?string $startDate, ?string $endDate, ?string $source): void
    {

        // Check if end date is in the format Y-m-d
        if ($endDate !== null && !DateTimeImmutable::createFromFormat('Y-m-d', $endDate)) {
            echo ">> End dates must be in the format YYYY-MM-DD\n";
            error_log(">> End date must be in the format YYYY-MM-DD", 0);
            exit;
        }

        if ($startDate !== null && !DateTimeImmutable::createFromFormat('Y-m-d', $startDate)) {
            echo ">> Start dates must be in the format YYYY-MM-DD\n";
            error_log(">> Start date must be in the format YYYY-MM-DD", 0);
            exit;
        }

        if (($startDate !== 'latest' && $endDate !==null) && 
            (DateTimeImmutable::createFromFormat('Y-m-d', $startDate) > DateTimeImmutable::createFromFormat('Y-m-d', $endDate))) {
            echo ">> Start date must be before end date\n";
            error_log(">> Start date must be before end date", 0);
            exit;
        }
    } 
}
