<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/ingredient')]
class AdminIngredientController extends AbstractController
{
    #[Route('/', name: 'admin_ingredient_index', methods: ['GET'])]
    public function index(IngredientRepository $ingredientRepository): Response
    {
        return $this->render('admin_ingredient/index.html.twig', [
            'ingredients' => $ingredientRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_ingredient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ManagerRegistry $managerRegistry)
    {
        $ingredient = new Ingredient(); // création d'une nouvelle Ingredient
        $form = $this->createForm(IngredientType::class, $ingredient); // création du formulaire avec en paramètre la nouvelle Ingredient
        $form->handleRequest($request); // gestionnaire de requêtes HTTP
        
        if ($form->isSubmitted() && $form->isValid()) {
            $infoImage = $form['image']->getData(); // récupère les informations de l'image 1
            $extensionImage = $infoImage->guessExtension(); // récupère l'extension de fichier de l'image 1
            $nomImage = time() . '-1.' . $extensionImage; // reconstitue un nom d'image unique pour l'image 1
            $infoImage->move($this->getParameter('ingredient_pictures_directory'), $nomImage); // déplace l'image 1 dans le dossier adéquat
            $ingredient->setImage($nomImage); // définit le nom de l'iamge 2 à mettre en base de données
           
            $manager = $managerRegistry->getManager(); // récupère le manager de Doctrine
            $manager->persist($ingredient); // dit à Doctrine qu'on va vouloir sauvegarder en bdd
            $manager->flush(); // exécute la requête
            $this->addFlash('success', 'L\ingredient a bien été ajoutée'); // génère un message flash
            return $this->redirectToRoute('admin_ingredient_index', [], Response::HTTP_SEE_OTHER);
        }    
        return $this->renderForm('admin/ingredient/new.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form, // création de la vue du formulaire et envoi à la vue (fichier)
        ]);
    }

    #[Route('/{id}', name: 'admin_ingredient_show', methods: ['GET'])]
    public function show(Ingredient $ingredient): Response
    {
        return $this->render('admin_ingredient/show.html.twig', [
            'ingredient' => $ingredient,
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_ingredient_edit', methods: ['GET', 'POST'])]
       public function edit(IngredientRepository $ingredientRepository, int $id, Request $request, ManagerRegistry $managerRegistry)
    {
        $ingredient = $ingredientRepository->find($id); 
        $form = $this->createForm(IngredientType::class, $ingredient); 
        $form->handleRequest($request);
            // vérifier s'il y a une img2 dans le formulaire
                // non : garde l'ancienne
                // oui : récupérer le nom de l'ancienne img2
                    // s'il y en a une => supprimer
                    // sinon => ajout
        if ($form->isSubmitted() && $form->isValid()) {
            $infoImage = $form['image']->getData();
            $nomOldImage = $ingredient->getImage(); 
            if ($infoImage !== null) { 
                $cheminOldImage = $this->getParameter('ingredient_pictures_directory') . '/' . $nomOldImage;//voir kernel service yamel
                if (file_exists($cheminOldImage)) {
                    unlink($cheminOldImage); // supprime l'ancienne Image
                }
                $nomImage = time() . '-1.' . $infoImage->guessExtension(); // reconstitue le nom de la nouvelle Image
                $ingredient->setImage($nomImage); // définit le nom de l'Image de l'objet Ingredient
                $infoImage->move($this->getParameter('ingredient_pictures_directory'), $nomImage); // upload
            } else {
                $ingredient->setImage($nomOldImage); // re-définit le nom de l'Image à mettre en bdd
            }
            
            $manager = $managerRegistry->getManager();
            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash('success', 'L\ingredient a bien été modifiée');
            return $this->redirectToRoute('admin_ingredient_index');
            
        }
        return $this->renderForm('admin/ingredient/edit.html.twig', [//renderForm ou createView()
            'form' => $form,
            'ingredient' => $ingredient,
        ]);
    }
    

    #[Route('/admin/delete/{id}', name: 'admin_ingredient_delete', methods: ['POST'])]
     public function delete(IngredientRepository $ingredientRepository, int $id,ManagerRegistry $managerRegistry)
    {
        $ingredient = $ingredientRepository->find($id);
       
        // throw new \Exception('TODO: gérer la suppression des images du dossier img');
        $image = $this->getParameter('ingredient_pictures_directory') . '/' . $ingredient->getImage();
        
        if ($ingredient->getImage() && file_exists($image)) {
            unlink($image);
        }
        
        
        $manager = $managerRegistry->getManager();
        $manager->remove($ingredient);
        $manager->flush();
        $this->addFlash('success', 'L\ingredient a bien été supprimée');
        return $this->redirectToRoute('admin_ingredient_index', [], Response::HTTP_SEE_OTHER);
    }
}