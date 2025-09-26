<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Database\Connection;
use DirectoryIterator;

class MigrateCommand extends Command
{
    protected static $defaultName = 'jarvish:migrate';

    public static function getDefaultName(): string
    {
        return self::$defaultName;
    }

    protected function configure(): void
    {
        $this->setDescription('Run all database migrations in src/Migrations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $conn = Connection::get(); // centralized DB connection
        $output->writeln("✅ Connected to database successfully.");

        $migrationsDir = __DIR__ . '/../Migrations';
        $migrationFiles = [];

        // Scan directory for PHP files
        foreach (new DirectoryIterator($migrationsDir) as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getExtension() === 'php') {
                $migrationFiles[] = $fileInfo->getFilename();
            }
        }

        if (empty($migrationFiles)) {
            $output->writeln("⚠️ No migration files found in src/Migrations.");
            return Command::SUCCESS;
        }

        foreach ($migrationFiles as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            $fullClass = 'App\\Migrations\\' . $className;

            if (class_exists($fullClass)) {
                $migration = new $fullClass();
                $migration->up($conn);
                $output->writeln("✅ Migration ran: $className");
            } else {
                $output->writeln("⚠️ Class $fullClass does not exist or autoloading failed.");
            }
        }

        $output->writeln("🎉 All migrations completed successfully!");
        return Command::SUCCESS;
    }
}
