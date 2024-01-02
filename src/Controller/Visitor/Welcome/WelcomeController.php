<?php

namespace App\Controller\Visitor\Welcome;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    #[Route('/', name: 'visitor_welcome_index', methods:['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy(['isPublished' => true], ['publishedAt' => 'DESC'], 3);

        return $this->render('pages/visitor/welcome/index.html.twig', [
            "posts" => $posts
        ]);
    }
}
