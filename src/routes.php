<?php
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

$routes->add('home', new Route('/', [
    '_controller' => 'App\\Controller\\HomeController::index',
]));

$routes->add('users', new Route('/users', [
    '_controller' => 'App\\Controller\\UserController::list',
]));

$routes->add('contact', new Route('/contact', [
    '_controller' => 'App\\Controller\\MailController::contactForm',
], [], [], '', [], ['GET', 'POST']));

$routes->add('contact_list', new Route('/contacts', [
    '_controller' => 'App\\Controller\\ContactUsList::index',
]));

$routes->add('docs', new Route('/docs', [
    '_controller' => 'App\\Controller\\DocsController::index',
]));

return $routes;
