<?php
namespace App\Controller;

use App\Database\Connection;
use App\Model\Contact;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ContactUsList
{
    private Environment $twig;
    private Contact $contactModel;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        $conn = Connection::get();
        $this->contactModel = new Contact($conn);
    }

    public function index(Request $request): Response
    {
        $contact = $this->contactModel->getAll(20);
        $html = $this->twig->render('contactuslist.html.twig', [
            'message' => 'Contact Us List',
            'contacts' => $contact
        ]);
        return new Response($html);
    }
}