<?php
namespace App\Handlers;

class DataFetcher
{
    private $tmpDir;

    /**
     * DataFetcher constructor.
     *
     * Sets the temporary directory path.
     */
    public function __construct()
    {
        // Set the temporary directory path
        $this->tmpDir = dirname(__DIR__) . '/../tmp/';
    }

    /**
     * Download data directly from the API.
     *
     * This function downloads data from the API using cURL.
     * It returns the downloaded data in the form of an associative array.
     *
     * @param string $url The URL to download data from
     * @return array<string,mixed> The downloaded data in the form of an associative array
     * @throws \Exception If there is an error in the cURL request or
     *                          if there is an error in decoding the JSON response
     */
    public function downloadDirectlyThroughApi(string $url): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $response = curl_exec($ch);

        // Check for cURL errors
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            error_log("cURL error: " . $error);
            throw new \Exception("cURL error: $error");
        }

        curl_close($ch);
        $data = json_decode($response, true);

        return $data;
    }

    /**
     * Download and use data from the given URL and dates.
     *
     * This function downloads data from the given URL using the `downloadDirectlyThroughApi`
     * function and saves it to a file using the given dates as the file name.
     * It then reads the data from the file and returns it as an array.
     *
     * @param string $url The URL to download data from
     * @param string $date The date of the JSON file
     * @param string|null $endDate The end date of the JSON file (optional)
     * @return array The downloaded data from the file
     */
    public function downloadAndUseTheseData(string $url, string $date, ?string $endDate = null): array
    {
        $response = $this->downloadDirectlyThroughApi($url);
        $pathFile = $this->formatFilePath($date, $url, $endDate);
        file_put_contents($pathFile, json_encode($response, JSON_PRETTY_PRINT));

        echo ">> Data fetched successfully\n\n";
        return $this->useDownloadedData($url, $date, $endDate);
    }

    /**
     * Retrieves and decodes data from a downloaded JSON file.
     *
     * @param string $url The URL of the JSON file.
     * @param string $date The date of the JSON file.
     * @param string|null $endDate The end date of the JSON file (optional).
     * @return array The decoded JSON data.
     */
    public function useDownloadedData(string $url, string $date, ?string $endDate = null): array
    {
        $filePath = $this->formatFilePath($date, $url, $endDate);
        return json_decode(file_get_contents($filePath), true, 512, JSON_OBJECT_AS_ARRAY);
    }

    /**
     * Checks whether the file path exists for a given date and optional end date.
     *
     * @param string $url The URL to extract the domain name from.
     * @param string $date The date to check the file path for.
     * @param string|null $endDate The optional end date to check the file path for.
     * @return bool Returns true if the file path exists, false otherwise.
     */
    public function checkWhetherFilePathExists(string $url, string $date, ?string $endDate = null): bool
    {
        $this->checkIfTmpFolderExists();
        $filePath = $this->formatFilePath($date, $url, $endDate);
        return file_exists($filePath);
    }

    /**
     * Format file path based on URL, date, and optional end date.
     *
     * @param string $url The URL to extract the domain name from.
     * @param string $date The date to include in the file path.
     * @param string|null $endDate Optional end date to include in the file path.
     * @return string The formatted file path.
     */
    public function formatFilePath(string $date, string $url, ?string $endDate = null): string
    {
        $source = $this->getDomainNameFromUrl($url);
        return $this->tmpDir . $source . "_" . (string) $date . ($endDate ? "_" . (string) $endDate : "") . ".json";
    }

    /**
     * Check if the temporary folder exists and create it if necessary.
     *
     * This method checks if the temporary folder specified by the STORE_DIR class constant
     * exists. If the folder does not exist, it creates it recursively using the
     * mkdir function with the 0777 permission.
     *
     * @param string $dir The directory path to check.
     * @param int $mode The folder permissions to use when creating the folder.
     * @param bool $recursive Whether to create the folder recursively if it does not exist.
     * @return bool True if the folder was created or already exists, false otherwise.
     */
    public function checkIfTmpFolderExists(int $mode = 0777, bool $recursive = true): bool
    {
        return !file_exists($this->tmpDir) ? mkdir($this->tmpDir, $mode, $recursive) : true;
    }

    /**
     * Extract the domain name from a given URL.
     *
     * This method extracts the domain name from a given URL.
     * For example, if the given URL is "https://www.example.com", this method
     * will return "www".
     *
     * @param string $url The URL to extract the domain name from (e.g. "https://www.example.com").
     * @return string The extracted domain name (e.g. "www").
     */
    public function getDomainNameFromUrl(string $url): string
    {
        // Parse the URL
        $parsedUrl = parse_url($url); 
        $host = $parsedUrl['host'] ?? '';
        $parts = explode('.', $host); 
        $domainName = $parts[0] ?? '';

        return $domainName;
    }
}
