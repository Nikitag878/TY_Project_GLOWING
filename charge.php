<?php
require 'vendor/autoload.php'; // Include Stripe's library

\Stripe\Stripe::setApiKey('your_secret_key'); // Use your Stripe Secret Key

try {
    $charge = \Stripe\Charge::create([
        'amount' => 5000, // Amount in cents
        'currency' => 'usd',
        'description' => 'Order Payment',
        'source' => $_POST['stripeToken'],
    ]);

    // Payment success
    echo "Payment successful! Charge ID: " . $charge->id;
} catch (\Stripe\Exception\CardException $e) {
    echo "Error: " . $e->getError()->message;
}
?>
