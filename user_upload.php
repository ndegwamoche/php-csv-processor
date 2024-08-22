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
        // Prompt for missing connection details
        $host = $this->args['host'] ?? $this->prompt('Enter PostgreSQL host (default: localhost): ', 'localhost');
        $username = $this->args['username'] ?? $this->prompt('Enter PostgreSQL username: ');
        $password = $this->args['password'] ?? $this->prompt('Enter PostgreSQL password: ', '', true);

        try {
            // Check if the PDO PostgreSQL extension is loaded
            if (!extension_loaded('pdo_pgsql')) {
                throw new \Exception("Required PHP extensions for PostgreSQL are not installed." .
                    " Please install or enable 'pgsql' and 'pdo_pgsql' extensions.");
            }

            // Create a PDO instance with the provided or default connection details
            $dsn = 'pgsql:host=' . $host . ';dbname=postgres';
            $this->pdo = new \PDO($dsn, $username, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->printInfo("Database connection successful.");
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
     * Process the CSV file, validate the data, and insert valid records into the database.
     *
     * @return void
     * @throws Exception If no CSV file is provided or the file cannot be opened
     */

    public function processCSVFile()
    {
        // Check if the --file argument is provided
        if (!isset($this->args['file'])) {
            throw new \Exception("No CSV file provided. Use --file [filename] to specify the CSV file.");
        }

        // Open the CSV file for reading
        $file = fopen($this->args['file'], 'r');
        if (!$file) {
            throw new \Exception("Could not open the file: " . $this->args['file']);
        }

        // Read the header row (assuming it exists)
        $header = fgetcsv($file);

        // Initialize counters for progress tracking
        $totalLines = $this->countLines($this->args['file']);
        $processedLines = 0;

        // Process each row in the CSV file
        while (($row = fgetcsv($file)) !== false) {
            $name = ucfirst(strtolower($row[0]));
            $surname = ucfirst(strtolower($row[1]));
            $email = strtolower($row[2]);

            // Validate email format
            if ($this->validateEmail($email)) {
                // Output validated emails
                echo "Name: $name Surname: $surname Email: $email" . PHP_EOL;
            } else {
                // Output invalid email format message
                echo "Invalid email format: $email" . PHP_EOL;
            }

            $processedLines++;
        }

        echo "Total lines: $totalLines Processed lines: $processedLines" . PHP_EOL;

        // Close the file
        fclose($file);
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
        while (!feof($file)) {
            $line = fgets($file);
            $lines++;
        }
        fclose($file);
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
