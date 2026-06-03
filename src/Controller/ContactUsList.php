<?php
namespace App\Controller;

use App\Database\Connection;
use App\Model\Contact;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactUsList extends AbstractController
{
    private Contact $contactModel;

    public function __construct(\Twig\Environment $twig)
    {
        parent::__construct($twig);
        $this->contactModel = new Contact(Connection::get());
    }

    public function index(Request $request): Response
    {
        return $this->render('contactuslist.html.twig', [
            'contacts' => $this->contactModel->getAll(50),
        ]);
    }
}
