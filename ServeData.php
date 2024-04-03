<?php
require_once realpath('vendor/autoload.php');

class ServeData
{      
    /**
     * true: only real time data for each API
     * false: use downloaded data
     */
    protected $onlyLiveData = false; 

    /**
     * Constructor
     *
     * Checks if the script is running from the command line interface.
     *
     * @throws Exception if the script is not running from the command line
     */
    public function __construct()
    {
        // Check if running from CLI
        if (php_sapi_name() !== 'cli') {
            // Print warning message
            error_log("> This script must be run from the command line\n", 0);

            // Throw exception
            throw new \Exception('This script must be run from the command line');
        }
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
        // Handle case when no command line arguments are provided
        if (empty($argv)) {
            // Print usage message
            echo "Usage: php ServeData.php start_date=YYYY-MM-DD (optional) end_date=YYYY-MM-DD (optional) source=empty (optional)\n";
            return;
        }

        // Pre-define CLI arguments, if they are no in name value pairs
        $startDate = $argv[1] ?? null;
        $endDate = $argv[2] ?? null;
        $source = trim($argv[3] ?? 'Exchangerate');

        // Extract command-line argument values, if they are in name-value pairs
        foreach ($argv as $arg) {
            if (strpos($arg, 'start_date=') !== false) {
                /**
                 * Set the start date to be used in the data retrieval and processing.
                 * If not provided, it will be set to the current date by default.
                 */
                $startDate = substr($arg, strpos($arg, '=') + 1);
            } elseif (empty($argv[1]) && $startDate === null) {
                /**
                 * If the start date is not provided at all, set it to the current date
                 */
                $startDate = date('Y-m-d');
            }
            if (strpos($arg, 'end_date=') !== false) {
                /**
                 * Set the end date to be used in the data retrieval and processing.
                 * If not provided, it will be set to an empty string by default.
                 */
                $endDate = substr($arg, strpos($arg, '=') + 1) ?? '';
            }
            if (strpos($arg, 'source=') !== false) {
                /**
                 * Set the data source to be used in the data retrieval and processing.
                 * If not provided, it will keep 'Exchangerate' by default.
                 */
                $source = substr($arg, strpos($arg, '=') + 1);
            }
        }

        // Catch misstypes
        /**
         * Throw exceptions if the provided start date, end date or source is not of the
         * right format.
         */
        $this->catchCLIMisstype($startDate, $endDate, $source);

        // Prepair providers
        /**
         * Create a new DataProcessor instance and prepare the providers for the
         * provided start date, end date and data source.
         */
        $this->callRelevantProvider($startDate, $endDate, $source);
    }

    /**
     * Processes data within a specified date range and source.
     *
     * This method retrieves data from the external provider API and stores it in
     * the database based on the given start and end dates and source.
     *
     * @param string $startDate The start date of the data range (format YYYY-MM-DD) for today.
     * @param string|null $endDate The end date of the data range (format YYYY-MM-DD) or null for $startDate.
     * @param string $source The source of the data (optional).
     *
     * @return void
     */
    function callRelevantProvider(string $startDate, ?string $endDate = null, string $source): void
    {
        // Start timer for execution time
        $startTime = microtime(true);

        try {
            /**
             * Dynamically require the file for the class based on the given source
             */
            $className = ucfirst(strtolower($source)); // Convert source to class name, with first letter capitalized
            $classFile = __DIR__ . "/app/Providers/{$className}.php"; // Assuming the class files are in the same directory as this script

            if (file_exists($classFile)) {
                // Adjust the class name to include namespace
                $fullClassName = "App\\Providers\\{$className}";

                if (class_exists($fullClassName)) {
                    /**
                     * Create a new instance of the class and call its constructor, passing
                     * in the $onlyLiveData, $startDate and $endDate parameters
                     */
                    (new $fullClassName())->collectDataFromProvider($this->onlyLiveData, $startDate, $endDate);
                } else {
                    throw new \Exception("Class {$fullClassName} not found in file {$classFile}");
                }
            } else {
                error_log("Class file for {$className} not found. Make sure that the source (provider) is the same as the file/class you're asking for", 0);
                throw new \Exception("Class file for {$className} not found. Make sure that the source (provider) is the same as the file/class you're asking for");
            }
        } catch (\Exception $e) {
            error_log($e->getMessage(), 0);
            throw new \Exception($e->getMessage());
        }

        // End timer for execution time
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Print execution time
        echo "Execution time: {$executionTime}\n\n";
    }

    /**
     * Function to catch misstypes in CLI arguments
     *
     * This function checks if the CLI arguments are in the correct format,
     * specifically checks if the start and end dates are in the format Y-m-d
     * and if the start date is before the end date.
     *
     * @param string $startDate The start date to check (format Y-m-d)
     * @param null|string $endDate The end date to check (format Y-m-d or null)
     * @param string $source The source to check (string or 'Exchangerate' by default)
     *
     * @return void
     */
    private function catchCLIMisstype(string $startDate, ?string $endDate, string $source): void
    {
        // Check if end date is in the format Y-m-d
        if ($endDate !== null && !\DateTimeImmutable::createFromFormat('Y-m-d', $endDate)) {
            error_log(">> End date must be in the format YYYY-MM-DD", 0);
            throw new \Exception('End date must be in the format YYYY-MM-DD');
        }

        // Check if start date is in the format Y-m-d
        if (!\DateTimeImmutable::createFromFormat('Y-m-d', $startDate)) {
            error_log(">> Start date must be in the format YYYY-MM-DD", 0);
            throw new \Exception('Start date must be in the format YYYY-MM-DD');
        }

        // Check if start date is before end date
        if (($startDate !== 'latest' && $endDate !== null) &&
            (\DateTimeImmutable::createFromFormat('Y-m-d', $startDate) > \DateTimeImmutable::createFromFormat('Y-m-d', $endDate))) {
            error_log(">> Start date must be before end date", 0);
            throw new \Exception('Start date must be before end date');
        }

        // Check if source is a string
        if ($source !== null && is_numeric($source)) {
            error_log(">> Source must be a string", 0);
            throw new \Exception('Source must be a string');
        }
    }
}

// Instantiate ServeData and run main function
if (isset($argv)) {
    $serveData = new ServeData();
    $serveData->run($argv);
}
