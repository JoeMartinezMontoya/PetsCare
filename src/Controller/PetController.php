<?php

namespace App\Controller;

use App\Entity\Pet;
use App\Entity\Picture;
use App\Form\PetIsFoundType;
use App\Form\PetType;
use App\Repository\PetRepository;
use App\Repository\PostRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pet")
 */
class PetController extends AbstractController
{
    /**
     * @Route("/", name="pet_list", methods={"GET"})
     */
    public function index(PetRepository $petRepository): Response
    {
        return $this->render('pet/index.html.twig', [
            'pets' => $petRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="pet_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $pet = new Pet();
        $user = $this->getUser();
        $form = $this->createForm(PetType::class, $pet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->getData()->isOwned() === true) {
                $pet->setIsMissing(false);
                $pet->setOwned(true);
                $pet->setOwner($user);
            }
            $pet->setCreatedAt(new DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pet);
            $entityManager->flush();

            return $this->redirectToRoute('user_profile', [
                'id' => $user->getId(),
                'slug' => $user->getSlug()
            ]);
        }

        return $this->render('pet/new.html.twig', [
            'pet' => $pet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pet_show", methods={"GET|POST"})
     */
    public function show(Pet $pet, Request $request, PostRepository $repository): Response
    {
        $form = $this->createForm(PetIsFoundType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $posts = $repository->findLostAndFoundPostsAbout($pet->getId());
            $entityManager = $this->getDoctrine()->getManager();
            $pet->setIsMissing(false);
            $entityManager->persist($pet);
            $entityManager->flush();
        }

        return $this->render('pet/show.html.twig', [
            'pet' => $pet,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pet_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Pet $pet): Response
    {
        $form = $this->createForm(PetType::class, $pet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('pet_show', [
                'id' => $pet->getId()
            ]);
        }

        return $this->render('pet/edit.html.twig', [
            'pet' => $pet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pet_picture_delete", methods={"DELETE"})
     * @param Picture $picture
     * @param Request $request
     * @return JsonResponse
     * @throws \JsonException
     */
    public function pictureDelete(Picture $picture, Request $request)
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $data['_token'])) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($picture);
            $entityManager->flush();

            return new JsonResponse([
                'success' => 1
            ]);

        }
        return new JsonResponse([
            'error' => 'Token invalide'
        ], 400);

    }

    /**
     * @Route("/{id}", name="pet_delete", methods={"POST"})
     */
    public function delete(Request $request, Pet $pet): Response
    {
        if ($this->isCsrfTokenValid('delete' . $pet->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_profile', [
            'id' => $this->getUser()->getId(),
            'slug' => $this->getUser()->getSlug()
        ]);
    }
}
