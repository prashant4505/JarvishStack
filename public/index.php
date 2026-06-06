<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../global.php';

use App\Database\QueryBuilder;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Load environment
(new Dotenv())->loadEnv(__DIR__ . '/../.env');

$isDev = ($_ENV['APP_ENV'] ?? 'prod') === 'dev';

// Twig setup
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig   = new Environment($loader, [
    'debug' => $isDev,
    'cache' => $isDev ? false : __DIR__ . '/../var/cache/twig',
]);

if ($isDev) {
    $twig->addExtension(new \Twig\Extension\DebugExtension());
}

// asset() helper: appends ?v=<mtime> for cache-busting
$twig->addFunction(new \Twig\TwigFunction('asset', function (string $path): string {
    $fullPath = __DIR__ . '/' . ltrim($path, '/');
    $version  = file_exists($fullPath) ? filemtime($fullPath) : time();
    return '/' . ltrim($path, '/') . '?v=' . $version;
}));

// Check which tables exist so templates can hide nav links before migrations run
$tablesReady = ['users' => false, 'contacts' => false];
try {
    $conn = App\Database\Connection::get();
    $tablesReady['users']    = QueryBuilder::tableExists($conn, 'users');
    $tablesReady['contacts'] = QueryBuilder::tableExists($conn, 'contact_us');
} catch (\Throwable) {
    // DB unavailable — both flags stay false, links stay hidden
}
$twig->addGlobal('tablesReady', $tablesReady);

// Load routes
$routes = require __DIR__ . '/../src/routes.php';

// Handle request
$request = Request::createFromGlobals();
$context = new RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

try {
    $parameters = $matcher->match($request->getPathInfo());
    [$class, $method] = explode('::', $parameters['_controller']);

    $controller = new $class($twig);
    $response   = call_user_func([$controller, $method], $request);

} catch (ResourceNotFoundException) {
    $body     = file_exists(__DIR__ . '/../templates/errors/404.html.twig')
        ? $twig->render('errors/404.html.twig')
        : '<h1>404 Not Found</h1>';
    $response = new Response($body, 404);

} catch (MethodNotAllowedException) {
    $body     = file_exists(__DIR__ . '/../templates/errors/404.html.twig')
        ? $twig->render('errors/404.html.twig')
        : '<h1>405 Method Not Allowed</h1>';
    $response = new Response($body, 405);

} catch (\Throwable $e) {
    $message  = $isDev ? $e->getMessage() : 'An unexpected error occurred. Please try again later.';
    $body     = file_exists(__DIR__ . '/../templates/errors/500.html.twig')
        ? $twig->render('errors/500.html.twig', ['message' => $message])
        : '<h1>500 Internal Server Error</h1>';
    $response = new Response($body, 500);
}

$response->send();
