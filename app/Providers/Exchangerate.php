<?php
namespace App\Providers;

use App\DB\DataQueries;
use App\Handlers\DataLoader;


class Exchangerate
{
    const exchangeRatesApiKey = ''; // Add your Open Exchange Rates API Key
    private $exchangeRatesLiveData = false; // = true/false -> real time data, or downloaded data, for the specific API only
    private $data = [];
    public $dataLoader;
    public $dataQueries;

    /**
     * Class constructor
     *
     * Initializes a new Exchangerate object.
     *
     * @param bool $onlyLiveData Should the data be retrieved from the API or from a local file? Defaults to false.
     * @param string $startDate The start date for processing exchange rate data
     * @param string|null $endDate The end date for processing exchange rate data or null for $startDate
     */
    public function __construct()
    {
        // Check if the API key is set
        if (empty(self::exchangeRatesApiKey)) {
            error_log('API Key not set for Open Exchange Rates API', 0);
            throw new \Exception('API Key not set for Open Exchange Rates API');
        }

        // Initialize Handlers
        $this->dataQueries = new DataQueries();
        $this->dataLoader = new DataLoader();
    }

    /**
     * Process exchange rate data based on the start and end dates.
     *
     * This method retrieves exchange rate data from the external provider API
     * and stores it in the database.
     *
     * @param bool $onlyLiveData Should the data be retrieved from the API or from a local file?
     * Defaults to false.
     * @param string $startDate The start date for processing exchange rate data.
     * @param string|null $endDate The end date for processing exchange rate data or null for $startDate.
     *
     * @return void
     */
    public function collectDataFromProvider(bool $onlyLiveData = false, string $startDate, ?string $endDate = null): void
    {
        // If onlyLiveData is true, set exchangeRatesLiveData to true, otherwise leave it as is
        $this->exchangeRatesLiveData = $onlyLiveData === true ? true : $this->exchangeRatesLiveData;

        // If end date is not null and greater than start date, retrieve data between start and end date
        if ($endDate !== null && \DateTimeImmutable::createFromFormat('Y-m-d', $endDate) > \DateTimeImmutable::createFromFormat('Y-m-d', $startDate)) {
            $data = $this->getExRsRangeOfDatesData($startDate, $endDate);
        } else {
            $data = $this->getExRsSpecificDateData($startDate);
        }

        // Store data in the database if it's not empty
        if (!empty($data)) {
            $this->storeExchangeRatesDataInDB($data);
        } else {
            error_log('No data retrieved from Open Exchange Rates API', 0);
            throw new \Exception('No data retrieved from Open Exchange Rates API');
        }
    }

    /**
     * Store the received exchange rates data in the database
     *
     * @param array<non-empty-string, array<string, mixed>> $data The exchange rates data indexed by the date
     * @return void
     */
    public function storeExchangeRatesDataInDB(array $data): void
    {
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
                $this->dataQueries = new DataQueries();
                $this->dataQueries->saveCurrencyRatesInDB($date, $symbol, $rate);  // Line A
            }
        }
    }

    /**
     * Get specific exchange rates data from the openexchangerates.org API
     *
     * @param string $date The date in the format 'Y-m-d'
     * @return array<non-empty-string, array<string, mixed>> The data indexed by the date
     */
    public function getExRsSpecificDateData(string $date): array
    {
        $url = (string) "https://openexchangerates.org/api/historical/$date.json?app_id=" . self::exchangeRatesApiKey;
        $this->data[(string) $date] = $this->exchangeRatesLiveData ? 
                                $this->dataLoader->getDataDirectlyFromApi($url) : $this->dataLoader->getDataOnlineAndSaveForNextUse($url, $date);
        return $this->data;
    }

    /**
     * Get exchange rates data for a range of dates from the openexchangerates.org API
     *
     * @param string $date The start date in the format 'Y-m-d'
     * @param string $endDate The end date in the format 'Y-m-d'
     * @return array<non-empty-string, array<string, mixed>> The data indexed by the date
     */
    public function getExRsRangeOfDatesData(string $date, string $endDate): array
    {
        while ($date <= $endDate) {
            $url = (string) "https://openexchangerates.org/api/historical/$date.json?app_id=" . self::exchangeRatesApiKey;
            $this->data[(string) $date] = $this->exchangeRatesLiveData ? $this->dataLoader->getDataDirectlyFromApi($url) : $this->dataLoader->getDataOnlineAndSaveForNextUse($url, $date, $endDate);
            
            $date = (string) date('Y-m-d', strtotime($date . ' +1 day'));
        }

        return $this->data;
    }
}

