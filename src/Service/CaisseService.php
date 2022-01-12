<?php

namespace App\Service;

class CaisseService
{

    public function encaisserBonDeCommande($bonDeCommande)
    {

        $stripe = new \Stripe\StripeClient('sk_test_51KEW8XB7xXakC0tTcxGD1IbgUmkEfCHlcujqs2XTffnLkYodgvgXI2MUdSNDaqtBkEU9GfkvZmyORJQMTqtIlcwR00NrfyrO0H');
        $session = $stripe->checkout->sessions->create([
            'success_url' => 'http://localhost:8000/payment/success',
            'cancel_url' => 'http://localhost:8000/payment/failed',
            'payment_method_types' => [
                'card'
            ],
            'mode' => 'payment',
            'line_items' => $bonDeCommande
        ]);

        return $session;
    }


}