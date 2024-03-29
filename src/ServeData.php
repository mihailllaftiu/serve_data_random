<?php
require_once 'DataProcessor.php';

class ServeData
{
    private $dataProcessor;
    
    /**
     * Constructor for the class.
     *
     * This constructor sets the DataProcessor object used by the class.
     *
     * @param DataProcessor|null $dataProcessor An optional DataProcessor object.
     *     If not provided, a new instance of DataProcessor will be created.
     */
    public function __construct(DataProcessor $dataProcessor = null)
    {
        // Check if running from CLI
        if (php_sapi_name() !== 'cli') {
            echo "> This script must be run from the command line\n";
            return;
        }

        $this->dataProcessor = $dataProcessor ?? new DataProcessor();
    }


    /**
     * Main function for running the script.
     *
     * This function gets called when the script is executed from the command line.
     * It parses the provided command line arguments and passes them to the DataProcessor
     * to process the data.
     *
     * @param string[] $argv The command line arguments.
     * @return void
     */
    public function run(array $argv): void
    {
        // Get CLI arguments
        $startDate = null;
        $endDate = null;
        $source = null;

        // Extract command-line arguments
        foreach ($argv as $arg) {
            if (strpos($arg, 'start_date=') !== false) {
                // If a start date is provided, set the variable to its value,
                // otherwise set it to 'latest'
                $startDate = substr($arg, strpos($arg, '=') + 1) ?? $startDate = date('Y-m-d');
            } else if ($startDate === null) {
                $startDate = $startDate = date('Y-m-d');
            }
            if (strpos($arg, 'end_date=') !== false) {
                // If an end date is provided, set the variable to its value,
                // otherwise set it to an empty string
                $endDate = substr($arg, strpos($arg, '=') + 1) ?? '';
            }
            if (strpos($arg, 'source=') !== false) {
                // If a source is provided, set the variable to its value,
                // otherwise set it to 'empty'
                $source = substr($arg, strpos($arg, '=') + 1) ?? 'empty';
            }
        }

        // Pass the arguments to the data processor and let it do its magic
        $this->dataProcessor->processData($startDate, $endDate, $source);
    }
}

// Instantiate ServeData and run main function
if (empty($argv)) { echo "Usage: php ServeData.php start_date=YYYY-MM-DD (optional) end_date=YYYY-MM-DD (optional) source=empty (optional)\n"; return; }
$dataProcessor = new DataProcessor();
$serveData = new ServeData($dataProcessor);
$serveData->run($argv);
