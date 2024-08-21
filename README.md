## PHP CSV Processor

This project is a PHP script designed to process a CSV file and insert the data into a PostgreSQL database. The script is executed from the command line and includes various options for creating the database table, running in dry-run mode, and more.

## Overview

The user_upload.php script is designed to handle the bulk uploading of user data from a CSV file into a PostgreSQL database. The script can validate email addresses, format names, and ensure that records are only inserted if they meet the required criteria. Additionally, it can create or rebuild the necessary database table before inserting records. This project is suitable for scenarios where bulk user data needs to be imported and processed efficiently.

## Features

- **CSV Parsing**: Reads user data from a CSV file.
- **Email Validation**: Ensures that only records with valid email addresses are inserted into the database.
- **Name Formatting**: Automatically capitalizes the first letter of names and surnames.
- **Dry Run Mode**: Allows you to simulate the upload process without modifying the database.
- **Database Table Creation**: Automatically creates or rebuilds the users table in PostgreSQL if needed.
- **Command-Line Interface**: Provides a command-line interface with multiple options for flexibility.

## Prerequisites

Before you can use the `user_upload.php` script, ensure you have the following:

- **PHP**: Version 8.2.x or higher installed on your system.
- **PostgreSQL**: Version 16.4 or higher installed and running.
- **CSV File**: A properly formatted CSV file containing user data.

## Error Handling

The `user_upload.php` script includes basic error handling for database connection issues and missing command-line arguments. If the script encounters an error, it will display a message and exit with a non-zero status code.

Common error scenarios include:

- **Missing Database Extensions**: If the required PHP extensions for PostgreSQL (`pgsql` and `pdo_pgsql`) are not installed, the script will prompt you to install them.

- **Database Connection Failures**: If the connection to the PostgreSQL database fails due to incorrect credentials or other issues, the script will show an error message.

- **Invalid Command-Line Arguments**: If the script is run with missing or invalid arguments, it will show an error message and provide guidance on how to use the script correctly.

## Database Connections

The script connects to a PostgreSQL database using PHP’s PDO extension. When establishing a connection, the script will:

1. **Prompt for Missing Details**: If necessary connection details (host, username, or password) are not provided via command-line arguments, the script will prompt you to enter them interactively.

2. **Check for Required Extensions**: It verifies that the `pgsql` and `pdo_pgsql` extensions are installed and enabled.

3. **Create PDO Instance**: It creates a PDO instance using the provided or default connection details and sets the error mode to `PDO::ERRMODE_EXCEPTION` to handle exceptions.

## Database Table Creation

The script includes functionality to create or rebuild the 'users' table in the PostgreSQL database. When you run the script with the `--create_table` option, it will:

1. **Check Table Existence**: It checks if the 'users' table already exists in the database.

2. **Execute SQL Query**: If the table does not exist, it executes a SQL query to create the table with the following schema:

   ```sql
   CREATE TABLE IF NOT EXISTS users (
       id SERIAL PRIMARY KEY,
       name VARCHAR(255) NOT NULL,
       surname VARCHAR(255) NOT NULL,
       email VARCHAR(255) NOT NULL UNIQUE
   );
   ```
