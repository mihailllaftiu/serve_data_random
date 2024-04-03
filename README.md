## 🚀 Overview
This PHP application is designed to handle exchange rate data retrieval, processing, and storage. It utilizes the Open Exchange Rates API (as an example) for fetching exchange rate data and stores it in a MySQL database. The application consists of several classes, each responsible for specific tasks such as data retrieval, processing, and database interaction. Through modular design and encapsulation, the application achieves flexibility and maintainability, for the current example or for any other relevant ER platform that can be extended.

## 🌟 Features
- **Data Retrieval**: Fetch exchange rate data from the Open Exchange Rates API either directly or through saved local files.
- **Data Processing**: Process retrieved data to save currency rates into a MySQL database.
- **Error Handling**: Ensure robust error handling, including validation of input parameters and handling of connection errors.
- **CLI Interface**: Interact with the application via the command-line interface, supporting options for specifying start date, end date, and data source.

## 🛠️ Components
- **ServeData Class**: Responsible for CLI execution, argument parsing, and initiation of data processing.
  - **__construct**: Initializes a ServeData object and ensures that the script is run from the command line interface. If the script is not run from the command line, it throws an exception.
  - **run**: Parses command-line arguments and initiates the data processing by calling the relevant data provider based on the provided arguments.
  - **callRelevantProvider**: Dynamically requires the file for the class based on the given source and instantiates the class to initiate data collection and processing.
  - **catchCLIMisstype**: Validates the format and content of the command-line arguments to ensure they meet the required criteria, such as date format and data source validity.

- **Exchangerate Class**: Manages exchange rate data retrieval, processing, and storage.
  - **__construct**: Initializes a new Exchangerate object and checks if the API key for the Open Exchange Rates API is set. It also initializes instances of the DataQueries and - DataLoader classes.
  - **collectDataFromProvider**: Retrieves exchange rate data from the external provider API based on the specified start and end dates. It stores the retrieved data in the database.
  - **storeExchangeRatesDataInDB**: Stores the received exchange rates data in the database. It iterates through the data and saves each currency rate for each date in the database.
  - **getExRsSpecificDateData**: Retrieves specific exchange rate data from the Open Exchange Rates API for a given date. It returns the data indexed by the date.
  - **getExRsRangeOfDatesData**: Retrieves exchange rate data for a range of dates from the Open Exchange Rates API. It returns the data indexed by the date.

- **DataLoader Class**: Facilitates data retrieval either directly from the API or through saved local files.
  - **__construct**: Initializes a DataLoader object with a DataFetcher instance. This function sets up the DataLoader class with a DataFetcher dependency for data retrieval.
  - **getDataOnlineAndSaveForNextUse**: Retrieves data from the API or saved files based on the provided URL and date. If the data is not already saved locally, it downloads it from the API and saves it for future use. If the data is available locally, it uses the saved data. It returns the retrieved data.
  - **getDataDirectlyFromApi**: Retrieves data directly from the API using the provided URL. It sends a request to the API and fetches the data. It returns the fetched data.

- **DataQueries Class**: Handles database interaction for saving currency rates.
  - **connect**: Connects to the specified database driver using PDO. This method uses the PDO extension to connect to a database using a PDO driver. It accepts the name of the driver as a parameter and returns the database connection. If the driver name is not a string, it throws an InvalidArgumentException. If the connection fails, it throws a PDOException.
  - **saveCurrencyRatesInDB**: Saves currency rates in the database. This method checks if the currency rate for the given date and symbol already exists in the database. If it does not exist, it inserts a new record. Otherwise, it updates the existing one. It accepts the date, currency symbol, and rate as parameters and performs the database operation accordingly.

- **DataDB Class**: Provides an abstract interface for database connectivity and query execution.
  - **connect**: Abstract method for connecting to a database. This method defines the interface for establishing a connection to a database and is intended to be implemented by subclasses.
  - **saveCurrencyRatesInDB**: Abstract method for saving currency rates into a database. This method defines the interface for saving currency rates into a database and is intended to be implemented by subclasses.
  - **runQueries**: Executes SQL queries using PDO. This method executes SQL queries using PDO (PHP Data Objects) for database interaction, providing a standardized way to run queries across different database drivers. It accepts the SQL query and optional parameters as inputs and executes the query, handling any connection errors by throwing a PDOException.
  - **connectViaPDO**: Connects to a database using PDO. This method establishes a connection to a database using PDO (PHP Data Objects), providing a flexible and secure approach to database connectivity. It accepts the database driver as input and retrieves the necessary database credentials based on the driver. It constructs a DSN (Data Source Name) string and creates a new PDO instance for the database connection, setting the error mode to throw exceptions on error.
  - **dbCredentials**: Returns database connection credentials based on the specified driver. This method retrieves database connection credentials based on the specified driver, allowing for dynamic configuration of database connections. It returns an associative array containing the host, username, password, and database name based on the specified driver. If an unsupported database driver is specified, it throws an Exception.

## 💻 Usage
You have to setup your DB first, so the data have to be store somewhere.
> [!IMPORTANT]
> In the /setup you will find both mysql and postgresql queries, for DB/Tables/Views creation.

To use the application, execute script based on the bellow usage(s), from the command line.
> [!NOTE]
> Default usage (start_date is optional, as when it's not there it gets automatically the todays data)
```
php ServeData.php
```

> [!TIP]
> You can add the following arg(s) for further controll
> - start_date=0000-00-00 (optional)
> - end_date=0000-00-00 (optional)
> - source=example (optional)


## 📄 Requirements
- PHP 7.4 or higher
- cURL extension enabled
- MySQL or PostgreSQL database

## License
This project is licensed under the MIT License
