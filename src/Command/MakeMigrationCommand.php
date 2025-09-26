<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MakeMigrationCommand extends Command
{
    protected static $defaultName = 'jarvish:make:migration';

    public static function getDefaultName(): string
    {
        return self::$defaultName;
    }

    protected function configure(): void
    {
        $this->setDescription('Creates a new migration file in src/Migrations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        // Ask for table name
        $question = new Question('Enter the table name for migration (e.g. users): ');
        $tableName = $helper->ask($input, $output, $question);

        if (!$tableName) {
            $output->writeln('<error>Table name is required!</error>');
            return Command::FAILURE;
        }

        // Prepare migration class name
        $className = 'Create' . ucfirst($tableName) . 'Table';

        // File path
        $migrationsDir = __DIR__ . '/../Migrations';
        if (!is_dir($migrationsDir)) {
            mkdir($migrationsDir, 0755, true);
        }

        $filePath = $migrationsDir . '/' . $className . '.php';

        // Generate migration file content
        $migrationCode = <<<PHP
<?php
namespace App\Migrations;

use mysqli;

class $className
{
    public function up(mysqli \$conn)
    {
        \$sql = "
            CREATE TABLE IF NOT EXISTS $tableName (
                id INT AUTO_INCREMENT PRIMARY KEY,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        \$conn->query(\$sql);
    }
}
PHP;

        // Save file
        file_put_contents($filePath, $migrationCode);

        $output->writeln("<info>✅ Migration file created: $filePath</info>");

        return Command::SUCCESS;
    }
}
