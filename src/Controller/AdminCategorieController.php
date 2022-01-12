<?php

namespace App\Controller;
use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/categorie')]
class AdminCategorieController extends AbstractController
{
    #[Route('/', name: 'admin_categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('admin_categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ManagerRegistry $managerRegistry)
    {
        $categorie = new Categorie(); // création d'une nouvelle categorie
        $form = $this->createForm(CategorieType::class, $categorie); // création du formulaire avec en paramètre la nouvelle categorie
        $form->handleRequest($request); // gestionnaire de requêtes HTTP
        
        if ($form->isSubmitted() && $form->isValid()) {
            $infoImage = $form['image']->getData(); // récupère les informations de l'image 1
            $extensionImage = $infoImage->guessExtension(); // récupère l'extension de fichier de l'image 1
            $nomImage = time() . '-1.' . $extensionImage; // reconstitue un nom d'image unique pour l'image 1
            $infoImage->move($this->getParameter('categorie_pictures_directory'), $nomImage); // déplace l'image 1 dans le dossier adéquat
            $categorie->setImage($nomImage); // définit le nom de l'iamge 2 à mettre en base de données
           
            $manager = $managerRegistry->getManager(); // récupère le manager de Doctrine
            $manager->persist($categorie); // dit à Doctrine qu'on va vouloir sauvegarder en bdd
            $manager->flush(); // exécute la requête
            $this->addFlash('success', 'La categorie a bien été ajoutée'); // génère un message flash
            return $this->redirectToRoute('admin_categorie_index', [], Response::HTTP_SEE_OTHER);
        }    
        return $this->renderForm('admin/categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form, // création de la vue du formulaire et envoi à la vue (fichier)
        ]);
    }

    #[Route('/{id}', name: 'admin_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('admin_categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_categorie_edit', methods: ['GET', 'POST'])]
       public function edit(CategorieRepository $categorieRepository, int $id, Request $request, ManagerRegistry $managerRegistry)
    {
        $categorie = $categorieRepository->find($id); 
        $form = $this->createForm(categorieType::class, $categorie); 
        $form->handleRequest($request);
            // vérifier s'il y a une img2 dans le formulaire
                // non : garde l'ancienne
                // oui : récupérer le nom de l'ancienne img2
                    // s'il y en a une => supprimer
                    // sinon => ajout
        if ($form->isSubmitted() && $form->isValid()) {
            $infoImage = $form['image']->getData();
            $nomOldImage = $categorie->getImage(); 
            if ($infoImage !== null) { 
                $cheminOldImage = $this->getParameter('categorie_pictures_directory') . '/' . $nomOldImages;//voir kernel service yamel
                if (file_exists($cheminOldImage)) {
                    unlink($cheminOldImage); // supprime l'ancienne Image
                }
                $nomImage = time() . '-1.' . $infoImage->guessExtension(); // reconstitue le nom de la nouvelle Image
                $categorie->setImage($nomImage); // définit le nom de l'Image de l'objet categorie
                $infoImage->move($this->getParameter('categorie_pictures_directory'), $nomImage); // upload
            } else {
                $categorie->setImage($nomOldImage); // re-définit le nom de l'Image à mettre en bdd
            }
            
            $manager = $managerRegistry->getManager();
            $manager->persist($categorie);
            $manager->flush();
            $this->addFlash('success', 'L\categorie a bien été modifiée');
            return $this->redirectToRoute('admin_categorie_index');
            
        }
        return $this->renderForm('admin/categorie/edit.html.twig', [//renderForm ou createView()
            'form' => $form,
            'categorie' => $categorie,
        ]);
    }
    

    #[Route('/admin/delete/{id}', name: 'admin_categorie_delete', methods: ['POST'])]
     public function delete(CategorieRepository $categorieRepository, int $id,ManagerRegistry $managerRegistry)
    {
        $categorie = $categorieRepository->find($id);
       
        // throw new \Exception('TODO: gérer la suppression des images du dossier img');
        $images = $this->getParameter('categorie_pictures_directory') . '/' . $categorie->getImage();
        
        if ($categorie->getImage() && file_exists($images)) {
            unlink($images);
        }
        
        
        $manager = $managerRegistry->getManager();
        $manager->remove($categorie);
        $manager->flush();
        $this->addFlash('success', 'L\categorie a bien été supprimée');
        return $this->redirectToRoute('admin_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}