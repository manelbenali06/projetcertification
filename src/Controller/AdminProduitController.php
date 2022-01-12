<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/produit')]
class AdminProduitController extends AbstractController
{
    #[Route('/', name: 'admin_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('admin_produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ManagerRegistry $managerRegistry)
    {
        $produit = new Produit(); // création d'une nouvelle produit
        $form = $this->createForm(ProduitType::class, $produit); // création du formulaire avec en paramètre la nouvelle produit
        $form->handleRequest($request); // gestionnaire de requêtes HTTP
        
        if ($form->isSubmitted() && $form->isValid()) {
            $infoImage = $form['image']->getData(); // récupère les informations de l'image 1
            $extensionImage = $infoImage->guessExtension(); // récupère l'extension de fichier de l'image 1
            $nomImage = time() . '-1.' . $extensionImage; // reconstitue un nom d'image unique pour l'image 1
            $infoImage->move($this->getParameter('produit_pictures_directory'), $nomImage); // déplace l'image 1 dans le dossier adéquat
            $produit->setImage($nomImage); // définit le nom de l'iamge 2 à mettre en base de données
           
            $manager = $managerRegistry->getManager(); // récupère le manager de Doctrine
            $manager->persist($produit); // dit à Doctrine qu'on va vouloir sauvegarder en bdd
            $manager->flush(); // exécute la requête
            $this->addFlash('success', 'La produit a bien été ajoutée'); // génère un message flash
            return $this->redirectToRoute('admin_produit_index', [], Response::HTTP_SEE_OTHER);
        }    
        return $this->renderForm('admin/produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form, // création de la vue du formulaire et envoi à la vue (fichier)
        ]);
    }

    #[Route('/{id}', name: 'admin_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('admin_produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_produit_edit', methods: ['GET', 'POST'])]
    public function delete(ProduitRepository $produitRepository, int $id,ManagerRegistry $managerRegistry)
    {
        $produit = $produitRepository->find($id);
       
        // throw new \Exception('TODO: gérer la suppression des images du dossier img');
        $image = $this->getParameter('produit_pictures_directory') . '/' . $produit->getImage();
        
        if ($produit->getImage() && file_exists($image)) {
            unlink($image);
        }
        
        
        $manager = $managerRegistry->getManager();
        $manager->remove($produit);
        $manager->flush();
        $this->addFlash('success', 'L\produit a bien été supprimée');
        return $this->redirectToRoute('admin_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}