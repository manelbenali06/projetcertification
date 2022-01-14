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
        return $this->renderForm('admin_produit/new.html.twig', [
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

    #[Route('/edit/{id}', name: 'admin_produit_edit', methods: ['GET', 'POST'])]
       public function edit(ProduitRepository $produitRepository, int $id, Request $request, ManagerRegistry $managerRegistry)
    {
        $produit = $produitRepository->find($id); 
        $form = $this->createForm(ProduitType::class, $produit); 
        $form->handleRequest($request);
            // vérifier s'il y a une img2 dans le formulaire
                // non : garde l'ancienne
                // oui : récupérer le nom de l'ancienne img2
                    // s'il y en a une => supprimer
                    // sinon => ajout
        if ($form->isSubmitted() && $form->isValid()) {
            $infoImage = $form['image']->getData();
            $nomOldImage = $produit->getImage(); 
            if ($infoImage !== null) { 
                $cheminOldImage = $this->getParameter('produit_pictures_directory') . '/' . $nomOldImage;//voir kernel service yamel
                if (file_exists($cheminOldImage)) {
                    unlink($cheminOldImage); // supprime l'ancienne Image
                }
                $nomImage = time() . '-1.' . $infoImage->guessExtension(); // reconstitue le nom de la nouvelle Image
                $produit->setImage($nomImage); // définit le nom de l'Image de l'objet Ingredient
                $infoImage->move($this->getParameter('produit_pictures_directory'), $nomImage); // upload
            } else {
                $produit->setImage($nomOldImage); // re-définit le nom de l'Image à mettre en bdd
            }
            
            $manager = $managerRegistry->getManager();
            $manager->persist($produit);
            $manager->flush();
            $this->addFlash('success', 'Le produit a bien été modifiée');
            return $this->redirectToRoute('admin_produit_index');
            
        }
        return $this->renderForm('admin_produit/edit.html.twig', [//renderForm ou createView()
            'form' => $form,
            'produit' => $produit,
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_produit_delete', methods: ['POST'])]
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
        $this->addFlash('success', 'Le produit a bien été supprimé');
        return $this->redirectToRoute('admin_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}