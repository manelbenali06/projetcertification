<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Service\HelpService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(helpService $help): Response
    {
        $panier = $help->recupererLePanier();
        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="panier_add")
     */
    public function add(Produit $produit, HelpService $help): Response
    {
        $help->chercherUnPanier();
        $help->mettreAJourLaQuantite($produit);
        return $this->redirectToRoute('panier');
    }

    
    /**
     * @Route("/panier/clear", name="panier_clear")
     */
    public function clear(helpService $help): Response
    {
        $help->viderLePanier();
        return $this->redirectToRoute('panier');
    }

}