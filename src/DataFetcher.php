<?php
class DataFetcher
{
    const STORE_DIR = 'tmp/';

    private $curlMulti;
    private $handles;

    public function __construct()
    {
        $this->curlMulti = curl_multi_init();
        $this->handles = [];
    }

    public function downloadDirectlyThroughApi($url): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"]
        ]);

        curl_multi_add_handle($this->curlMulti, $ch);
        $this->handles[] = $ch;

        do {
            curl_multi_exec($this->curlMulti, $active);
            usleep(10000); // Sleep for a short time to avoid high CPU usage
        } while ($active > 0);

        $response = curl_multi_getcontent($ch);
        curl_multi_remove_handle($this->curlMulti, $ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data;
    }

    public function downloadAndUseTheseData($url, $date, $endDate = null): array
    {
        $response = $this->downloadDirectlyThroughApi($url);
        $pathFile = $this->formatFilePath($url, $date, $endDate);
        file_put_contents($pathFile, json_encode($response, JSON_PRETTY_PRINT));

        echo ">> Data fetched successfully\n\n";
        return $this->useDownloadedData($url, $date, $endDate);
    }

    public function useDownloadedData($url, $date, $endDate = null): array
    {
        $filePath = $this->formatFilePath($url, $date, $endDate);
        return json_decode(file_get_contents($filePath), true, 512, JSON_OBJECT_AS_ARRAY);
    }

    public function checkWhetherFilePathExists($date, $endDate = null): bool
    {
        $this->checkIfTmpFolderExists();
        $filePath = $this->formatFilePath($date, $endDate);
        return file_exists($filePath);
    }

    public function formatFilePath($url, $date, $endDate = null): string
    {
        $source = $this->getDomainNameFromUrl($url);
        return self::STORE_DIR . $source . "_" . $date . ($endDate ? "_$endDate" : "") . ".json";
    }

    public function checkIfTmpFolderExists()
    {
        if (!file_exists(self::STORE_DIR)) {
            mkdir(self::STORE_DIR, 0777, true); // Recursive directory creation
        }
    }

    public function getDomainNameFromUrl($url): string
    {
        // Parse the URL
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'];
        $parts = explode('.', $host);
        $domainName = $parts[0];

        return $domainName;
    }

    public function __destruct()
    {
        foreach ($this->handles as $handle) {
            curl_multi_remove_handle($this->curlMulti, $handle);
            curl_close($handle);
        }
        curl_multi_close($this->curlMulti);
    }
}
