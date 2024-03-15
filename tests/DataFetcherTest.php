<?php
require_once dirname(__FILE__) . '/src/DataFetcher.php';
use PHPUnit\Framework\TestCase;

class DataFetcherTest extends TestCase
{
    private $dataFetcher;

    protected function setUp(): void
    {
        $this->dataFetcher = new DataFetcher();
    }

    public function testDownloadDirectlyThroughApi()
    {
        // Mock a URL for testing
        $date = '2024-01-01';
        $url = 'https://openexchangerates.org/api/historical/'.$date.'.json';

        // Call the method being tested
        $result = $this->dataFetcher->downloadDirectlyThroughApi($url);
        $this->assertIsArray($result);
    }

    public function testDownloadAndUseTheseData()
    {
        // Mock URL and date for testing
        $date = '2024-01-01';
        $url = 'https://openexchangerates.org/api/historical/'.$date.'.json';

        // Call the method being tested
        $result = $this->dataFetcher->downloadAndUseTheseData($url, $date);
        $this->assertIsArray($result);
    }
}
