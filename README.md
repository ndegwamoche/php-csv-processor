# PHP CSV Processor

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

# CSV Processing Script Documentation

## Overview

This documentation covers the functionality added to the CSV processing script, including CSV parsing, line counting, name capitalization, and email normalization. The script handles reading from a CSV file, validating and formatting data, and preparing it for further processing.

## Features

### CSV Parsing

- **Functionality**: The script parses CSV files to read user data.
- **Details**: Each row of the CSV file is read and processed to extract user information.
- **File Handling**: Handles file operations with improved error checking and validation.

### Line Counting

- **Functionality**: The script includes a function to count the number of lines in a CSV file.
- **Purpose**: Helps determine the size of the file and manage processing efficiently.

### Name and Email Formatting

- **Name Capitalization**: Names are capitalized before processing:
  - **Example**: Converts 'john' to 'John'.
- **Email Normalization**: Email addresses are converted to lowercase before processing:
  - **Example**: Converts 'JOHN@EXAMPLE.COM' to 'john@example.com'.

## Functions

### `processCSVFile()`

- **Purpose**: Parses the CSV file, validates, and formats user data.
- **Steps**:
  1.  Checks if the file argument is provided.
  2.  Opens and reads the CSV file.
  3.  Skips the header row.
  4.  Processes each row:
      - Capitalizes names.
      - Converts emails to lowercase.
  5.  Displays processed data or performs a dry run based on the `--dry_run` flag.

### `countLines($filename)`

- **Purpose**: Counts the total number of lines in a CSV file.
- **Parameters**:
  - `$filename` (string): The path to the CSV file.
- **Returns**: Integer value representing the number of lines in the file.

### `isValidCsvFile($filename)`

- **Purpose**: Validates if a CSV file is valid and meets the required criteria.
- **Parameters**:
  - `$filename` (string): The path to the CSV file.
- **Returns**: Boolean value indicating if the file is valid.

To ensure that only valid CSV files are processed, the script includes a method for validating CSV files. This method checks the following:

- **File Existence and Readability:** Confirms the file exists and is readable.
- **File Extension:** Ensures the file has a `.csv` extension.
- **MIME Type:** Verifies the file's MIME type to ensure it's a CSV file.
- **File Structure:** Checks for consistent column counts and ensures the file contains rows.

**How to Use:**

Before processing a CSV file, call the `isValidCsvFile` method to validate it. This method will help to avoid errors by confirming the file meets the required criteria.

**Example Usage:**

php

Copy code

`$fileName = $this->args['file'];

// Validate the CSV file
if (!$this->isValidCsvFile($fileName)) {
throw new \Exception("Invalid CSV file: " . $fileName . PHP_EOL);
}`

This approach provides a clear understanding of th

## Error Handling

- **File Errors**: Provides descriptive messages if the file cannot be opened or if the file argument is missing.
- **Email Validation**: Checks and reports invalid email formats.

### Email Validation

The `UserUpload` script now includes an email validation feature to ensure that only valid email addresses are processed and inserted into the PostgreSQL database. The validation is handled by the `validateEmail` method, which uses PHP's `filter_var()` function with the `FILTER_VALIDATE_EMAIL` filter.

#### How It Works

Before inserting user data into the database, the script checks each email address using the `validateEmail` method. If an email address does not pass the validation, it will be excluded from the insertion process, ensuring that only properly formatted email addresses are stored.

#### Benefits

- **Data Integrity**: Prevents invalid email addresses from being stored in the database.
- **Improved Data Quality**: Ensures that only valid and usable email addresses are inserted.

This feature is particularly useful in scenarios where clean and reliable user data is essential for the operation of the system, such as in email marketing or user authentication systems.

# Code Style and Standards

## PHP CodeSniffer

This project uses [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) to enforce PSR-12 coding standards, ensuring consistent code style across the project.

## Installation

If you already have Composer installed and a `composer.json` file set up in your project, you can install all the required dependencies, including PHP CodeSniffer, by running:

bash

Copy code

`composer install`

This command will install PHP CodeSniffer as a dev dependency, along with any other dependencies specified in your `composer.json`.

## Usage

You can use PHP CodeSniffer to check your code for style violations and to automatically fix issues.

### Checking Code Style

To check your code for coding standard violations, run:

bash

Copy code

`vendor/bin/phpcs --standard=PSR12 user_upload.php`

### Fixing Code Style Issues

To automatically fix fixable coding standard violations, run:

bash

Copy code

`vendor/bin/phpcbf --standard=PSR12 user_upload.php`

## Integration and Automation

### Git Hooks

To enforce coding standards before committing, you can add PHP CodeSniffer to your Git hooks:

1.  Create or edit the `.git/hooks/pre-commit` file:

        bash

        Copy code

        `#!/bin/sh

    vendor/bin/phpcs --standard=PSR12 user_upload.php`

2.  Make the pre-commit file executable:

    bash

    Copy code

    `chmod +x .git/hooks/pre-commit`

# **Windows: Enabling PostgreSQL Extensions for PHP**

### **Step 1: Verify PHP Installation**

1.  Make sure PHP is installed on your system. You can verify by running the following command in Command Prompt or PowerShell:

    bash

    Copy code

    `php -v`

2.  Confirm the version and installation path of PHP.

### **Step 2: Locate `php.ini` File**

1.  Find the `php.ini` file, typically located in the PHP installation directory (e.g., `C:\php`, `C:\Program Files\PHP\`, or `C:\xampp\php\`).
2.  If you have multiple PHP installations, ensure you're editing the correct `php.ini` file used by your web server or CLI.

### **Step 3: Enable PostgreSQL Extensions**

1.  Open the `php.ini` file in a text editor (e.g., Notepad, Notepad++, or VS Code).
2.  Search for the following lines:

        ini

        Copy code

        `;extension=pgsql

    ;extension=pdo_pgsql`

3.  Uncomment these lines by removing the semicolons (`;`) at the beginning:

        ini

        Copy code

        `extension=pgsql

    extension=pdo_pgsql`

4.  Save the `php.ini` file.

### **Step 4: Restart Web Server (If Applicable)**

1.  If you're using a web server like Apache or Nginx, restart it to apply the changes.

    - For Apache, you can restart it using the following command in Command Prompt:

      bash

      Copy code

      `httpd -k restart`

    - If using XAMPP, restart Apache from the XAMPP Control Panel.

### **Step 5: Verify Installation**

1.  Create a `phpinfo.php` file in your web server's root directory with the following content:

    php

    Copy code

    `<?php phpinfo(); ?>`

2.  Access this file from your web browser (e.g., `http://localhost/phpinfo.php`) and search for "pgsql" and "pdo_pgsql" to ensure the extensions are enabled.

# **Unix/Linux: Installing and Enabling PostgreSQL Extensions for PHP**

### **Step 1: Install PostgreSQL and PHP**

1.  Ensure that both PostgreSQL and PHP are installed. You can verify using the following commands:

        bash

        Copy code

        `php -v

    psql --version`

2.  If they are not installed, install them using your package manager. For example, on Ubuntu:

        bash

        Copy code

        `sudo apt update

    sudo apt install php postgresql`

### **Step 2: Install PHP PostgreSQL Extensions**

1.  Install the necessary PHP PostgreSQL extensions using your package manager. On Ubuntu or Debian-based systems:

    bash

    Copy code

    `sudo apt-get install php-pgsql`

    On Red Hat or CentOS:

    bash

    Copy code

    `sudo yum install php-pgsql`

### **Step 3: Locate and Edit `php.ini` File**

1.  Find the `php.ini` file, usually located in `/etc/php/{version}/cli/php.ini` or `/etc/php/{version}/apache2/php.ini`.
2.  Open it in a text editor:

    bash

    Copy code

    `sudo nano /etc/php/{version}/cli/php.ini`

### **Step 4: Enable Extensions**

1.  Ensure that the following lines are present and uncommented in the `php.ini` file:

        ini

        Copy code

        `extension=pgsql.so

    extension=pdo_pgsql.so`

### **Step 5: Restart Web Server**

1.  Restart your web server to apply changes:

    bash

    Copy code

    `sudo systemctl restart apache2`

    Or for Nginx:

    bash

    Copy code

    `sudo systemctl restart nginx`

### **Step 6: Verify Installation**

1.  Create a `phpinfo.php` file in your web server’s root directory:

    bash

    Copy code

    `echo "<?php phpinfo(); ?>" | sudo tee /var/www/html/phpinfo.php`

2.  Open this file in a web browser (`http://your-server-ip/phpinfo.php`) and search for "pgsql" and "pdo_pgsql" to verify the extensions are enabled.

## **Common Troubleshooting Steps**

- **Missing Extensions:** Ensure that the PHP extensions are correctly installed. If not, try reinstalling PHP and the extensions.
- **Incorrect `php.ini` File:** Ensure you’re editing the correct `php.ini` file by checking the output of `phpinfo()`.

Following these steps will help you enable PostgreSQL support in PHP on both Windows and Unix-based systems.
