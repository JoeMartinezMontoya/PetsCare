<?php

namespace App\Controller;

use App\Entity\Pet;
use App\Form\PetIsFoundType;
use App\Form\PetType;
use App\Repository\PictureRepository;
use App\Repository\PostRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/pet")
 */
class PetController extends AbstractController
{
    /**
     * @Route("/new", name="pet_new")
     */
    public function new(Request $request): Response
    {
        $pet = new Pet();
        $form = $this->createForm(PetType::class, $pet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pet->setOwner($this->getUser());
            $pet->setIsMissing(false);
            $pet->setCreatedAt(new DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pet);
            $entityManager->flush();

            return $this->redirectToRoute('pet_show', [
                'id' => $pet->getId(),
            ]);
        }

        return $this->render('pet/new.html.twig', [
            'pet' => $pet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="pet_show")
     */
    public function show(Pet $pet, Request $request, PostRepository $postRepository, PictureRepository $pictureRepository): Response
    {
        $form = $this->createForm(PetIsFoundType::class);
        $form->handleRequest($request);

        #TODO: Rework this
        if ($form->isSubmitted() && $form->isValid()) {
            $posts = $postRepository->findBy([
                'missingPet' => $pet->getId(),
                'category' => [1,3]
            ]);

            $postsIds = [];
            foreach ($posts as $post) {
                $postsIds[] = $post->getId();
            }
            $pictures = $pictureRepository->findBy([
                'post' => $postsIds
            ]);

            // Dissociate pictures from posts, otherwise it throws an error
            $entityManager = $this->getDoctrine()->getManager();
            foreach ($pictures as $picture) {
                $picture->setPost(null);
                $entityManager->persist($picture);
            }
            $entityManager->flush();

            $postRepository->deleteLostAndFoundPostsAbout($pet);
            $pet->setIsMissing(false);
            $entityManager->persist($pet);
            $entityManager->flush();

            $this->addFlash('success', 'Bon retour ' . $pet->getName());
        }

        return $this->render('pet/show.html.twig', [
            'pet' => $pet,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="pet_edit")
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
     * @Route("/delete/{id}", name="pet_delete", methods={"POST"})
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
