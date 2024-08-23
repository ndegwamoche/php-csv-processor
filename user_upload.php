<?php

/**
 * The UserUpload class handles the initial setup for database connection and command-line argument parsing.
 * The script is designed to manage the bulk uploading of user data from a CSV file into a PostgreSQL database.
 * It can validate email addresses, format names, and ensure that records are only inserted if they meet the criteria.
 * Additionally, it can create or rebuild the necessary database table before inserting records.
 * This project is suitable for scenarios where bulk user data needs to be imported and processed efficiently.
 *
 * Author: Martin Ndegwa Moche
 * Email: ndegwamoche@gmail.com
 */

namespace moche\phpcsvprocessor;

class UserUpload
{
    private $pdo;     // PDO instance for database connection
    private $args;    // Array to store command-line arguments

    // ANSI color codes for styling
    private const RED = "\033[31m";
    private const GREEN = "\033[32m";
    private const YELLOW = "\033[33m";
    private const RESET = "\033[0m";

    /**
     * Constructor initializes the class with command-line arguments.
     *
     * @param array $argv Command-line arguments
     */
    public function __construct($argv)
    {
        // Parse the command-line arguments
        $this->args = $this->parseArguments($argv);
    }

    /**
     * Parse command-line arguments.
     *
     * @param  array $argv Command-line arguments
     * @return array Parsed arguments
     */
    private function parseArguments($argv)
    {
        $args = [];
        for ($i = 1; $i < count($argv); $i++) {
            switch ($argv[$i]) {
                case '--file':
                    // Ensure there is a value after --file
                    if (isset($argv[$i + 1])) {
                        $args['file'] = $argv[++$i];
                    } else {
                        $this->printError("No file name provided for --file option. " .
                            "\nPlease use the options below to run the script.");
                        $this->printHelp();
                        exit(1);
                    }
                    break;
                case '--create_table':
                    $args['create_table'] = true;
                    break;
                case '--dry_run':
                    $args['dry_run'] = true;
                    break;
                case '-u':
                    if (isset($argv[$i + 1])) {
                        $args['username'] = $argv[++$i];
                    } else {
                        $this->printError("No username provided for -u option.");
                        $this->printHelp();
                        exit(1);
                    }
                    break;
                case '-p':
                    if (isset($argv[$i + 1])) {
                        $args['password'] = $argv[++$i];
                    } else {
                        $this->printError("No password provided for -p option.");
                        $this->printHelp();
                        exit(1);
                    }
                    break;
                case '-h':
                    if (isset($argv[$i + 1])) {
                        $args['host'] = $argv[++$i];
                    } else {
                        $this->printError("No host provided for -h option.");
                        $this->printHelp();
                        exit(1);
                    }
                    break;
                case '--help':
                    $this->printHelp();
                    exit;
                default:
                    $this->printError("Unknown argument: " . $argv[$i] .
                        "\nPlease use the options below to run the script.");
                    $this->printHelp();
                    exit(1);
            }
        }
        return $args;
    }

    /**
     * Establish a connection to the PostgreSQL database using PDO.
     *
     * @return void
     */
    private function connectDatabase()
    {
        if (isset($this->args['dry_run'])) {
            $this->printInfo("Dry run mode: Skipping database connection.");
            return;
        }

        // Prompt for missing connection details
        $host = $this->args['host'] ?? $this->prompt('Enter PostgreSQL host (default: localhost): ', 'localhost');
        $username = $this->args['username'] ?? $this->prompt('Enter PostgreSQL username: ');
        $password = $this->args['password'] ?? $this->prompt('Enter PostgreSQL password: ', '', true);

        try {
            // Check if the PDO PostgreSQL extension is loaded
            if (!extension_loaded('pdo_pgsql')) {
                $this->printError("Required PHP extensions for PostgreSQL are not installed." .
                    " Please install or enable 'pgsql' and 'pdo_pgsql' extensions.");
            }

            // Create a PDO instance with the provided or default connection details
            $dsn = 'pgsql:host=' . $host . ';dbname=postgres';
            $this->pdo = new \PDO($dsn, $username, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->printInfo("\nDatabase connection successful.\n");
        } catch (\PDOException $e) {
            // Handle connection errors
            $this->printError("Database connection failed: " . $e->getMessage());
            exit(1);
        } catch (\Exception $e) {
            // Handle other errors
            $this->printError($e->getMessage());
            exit(1);
        }
    }

    /**
     * Prompts the user for input and returns the entered value.
     *
     * This method displays a message to the user and reads a line of input from
     * the standard input (STDIN). If no input is provided, it returns a default
     * value if specified.
     *
     * @param string $message The message to display to the user.
     * @param string $default The default value to return if no input is provided.
     * @return string The user's input or the default value if no input is provided.
     */

    private function prompt($message, $default = '')
    {
        echo $message;
        $input = trim(fgets(STDIN));
        return empty($input) ? $default : $input;
    }

    /**
     * Display help information about how to use the script.
     *
     * @return void
     */
    private function printHelp()
    {
        $this->printInfo(str_repeat('*', 100));
        $this->printInfo(self::RESET . str_repeat('*', 23) .
            "                UserUpload Script Help                " .
            str_repeat('*', 23));
        $this->printInfo(str_repeat('*', 100));
        $this->printInfo("");
        echo self::YELLOW . "Usage:" . self::RESET . PHP_EOL;
        $this->printInfo("  php user_upload.php [options]");
        $this->printInfo("");
        echo self::YELLOW . "Options:" . self::RESET . PHP_EOL;
        $this->printInfo("  --file [csv file name]      " . self::RESET .
            "*Required*: The name of the CSV file to be processed.");
        $this->printInfo("  --create_table              " . self::RESET .
            "*Optional*: Creates the PostgreSQL users table and exits.");
        $this->printInfo("  --dry_run                   " . self::RESET .
            "*Optional*: Runs the script without inserting into the database, for testing purposes.");
        $this->printInfo("  -u [username]               " . self::RESET .
            "*Optional*: PostgreSQL username for database connection.");
        $this->printInfo("  -p [password]               " . self::RESET .
            "*Optional*: PostgreSQL password for database connection.");
        $this->printInfo("  -h [host]                   " . self::RESET .
            "*Optional*: PostgreSQL host, default is 'localhost'.");
        $this->printInfo("  --help                      " . self::RESET .
            "*Optional*: Displays this help message.");
        $this->printInfo("");
        $this->printInfo(str_repeat('*', 100));
    }

    /**
     * Print informational messages in green.
     *
     * @param string $message Message to display
     * @return void
     */
    private function printInfo($message)
    {
        echo self::GREEN . $message . self::RESET . PHP_EOL;
    }

    /**
     * Print error messages in red.
     *
     * @param string $message Message to display
     * @return void
     */
    private function printError($message)
    {
        echo self::RED . "Error: " . $message . self::RESET . PHP_EOL;
    }

    /**
     * Create the PostgreSQL users table.
     *
     * This method creates a 'users' table in the PostgreSQL database with the following columns:
     * - id (SERIAL PRIMARY KEY): A unique identifier for each user.
     * - name (VARCHAR(255) NOT NULL): The name of the user.
     * - surname (VARCHAR(255) NOT NULL): The surname of the user.
     * - email (VARCHAR(255) NOT NULL UNIQUE): The email address of the user, which must be unique.
     *
     * @return void
     */
    public function createTable()
    {
        try {
            $query = "
                CREATE TABLE IF NOT EXISTS users (
                    id SERIAL PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    surname VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL UNIQUE
                );
            ";
            $this->pdo->exec($query);
            $this->printInfo("Table 'users' created successfully.");
        } catch (\PDOException $e) {
            $this->printError("Failed to create table: " . $e->getMessage());
            exit;
        }
    }

    /**
     * Processes a CSV file and inserts valid user records into the database.
     *
     * This method validates the provided CSV file, reads and processes each row,
     * and inserts valid user records into the database. If the file contains invalid
     * email formats or other issues, appropriate messages are displayed.
     */

    public function processCSVFile()
    {
        // Check if the --file argument is provided
        if (!isset($this->args['file'])) {
            $this->printError("No CSV file provided. Use --file [filename] to specify the CSV file.");
            $this->printHelp();
            exit(1);
        }

        $fileName = $this->args['file'];

        // Validate the CSV file
        if (!$this->isValidCsvFile($fileName)) {
            $this->printError("Invalid CSV file: " . $fileName . PHP_EOL);
            $this->printHelp();
            exit(1);
        }

        // Open the CSV file for reading
        $file = fopen($fileName, 'r');
        if (!$file) {
            $this->printError("Could not open the file: " . $fileName);
            $this->printHelp();
            exit(1);
        }

        // Skip the header row if it exists
        fgetcsv($file);

        $users = []; // Array to store user records
        $totalLines = $this->countLines($fileName); // Get the total number of lines in the file
        $processedLines = 0; // Counter for processed lines
        $errors = []; // Array to store error messages
        $dryRun = isset($this->args['dry_run']); // Check if dry run is enabled

        // Process each row in the CSV file
        while (($row = fgetcsv($file)) !== false) {
            if (count($row) < 3) {
                // Skip rows with insufficient data; ensures that each row in the CSV file has at least three columns
                continue;
            }

            // Extract and format user data
            $name = ucfirst(strtolower($row[0]));
            $surname = ucfirst(strtolower($row[1]));
            $email = strtolower($row[2]);

            // Validate email format
            if ($this->validateEmail($email)) {
                // Add valid user record to the array
                $users[] = ['name' => $name, 'surname' => $surname, 'email' => $email];

                $processedLines++;
                $this->displayProgress($processedLines, $totalLines);
            } else {
                // Output invalid email format message
                $error = empty($email) ? "Empty email" : "Invalid email format";
                $errors[] = [
                    'name' => $name,
                    'surname' => $surname,
                    'email' => $email,
                    'error' => $error,
                ];
            }
        }

        fclose($file); // Close the file

        // If not a dry run, insert valid users into the database
        if (!$dryRun && !empty($users)) {
            $this->insertUsers($users, $errors);
        }

        // Print errors if any
        if (!empty($errors)) {
            // Define the maximum number of errors to display
            $maxDisplayErrors = 20;

            if (count($errors) > $maxDisplayErrors) {
                // Write errors to a text file if there are more than 20 errors
                $filePath = 'errors_log.txt'; // Path to the text file
                $fileHandle = fopen($filePath, 'w'); // Open the file for writing

                if ($fileHandle) {
                    fwrite($fileHandle, "Errors encountered:\n");
                    foreach ($errors as $error) {
                        fwrite($fileHandle, "Name: {$error['name']} {$error['surname']} | " .
                            "Email: {$error['email']} | Error: {$error['error']}\n");
                    }
                    fclose($fileHandle);

                    $this->printInfo(self::YELLOW . "\n\nMore than $maxDisplayErrors errors encountered. " .
                        "Details have been written to $filePath." . self::RESET . "\n");
                }
            } else {
                // Print errors if there are 20 or fewer
                echo "\n" . PHP_EOL;
                $this->printInfo(self::YELLOW . "\nErrors encountered:\n" . self::RESET);
                foreach ($errors as $error) {
                    $this->printError("Name: {$error['name']} {$error['surname']} |" .
                        " Email: {$error['email']} | " . self::RESET . " Error: {$error['error']}\n");
                }
            }
        }

        // Output the total and processed line counts
        $this->printInfo("Total lines: $totalLines | Processed lines: $processedLines |" .
            " Errors: " . count($errors) . PHP_EOL);

        // Notify if it's a dry run
        if ($dryRun) {
            $this->printInfo("Dry run completed: No data was inserted into the database." . PHP_EOL);
        }
    }
    /**
     * Inserts multiple user records into the database.
     *
     * This method inserts each user record in the provided array into the database.
     * If an error occurs during the insertion of a specific record, it is handled
     * individually, allowing the script to continue inserting the remaining records.
     *
     * @param array $users An array of associative arrays, each containing 'name',
     *                     'surname', and 'email' keys for the user record.
     */
    private function insertUsers(array $users, array &$errors)
    {
        // Prepare the SQL statement for inserting a user
        $stmt = $this->pdo->prepare("INSERT INTO users (name, surname, email) VALUES (:name, :surname, :email)");

        // Loop through each user record and execute the prepared statement
        foreach ($users as $user) {
            try {
                $stmt->execute([
                    ':name' => $user['name'],
                    ':surname' => $user['surname'],
                    ':email' => $user['email']
                ]);
            } catch (\PDOException $e) {
                // Handle the error for this specific user and add it to the errors array
                $errors[] = [
                    'name' => $user['name'],
                    'surname' => $user['surname'],
                    'email' => $user['email'],
                    'error' => $e->getMessage()
                ];
            }
        }
    }


    /**
     * Validate if the given file is a valid CSV file.
     *
     * This function performs several checks:
     * - Ensures the file exists and is readable.
     * - Verifies the file has a .csv extension.
     * - Checks the MIME type to ensure it matches that of a CSV file.
     * - Attempts to parse the file as CSV and verifies consistent column count across rows.
     *
     * @param  string $filename The path to the file to be validated.
     * @return bool True if the file is a valid CSV, false otherwise.
     */
    private function isValidCsvFile($filename)
    {
        // Check if file exists and is readable
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        // Check the file extension
        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
        if (strtolower($fileExtension) !== 'csv') {
            return false;
        }

        // Attempt to parse the file to check if it's valid CSV
        if (($handle = fopen($filename, 'r')) !== false) {
            $rowCount = 0;
            $columnCount = null;

            while (($data = fgetcsv($handle)) !== false) {
                $rowCount++;
                // On the first row, store the column count
                if ($columnCount === null) {
                    $columnCount = count($data);
                } else {
                    // Check if the current row has the same number of columns
                    if (count($data) !== $columnCount) {
                        fclose($handle);
                        return false; // Mismatch in column count
                    }
                }
            }

            fclose($handle);

            // Check if there were any rows
            return $rowCount > 0;
        }

        return false;
    }

    /**
     * Displays a progress bar in the command line.
     *
     * @param int $processedLines The number of lines processed
     * @param int $totalLines The total number of lines
     * @param bool $complete Optional parameter to indicate completion
     * @return void
     */
    private function displayProgress($processedLines, $totalLines, $complete = false)
    {

        // Calculate the progress percentage
        $progress = $totalLines > 0 ? ($processedLines / $totalLines) * 100 : 0;

        // Create a progress bar
        $barLength = 50; // Length of the progress bar
        $filledLength = (int)($progress / 100 * $barLength);
        $bar = str_repeat('=', $filledLength) . str_repeat('-', $barLength - $filledLength);

        // Output the progress bar
        $status = $complete ? 'Completed' : sprintf('Processing: %d%%', $progress);
        echo sprintf("\r[%s] %s", $bar, $status);
        //echo "\n" . PHP_EOL;
    }

    /**
     * Validate email address format.
     *
     * @param  string $email Email address to validate
     * @return bool True if email is valid, false otherwise
     */
    private function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Counts the number of lines in a file.
     *
     * @param string $filename The name of the file
     * @return int The total number of lines in the file
     */
    private function countLines($filename)
    {
        $file = fopen($filename, 'r');
        $lines = 0;

        while ($line = fgets($file)) {
            $lines++;
        }

        fclose($file); // Don't forget to close the file

        $lines--; // Subtract 1 to account for the header row
        return $lines;
    }

    /**
     * Main function to run the script based on the parsed arguments.
     *
     * @return void
     */
    public function run()
    {
        // Check if any command-line arguments were provided
        if (empty($this->args)) {
            $this->printError("Please enter a command to start" . "\nPlease use the options below to run the script.");
            $this->printHelp();
            exit(1);
        }

        // Establish a database connection if necessary
        if (isset($this->args['create_table']) || isset($this->args['file'])) {
            // Connect to the database
            $this->connectDatabase();

            // Create the users table if requested
            if (isset($this->args['create_table'])) {
                $this->createTable();
                exit; // Exit after creating the table, as no further actions are needed
            } elseif (isset($this->args['file'])) {
                // Process the CSV file if requested
                $this->processCSVFile();
            }
        } elseif (isset($this->args['dry_run'])) {
            $this->printInfo("Dry run mode enabled. No changes will be made to the database.");
            $this->processCSVFile();
        } else {
            // The following lines enforce that either --create_table or --file must be specified.
            $this->printError("You must specify either --create_table or --file to proceed." .
                "\nPlease use the options below to run the script.");
            $this->printHelp();
            exit(1);
        }
    }
}

// Execute the script
try {
    $userUpload = new UserUpload($argv);
    $userUpload->run();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
