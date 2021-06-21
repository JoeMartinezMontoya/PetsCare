<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\PostSearch;
use App\Form\PostAdoptionType;
use App\Form\PostFoundType;
use App\Form\PostMissingType;
use App\Form\PostSearchType;
use App\Form\PostJobType;
use App\Repository\PostRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
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
     * @Route("/new", name="post_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $post = new Post();
        if (!empty($_GET) && isset($_GET['choice'])) {
            $choice = $_GET['choice'];
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

                // MISSING
                if ($choice === 1) {
                    // Looking for the corresponding entity
                    $missingPetIndex = (int)$request->request->get($key)['missingPet'];
                    $missingPet = $this->getUser()->getPets()[$missingPetIndex]->getId();

                    $post->setMissingPet($missingPet);
                }


                // GLOBAL
                $post->setTitle($request->request->get($key)['title'] ?? $title);
                $post->setContent($request->request->get($key)['content'] ?? '');
                $post->setCategory($choice);
                $post->setCreatedAt(new DateTime());
                $post->setAuthor($this->getUser());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($post);
                $entityManager->flush();

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
        $form = $this->createForm(PostJobType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
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
