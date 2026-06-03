<?php
namespace App\Controller;

use App\Database\Connection;
use App\Model\Contact;
use App\Service\SmtpMailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MailController extends AbstractController
{
    private SmtpMailer $mailer;
    private Contact $contactModel;

    public function __construct(\Twig\Environment $twig)
    {
        parent::__construct($twig);
        $this->mailer = new SmtpMailer();
        $this->contactModel = new Contact(Connection::get());
    }

    public function contactForm(Request $request): Response
    {
        $message = null;
        $csrfToken = $this->generateCsrfToken('contact_form');

        if ($request->getMethod() === 'POST') {
            $submittedToken = $request->request->get('_token', '');

            if (!$this->validateCsrfToken('contact_form', $submittedToken)) {
                $message = 'Invalid request. Please try again.';
            } else {
                $to      = trim($request->request->get('email', ''));
                $subject = trim($request->request->get('subject', ''));
                $body    = trim($request->request->get('message', ''));

                if (!$to || !$subject || !$body) {
                    $message = 'Please fill in all fields.';
                } elseif (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                    $message = 'Please enter a valid email address.';
                } else {
                    $this->contactModel->create([
                        'email'   => $to,
                        'subject' => $subject,
                        'message' => $body,
                    ]);

                    $sent = $this->mailer->send(
                        $to,
                        $subject,
                        nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8'))
                    );

                    $message = $sent
                        ? 'Your message has been sent successfully!'
                        : 'Message saved, but email delivery failed. We will follow up shortly.';
                }
            }
        }

        return $this->render('contact.html.twig', [
            'message'    => $message,
            'csrf_token' => $csrfToken,
        ]);
    }
}
