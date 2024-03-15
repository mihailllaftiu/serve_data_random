-- Drop database if exists
DROP DATABASE IF EXISTS exchange_db;

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS exchange_db 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci 
    ENGINE = InnoDB;

-- Use the exchange_db database
USE exchange_db;

-- Drop table if exists
DROP TABLE IF EXISTS currency_rates;

-- Create currency_rates table
CREATE TABLE IF NOT EXISTS currency_rates (
    currency_date DATE,
    currency_symbol VARCHAR(50),
    currency_rate DECIMAL(21, 15),
    PRIMARY KEY (currency_date, currency_symbol)
);

-- Drop view if exists
DROP VIEW IF EXISTS monthly_currency_rates;

-- Create monthly_currency_rates view
CREATE VIEW monthly_currency_rates AS
SELECT 
    DATE_FORMAT(currency_date, '%Y-%m-01') AS month,
    currency_symbol,
    MIN(currency_rate) AS minimum,
    MAX(currency_rate) AS maximum,
    ROUND(AVG(currency_rate), 15) AS average 
FROM 
    currency_rates
GROUP BY 
    DATE_FORMAT(currency_date, '%Y-%m-01'), currency_symbol;

-- Drop view if exists
DROP VIEW IF EXISTS todays_currency_rates;

-- Create todays_currency_rates view
CREATE VIEW IF NOT EXISTS todays_currency_rates AS
SELECT 
    currency_date,
    currency_symbol,
    currency_rate
FROM 
    currency_rates
WHERE 
    currency_date = CURRENT_DATE;
