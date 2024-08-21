<?php

/**
 * The UserUpload class handles the initial setup for database connection and command-line argument parsing.
 * The script is designed to manage the bulk uploading of user data from a CSV file into a PostgreSQL database.
 * It can validate email addresses, format names, and ensure that records are only inserted if they meet the required criteria.
 * Additionally, it can create or rebuild the necessary database table before inserting records.
 * This project is suitable for scenarios where bulk user data needs to be imported and processed efficiently.
 *
 * Author: Martin Ndegwa Moche
 * Email: ndegwamoche@gmail.com
 */

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
                    $args['file'] = $argv[++$i];
                    break;
                case '--create_table':
                    $args['create_table'] = true;
                    break;
                case '--dry_run':
                    $args['dry_run'] = true;
                    break;
                case '-u':
                    $args['username'] = $argv[++$i];
                    break;
                case '-p':
                    $args['password'] = $argv[++$i];
                    break;
                case '-h':
                    $args['host'] = $argv[++$i];
                    break;
                case '--help':
                    $this->printHelp();
                    exit;
                default:
                    $this->printError("Unknown argument: " . $argv[$i]);
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
            // Create a PDO instance with the provided or default connection details
            $dsn = 'pgsql:host=' . $host . ';dbname=postgres';
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->printInfo("Database connection successful.");
        } catch (PDOException $e) {
            // Handle connection errors
            $this->printError("Database connection failed: " . $e->getMessage());
            exit(1);
        }
    }

    /**
     * Prompt user for input with optional hiding of input.
     *
     * @param  string $message Prompt message to display
     * @param  string $default Default value if no input is provided
     * @param  bool   $hideInput Whether to hide the input (for passwords)
     * @return string User input
     */
    private function prompt($message, $default = '', $hideInput = false)
    {
        echo $message;
        $input = $hideInput ? $this->readHiddenInput() : trim(fgets(STDIN));
        return empty($input) ? $default : $input;
    }

    /**
     * Read hidden input for password (platform-specific handling).
     *
     * @return string User input
     */
    private function readHiddenInput()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return $this->readHiddenInputWindows();
        } else {
            return $this->readHiddenInputUnix();
        }
    }

    /**
     * Read hidden input on Windows using PowerShell.
     *
     * @return string User input
     */
    private function readHiddenInputWindows()
    {
        $value = '';
        $command = 'powershell -Command "[System.Console]::ReadKey($true).KeyChar"';
        exec($command, $output);
        foreach ($output as $char) {
            if ($char === "\r") {
                break;
            }
            $value .= $char;
        }
        return $value;
    }

    /**
     * Read hidden input on Unix-like systems using stty.
     *
     * @return string User input
     */
    private function readHiddenInputUnix()
    {
        $value = '';
        exec('stty -echo');
        $value = rtrim(fgets(STDIN));
        exec('stty echo');
        echo PHP_EOL; // Move to the next line
        return $value;
    }

    /**
     * Display help information about how to use the script.
     *
     * @return void
     */
    private function printHelp()
    {
        $this->printInfo(str_repeat('*', 100));
        $this->printInfo(str_repeat('*', 20) . "                UserUpload Script Help                " . str_repeat('*', 20));
        $this->printInfo(str_repeat('*', 100));
        $this->printInfo("");
        $this->printInfo("Usage:");
        $this->printInfo("  php user_upload.php [options]");
        $this->printInfo("");
        $this->printInfo("Options:");
        $this->printInfo("  --file [csv file name]      *Required*: The name of the CSV file to be processed.");
        $this->printInfo("  --create_table              *Optional*: Creates the PostgreSQL users table and exits.");
        $this->printInfo("  --dry_run                   *Optional*: Runs the script without inserting into the database, for testing purposes.");
        $this->printInfo("  -u [username]               *Optional*: PostgreSQL username for database connection.");
        $this->printInfo("  -p [password]               *Optional*: PostgreSQL password for database connection.");
        $this->printInfo("  -h [host]                   *Optional*: PostgreSQL host, default is 'localhost'.");
        $this->printInfo("  --help                      *Optional*: Displays this help message.");
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
     * Main function to run the script based on the parsed arguments.
     *
     * @return void
     */
    public function run()
    {
        // Check if any command-line arguments were provided
        if (empty($this->args)) {
            $this->printError("Please enter a command to start");
            $this->printHelp();
            exit(1);
        }

        // Establish a database connection if necessary
        if (isset($this->args['create_table']) || isset($this->args['file'])) {
            $this->connectDatabase();
        }

        // Proceed with the script logic if arguments are present
        $this->printInfo("Running UserUpload script...");

        // Add additional logic here based on the provided arguments
    }
}

// Execute the script
try {
    $userUpload = new UserUpload($argv);
    $userUpload->run();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
