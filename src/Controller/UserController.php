<?php
namespace App\Controller;

use App\Database\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use App\Model\User;

class UserController
{
    private Environment $twig;
    private User $userModel;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        $conn = Connection::get();
        $this->userModel = new User($conn);
    }

    public function list(Request $request): Response
    {
        $users = $this->userModel->getAll(20);

        $html = $this->twig->render('users.html.twig', [
            'users' => $users
        ]);
        return new Response($html);
    }
}
