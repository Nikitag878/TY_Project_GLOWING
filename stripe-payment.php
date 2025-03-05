<?php
session_start();
require 'vendor/autoload.php';
include('includes/config.php');

\Stripe\Stripe::setApiKey('sk_test_51Qw4ujBSWKm7nY98QoJaKv5P90oUax5ky3gwn1TtnSdeuVzstJ4uDsB6uwkywbAFgm2U4qSIuSJj0TudT8k4YcO400mHUPMAVI'); // Replace with your Secret Key

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');

    try {
        if (!isset($_POST['payment_method_id']) || !isset($_POST['total_amount'])) {
            throw new Exception("Payment method ID or amount is missing.");
        }

        // Retrieve total amount from user input (in INR)
        $totalAmount = floatval($_POST['total_amount']);
        $totalAmountPaisa = $totalAmount * 100; // Convert to paisa (smallest currency unit for INR)

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $totalAmountPaisa,  // Use INR amount in paisa
            'currency' => 'inr',
            'payment_method' => $_POST['payment_method_id'],
            'confirmation_method' => 'manual',
            'confirm' => true,
            'return_url' => 'http://localhost/shopping/shopping/index.php' // Redirect to homepage
        ]);

        if ($paymentIntent->status == 'requires_action' || $paymentIntent->status == 'requires_source_action') {
            echo json_encode([
                "success" => false,
                "requires_action" => true,
                "payment_intent_client_secret" => $paymentIntent->client_secret
            ]);
            exit();
        } elseif ($paymentIntent->status == 'succeeded') {
            // Update the database on successful payment
            mysqli_query($con, "UPDATE orders SET paymentMethod='Stripe', paymentStatus='Paid' WHERE userId='" . $_SESSION['id'] . "' AND paymentMethod IS NULL");
            unset($_SESSION['cart']);

            echo json_encode(["success" => true]);
            exit();
        } else {
            echo json_encode(["success" => false, "error" => "Payment not completed."]);
            exit();
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment (INR)</title>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
   body {
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f8f9fa;
    margin: 0;
}

#payment-form {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.25); /* Soft shadow effect */
    text-align: center;
    width: 350px;
}

h2 {
    margin-bottom: 20px;
    font-size: 22px;
    color: #333;
}

#card-element, #amount-input {
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 15px;
    width: calc(100% - 30px);
    font-size: 16px;
    background: #fff;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}

#pay-button {
    background: #007bff;
    color: white;
    border: none;
    padding: 12px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    width: 100%;
    transition: background 0.3s ease;
}

#pay-button:hover {
    background: #0056b3;
}

@media (max-width: 400px) {
    #payment-form {
        width: 90%;
    }
}

    </style>
</head>
<body>
    <h2>Complete Your Payment</h2>
    <form id="payment-form">
        <input type="number" id="amount-input" placeholder="Enter amount in ₹" required>
        <div id="card-element"></div>
        <button type="submit" id="pay-button">Pay</button>
    </form>

    <script>
        var stripe = Stripe('pk_test_51Qw4ujBSWKm7nY98bkml3BBqzLZp9bddYM8c27qXA8Q1Sd29d3AB2NnFrtaUFZPRIkOG01WQvyztgeqAqSJ3xmkb00Y84ByOuV');
        var elements = stripe.elements();
        var cardElement = elements.create('card');
        cardElement.mount('#card-element');

        document.getElementById("payment-form").addEventListener("submit", async function (e) {
            e.preventDefault();
            
            var totalAmount = document.getElementById("amount-input").value;
            if (!totalAmount || totalAmount <= 0) {
                alert("Please enter a valid amount.");
                return;
            }

            var { paymentMethod, error } = await stripe.createPaymentMethod({
                type: "card",
                card: cardElement
            });

            if (error) {
                alert(error.message);
                return;
            }

            let response = await fetch("stripe-payment.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `payment_method_id=${paymentMethod.id}&total_amount=${totalAmount}`
            });

            let result = await response.json();

            if (result.requires_action) {
                let { paymentIntent, error } = await stripe.handleCardAction(result.payment_intent_client_secret);

                if (error) {
                    alert("Authentication failed: " + error.message);
                    return;
                }

                response = await fetch("stripe-payment.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `payment_method_id=${paymentIntent.payment_method}&total_amount=${totalAmount}`
                });

                result = await response.json();
            }

            if (result.success) {
                alert("Payment successful!");
                window.location.href = "index.php";
            } else {
                alert("Payment failed: " + result.error);
            }
        });
    </script>
</body>
</html>