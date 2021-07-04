<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(PostRepository $posts): Response
    {
        $petSitting = $posts->findLatest(0);
        $petMissing = $posts->findLatest(1);
        $petToAdopt = $posts->findLatest(2);
        $petFound = $posts->findLatest(3);
        return $this->render('page/home.html.twig', [
            'current_menu' => 'home',
            'petSitting' => $petSitting,
            'petMissing' => $petMissing,
            'petToAdopt' => $petToAdopt,
            'petFound' => $petFound
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
                ->to('martinez.m.joe@hotmail.fr')
                ->subject('vous avez reçu un email')
                ->text('Sender : ' . $contactFormData['email'] . \PHP_EOL .
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
