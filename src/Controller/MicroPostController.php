<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\MicroPost;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MicroPostController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $posts): Response
    {
        // dd($posts->findOneBy(['title' => 'Welcome!']));
        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts->findAllWithComments()
        ]);
    }

    #[Route('/micro-post/top-liked', name: 'app_micro_post_topliked')]
    public function topLiked(MicroPostRepository $posts): Response
    {
        return $this->render('micro_post/top_liked.html.twig', [
            'posts' => $posts->findAllWithMinLikes(2)
        ]);
    }

    #[Route('/micro-post/follows', name: 'app_micro_post_follows')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function follows(MicroPostRepository $posts): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        return $this->render('micro_post/follows.html.twig', [
            'posts' => $posts->findAllByAuthors($currentUser->getFollows())
        ]);
    }

    #[Route(path: '/micro-post/{post}', name: 'app_micro_post_show')]
    #[IsGranted(MicroPost::VIEW, 'post')]
    public function showOne(MicroPost $post): Response
    {

        return $this->render('micro_post/show.html.twig', [
            'post' => $post
        ]);
    }

    #[Route(path: '/micro-post/add', name: 'app_micro_post_add', priority: 2)]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(Request $request): Response
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $form = $this->createForm(MicroPostType::class, new MicroPost());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setAuthor($this->getUser());

            $this->em->persist($post);
            $this->em->flush();

            // Add a flash
            $this->addFlash('success', 'Your micro post have been added');
            // Redirect 
            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render('micro_post/add.html.twig', [
            'form' => $form
        ]);
    }

    #[Route(path: '/micro-post/{post}/edit', name: 'app_micro_post_edit')]
    #[IsGranted(MicroPost::EDIT, 'post')]
    public function edit(MicroPost $post, Request $request): Response
    {

        $form = $this->createForm(MicroPostType::class, $post);

        $form->handleRequest($request);

        // $this->denyAccessUnlessGranted(MicroPost::EDIT, $post);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            $this->em->persist($post);
            $this->em->flush();

            // Add a flash
            $this->addFlash('success', 'Your micro post have been updated');
            // Redirect 
            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render('micro_post/edit.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }

    #[Route(path: '/micro-post/{post}/comment', name: 'app_micro_post_comment')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function addComment(MicroPost $post, Request $request, CommentRepository $comments): Response
    {

        $form = $this->createForm(CommentType::class, new Comment());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setPost($post);
            $comment->setAuthor($this->getUser());

            $this->em->persist($comment);
            $this->em->flush();

            // Add a flash
            $this->addFlash('success', 'Your comment have been updated');
            // Redirect 
            return $this->redirectToRoute('app_micro_post_show', [
                'post' => $post->getId()
            ]);
        }

        return $this->render('micro_post/comment.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }
}
