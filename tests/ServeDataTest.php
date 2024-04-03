<?php
declare(strict_types=1);
require_once realpath('vendor/autoload.php');
require_once __DIR__ . '/../ServeData.php';

class ServeDataTest extends \PHPUnit\Framework\TestCase
{
    /** @var ServeData $serveData */
    private $serveData;

    public function setUp(): void
    {
        $this->serveData = new ServeData();
    }

    public function testRun_ValidArguments_ShouldThrowEndDateFormatException()
    {
        $startDate = '2024-04-02';
        $invalidEndDate = 'invalid_date'; // Set an invalid end date
        $source = 'ExchangeRate';
        $argv = [$startDate, $invalidEndDate, $source];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('End date must be in the format YYYY-MM-DD');
        $this->serveData->run($argv);
    }
}
