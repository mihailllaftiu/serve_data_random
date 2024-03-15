# Exchange Rate Data Processor

## Overview
This PHP application serves as a data processor for exchange rate information sourced from the Open Exchange Rates API. It facilitates the retrieval, processing, and storage of exchange rate data based on specified criteria such as date range and source.

## Features
- **Data Retrieval**: Utilizes the Open Exchange Rates API to fetch exchange rate data either directly or through saved local files.
- **Data Processing**: Processes retrieved data to save currency rates into a MySQL database.
- **CLI Interface**: Allows interaction with the application via the command-line interface, supporting options for specifying start date, end date, and data source.
- **Error Handling**: Ensures robust error handling, including validation of input parameters and handling of connection errors.

## Components
- **ServeData Class**: Handles CLI execution, argument parsing, and initiation of data processing.
- **DataProcessor Class**: Processes retrieved data, performs validations, and saves currency rates into the database.
- **DataProvider Class**: Retrieves exchange rate data from the Open Exchange Rates API based on specified criteria.
- **DataLoader Class**: Facilitates data retrieval either directly from the API or through saved local files.
- **DataFetcher Class**: Manages the download and processing of data, including handling CURL requests and file operations.
- **DataDB Class**: Provides an abstract interface for database connectivity and query execution.

## Usage
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


## Requirements
- PHP 7.0 or higher
- MySQL database (PostgreSQL, supported also)

## License
This project is licensed under the [MIT License](LICENSE).
