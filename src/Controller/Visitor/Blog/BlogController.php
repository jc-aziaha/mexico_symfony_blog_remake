<?php

namespace App\Controller\Visitor\Blog;

use App\Entity\Tag;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\CommentFormType;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'visitor_blog_index', methods:['GET'])]
    public function index(
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        PostRepository $postRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $categories = $categoryRepository->findAll();
        $tags       = $tagRepository->findAll();
        $query      = $postRepository->findBy(['isPublished' => true], ['publishedAt' => 'DESC']);

        $posts = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2 /*limit per page*/
        );

        return $this->render('pages/visitor/blog/index.html.twig', [
            'categories' => $categories,
            'tags'       => $tags,
            'posts'      => $posts,
        ]);
    }


    #[Route('/blog/posts/filter-by-category/{id}/{slug}', name: 'visitor_blog_posts_filter_by_category', methods:['GET'])]
    public function filterByCategory(
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        PostRepository $postRepository,
        Category $category
    ): Response
    {

        $categories = $categoryRepository->findAll();
        $tags       = $tagRepository->findAll();
        $posts      = $postRepository->filterPostsByCategory($category->getId());

        return $this->render('pages/visitor/blog/index.html.twig', [
            'categories' => $categories,
            'tags'       => $tags,
            'posts'      => $posts,
        ]);
    }


    #[Route('/blog/posts/filter-by-tag/{id}/{slug}', name: 'visitor_blog_posts_filter_by_tag', methods:['GET'])]
    public function filterByTag(
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository,
        PostRepository $postRepository,
        Tag $tag
    ): Response
    {

        $categories = $categoryRepository->findAll();
        $tags       = $tagRepository->findAll();
        $posts      = $postRepository->filterPostsByTag($tag->getId());

        return $this->render('pages/visitor/blog/index.html.twig', [
            'categories' => $categories,
            'tags'       => $tags,
            'posts'      => $posts,
        ]);
    }


    #[Route('/blog/post/{id}/{slug}/show', name: 'visitor_blog_post_show', methods:['GET', 'POST'])]
    public function show(
        Post $post, 
        Request $request, 
        EntityManagerInterface $em,
    ): Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentFormType::class, $comment);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() )  
        {

            $comment->setUser($this->getUser());
            $comment->setPost($post);
            $comment->setIsEnable(true);

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('visitor_blog_post_show', [
                'id' => $post->getId(), 
                'slug' => $post->getSlug()
            ]);
        }

        return $this->render('pages/visitor/blog/show.html.twig', [
            "post" => $post,
            "form" => $form->createView()
        ]);
    }



}
