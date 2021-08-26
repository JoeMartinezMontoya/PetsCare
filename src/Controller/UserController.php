<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{slug}-{id}", name="user_profile", requirements={"slug" : "[a-z0-9\-]*"})
     */
    public function userProfile(User $user, string $slug): Response
    {
        //Redirection si quelqu'un essaie de rentrer un autre slug
        if ($user->getSlug() !== $slug) {
            return $this->redirectToRoute('user_profile', [
                'id' => $user->getId(),
                'slug' => $user->getSlug()
            ], 301);
        }
        return $this->render('user/user_profile.html.twig');
    }

    /**
     * @Route("/user/{id}/edit", name="user_edit")
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_profile', [
                'id' => $this->getUser()->getId(),
                'slug' => $this->getUser()->getSlug()
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        $currentUserId = $user->getId();
        if ($currentUserId === $user->getId()) {
            $session = new Session();
            $session->invalidate();
        }

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('home');
    }
}
