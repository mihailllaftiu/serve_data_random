<?php
require_once 'DataFetcher.php';

class DataLoader
{

    private DataFetcher $dataFetcher;

    public function __construct()
    {
        $this->dataFetcher = new DataFetcher();
    }

    public function getDataOnlineAndSaveForNextUse($url, $date, $endDate = null)
    {
        if (!$this->dataFetcher->checkWhetherFilePathExists($url, $date)) {
            echo ">> Downloading data from: " . $url . " \n";
            return $this->dataFetcher->downloadAndUseTheseData($url, $date);
        } else {
            echo ">> Using existing data from: " . $this->dataFetcher->formatFilePath($url, $date) . " \n";
            return $this->dataFetcher->useDownloadedData($url, $date);
        }
    }

    public function getDataDirectlyFromApi($url)
    {
        echo ">> By the API: " . $url . " \n";
        return $this->dataFetcher->downloadDirectlyThroughApi($url);
    }
}
