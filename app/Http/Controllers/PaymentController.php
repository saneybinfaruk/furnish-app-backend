<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function makePayment(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            // Default product details
            $defaultProduct = [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'Default Product',
                    'description' => 'A default product description',
                ],
                'unit_amount' => 500 * 100, // Amount in cents
            ];

            // Default line item
            $defaultLineItem = [
                'price_data' => $defaultProduct,
                'quantity' => 1,
            ];

            // Checkout session creation
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [$defaultLineItem], // Add more items if needed
                'mode' => 'payment',
                'success_url' => route('payment.success'),
                'cancel_url' => route('payment.cancel'),
            ]);

            // Return session ID
            return response()->json(['clientSecret' => $session->id, 'publishableKey' => env('STRIPE_KEY')]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
