<?php
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

// Create a route collection
$routes = new RouteCollection();

// Home page
$routes->add('home', new Route('/', [
    '_controller' => 'App\\Controller\\HomeController::index'
]));

// Users page
$routes->add('users', new Route('/users', [
    '_controller' => 'App\\Controller\\UserController::list'
]));

// Contact form page
$routes->add('contact', new Route('/contact', [
    '_controller' => 'App\\Controller\\MailController::contactForm'
]));

// Send test email page
$routes->add('send_mail', new Route('/send-mail', [
    '_controller' => 'App\\Controller\\MailController::sendTest'
]));


// Auto-generated route for ContactUsList
$routes->add('contactuslist', new Symfony\Component\Routing\Route('/contacts', [
    '_controller' => 'App\\Controller\\ContactUsList::index'
]));


// Auto-generated route for DocsController
$routes->add('docs', new Symfony\Component\Routing\Route('/docs', [
    '_controller' => 'App\\Controller\\DocsController::index'
]));

return $routes;
