<?php

namespace App\Service;

use App\Model\Panier;
use App\Model\ElementPanier;
use Symfony\Component\HttpFoundation\RequestStack;

class HelpService
{

    public $panier;
    public $requestStack; //session interface sert à conserver les elements et c'est le systeme qui permet de faire la sauvegarde dans le serveur

    public function __construct(RequestStack $requestStack) //constructeur dit des que j'existe demandez au systeme de vous donner la sessioninterface 

    {
        $this->requestStack = $requestStack; //help le garde  avec $this->requestStack
    }


    public function ajouterUneAutreLigneAuPanier($produit)
    { //$produit c'est le produit qui correspond a l'id dans la route
        //on ajoute une nouvelle ligne car on a le meme element
        $element = new ElementPanier();
        $element->produit = $produit;
        $element->quantite = 1;
        $this->panier->elements[] = $element;
        $this->panier->prixtotal = $this->panier->prixtotal + $produit->getPrix(); //calcul le prix du panier final 
        $this->requestStack->getSession()->set('cart', $this->panier); //$this c'est la sessioninterface qu'on a garder donc on sauvegarde le panier dans la session interface(c'est le fichier dans le serveur)
    }

    //la fonctionnalité de Help c'est de chercher un panier si il existe sinon elle crée un panier
    public function chercherUnPanier()
    {

        $panier = $this->requestStack->getSession()->get('cart');
        if ($panier === null) {
            $panier = new Panier();
            $panier->elements = [];
        }
        $this->panier = $panier;
        return; //help ne retourne pas le panier donc il le sauvegarde donc on crée une propriété 
    }

    public function mettreAJourLaQuantite($produit)
    {
        //si dans le panier on a le meme element on ajoute a la quantité+1 mais si c'est un nouvel element ex:product on ajoute une nouvelle ligne le sauvegarder et mettre a jour la session
        foreach ($this->panier->elements as $product) {
            $produitDansLePanier = $product->produit;
            if ($produitDansLePanier->getId() === $produit->getId()) {
                $product->quantite = $product->quantite + 1;
                $this->panier->prixtotal = $this->panier->prixtotal + $produit->getPrix();
                $this->requestStack->getSession()->set('cart', $this->panier);
                return; //help na pas besoin de retourner le panier 
            }
        }
        $this->ajouterUneAutreLigneAuPanier($produit);
        return;
    }

    public function viderLePanier()
    { //car c'est help qui gère le panier
        $this->requestStack->getSession()->set('cart', null);
    }

    public function recupererLePanier()
    {
        $panier = $this->requestStack->getSession()->get('cart');
        return $panier;
    }
}