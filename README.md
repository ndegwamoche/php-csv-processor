# php-csv-processor

This project is a PHP script designed to process a CSV file and insert the data into a PostgreSQL database. The script is executed from the command line and includes various options for creating the database table, running in dry-run mode, and more.

##Overview
The user_upload.php script is designed to handle the bulk uploading of user data from a CSV file into a PostgreSQL database. The script can validate email addresses, format names, and ensure that records are only inserted if they meet the required criteria. Additionally, it can create or rebuild the necessary database table before inserting records. This project is suitable for scenarios where bulk user data needs to be imported and processed efficiently.

##Features

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
