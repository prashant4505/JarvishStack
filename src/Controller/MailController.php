<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use App\Service\SmtpMailer;
use App\Model\Contact; // Model to interact with contact_us table
use App\Database\Connection;

class MailController
{
    private Environment $twig;
    private SmtpMailer $mailer;
    private Contact $contactModel;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        $this->mailer = new SmtpMailer();

        // Initialize contact model with database connection
        $conn = Connection::get();
        $this->contactModel = new Contact($conn);
    }

    // Show the form
    public function contactForm(Request $request): Response
    {
        $message = null;

        if ($request->getMethod() === 'POST') {
            $to = $request->request->get('email');
            $subject = $request->request->get('subject');
            $body = $request->request->get('message');

            if ($to && $subject && $body) {
                // Send email
                $success = $this->mailer->send($to, $subject, nl2br(htmlspecialchars($body)));

                // Store in database
                $this->contactModel->create([
                    'email' => $to,
                    'subject' => $subject,
                    'message' => $body
                ]);

                $message = $success ? 'Email sent successfully!' : 'Failed to send email, but stored in DB.';
            } else {
                $message = 'Please fill in all fields.';
            }
        }

        $html = $this->twig->render('contact.html.twig', [
            'message' => $message
        ]);
        return new Response($html);
    }
}
