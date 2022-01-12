<?php

namespace App\Controller;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact/success', name: 'contact_success')]
     public function success(): Response
    {
        return $this->renderForm('contact/success.html.twig');   
    }

    #[Route('/contact', name: 'contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        
        $form = $this->createForm(ContactType::class);// on cree un formulaire a partir d'un type et de l'entité pour produire le formulaire a afficher
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // envoyer un mail depuis le formulaire
            $data = $form->getData();//on reccupere l'email du formulaire
            //dd($data); pour voir le resultat en symfony (d'abord remplir le form et l'envoyé)
            
            $email = (new Email())
                ->from($data['email'])//adresse du visiteur
                ->to('test@test.com')//notre adresse admin de reçeption
                ->subject('Demande de contact')
                ->text($data['message'])//corp du mail
                ->html($data['message']);//le corp du mail

            $mailer->send($email);//Mailer interface envoie l'email avec send
        
            return $this->redirectToRoute('contact_success', [], Response::HTTP_SEE_OTHER);
            
        }
        
        return $this->renderForm('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'form' => $form
        ]);
    }
}