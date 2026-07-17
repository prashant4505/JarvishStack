<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

abstract class AbstractController
{
    protected Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    protected function render(string $template, array $context = [], int $status = 200): Response
    {
        return new Response($this->twig->render($template, $context), $status);
    }

    protected function redirect(string $url, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }

    protected function json(mixed $data, int $status = 200): JsonResponse
    {
        return new JsonResponse($data, $status);
    }

    protected function generateCsrfToken(string $action): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = [];
        }
        $token = bin2hex(random_bytes(32));
        $_SESSION['_csrf'][$action] = $token;
        return $token;
    }

    protected function validateCsrfToken(string $action, string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $valid = isset($_SESSION['_csrf'][$action])
            && hash_equals($_SESSION['_csrf'][$action], $token);

        unset($_SESSION['_csrf'][$action]);

        return $valid;
    }
}
