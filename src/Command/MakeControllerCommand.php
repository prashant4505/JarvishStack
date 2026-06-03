<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MakeControllerCommand extends Command
{
    protected static $defaultName = 'jarvish:make:controller';

    public static function getDefaultName(): string
    {
        return self::$defaultName;
    }

    protected function configure(): void
    {
        $this->setDescription('Scaffold a new controller, Twig template, and route.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $controllerName = $helper->ask($input, $output, new Question('Controller name (e.g. HelloController): '));
        if (!$controllerName) {
            $output->writeln('<error>Controller name is required.</error>');
            return Command::FAILURE;
        }

        $routePath = $helper->ask($input, $output, new Question('Route path (e.g. /hello): '));
        if (!$routePath) {
            $output->writeln('<error>Route path is required.</error>');
            return Command::FAILURE;
        }

        $controllerFile = __DIR__ . '/../Controller/' . $controllerName . '.php';
        $templateName   = $this->getTemplateName($controllerName);
        $templateFile   = __DIR__ . '/../../templates/' . $templateName;
        $routesFile     = __DIR__ . '/../../src/routes.php';
        $routeName      = $this->getRouteName($controllerName);

        // Controller
        $controllerCode = <<<PHP
        <?php
        namespace App\Controller;

        use Symfony\Component\HttpFoundation\Request;
        use Symfony\Component\HttpFoundation\Response;

        class $controllerName extends AbstractController
        {
            public function index(Request \$request): Response
            {
                return \$this->render('$templateName', [
                    'message' => 'Hello from $controllerName!',
                ]);
            }
        }
        PHP;

        file_put_contents($controllerFile, $controllerCode);
        $output->writeln("<info>Controller created:  $controllerFile</info>");

        // Twig template
        $templateCode = <<<TWIG
        {% extends 'base.html.twig' %}

        {% block title %}$controllerName{% endblock %}

        {% block body %}
        <h1>Hello from $controllerName!</h1>
        <p>{{ message }}</p>
        {% endblock %}
        TWIG;

        file_put_contents($templateFile, $templateCode);
        $output->writeln("<info>Template created:    $templateFile</info>");

        // Inject route into routes.php before the return statement
        $routesContent   = file_get_contents($routesFile);
        $routeEntry      = "\n// Route for $controllerName\n"
            . "\$routes->add('$routeName', new Symfony\\Component\\Routing\\Route('$routePath', [\n"
            . "    '_controller' => 'App\\\\Controller\\\\$controllerName::index'\n"
            . "]));\n\n";
        $returnPosition  = strpos($routesContent, 'return $routes;');

        if ($returnPosition === false) {
            $output->writeln("<error>Could not locate 'return \$routes;' in routes.php.</error>");
            return Command::FAILURE;
        }

        file_put_contents(
            $routesFile,
            substr($routesContent, 0, $returnPosition) . $routeEntry . substr($routesContent, $returnPosition)
        );
        $output->writeln("<info>Route registered:    $routePath -> $controllerName::index</info>");

        return Command::SUCCESS;
    }

    private function getTemplateName(string $controllerName): string
    {
        return strtolower(str_replace('Controller', '', $controllerName)) . '.html.twig';
    }

    private function getRouteName(string $controllerName): string
    {
        return strtolower(str_replace('Controller', '', $controllerName));
    }
}
