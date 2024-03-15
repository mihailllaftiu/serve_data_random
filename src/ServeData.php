<?php
require_once 'DataProcessor.php';

class ServeData
{
    private $dataProcessor;
    
    public function __construct(DataProcessor $dataProcessor = null)
    {
        // Check if running from CLI
        if (php_sapi_name() !== 'cli') {
            echo "> This script must be run from the command line\n";
            return;
        }

        $this->dataProcessor = $dataProcessor;
    }

    public function run($argv)
    {
        $numArgs = count($argv);
        
        // Get CLI arguments
        $startDate = null;
        $endDate = null;
        $source = null;

        // Extract command-line arguments
        foreach ($argv as $arg) {
            if (strpos($arg, 'start_date=') !== false) {
                if (substr($arg, strpos($arg, '=') + 1) !== '') $startDate = substr($arg, strpos($arg, '=') + 1) ?? 'latest';
            } elseif (strpos($arg, 'end_date=') !== false) {
                if (substr($arg, strpos($arg, '=') + 1) !== '') $endDate = substr($arg, strpos($arg, '=') + 1) ?? '';
            } elseif (strpos($arg, 'source=') !== false) {
                if (substr($arg, strpos($arg, '=') + 1) !== '') $source = substr($arg, strpos($arg, '=') + 1) ?? 'empty';
            }
        }

        return $this->dataProcessor->processData($startDate, $endDate, $source);
    }
}

// Instantiate ServeData and run main function
if (empty($argv)) { echo "Usage: php ServeData.php start_date=YYYY-MM-DD (optional) end_date=YYYY-MM-DD (optional) source=empty (optional)\n"; return; }
$dataProcessor = new DataProcessor();
$serveData = new ServeData($dataProcessor);
$serveData->run($argv);
