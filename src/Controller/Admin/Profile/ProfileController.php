<?php

namespace App\Controller\Admin\Profile;

use App\Form\EditUserPasswordFormType;
use App\Form\EditUserProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route('/admin/profile', name: 'admin_profile_index', methods:['GET'])]
    public function index(): Response
    {
        return $this->render('pages/admin/profile/index.html.twig');
    }


    #[Route('/admin/profile/edit', name: 'admin_profile_edit', methods:['GET', 'PUT'])]
    public function editProfile(Request $request, EntityManagerInterface $em): Response
    {

        $admin = $this->getUser();

        $form = $this->createForm(EditUserProfileFormType::class, $admin, [
            "method" => "PUT"
        ]);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) 
        {
            $em->persist($admin);
            $em->flush();

            $this->addFlash('success', "Le profil a été modifié");

            return $this->redirectToRoute("admin_profile_index");
        }

        return $this->render('pages/admin/profile/edit_profile.html.twig', [
            "form" => $form->createView()
        ]);
    }


    #[Route('/admin/profile/edit/password', name: 'admin_profile_edit_password', methods:['GET', 'PUT'])]
    public function editPassword(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {

        $admin = $this->getUser();

        $form = $this->createForm(EditUserPasswordFormType::class, null, [
            "method" => "PUT"
        ]);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) 
        {
            $plainPasssword = $form->getData()['plainPassword'];

            $passwordHashed = $hasher->hashPassword($admin, $plainPasssword);

            $admin->setPassword($passwordHashed);

            $em->persist($admin);
            $em->flush();

            $this->addFlash('success', 'Le mot de passe a été modifié.');

            return $this->redirectToRoute('admin_profile_index');
        }

        return $this->render('pages/admin/profile/edit_password.html.twig', [
            "form" => $form->createView()
        ]);
    }


    #[Route('/admin/profile/delete', name: 'admin_profile_delete', methods:['DELETE'])]
    public function delete(Request $request, EntityManagerInterface $em): Response
    {
        if ( $this->isCsrfTokenValid('delete_profile', $request->request->get('csrf_token')) ) 
        {
            $admin = $this->getUser();

            $this->addFlash('success', "{$admin->getFirstName()} {$admin->getLastName()} a été supprimé!");

            $posts = $admin->getPosts();

            foreach ($posts as $post) 
            {
                $post->setUser(null);
            }

            $this->container->get('security.token_storage')->setToken(null);

            $em->remove($admin);
            $em->flush();

        }
        
        return $this->redirectToRoute('admin_profile_index');
        
    }


}
