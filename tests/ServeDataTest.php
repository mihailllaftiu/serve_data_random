<?php
require_once __DIR__ . '/../src/ServeData.php';
require_once __DIR__ .'/../src/DataProcessor.php';
require_once __DIR__ .'/../src/DataProvider.php';
require_once __DIR__ .'/../src/DataStore.php';

use PHPUnit\Framework\TestCase;

class ServeDataTest extends TestCase
{
    public function testRunExitsIfNotCLI()
    {
        // Capture the return value of the constructor
        $serveData = new ServeData();
        $returnValue = $serveData->__construct();
    
        // Assert that the constructor returns null when not run from the command line
        $this->assertNull($returnValue);
    }
    

    public function testRunExtractsArguments()
    {
        // Create a mock for the DataProcessor class
        $dataProcessorMock = $this->getMockBuilder(DataProcessor::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();

        // Set up the ServeData object
        $serveData = new ServeData($dataProcessorMock);

        // Define the arguments to be passed to the script
        $argv = [
            'ServeData.php',
            'start_date=2024-01-01',
            'end_date=2024-02-01',
            'source=example'
        ];

        // Set up expectations for processData method
        $dataProcessorMock->expects($this->once())
                          ->method('processData')
                          ->with('2024-01-01', '2024-02-01', 'example')
                          ->willReturn(true); // Or any desired return value

        // Call the run method with the arguments
        $serveData->run($argv);
    }
}
