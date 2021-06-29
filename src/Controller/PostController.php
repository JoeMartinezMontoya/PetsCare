<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\Post;
use App\Entity\PostSearch;
use App\Form\PostAdoptionType;
use App\Form\PostFoundType;
use App\Form\PostMissingType;
use App\Form\PostSearchType;
use App\Form\PostJobType;
use App\Repository\PetRepository;
use App\Repository\PostRepository;
use DateTime;
use Doctrine\DBAL\Driver\Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="post_index", methods={"GET"})
     */
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $search = new PostSearch();
        $form = $this->createForm(PostSearchType::class, $search);
        $form->handleRequest($request);

        $posts = $paginator->paginate(
            $postRepository->findAllVisible($search),
            $request->query->getInt('page', 1),
            12
        );
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'current_menu' => 'postList',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/post-category", name="post_category_selection")
     */
    public function postCategorySelection()
    {
        if (!empty($_POST) && isset($_POST['choice'])) {
            return $this->redirectToRoute('post_new', [
                'choice' => $_POST['choice']
            ]);
        }

        return $this->render('post/selection.html.twig', [
            'post' => $_POST
        ]);
    }

    /**
     * @Route("/check", name="post_check")
     */
    public function check(PetRepository $repository): Response
    {
        $missingPets = [];
        if (!empty($_GET) && isset($_GET['check'])) {
            $results = $_GET['check'];

            // Transform every result to Object
            foreach ($results as $result) {
                $missingPets[] = $repository->findOneBy(['id' => $result['id']]);
            }

            // For the example, set the first of their pictures as their banner
            foreach ($missingPets as $iValue) {
                if (!empty($iValue->getPictures()->getValues())) {
                    $iValue->setPicture($iValue->getPictures()->first());
                }
            }
        }
        return $this->render('post/check.html.twig', [
            'missingPets' => $missingPets
        ]);
    }

    /**
     * @Route("/new", name="post_new", methods={"GET","POST"})
     * @throws \Exception
     */
    public function new(Request $request, PetRepository $repository): Response
    {
        $post = new Post();
        $choice = null;
        if (!empty($_GET) && isset($_GET['choice'])) {
            $choice = (int)$_GET['choice'];
            switch ($choice) {
                case 0:
                    $form = $this->createForm(PostJobType::class, $post);
                    $selectedForm = 'post/_form_job.html.twig';
                    $title = "Demande de PetSitting";
                    $key = 'post_job';
                    break;
                case 1:
                    $form = $this->createForm(PostMissingType::class, $post);
                    $selectedForm = 'post/_form_missing.html.twig';
                    $title = "Disparition";
                    $key = 'post_missing';
                    break;
                case 2:
                    $form = $this->createForm(PostAdoptionType::class, $post);
                    $selectedForm = 'post/_form_adoption.html.twig';
                    $title = "Adoption";
                    $key = 'post_adoption';
                    break;
                case 3:
                    $form = $this->createForm(PostFoundType::class, $post);
                    $selectedForm = 'post/_form_found.html.twig';
                    $title = "Apercu";
                    $key = 'post_found';
                    break;
            }
        }

        if (isset($form)) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                // GLOBAL
                $post->setTitle($form->getData()->getTitle() ?? $title);
                $post->setContent($form->getData()->getContent() ?? '');
                $post->setCategory($choice);
                $post->setCreatedAt(new DateTime());
                $post->setAuthor($this->getUser());

                // MISSING
                if ($choice === 1) {
                    // Looking for the corresponding entity
                    $missingPet = $this->getUser()->getPets()[$form->getData()->getMissingPet()];
                    $missingPet->setIsMissing(true);
                    $post->setMissingPet($missingPet->getId());
                }

                // FOUND
                if ($choice === 3) {
                    $foundPet = $form->getData();
                    if (!empty($foundPet->getTags()->toArray())) {
                        $tags = [];
                        foreach ($foundPet->getTags() as $tag) {
                            $tags[] = $tag->getId();
                        }
                        try {
                            $check = $repository->findLostByTags($tags);
                        } catch (Exception | \Doctrine\DBAL\Exception $e) {
                            throw new \Exception($e->getMessage());
                        }
                    }
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($post);
                $entityManager->flush();

                if (isset($check)) {
                    // If there is a match, it is sent by GET method
                    return $this->redirectToRoute('post_check', [
                        'check' => $check
                    ]);
                }

                return $this->redirectToRoute('post_index');
            }
            return $this->render('post/new.html.twig', [
                'post' => $post,
                'form' => $form->createView(),
                'selectedForm' => $selectedForm,
                'current_menu' => 'createPost'
            ]);
        }
        // Redirection if no choice passed in GET
        return $this->redirectToRoute('post_category_selection');
    }

    /**
     * @Route("/{id}", name="post_show", methods={"GET"})
     */
    public function show(Post $post, Security $security): Response
    {
        $missingPet = [];
        switch ($post->getCategory()) {
            case 0:
                $template = 'post/job.html.twig';
                break;
            case 1:
                $template = 'post/missing.html.twig';
                foreach ($security->getUser()->getPets() as $pet) {
                    if ($pet->getId() === $post->getMissingPet()) {
                        $missingPet = $pet;
                    }
                }
                break;
            case 2:
                $template = 'post/adoption.html.twig';
                break;
            case 3:
                $template = 'post/found.html.twig';
                break;
        }
        return $this->render($template, [
            'post' => $post,
            'missingPet' => $missingPet
        ]);
    }

    /**
     * @Route("/{id}/edit", name="post_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Post $post): Response
    {
        $template = '';
        $form = '';
        switch ($post->getCategory()) {
            case 0:
                $template = 'post/job.html.twig';
                $form = PostJobType::class;
                break;
            case 1:
                $template = 'post/_form_missing.html.twig';
                $form = PostMissingType::class;
                break;
            case 2:
                $template = 'post/_form_adoption.html.twig';
                $form = PostAdoptionType::class;
                break;
            case 3:
                $template = 'post/found.html.twig';
                $form = PostFoundType::class;
                break;
        }

        $form = $this->createForm($form, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'template' => $template,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="post_picture_delete", methods={"DELETE"})
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
     * @Route("/{id}", name="post_delete", methods={"POST"})
     */
    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_index');
    }
}
