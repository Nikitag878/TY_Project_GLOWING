<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COD Payment Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
        }
        .success-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: green;
        }
        .details {
            margin-top: 10px;
            font-size: 18px;
        }
        .home-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
        }
        .home-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

    <div class="success-box">
        <h1>✅ Payment Successful!</h1>
        <p class="details">Your order has been placed successfully via <strong>Cash on Delivery</strong>.</p>
        <p class="details">You will pay in cash upon receiving your order.</p>
        <a href="index.php" class="home-btn">Go to Home</a>
    </div>

</body>
</html>
