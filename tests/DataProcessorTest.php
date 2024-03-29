<?php
require_once  __DIR__ . '/../src/DataProcessor.php';
require_once  __DIR__ . '/../src/DataProcessor.php';

use PHPUnit\Framework\TestCase;

class DataProcessorTest extends TestCase
{
    public function testProcessExchangeRateDataWithLatestDate()
    {
        // Mock DataProvider and DataStore
        $dataProviderMock = $this->getMockBuilder(DataProvider::class)
                                 ->getMock();
        $dataStoreMock = $this->getMockBuilder(DataStore::class)
                              ->getMock();

        // Set up expectations for DataProvider
        $dataProviderMock->expects($this->once())
                         ->method('getExRsLatestDateData')
                         ->willReturn(['2024-03-28' => ['rates' => ['USD' => 1.23, 'EUR' => 0.89]]]);

        // Set up expectations for DataStore
        $dataStoreMock->expects($this->exactly(2)) // Expect two calls to saveCurrencyRatesInDB
                      ->method('saveCurrencyRatesInDB');

        // Create DataProcessor instance with mocked dependencies
        $dataProcessor = new DataProcessor();
        $dataProcessor->setDataProvider($dataProviderMock);
        $dataProcessor->setDataStore($dataStoreMock);

        // Call processData with null start date and ensure proper processing
        $this->expectOutputString("Execution time: \n\n");
        $dataProcessor->processData(null, null, 'exchangerate');
    }

    public function testProcessExchangeRateDataWithRangeOfDates()
    {
        // Mock DataProvider and DataStore
        $dataProviderMock = $this->getMockBuilder(DataProvider::class)
                                 ->getMock();
        $dataStoreMock = $this->getMockBuilder(DataStore::class)
                              ->getMock();

        // Set up expectations for DataProvider
        $dataProviderMock->expects($this->once())
                         ->method('getExRsRangeOfDatesData')
                         ->willReturn(['2024-03-28' => ['rates' => ['USD' => 1.23, 'EUR' => 0.89]]]);

        // Set up expectations for DataStore
        $dataStoreMock->expects($this->exactly(2)) // Expect two calls to saveCurrencyRatesInDB
                      ->method('saveCurrencyRatesInDB');

        // Create DataProcessor instance with mocked dependencies
        $dataProcessor = new DataProcessor();
        $dataProcessor->setDataProvider($dataProviderMock);
        $dataProcessor->setDataStore($dataStoreMock);

        // Call processData with start and end dates and ensure proper processing
        $this->expectOutputString("Execution time: \n\n");
        $dataProcessor->processData('2024-03-01', '2024-03-28', 'exchangerate');
    }

    // More test cases can be added to cover other scenarios such as processing data for a specific date, handling exceptions, etc.
}
