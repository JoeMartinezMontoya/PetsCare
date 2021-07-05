<?php

namespace App\Controller;

use App\Form\ContactUserType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/meeting/{userid}-{petid}-{ownerid}", name="meeting")
     * @throws TransportExceptionInterface
     */
    public function meeting(UserRepository $userRepository, PostRepository $postRepository, Request $request, MailerInterface $mailer): Response
    {
        // Catch the ids in GET
        #TODO: Find a better way to do so
        $array = explode('/', $request->getPathInfo());
        $subjects = explode('-', end($array));
        $founderId = (int)reset($subjects);
        $petId = (int)$subjects[1];
        $ownerId = (int)end($subjects);

        $founder = $userRepository->findOneBy(['id' => $founderId]);
        $owner = $userRepository->findOneBy(['id' => $ownerId]);

        $form = $this->createForm(ContactUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();
            $message = (new Email())
                ->from($contactFormData['email'])
                ->to($owner->getEmail())
                ->subject('Votre animal à été retrouvé !')
                ->text('Sender : ' . $contactFormData['email'] . \PHP_EOL .
                    'Name :' . $contactFormData['username'] . 'Content :' . $contactFormData['message'],
                    'text/plain');
            $mailer->send($message);

            // Get the last Post from founder
            $lastPost = $postRepository->findOneBy(['author' => $founderId], ['created_at'=>'DESC'],1,0);
            $lastPost->setMissingPet($petId);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lastPost);
            $entityManager->flush();

            $this->addFlash('success', 'Votre message a été envoyé');
            return $this->redirectToRoute('user_profile', [
                'id' => $this->getUser()->getId(),
                'slug' => $this->getUser()->getSlug()
            ]);

        }

        return $this->render('contact/meeting.html.twig', [
            'form' => $form->createView(),
            'founder' => $founder
        ]);
    }
}
