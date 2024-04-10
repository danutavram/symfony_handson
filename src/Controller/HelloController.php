<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    #[Route(path: '/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        return new Response("HI!");
    }
}