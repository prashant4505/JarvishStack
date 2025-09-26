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

    protected function configure()
    {
        $this->setDescription('Creates a new controller, route, and twig template.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
{
    $helper = $this->getHelper('question');

    // Ask for Controller name
    $question = new Question('Enter Controller name (e.g. HelloController): ');
    $controllerName = $helper->ask($input, $output, $question);
    if (!$controllerName) {
        $output->writeln("<error>Controller name is required!</error>");
        return Command::FAILURE;
    }

    // Ask for Route path
    $question = new Question('Enter route path (e.g. /hello): ');
    $routePath = $helper->ask($input, $output, $question);
    if (!$routePath) {
        $output->writeln("<error>Route path is required!</error>");
        return Command::FAILURE;
    }

    // Paths
    $controllerFile = __DIR__ . '/../Controller/' . $controllerName . '.php';
    $templateFile   = __DIR__ . '/../../templates/' . strtolower(str_replace('Controller', '', $controllerName)) . '.html.twig';
    $routesFile     = __DIR__ . '/../../src/routes.php';

    // Create Controller
    $controllerCode = <<<PHP
    <?php
    namespace App\Controller;

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Twig\Environment;

    class $controllerName
    {
        private Environment \$twig;

        public function __construct(Environment \$twig)
        {
            \$this->twig = \$twig;
        }

        public function index(Request \$request): Response
        {
            \$html = \$this->twig->render('{$this->getTemplateName($controllerName)}', [
                'message' => 'Hello from $controllerName!'
            ]);
            return new Response(\$html);
        }
    }
    PHP;

    file_put_contents($controllerFile, $controllerCode);
    $output->writeln("<info>✅ Controller created at: $controllerFile</info>");

    // Create Twig template
    $templateCode = <<<TWIG
    {% extends 'base.html.twig' %}

    {% block title %}$controllerName{% endblock %}

    {% block body %}
        <h1>Hello from $controllerName!</h1>
        <p>{{ message }}</p>
    {% endblock %}
    TWIG;

    file_put_contents($templateFile, $templateCode);
    $output->writeln("<info>✅ Template created at: $templateFile</info>");

    // Update routes.php - Insert before return statement
    $routesContent = file_get_contents($routesFile);
    
    // Prepare the new route entry
    $routeEntry = "\n// Auto-generated route for $controllerName\n\$routes->add('{$this->getRouteName($controllerName)}', new Symfony\Component\Routing\Route('$routePath', [\n    '_controller' => 'App\\\\Controller\\\\$controllerName::index'\n]));\n\n";
    
    // Find the position of "return $routes;" and insert before it
    $returnPosition = strpos($routesContent, 'return $routes;');
    
    if ($returnPosition !== false) {
        // Insert the new route before the return statement
        $newRoutesContent = substr($routesContent, 0, $returnPosition) . 
                           $routeEntry . 
                           substr($routesContent, $returnPosition);
        
        file_put_contents($routesFile, $newRoutesContent);
        $output->writeln("<info>✅ Route added to routes.php</info>");
    } else {
        $output->writeln("<error>❌ Could not find 'return \$routes;' in routes.php</error>");
        return Command::FAILURE;
    }

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
