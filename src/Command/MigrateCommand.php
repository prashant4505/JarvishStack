<?php
namespace App\Command;

use App\Database\Connection;
use DirectoryIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected static $defaultName = 'jarvish:migrate';

    public static function getDefaultName(): string
    {
        return self::$defaultName;
    }

    protected function configure(): void
    {
        $this->setDescription('Run pending database migrations in src/Migrations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $conn = Connection::get();
        $output->writeln('<info>Connected to database.</info>');

        $this->ensureMigrationsTable($conn);

        $ran = $this->getRanMigrations($conn);

        $migrationsDir = __DIR__ . '/../Migrations';
        $files = [];

        foreach (new DirectoryIterator($migrationsDir) as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getExtension() === 'php') {
                $files[] = $fileInfo->getFilename();
            }
        }

        if (empty($files)) {
            $output->writeln('<comment>No migration files found.</comment>');
            return Command::SUCCESS;
        }

        sort($files);
        $count = 0;

        foreach ($files as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);

            if (in_array($className, $ran, true)) {
                $output->writeln("<comment>Skipped (already ran): $className</comment>");
                continue;
            }

            $fullClass = 'App\\Migrations\\' . $className;

            if (!class_exists($fullClass)) {
                $output->writeln("<error>Class $fullClass not found — skipping.</error>");
                continue;
            }

            try {
                (new $fullClass())->up($conn);
                $this->recordMigration($conn, $className);
                $output->writeln("<info>Migrated: $className</info>");
                $count++;
            } catch (\Throwable $e) {
                $output->writeln("<error>Failed [$className]: {$e->getMessage()}</error>");
                return Command::FAILURE;
            }
        }

        $output->writeln($count > 0
            ? "<info>$count migration(s) completed successfully.</info>"
            : '<comment>Nothing to migrate — all migrations are up to date.</comment>'
        );

        return Command::SUCCESS;
    }

    private function ensureMigrationsTable(\mysqli $conn): void
    {
        $conn->query("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    private function getRanMigrations(\mysqli $conn): array
    {
        $result = $conn->query("SELECT migration FROM migrations");
        if (!$result) {
            return [];
        }
        return array_column($result->fetch_all(MYSQLI_ASSOC), 'migration');
    }

    private function recordMigration(\mysqli $conn, string $className): void
    {
        $stmt = $conn->prepare("INSERT IGNORE INTO migrations (migration) VALUES (?)");
        $stmt->bind_param("s", $className);
        $stmt->execute();
        $stmt->close();
    }
}
