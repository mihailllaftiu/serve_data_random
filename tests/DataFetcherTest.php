<?php

namespace Tests;

use App\Handlers\DataFetcher;
use PHPUnit\Framework\TestCase;

class DataFetcherTest extends TestCase
{
    private $dataFetcher;

    protected function setUp(): void
    {
        $this->dataFetcher = new DataFetcher();
    }

    public function testDownloadAndUseTheseData()
    {
        // Mock the response from the API
        $mockApiResponse = [
            'rates' => [
                'USD' => 1,
                'EUR' => 0.91285,
                'AED' => 3.6728,
                'AFN' => 70.000001,
            ],
            'disclaimer' => 'Usage subject to terms: https://openexchangerates.org/terms',
            'license' => 'https://openexchangerates.org/license',
            'timestamp' => 1704585586,
            'base' => 'USD',
        ];

        $this->dataFetcher->downloadDirectlyThroughApi = function ($url) use ($mockApiResponse) {
            if ($url === 'https://openexchangerates.org/api/historical/2024-01-06.json?app_id=') { // fill with the api key
                return $mockApiResponse;
            } else {
                throw new \Exception("cURL error: Could not resolve host: $url");
            }
        };

        $date = '2024-01-06';
        $url = 'https://openexchangerates.org/api/historical/2024-01-06.json?app_id='; // fill with the api key
        $data = $this->dataFetcher->downloadAndUseTheseData($url, $date);

        // Check if the result returned is an array
        $this->assertIsArray($data);

        // Check if the file exists in the temporary directory
        $filePath = $this->dataFetcher->formatFilePath($date, $url);
        $this->assertFileExists($filePath);
    }
}
