<?php
namespace App\Database;

use mysqli;
use RuntimeException;
use Symfony\Component\Dotenv\Dotenv;

class Connection
{
    private static ?mysqli $instance = null;

    public static function get(): mysqli
    {
        if (self::$instance === null) {
            // Load .env when running via CLI (web entry point loads it already)
            if (empty($_ENV['DB_HOST'])) {
                (new Dotenv())->load(__DIR__ . '/../../.env');
            }

            $driver = strtolower($_ENV['DATABASE'] ?? 'mysql');
            if ($driver !== 'mysql') {
                throw new RuntimeException(
                    "Unsupported DATABASE driver \"{$driver}\". Only \"mysql\" is currently supported."
                );
            }

            $host = $_ENV['DB_HOST'] ?? null;
            $user = $_ENV['DB_USER'] ?? null;
            $pass = $_ENV['DB_PASS'] ?? null;
            $name = $_ENV['DB_NAME'] ?? null;

            if (!$host || !$user || !$name) {
                throw new RuntimeException(
                    'Database configuration incomplete. Check DB_HOST, DB_USER, DB_NAME in your .env file.'
                );
            }

            self::$instance = new mysqli($host, $user, $pass, $name);

            if (self::$instance->connect_error) {
                throw new RuntimeException(
                    'Database connection failed: ' . self::$instance->connect_error
                );
            }

            self::$instance->set_charset('utf8mb4');
        }

        return self::$instance;
    }
}
