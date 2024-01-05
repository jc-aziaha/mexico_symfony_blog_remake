<?php

namespace App\Controller\Admin\Home;

use App\Controller\Admin\Comment\CommentController;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\ContactRepository;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/admin/home', name: 'admin_home_index', methods:['GET'])]
    public function index(
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        PostRepository $postRepository,
        ContactRepository $contactRepository,
        UserRepository $userRepository,
        CommentRepository $commentRepository,
        LikeRepository $likeRepository
    ): Response
    {
        return $this->render('pages/admin/home/index.html.twig', [
            "categories" => $categoryRepository->findAll(),
            "tags"       => $tagRepository->findAll(),
            "posts"      => $postRepository->findAll(),
            "contacts"   => $contactRepository->findAll(),
            "users"      => $userRepository->findAll(),
            "comments"   => $commentRepository->findAll(),
            "likes"      => $likeRepository->findAll(),
        ]);
    }
}
