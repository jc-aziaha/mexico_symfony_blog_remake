<?php

namespace App\Controller\Admin\Tag;

use App\Entity\Tag;
use App\Form\TagFormType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagController extends AbstractController
{
    #[Route('/admin/tag/list', name: 'admin_tag_index', methods:['GET'])]
    public function index(TagRepository $tagRepository): Response
    {
        $tags = $tagRepository->findAll();

        return $this->render('pages/admin/tag/index.html.twig', [
            "tags" => $tags
        ]);
    }


    #[Route('/admin/tag/create', name: 'admin_tag_create', methods:['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $tag = new Tag();

        $form = $this->createForm(TagFormType::class, $tag);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) 
        {
            $em->persist($tag);
            $em->flush();

            $this->addFlash("success", "Le tag a été ajouté.");

            return $this->redirectToRoute("admin_tag_index");
        }

        return $this->render('pages/admin/tag/create.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/admin/tag/{id}/edit', name: 'admin_tag_edit', methods:['GET', 'PUT'])]
    public function edit(Tag $tag, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(TagFormType::class, $tag, [
            "method" => "PUT"
        ]);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) 
        {
            $em->persist($tag);
            $em->flush();

            $this->addFlash('success', "Le tag a été modifié");

            return $this->redirectToRoute("admin_tag_index");
        }

        return $this->render("pages/admin/tag/edit.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/admin/tag/{id}/delete', name: 'admin_tag_delete', methods:['DELETE'])]
    public function delete(Tag $tag, Request $request, EntityManagerInterface $em): Response
    {
        if ( $this->isCsrfTokenValid('delete_tag_'.$tag->getId(), $request->request->get('csrf_token')) ) 
        {
            $em->remove($tag);
            $em->flush();

            $this->addFlash('success', "Le tag a été supprimé.");
        }
        
        return $this->redirectToRoute('admin_tag_index');
        
    }

}
