<?php
namespace App\Controller;

use App\Database\Connection;
use App\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    private User $userModel;

    public function __construct(\Twig\Environment $twig)
    {
        parent::__construct($twig);
        $this->userModel = new User(Connection::get());
    }

    public function list(Request $request): Response
    {
        return $this->render('users.html.twig', [
            'users' => $this->userModel->getAll(20),
        ]);
    }
}
