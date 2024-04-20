<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
            'posts' => $posts->findAll()
        ]);
    }

    #[Route(path: '/micro-post/{post}', name: 'app_micro_post_show')]
    public function showOne(MicroPost $post): Response
    {

        return $this->render('micro_post/show.html.twig', [
            'post' => $post
        ]);
    }

    #[Route(path: '/micro-post/add', name: 'app_micro_post_add', priority: 2)]
    public function add(Request $request): Response
    {
        $form = $this->createForm(MicroPostType::class, new MicroPost());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreated(new DateTime());

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
    public function edit(MicroPost $post, Request $request): Response
    {

        $form = $this->createForm(MicroPostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            $this->em->persist($post);
            $this->em->flush();

            // Add a flash
            $this->addFlash('success', 'Your micro post have been updated');
            // Redirect 
            return $this->redirectToRoute('app_micro_post');
        }

        return $this->render('micro_post/add.html.twig', [
            'form' => $form
        ]);
    }
}
