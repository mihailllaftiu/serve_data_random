-- Drop table if exists
DROP TABLE IF EXISTS currency_rates;

-- Create currency_rates table
CREATE TABLE IF NOT EXISTS currency_rates (
    currency_date DATE,
    currency_symbol VARCHAR(50),
    currency_rate DECIMAL(21, 15),
    PRIMARY KEY (currency_date, currency_symbol)
);

-- Drop materialized view if exists
DROP MATERIALIZED VIEW IF EXISTS monthly_currency_rates;

-- Create monthly_currency_rates materialized view
CREATE MATERIALIZED VIEW monthly_currency_rates AS
SELECT 
    DATE_TRUNC('month', currency_date) AS month,
    currency_symbol,
    MIN(currency_rate) AS minimum,
    MAX(currency_rate) AS maximum,
    ROUND(AVG(currency_rate), 15) AS average 
FROM 
    currency_rates
GROUP BY 
    DATE_TRUNC('month', currency_date), currency_symbol;

-- Drop materialized view if exists
DROP MATERIALIZED VIEW IF EXISTS todays_currency_rates;

-- Create todays_currency_rates materialized view
CREATE MATERIALIZED VIEW todays_currency_rates AS
SELECT 
    currency_date,
    currency_symbol,
    currency_rate
FROM 
    currency_rates
WHERE 
    currency_date = CURRENT_DATE;
