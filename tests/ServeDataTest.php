<?php
require_once dirname(__FILE__) . '/src/DataProcessor.php';
require_once dirname(__FILE__) . '/src/ServeData.php';

use PHPUnit\Framework\TestCase;

class ServeDataTest extends TestCase
{
    private $dataProcessorMock;

    protected function setUp(): void
    {
        $this->dataProcessorMock = $this->createMock(DataProcessor::class);
    }

    public function testRunExitsIfNotCLI()
    {
        $serveData = new ServeData($this->dataProcessorMock);

        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->expectOutputString("> This script must be run from the command line\n");
        $this->assertNull($serveData->run([]));
    }

    public function testRunExtractsArguments()
    {
        $serveData = new ServeData($this->dataProcessorMock);

        $argv = [
            'serveData.php',
            'start_date=',
            'end_date=',
            'source='
        ];

        // Instruct the mock to return a specific value when processData is called
        $this->dataProcessorMock->expects($this->once())
            ->method('processData')
            ->with('', '', '')
            ->willReturn(true); // Or any desired return value

        $serveData->run($argv);
    }
}