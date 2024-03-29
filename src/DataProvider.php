<?php
require_once 'DataLoader.php';

class DataProvider
{
    const exchangeRatesApiKey = ''; // API Key
    private $onlyLiveData; // = true; -> only real time data for every API
    private $exchangeRatesLiveData = false; // = true/false -> real time data, or downloaded data, for the specific API only
    private $data = [];
    private $dataLoader;

    public function __construct()
    {
        $this->dataLoader = new DataLoader();
        $this->exchangeRatesLiveData = $this->onlyLiveData ?? $this->exchangeRatesLiveData;

        if (empty(self::exchangeRatesApiKey)) {
            error_log('API Key not set');
            throw new \Exception('API Key not set');
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

