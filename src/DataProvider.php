<?php
require_once 'DataLoader.php';

class DataProvider
{
    const exchangeRatesApiKey = ''; // API Key
    private $onlyLiveData; // = true; -> only real time data for every API
    private $exchangeRatesLiveData = true; // = true/false -> real time data, or downloaded data, for the specific API only
    private $data = [];
    private $dataLoader;

    public function __construct()
    {
        $this->dataLoader = new DataLoader();
        $this->exchangeRatesLiveData = $this->onlyLiveData ?? $this->exchangeRatesLiveData;
    }

    public function getExRsLatestDateData($date)
    {
        $url = "https://openexchangerates.org/api/$date.json?app_id=" . self::exchangeRatesApiKey;
        $date = date('Y-m-d'); // as we want to print everywhere the current date, instead of the latest
        $this->data[$date] = $this->exchangeRatesLiveData ? $this->dataLoader->getDataDirectlyFromApi($url) : $this->dataLoader->getDataOnlineAndSaveForNextUse($url, $date);
        return $this->data;
    }

    public function getExRsSpecificDateData($date)
    {
        $url = "https://openexchangerates.org/api/historical/$date.json?app_id=" . self::exchangeRatesApiKey;
        $this->data[$date] = $this->exchangeRatesLiveData ? $this->dataLoader->getDataDirectlyFromApi($url) : $this->dataLoader->getDataOnlineAndSaveForNextUse($url, $date);
        return $this->data;
    }

    public function getExRsRangeOfDatesData($date, $endDate = null)
    {
        while ($date <= $endDate) {
            $url = "https://openexchangerates.org/api/historical/$date.json?app_id=" . self::exchangeRatesApiKey;
            $this->data[$date] = $this->exchangeRatesLiveData ? $this->dataLoader->getDataDirectlyFromApi($url) : $this->dataLoader->getDataOnlineAndSaveForNextUse($url, $date, $endDate);
            $date = date('Y-m-d', strtotime($date . ' +1 day'));
        }

        return $this->data;
    }
}

