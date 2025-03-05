<?php
require 'vendor/autoload.php'; // Ensure Stripe library is installed
include "config.php"; // Include database connection

\Stripe\Stripe::setApiKey("your-secret-key-here"); // Use your Stripe Secret Key

$data = json_decode(file_get_contents("php://input"), true);
$amount = $data['amount'];

$session = \Stripe\Checkout\Session::create([
    "payment_method_types" => ["card"],
    "line_items" => [[
        "price_data" => [
            "currency" => "inr",
            "product_data" => ["name" => "Shopping Payment"],
            "unit_amount" => $amount,
        ],
        "quantity" => 1,
    ]],
    "mode" => "payment",
    "success_url" => "http://yourwebsite.com/payment-success.php?session_id={CHECKOUT_SESSION_ID}",
    "cancel_url" => "http://yourwebsite.com/payment-failed.php",
]);

echo json_encode(["url" => $session->url]);
?>
