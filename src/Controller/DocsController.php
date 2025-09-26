<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class DocsController
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function index(Request $request): Response
    {
        $html = $this->twig->render('docs.html.twig', [
            'title' => 'JarvishStack Documentation'
        ]);
        return new Response($html);
    }
}