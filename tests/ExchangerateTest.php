<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Providers\Exchangerate;
use App\DB\DataQueries;
use App\Handlers\DataLoader;

final class ExchangerateTest extends TestCase
{
    public function testProcessExchangeRateStoresDataInDatabaseWhenNoDataRetrieved(): void
    {
        // Mock DataQueries and DataLoader objects
        $dataLoaderMock = $this->createMock(DataLoader::class);

        // Sample data to be returned by the DataLoader mock
        $sampleData = [
            '2024-04-01' => [
                'disclaimer' => 'Usage subject to terms: https://openexchangerates.org/terms',
                'license' => 'https://openexchangerates.org/license',
                'timestamp' => 1704153599,
                'base' => 'USD',
                'rates' => [
                    'AED' => 3.6728,
                ]
            ]
        ];

        // Set up expectations for the mock DataLoader object
        $dataLoaderMock->expects($this->once())
            ->method('getDataDirectlyFromApi')
            ->with('https://openexchangerates.org/api/historical/2024-04-01.json?app_id=' . Exchangerate::exchangeRatesApiKey)
            ->willReturn($sampleData);

        // Set up expectations for the mock DataQueries object using getMockBuilder
        $dataQueriesMock = $this->getMockBuilder(DataQueries::class)
            ->getMock();

        // Create an instance of Exchangerate class
        $exchangerate = new Exchangerate();
        $exchangerate->dataLoader = $dataLoaderMock;
        $exchangerate->dataQueries = $dataQueriesMock;

        // Expecting storeExchangeRatesDataInDB() not to be called when no data is retrieved
        $dataQueriesMock->expects($this->never())
            ->method('saveCurrencyRatesInDB');

        // No exceptions should be thrown when no data is retrieved
        try {
            // Call the method under test
            $exchangerate->collectDataFromProvider(true, '2024-04-01');
        } catch (\Exception $e) {
            $this->fail('No exception should be thrown when no data is retrieved');
        }
    }
}
