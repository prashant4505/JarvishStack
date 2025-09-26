<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../global.php';

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

// ----------------------
// Load environment variables
// ----------------------
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/../.env');

// ----------------------
// Twig setup
// ----------------------
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

// Add asset() function for CSS/JS
$twig->addFunction(new \Twig\TwigFunction('asset', function ($path) {
    $fullPath = __DIR__ . '/../public/' . ltrim($path, '/');
    $version = file_exists($fullPath) ? filemtime($fullPath) : time();
    return '/' . ltrim($path, '/') . '?v=' . $version;
}));

// ----------------------
// Load routes
// ----------------------
$routes = require __DIR__ . '/../src/routes.php';

// ----------------------
// Handle Request
// ----------------------
$request = Request::createFromGlobals();
$context = new RequestContext();
$context->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

try {
    $parameters = $matcher->match($request->getPathInfo());
    [$class, $method] = explode('::', $parameters['_controller']);

    // Controller must accept Twig as constructor dependency
    $controller = new $class($twig);

    // Call controller method
    $response = call_user_func_array([$controller, $method], [$request]);

} catch (ResourceNotFoundException $e) {
    // Render 404 page if exists, otherwise plain text
    $template404 = __DIR__ . '/../templates/errors/404.html.twig';
    if (file_exists($template404)) {
        $response = new Response($twig->render('errors/404.html.twig'), 404);
    } else {
        $response = new Response("404 Not Found", 404);
    }
} catch (\Exception $e) {
    // Render 500 page if exists, otherwise plain text
    $template500 = __DIR__ . '/../templates/errors/500.html.twig';
    if (file_exists($template500)) {
        $response = new Response($twig->render('errors/500.html.twig', ['message' => $e->getMessage()]), 500);
    } else {
        $response = new Response("An error occurred: " . $e->getMessage(), 500);
    }
}

// Send the HTTP response
$response->send();
