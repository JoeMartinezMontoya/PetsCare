<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private const MAIL = 'martinez.m.joe@hotmail.fr';
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('page/home.html.twig', [
            'current_menu' => 'home'
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(): Response
    {
        return $this->render('page/about.html.twig', [
            'current_menu' => 'about'
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     * @throws TransportExceptionInterface
     */
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();
            $message = (new Email())
                ->from($contactFormData['email'])
                ->to(self::MAIL)
                ->subject('Quelqu\'un vous contacte via PetsCare !')
                ->text('Emetteur : ' . $contactFormData['email'] . \PHP_EOL .
                    $contactFormData['message'],
                    'text/plain');
            $mailer->send($message);
            $this->addFlash('success', 'Votre message a été envoyé');
            return $this->redirectToRoute('home');
        }

        return $this->render('page/contact.html.twig', [
            'form' => $form->createView(),
            'current_menu' => 'contact'
        ]);
    }
}
