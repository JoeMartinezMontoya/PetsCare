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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{

    private PostRepository $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="post_index")
     * @Route("/mine", name="my_post_index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $id = strpos($_SERVER['REQUEST_URI'], 'mine') ? $this->getUser()->getId() : null;
        $search = new PostSearch();
        $form = $this->createForm(PostSearchType::class, $search);
        $form->handleRequest($request);
        return $this->render('post/index.html.twig', [
            'current_menu' => 'post',
            'posts' => $this->repository->paginateAllVisible($search, $request->query->getInt('page', 1), $id),
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
            'current_menu' => 'post'
        ]);
    }

    /**
     * @Route("/new", name="post_new")
     * @throws \Exception
     */
    public function new(Request $request, PetRepository $repository): Response
    {
        $post = new Post();
        $choice = null;

        if (!empty($_GET) && isset($_GET['choice'])) {
            $choice = (int)$_GET['choice'];
            $selection = $this->crossRoad($choice, $post);
        }

        if (isset($selection['form'])) {
            $selection['form']->handleRequest($request);
            if ($selection['form']->isSubmitted() && $selection['form']->isValid()) {
                $data = $selection['form']->getData();
                // GLOBAL
                $post->setTitle($data->getTitle() ?? $selection['title']);
                $post->setContent($data->getContent() ?? '');
                $post->setCategory($choice);
                $post->setCreatedAt(new DateTime());
                $post->setAuthor($this->getUser());

                // PETSITTING
                if ($choice === 0) {
                    $post->setPetsToBeWatched($data->getPetsToBeWatched());

                    $userPets = $this->getUser()->getPets()->toArray();
                    $petsWatched = array_intersect_key($userPets, array_flip($data->getPetsToBeWatched()));

                    foreach ($petsWatched as $pet) {
                        if ($pet->getPictures()->first() !== false) {
                            $post->addPicture($pet->getPictures()->first());
                        }
                    }

                }

                // MISSING
                if ($choice === 1) {
                    // Looking for the corresponding entity
                    $missingPet = $this->getUser()->getPets()[$data->getMissingPet()];
                    $missingPet->setIsMissing(true);

                    $post->addPicture($missingPet->getPictures()->first());
                    $post->setMissingPet($missingPet->getId());
                }

                // FOUND
                if ($choice === 3) {
                    if (!empty($data->getTags()->toArray())) {
                        $tags = [];
                        foreach ($data->getTags() as $tag) {
                            $tags[] = $tag->getId();
                        }
                        try {
                            $check = $repository->findLostByTags($data->getSpecies(), $tags);
                        } catch (Exception | \Doctrine\DBAL\Exception $e) {
                            throw new \Exception($e->getMessage());
                        }
                    }
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($post);
                $entityManager->flush();

                if (!empty($check)) {
                    // If there is a match, it is sent by GET method
                    return $this->redirectToRoute('post_check', [
                        'check' => $check
                    ]);
                }
                $this->addFlash('success', 'Votre annonce à été publiée');
                return $this->redirectToRoute('my_post_index');
            }
            return $this->render('post/new.html.twig', [
                'current_menu' => 'post',
                'post' => $post,
                'form' => $selection['form']->createView(),
                'selectedForm' => $selection['selectedForm'],
                'slug' => $selection['slug']
            ]);
        }
        // Redirection if no choice passed in GET
        return $this->redirectToRoute('post_category_selection');
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
            'current_menu' => 'post',
            'missingPets' => $missingPets
        ]);
    }

    /**
     * @Route("/show/{id}", name="post_show")
     * @param Post $post
     * @param Security $security
     * @param int $id
     * @return Response
     */
    public function show(Post $post, Security $security, int $id): Response
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
     * @Route("/{id}/edit", name="post_edit")
     */
    public function edit(Request $request, Post $post): Response
    {
        if ($this->getUser()->getId() !== $post->getAuthor()->getId()) {
            $this->addFlash('danger', 'Il est interdit d\'essayer de modifier les données des autres !');
            return $this->redirectToRoute('my_post_index', [
            ], 301);
        }
        $selection = $this->crossRoad($post->getCategory(), $post);
        $selection['form']->handleRequest($request);

        if ($selection['form']->isSubmitted() && $selection['form']->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Annonce modifiée');
            return $this->redirectToRoute('my_post_index');
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'template' => $selection['selectedForm'],
            'form' => $selection['form']->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="post_delete", methods={"POST"})
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

    /**
     * @param $choice
     * @param $entity
     * @return array
     */
    private function crossRoad($choice, $entity): array
    {
        $output = [];
        switch ($choice) {
            case 0:
                $output['form'] = $this->createForm(PostJobType::class, $entity);
                $output['selectedForm'] = 'post/_form_job.html.twig';
                $output['title'] = "PetSitting";
                $output['slug'] = "job";
                break;
            case 1:
                $output['form'] = $this->createForm(PostMissingType::class, $entity);
                $output['selectedForm'] = 'post/_form_missing.html.twig';
                $output['title'] = "Disparition";
                $output['slug'] = "missing";
                break;
            case 2:
                $output['form'] = $this->createForm(PostAdoptionType::class, $entity);
                $output['selectedForm'] = 'post/_form_adoption.html.twig';
                $output['title'] = "Adoption";
                $output['slug'] = "adoption";
                break;
            case 3:
                $output['form'] = $this->createForm(PostFoundType::class, $entity);
                $output['selectedForm'] = 'post/_form_found.html.twig';
                $output['title'] = "Apercu";
                $output['slug'] = "found";
                break;
        }
        return $output;
    }
}