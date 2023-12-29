<?php

namespace App\Controller\Visitor\Contact;

use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Repository\SettingRepository;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'visitor_contact_create', methods:['GET', 'POST'])]
    public function create(
        Request $request, 
        EntityManagerInterface $em, 
        SendEmailService $sendEmailService,
        SettingRepository $settingRepository
    ): Response
    {
        $contact = new Contact();

        $form = $this->createForm(ContactFormType::class, $contact);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) 
        {
            $em->persist($contact);
            $em->flush();

            $sendEmailService->send([
                "sender_email" => "medecine-du-monde@gmail.com",
                "sender_name"  => "Jean Dupont",
                "recipient_email" => "medecine-du-monde@gmail.com",
                "subject" => "Un message reçu sur votre blog",
                "html_template" => "emails/contact.html.twig",
                "context"   => [
                    "contact_first_name"    => $contact->getFirstName(),
                    "contact_last_name"     => $contact->getLastName(),
                    "contact_email"         => $contact->getEmail(),
                    "contact_phone"         => $contact->getPhone(),
                    "contact_message"       => $contact->getMessage(),
                ]
            ]);

            $this->addFlash("success", "Votre a bien été envoyé. Nous vous recontacterons dans les plus brefs délais.");

            return $this->redirectToRoute('visitor_contact_create');
        }

        return $this->render('pages/visitor/contact/create.html.twig', [
            "form" => $form->createView(),
            "setting" => $settingRepository->find(1)
        ]);
    }
}
