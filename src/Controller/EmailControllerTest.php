<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class EmailControllerTest extends AbstractController
{
    #[Route('/send-test-email', name: 'send_test_email')]
    public function sendTestEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('aec.alexandru@gmail.com')
            ->to('test@example.com')
            ->subject('Prueba de Envío de Correo')
            ->text('Este es un correo de prueba.')
            ->html('<p>Este es un correo de prueba en formato HTML.</p>');

        try {
            $mailer->send($email);
            return new Response('Correo enviado exitosamente.');
        } catch (\Exception $e) {
            return new Response('Error al enviar el correo: ' . $e->getMessage(), 500);
        }
    }
}