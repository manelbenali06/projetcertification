<?php

namespace App\Controller;

use App\Service\CaisseService;
use App\Service\HelpService;
use App\Service\VenteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    /**
     * @Route("/payment", name="payment")
     */
    public function index(HelpService $help, VenteService $vendeur, CaisseService $caissiere): Response
    {
        $panier = $help->recupererLePanier();//créer une plus value reccuperation
        $bonDuVendeur = $vendeur->etablirBonDeCommande($panier);//plus value etablir bon de commande
        $session = $caissiere->encaisserBonDeCommande($bonDuVendeur);//plus value encaissement

        return $this->render('payment/index.html.twig', [
            'sessionId' => $session->id
        ]);
    }

    /**
     * @Route("/payment/success", name="payment_success")
     */
    public function success(): Response
    {
        return $this->render('payment/success.html.twig');
    }

    /**
     * @Route("/payment/failed", name="payment_failed")
     */
    public function failed(): Response
    {
        return $this->render('payment/failed.html.twig');
    }
}