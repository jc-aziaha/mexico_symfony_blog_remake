<?php

namespace App\Controller\Admin\Post;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/admin/post/list', name: 'admin_post_index', methods:['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();

        return $this->render('pages/admin/post/index.html.twig', [
            "posts" => $posts
        ]);
    }
    
    #[Route('/admin/post/create', name: 'admin_post_create', methods:['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $post = new Post();

        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) 
        {

            $admin = $this->getUser();

            $post->setUser($admin);
            $post->setIsPublished(false);

            $em->persist($post);
            $em->flush();

            $this->addFlash('success', "L'article a été créé et sauvegardé.");

            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('pages/admin/post/create.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/admin/post/{id}/publish', name: 'admin_post_publish', methods:['PUT'])]
    public function publish(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        if ( $this->isCsrfTokenValid("publish_post_" . $post->getId(), $request->request->get('csrf_token')) ) 
        {
            if ( false === $post->isIsPublished() ) 
            {
                $post->setIsPublished(true);
                $post->setPublishedAt(new DateTimeImmutable());

                $this->addFlash('success', "L'article a été publié");
            }
            else
            {
                $post->setIsPublished(false);
                $post->setPublishedAt(null);

                $this->addFlash('success', "L'article a été retiré de la liste des publications.");   
            }

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('admin_post_index');
        }
    }


    #[Route('/admin/post/{id}/show', name: 'admin_post_show', methods:['GET'])]
    public function show(Post $post): Response
    {
        return $this->render("pages/admin/post/show.html.twig", [
            'post' => $post
        ]);
    }


    #[Route('/admin/post/{id}/edit', name: 'admin_post_edit', methods:['GET', 'PUT'])]
    public function edit(Post $post, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(PostFormType::class, $post, [
            "method" => "PUT"
        ]);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) 
        {

            $admin = $this->getUser();

            $post->setUser($admin);
            $post->setIsPublished(false);

            $em->persist($post);
            $em->flush();

            $this->addFlash('success', "L'article a été modifié et sauvegardé.");

            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('pages/admin/post/edit.html.twig', [
            "post" => $post,
            "form" => $form->createView()
        ]);
    }


    #[Route('/admin/post/{id}/delete', name: 'admin_post_delete', methods:['DELETE'])]
    public function delete(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        if ( $this->isCsrfTokenValid("delete_post_".$post->getId(), $request->request->get('csrf_token')) ) 
        {
            $em->remove($post);
            $em->flush();

            $this->addFlash("success", "L'article a été supprimé.");
        }

        return $this->redirectToRoute("admin_post_index");
    }




}
