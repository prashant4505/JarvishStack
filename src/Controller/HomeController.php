<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class HomeController
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function index(Request $request): Response
    {
        $html = $this->twig->render('home.html.twig', [
            'message' => '🎉 Welcome to JarvishStack!'
        ]);
        return new Response($html);
    }
}
