<?php
namespace App\Database;

use mysqli;
use Symfony\Component\Dotenv\Dotenv;

class Connection
{
    private static ?mysqli $instance = null;

    public static function get(): mysqli
    {
        if (self::$instance === null) {
            // Load .env once
            $dotenv = new Dotenv();
            $dotenv->load(__DIR__ . '/../../.env'); // adjust path as needed

            $host = $_ENV['DB_HOST'] ?? NULL;
            $user = $_ENV['DB_USER'] ?? NULL;
            $pass = $_ENV['DB_PASS'] ?? NULL;
            $name = $_ENV['DB_NAME'] ?? NULL;

            self::$instance = new mysqli($host, $user, $pass, $name);

            if (self::$instance->connect_error) {
                die("Database connection failed: " . self::$instance->connect_error);
            }
        }

        return self::$instance;
    }
}
