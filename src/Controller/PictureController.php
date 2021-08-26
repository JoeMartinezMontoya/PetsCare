<?php

namespace App\Controller;

use App\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/picture")
 */
class PictureController extends AbstractController
{
    /**
     * @Route("/{id}", name="picture_delete", methods={"DELETE"})
     * @param Picture $picture
     * @param Request $request
     * @return JsonResponse
     * @throws \JsonException
     */
    public function pictureDelete(Picture $picture, Request $request): JsonResponse
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
}
