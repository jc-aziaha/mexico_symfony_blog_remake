<?php

namespace App\Controller\Admin\Comment;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    #[Route('/admin/comment/list', name: 'admin_comment_index', methods:['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findAll();

        return $this->render('pages/admin/comment/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route('/admin/comment/{id}/enable', name: 'admin_comment_enable', methods:['PUT'])]
    public function enable(Request $request, Comment $comment, EntityManagerInterface $em): Response
    {
        if ( $this->isCsrfTokenValid("enable_comment_" . $comment->getId(), $request->request->get('csrf_token')) ) 
        {
            if ( false === $comment->isIsEnable() ) 
            {
                $comment->setIsEnable(true);

                $this->addFlash('success', "Le commentaire a été activé");
            }
            else
            {
                $comment->setIsEnable(false);
                $comment->setDisabledAt(new DateTimeImmutable());

                $this->addFlash('success', "Le commentaire a été désactivé");   
            }

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('admin_comment_index');
        }
    }


    #[Route('/admin/comment/{id}/delete', name: 'admin_comment_delete', methods:['DELETE'])]
    public function delete(Comment $comment, Request $request, EntityManagerInterface $em): Response
    {
        if ( $this->isCsrfTokenValid('delete_comment_'.$comment->getId(), $request->request->get('csrf_token')) ) 
        {
            $em->remove($comment);
            $em->flush();

            $this->addFlash('success', "Ce commentaire a été supprimé.");
        }
        
        return $this->redirectToRoute('admin_comment_index');
        
    }
}
