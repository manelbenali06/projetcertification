<?php

namespace App\Service;

class VenteService {

    public function etablirBonDeCommande($commande) {
        $panierChezStripe = [];
        foreach ($commande->elements as $product) {
            $elementPourStripe = [
                'amount' => $product->quantite * $product->produit->getPrix() * 100,
                'quantity' => $product->quantite,
                'currency' => 'eur',
                'name' => $product->produit->getNom(),
            ];
            $panierChezStripe[] = $elementPourStripe;
        }
        return $panierChezStripe;
    }

}