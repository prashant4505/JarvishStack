<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DocsController extends AbstractController
{
    public function index(Request $request): Response
    {
        return $this->render('docs.html.twig', [
            'title' => 'JarvishStack Documentation',
        ]);
    }
}
