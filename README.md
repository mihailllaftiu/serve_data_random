# 📊 Serve Data

## 🚀 Overview
This PHP application serves as a data processor for exchange rate information sourced from the Exchange Rates. It facilitates the retrieval, processing, and storage of exchange rate data based on specified criteria such as date range and source. The logic of the script is designed to handle multiple exchange rates data efficiently. Each class within the application is structured to perform specific tasks, allowing for modularity and extensibility. This design enables classes to be easily called or extended in related contexts, enhancing the overall flexibility and maintainability of the codebase.

## 🌟 Features
- **Data Retrieval**: Utilizes the Open Exchange Rates API to fetch exchange rate data either directly or through saved local files.
- **Data Processing**: Processes retrieved data to save currency rates into a MySQL database.
- **CLI Interface**: Allows interaction with the application via the command-line interface, supporting options for specifying start date, end date, and data source.
- **Error Handling**: Ensures robust error handling, including validation of input parameters and handling of connection errors.

## 🛠️ Components
- **ServeData Class**: Handles CLI execution, argument parsing, and initiation of data processing.
  - **__construct**: Initializes a ServeData object with a DataProcessor instance. Checks if the script is run from the command line interface.
  - **run**: Parses command-line arguments and invokes the DataProcessor to process data based on provided arguments. This function serves as the main entry point for executing the data processing functionality of the application.

- **DataProcessor Class**: Processes retrieved data, performs validations, and saves currency rates into the database.
  - **__construct**: Initializes a DataProcessor object with DataProvider and DataStore instances. This function sets up the DataProcessor class with necessary dependencies for data retrieval and storage.
  - **processData**: Processes exchange rate data based on provided criteria such as start date, end date, and source. Handles error checking and calls relevant methods in DataProvider to retrieve data and DataStore to save it. This function serves as the main processing logic for exchange rate data.
  - **processExchangeRateData**: Processes exchange rate data specifically, including fetching data from DataProvider and saving it using DataStore. This function encapsulates the specific logic for handling exchange rate data processing.
  - **catchProblems**: Checks for and handles various potential issues, such as invalid date formats or end date being before start date. This function ensures robust error handling and data validation within the data processing pipeline.
  
- **DataProvider Class**: Retrieves exchange rate data from the Open Exchange Rates API based on specified criteria.
  - **__construct**: Initializes a DataProvider object with a DataLoader instance. This function sets up the DataProvider class with a DataLoader dependency for data retrieval.
  - **getExRsLatestDateData**: Retrieves exchange rate data for the latest date from the Open Exchange Rates API. This function fetches the latest exchange rate data based on the provided criteria and returns it.
  - **getExRsSpecificDateData**: Retrieves exchange rate data for a specific date from the Open Exchange Rates API. This function fetches exchange rate data for a specific date and returns it.
  - **getExRsRangeOfDatesData**: Retrieves exchange rate data for a range of dates from the Open Exchange Rates API. This function fetches exchange rate data for a range of dates and returns it as an array.

- **DataLoader Class**: Facilitates data retrieval either directly from the API or through saved local files.
  - **__construct**: Initializes a DataLoader object with a DataFetcher instance. This function sets up the DataLoader class with a DataFetcher dependency for data retrieval.
  - **getDataOnlineAndSaveForNextUse**: Retrieves data from the API or saved files, depending on availability, and saves the data for future use. This function facilitates data retrieval either directly from the API or through saved local files, ensuring efficient data handling.
  - **getDataDirectlyFromApi**: Retrieves data directly from the API. This function sends a request to the API and fetches data directly.
  - **checkWhetherFilePathExists**: Checks if the file path for saved data exists. This function verifies the existence of saved data files.
  - **formatFilePath**: Formats the file path for saved data based on the URL and date. This function constructs the file path for saved data files.
  - **checkIfTmpFolderExists**: Checks if the temporary folder for saved files exists. This function ensures the existence of the temporary folder for saved files.
  - **getDomainNameFromUrl**: Extracts the domain name from a given URL. This function parses the URL and extracts the domain name.
  
- **DataFetcher Class**: Manages the download and processing of data, including handling CURL requests and file operations.
    - **__construct**: Initializes a DataFetcher object with CURL settings. This function sets up the DataFetcher class with CURL settings for handling HTTP requests.
    - **downloadDirectlyThroughApi**: Downloads data directly from the API using CURL. This function sends a CURL request to the API and retrieves data directly.
    - **downloadAndUseTheseData**: Downloads data from the API and saves it for future use. This function manages the download of data from the API and saves it for future use, handling file operations.
    - **useDownloadedData**: Uses downloaded data from saved files. This function retrieves and uses downloaded data from saved files, facilitating data retrieval for subsequent usage.
    - **checkWhetherFilePathExists**: Checks if the file path for saved data exists. This function verifies the existence of saved data files.
    - **formatFilePath**: Formats the file path for saved data based on the URL and date. This function constructs the file path for saved data files.
    - **checkIfTmpFolderExists**: Checks if the temporary folder for saved files exists. This function ensures the existence of the temporary folder for saved files.
    - **getDomainNameFromUrl**: Extracts the domain name from a given URL. This function parses the URL and extracts the domain name.
    - **__destruct**: Cleans up CURL handles and resources upon object destruction. This function ensures proper cleanup of CURL handles and resources.
      
- **DataDB Class**: Provides an abstract interface for database connectivity and query execution.
  - **Abstract class**: Provides an abstract interface for database connectivity and query execution. This class serves as a blueprint for implementing database connectivity and query execution functionality.**connect**: Abstract method for connecting to a database. This method defines the interface for establishing a connection to a database and is intended to be implemented by subclasses.
  - **saveCurrencyRatesInDB**: Abstract method for saving currency rates into a database. This method defines the interface for saving currency rates into a database and is intended to be implemented by subclasses.
  - **runQueries**: Executes SQL queries using PDO. This method executes SQL queries using PDO (PHP Data Objects) for database interaction, providing a standardized way to run queries across different database drivers.
  - **connectViaPDO**: Connects to a database using PDO. This method establishes a connection to a database using PDO (PHP Data Objects), providing a flexible and secure approach to database connectivity.
  - **dbCredentials**: Returns database connection credentials based on the specified driver. This method retrieves database connection credentials based on the specified driver, allowing for dynamic configuration of database connections.   

## 💻 Usage
To use the application, execute script based on the bellow usage(s), from the command line.
> [!NOTE]
> Default usage (start_date is optional, as when it's there it gets automatically the todays data)
```
php src/ServeData.php
```

> [!TIP]
> You can add the following arg(s) for further controll
> - start_date=0000-00-00
> - end_date=0000-00-00 (optional)
> - source=example (optional)


## 📄 Requirements
- PHP 7.0 or higher
- MySQL database (PostgreSQL, supported also)

## License
This project is licensed under the MIT License
