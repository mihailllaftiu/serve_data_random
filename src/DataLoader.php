<?php
require_once 'DataFetcher.php';

class DataLoader
{

    private DataFetcher $dataFetcher;

    /**
     * A description of the entire PHP function.
     *
     * @param datatype $paramname description
     * @throws Some_Exception_Class description of exception
     * @return Some_Return_Value
     */
    public function __construct()
    {
        $this->dataFetcher = new DataFetcher();
    }

    /**
     * Get data online and save for next use.
     *
     * @param string $url description
     * @param string $date description
     * @param string|null $endDate description
     * @throws Some_Exception_Class description of exception
     * @return Some_Return_Value
     */
    public function getDataOnlineAndSaveForNextUse(string $url, string $date, ?string $endDate = null): array
    {
        if (!$this->dataFetcher->checkWhetherFilePathExists($url, $date)) {
            echo ">> Downloading data from: " . $url . " \n";
            return $this->dataFetcher->downloadAndUseTheseData($url, $date);
        } else {
            echo ">> Using existing data from: " . $this->dataFetcher->formatFilePath($date, $url) . " \n";
            return $this->dataFetcher->useDownloadedData($url, $date);
        }
    }

    /**
     * A function to get data directly from an API.
     *
     * @param string $url The URL of the API to fetch data from
     * @return array The data fetched from the API
     */
    public function getDataDirectlyFromApi(string $url): array
    {
        echo ">> By the API: " . $url . " \n";
        return $this->dataFetcher->downloadDirectlyThroughApi($url);
    }
}
