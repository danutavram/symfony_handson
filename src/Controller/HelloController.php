<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    private array $messages = ["Hello", "Hi", "Bye!"];

    #[Route(path: '/{limit?3}', name: 'home', methods: ['GET'])]
    public function index(int $limit): Response
    {
        return $this->render('hello/index.html.twig', [
            'messages' => $this->messages,
            'limit' => $limit
        ]);
    }

    #[Route('/messages/{id<\d+>}', name: "app_show_one")]
    public function showOne(int $id): Response
    {
        return $this->render(
            'hello/show_one.html.twig',
            [
                'message' => $this->messages[$id]
            ]
        );
        // return new Response($this->messages[$id]);
    }
}