<?php

namespace App\Controller;

use App\Form\ContactUserType;
use App\Repository\PetRepository;
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
    public function meeting(PetRepository $petRepository, UserRepository $userRepository, PostRepository $postRepository, Request $request, MailerInterface $mailer, int $userid, int $petid, int $ownerid): Response
    {
        if ($userid !== $this->getUser()->getId()) {
            return $this->redirectToRoute('meeting', [
                'userid' => $this->getUser()->getId(),
                'petid' => $petid,
                'ownerid' => $ownerid
            ], 301);
        }

        $founder = $userRepository->findOneBy(['id' => $userid]);
        $owner = $userRepository->findOneBy(['id' => $ownerid]);
        $foundPet = $petRepository->findOneBy(['id' => $petid]);

        if ($owner !== null) {
            $ownerPetsIds = [] ;
            foreach ($owner->getPets() as $pet) {
                $ownerPetsIds[] = $pet->getId();
            }
        }

        if (!in_array($petid, $ownerPetsIds, true) || $ownerid !== $foundPet->getOwner()->getId()) {
            #TODO: Find a better way to secure the URL
            $this->addFlash('alert', 'On ne fait pas de faux espoir !');
            return $this->redirectToRoute('home', [
            ], 301);
        }

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
            $lastPost = $postRepository->findOneBy(['author' => $founder->getId()], ['created_at' => 'DESC']);
            $lastPost->setMissingPet($foundPet->getId());

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
