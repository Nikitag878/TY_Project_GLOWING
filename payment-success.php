<?php
session_start();
require 'vendor/autoload.php'; // Load Stripe PHP SDK

\Stripe\Stripe::setApiKey('sk_test_51Qw4ujBSWKm7nY98QoJaKv5P90oUax5ky3gwn1TtnSdeuVzstJ4uDsB6uwkywbAFgm2U4qSIuSJj0TudT8k4YcO400mHUPMAVI'); // Replace with your actual Secret Key

if (isset($_SESSION['stripe_payment_intent'])) {
    $paymentIntent = \Stripe\PaymentIntent::retrieve($_SESSION['stripe_payment_intent']);

    if ($paymentIntent->status == 'succeeded') {
        echo "<h2>Payment Successful!</h2>";
        echo "<p>Transaction ID: " . $paymentIntent->id . "</p>";
        // Update order status in database here
        unset($_SESSION['stripe_payment_intent']);
    } else {
        echo "<h2>Payment Failed!</h2>";
    }
} else {
    echo "<h2>Invalid Payment Request</h2>";
}
?>
