# PHP CSV Processor

## Table of Contents

1.  [PHP CSV Processor](#php-csv-processor)
    1.  [Description](#description)
    2.  [Tech Stack](#tech-stack)
        1.  [Dependencies](#dependencies)
        2.  [Composer Configuration](#composer-configuration)
    3.  [Design](#design)
    4.  [Features](#features)
2.  [How to Run the Project](#how-to-run-the-project)
    1.  [Prerequisites](#prerequisites)
    2.  [Setup](#setup)
        1.  [Clone the Repository](#clone-the-repository)
        2.  [Install Dependencies](#install-dependencies)
        3.  [Configure Database Connection](#configure-database-connection)
    3.  [Usage](#usage)
    4.  [Example Commands](#example-commands)
3.  [Functions](#functions)
    1.  [run()](#run)
    2.  [countLines()](#countlines)
    3.  [validateEmail()](#validateemail)
    4.  [displayProgress()](#displayprogress)
    5.  [isValidCsvFile()](#isvalidcsvfile)
    6.  [insertUsers()](#insertusers)
    7.  [processCSVFile()](#processcsvfile)
    8.  [createTable()](#createtable)
    9.  [printError()](#printerror)
    10. [printInfo()](#printinfo)
    11. [printHelp()](#printhelp)
    12. [prompt()](#prompt)
    13. [connectDatabase()](#connectdatabase)
    14. [parseArguments()](#parsearguments)
4.  [Error Handling](#error-handling)
    1.  [Error Messages](#error-messages)
    2.  [Input Validation](#input-validation)
    3.  [File Validation](#file-validation)

## Description

The **PHP CSV Processor** is a command-line script designed for bulk importing user data from CSV files into a PostgreSQL database. This tool streamlines user record management by automating data validation, formatting, and database operations. It offers various command-line options for managing database schemas and performing data uploads, making it a versatile solution for database administrators and developers.

## Tech Stack

- **Programming Language**: PHP Version 8.2.x or higher.
- **Database**: PostgreSQL Version 16.4 or higher.
- **CSV File**: A properly formatted CSV file containing user data.
- **Dependency Management**: Composer

### Dependencies

This project relies on PHP CodeSniffer for maintaining coding standards. PHP CodeSniffer is a tool that helps ensure code adheres to defined coding standards, making it easier to maintain consistency and quality across the codebase.

- **PHP CodeSniffer (`squizlabs/php_codesniffer`)**: A development dependency used to check the code against predefined coding standards and detect issues related to code formatting and style.

  - **Version**: `4.0.x-dev`
  - **Purpose**: To enforce coding standards and automatically fix some common code style issues.

The `require-dev` section of the `composer.json` file includes this dependency, which means it is only needed during development and is not required in the production environment.

### Composer Configuration

The `composer.json` file includes the following configuration for PHP CodeSniffer:

```json
"require-dev": {
    "squizlabs/php_codesniffer": "4.0.x-dev"
}
```

This specifies that PHP CodeSniffer is a development dependency, allowing you to run code quality checks and ensure adherence to coding standards during the development process.

## Design

This project operates as a command-line utility and does not include a graphical user interface. Below is an example of how to interact with the script through the terminal.

![PHP CSV processor image file with a screenshot of the help section](https://raw.githubusercontent.com/ndegwamoche/php-csv-processor/main/php-csv-processor.png)

## Features

- **CSV Parsing & Bulk Upload** : Import user data from a CSV file into a PostgreSQL database.
- **Email Validation**: Validates email addresses and formats names.
- **CSV File Validation**: Ensures the CSV file is properly formatted, encoded in UTF-8, and contains the correct number and type of columns.
- **Name Formatting**: Automatically capitalizes the first letter of names and surnames.
- **Database Table Creation**: Automatically creates or rebuilds the users table in PostgreSQL if needed.
- **Dry Run Mode**: Allows you to simulate the upload process without modifying the database.
- **Command-Line Options**: Provides a command-line interface with multiple options for flexibility.

## How to Run the Project

### Prerequisites

- **PHP 8.2.7** or higher
- **PostgreSQL** database
- **Composer** for managing dependencies

### Setup

1.  **Clone the Repository**
        ```json
git clone https://github.com/ndegwamoche/php-csv-processor.git
        cd php-csv-processor
}
```

        ```json
        git clone https://github.com/ndegwamoche/php-csv-processor.git
        cd php-csv-processor
        ```
3.  **Install Dependencies**

    Ensure you have Composer installed, then run:

    bash

    Copy code

    `composer install`

4.  **Configure Database Connection**

    Database credentials can be specified via command-line options when running the script.

### Usage

Here are the common commands for using the `user_upload.php` script:

- **Show Help**

  bash

  Copy code

  `php user_upload.php --help`

  Displays available commands and options.

- **Create or Rebuild Table**

  bash

  Copy code

  `php user_upload.php --create_table`

  Creates or rebuilds the `users` table. Optionally provide credentials:

  bash

  Copy code

  `php user_upload.php --create_table -u username -p password -h hostname`

- **Upload User Data**

  bash

  Copy code

  `php user_upload.php --file users.csv`

  Uploads data from `users.csv` into the database. Optionally provide credentials:

  bash

  Copy code

  `php user_upload.php --file users.csv -u username -p password -h hostname`

- **Dry Run**

  bash

  Copy code

  `php user_upload.php --dry_run`

  Simulates the upload process without actual changes. Optionally specify a CSV file:

  bash

  Copy code

  `php user_upload.php --dry_run --file users.csv`

### Example Commands

1.  **Create the Table**

    bash

    Copy code

    `php user_upload.php --create_table -u myuser -p mypass -h localhost`

2.  **Upload Data**

    bash

    Copy code

    `php user_upload.php --file mydata.csv -u myuser -p mypass -h localhost`

3.  **Dry Run Upload**

    bash

    Copy code

    `php user_upload.php --dry_run --file mydata.csv`

## Functions

### `run()`

**Main function to execute the script based on the parsed arguments.**

- **Description**: This method handles the overall execution of the script. It checks the provided command-line arguments and performs actions such as creating the database table, processing the CSV file, or running in dry mode. It ensures that the necessary actions are taken based on user input.
- **Usage**:

  php

  Copy code

  `` // The `run` method is automatically called based on the command-line arguments. ``

### `countLines($filename)`

**Counts the number of lines in a specified file.**

- **Parameters**:
  - `string $filename`: The name of the file to count lines in.
- **Returns**: `int` - The total number of lines in the file.
- **Usage**:

  php

  Copy code

  `$lineCount = countLines('path/to/file.csv');`

### `validateEmail($email)`

**Validates the format of an email address.**

- **Parameters**:
  - `string $email`: Email address to validate.
- **Returns**: `bool` - `true` if the email is valid, `false` otherwise.
- **Usage**:

  php

  Copy code

  `$isValid = validateEmail('test@example.com');`

### `displayProgress($processedLines, $totalLines, $complete = false)`

**Displays a progress bar in the command line to indicate processing status.**

- **Parameters**:
  - `int $processedLines`: The number of lines processed so far.
  - `int $totalLines`: The total number of lines to process.
  - `bool $complete`: Optional parameter to indicate if the processing is complete.
- **Usage**:

  php

  Copy code

  `displayProgress(50, 100);
displayProgress(100, 100, true);`

### `isValidCsvFile($filename)`

**Validates if the given file is a proper CSV file.**

- **Description**: This function checks several criteria to confirm the file's validity, including existence, readability, file extension, MIME type, and consistent column count across rows.
- **Parameters**:
  - `string $filename`: The path to the file to be validated.
- **Returns**: `bool` - `true` if the file is a valid CSV, `false` otherwise.
- **Usage**:

  php

  Copy code

  `$isValid = isValidCsvFile('path/to/file.csv');`

### `insertUsers(array $users, array &$errors)`

**Inserts multiple user records into the database.**

- **Parameters**:
  - `array $users`: An array of associative arrays, each containing 'name', 'surname', and 'email' keys for user records.
  - `array &$errors`: An array that will be populated with errors encountered during insertion.
- **Usage**:

  php

  Copy code

  `insertUsers($userRecords, $errors);`

### `processCSVFile()`

**Processes a CSV file and inserts valid user records into the database.**

- **Description**: This method handles reading and validating the CSV file, processing each row, and inserting valid user records into the database. It also displays appropriate messages for invalid data.
- **Usage**:

  php

  Copy code

  `processCSVFile();`

### `createTable()`

**Creates the PostgreSQL users table.**

- **Description**: This method creates a 'users' table in the PostgreSQL database with columns for ID, name, surname, and email. The email column must be unique.
- **Usage**:

  php

  Copy code

  `createTable();`

### `printError($message)`

**Prints error messages in red.**

- **Parameters**:
  - `string $message`: Message to display.
- **Usage**:

  php

  Copy code

  `printError("An error occurred.");`

### `printInfo($message)`

**Prints informational messages in green.**

- **Parameters**:
  - `string $message`: Message to display.
- **Usage**:

  php

  Copy code

  `printInfo("Process completed successfully.");`

### `printHelp()`

**Displays help information about how to use the script.**

- **Description**: This method provides information on how to use the script, including available commands and options.
- **Usage**:

  php

  Copy code

  `printHelp();`

### `prompt($message, $default = '')`

**Prompts the user for input and returns the entered value.**

- **Parameters**:
  - `string $message`: The message to display to the user.
  - `string $default`: The default value to return if no input is provided.
- **Returns**: `string` - The user's input or the default value if no input is provided.
- **Usage**:

  php

  Copy code

  `$input = prompt("Enter your name:", "John Doe");`

### `connectDatabase()`

**Establishes a connection to the PostgreSQL database using PDO.**

- **Description**: This method creates a PDO connection to the PostgreSQL database, enabling interaction with the database for operations such as table creation and data insertion.
- **Usage**:

  php

  Copy code

  `connectDatabase();`

### `parseArguments($argv)`

**Parses command-line arguments.**

- **Parameters**:
  - `array $argv`: Command-line arguments.
- **Returns**: `array` - Parsed arguments.
- **Usage**:

  php

  Copy code

  `$args = parseArguments($argv);`

## Error Handling

The PHP CSV Processor script includes several mechanisms for error handling to ensure smooth execution and provide clear feedback when issues arise. Here’s an overview of how errors are managed within the script:

### Error Messages

- **Error Printing**: Error messages are printed in red to distinguish them from other types of output. This helps in quickly identifying problems when running the script from the command line.

  php

  Copy code

  `private function printError($message) {
    // Print error messages in red
    echo "\033[31m$message\033[0m\n";
}`

- **Informational Messages**: Informational messages are printed in green to provide feedback on the progress and status of the script.

  php

  Copy code

  `private function printInfo($message) {
    // Print informational messages in green
    echo "\033[32m$message\033[0m\n";
}`

### Input Validation

- **Command-Line Arguments**: The script checks if the necessary command-line arguments are provided. If not, it displays an error message and prints the help information. This ensures that users know how to properly run the script.

  php

  Copy code

  `if (empty($this->args)) {
    $this->printError("Please enter a command to start.\nPlease use the options below to run the script.");
    $this->printHelp();
    exit(1);
}`

### File Validation

- **CSV File Validation**: The script validates the CSV file to ensure it meets several criteria:

  - The file exists and is readable.
  - The file has a `.csv` extension.
  - The MIME type of the file matches that of a CSV file.
  - The file is parsed to verify consistent column count across rows.

  If the file does not meet these criteria, the script will output an error message and halt further processing.

  php

  Copy code

  `private function isValidCsvFile($filename) {
    // Validate CSV file
    if (!file_exists($filename)) {
        $this->printError("File does not exist.");
        return false;
    }
    if (!is_readable($filename)) {
        $this->printError("File is not readable.");
        return false;
    }
    if (pathinfo($filename, PATHINFO_EXTENSION) !== 'csv') {
        $this->printError("File is not a CSV.");
        return false;
    }
    // Additional validation logic
    return true;
}`

### Database Operations

- **Database Connection**: The script establishes a connection to the PostgreSQL database. If connection fails, an error message is printed.

  php

  Copy code

  `private function connectDatabase() {
    try {
        // Database connection logic
    } catch (PDOException $e) {
        $this->printError("Database connection failed: " . $e->getMessage());
        exit(1);
    }
}`

- **Record Insertion**: Errors during the insertion of user records into the database are handled individually. The script logs errors for specific records, allowing the remaining records to be processed.

  php

  Copy code

  `private function insertUsers(array $users, array &$errors) {
    foreach ($users as $user) {
        try {
            // Insert user record
        } catch (Exception $e) {
            $errors[] = "Failed to insert user: " . $e->getMessage();
        }
    }
}`

### Dry Run Mode

- **Dry Run Mode**: When running in dry-run mode, the script performs a simulated execution without making any changes to the database. This is useful for verifying the results before actual execution.

  php

  Copy code

  `if (isset($this->args['dry_run'])) {
    $this->printInfo("Dry run mode enabled. No changes will be made to the database.");
    $this->processCSVFile();
}`

## About the Author

**Martin Ndegwa Moche**

I am a WordPress PHP Developer with a passion for building robust and efficient web applications. If you have any questions or would like to connect, feel free to reach out!

- **LinkedIn**: [Martin Ndegwa Moche](https://www.linkedin.com/in/ndegwamoche/)
- **Email**: ndegwamoche@gmail.com
