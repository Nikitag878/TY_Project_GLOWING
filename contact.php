<?php
include(__DIR__ . '/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];
    
    // Insert data into database
    $sql = "INSERT INTO contact_us (name, email, phone, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $phone, $message);
    
    if ($stmt->execute()) {
        echo "<script>alert('Your message has been sent successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error! Please try again.');</script>";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
</head>
<body style="font-family: Arial, sans-serif; background-image: url(assets/images/hero-banner-1.jpg); margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; height: 100vh;">
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); width: 400px;">
        <h2 style="text-align: center; color: #333;">Contact Us</h2>
        <form method="post" action="" style="display: flex; flex-direction: column;">
            <label for="name" style="margin-bottom: 5px; font-weight: bold;">Name:</label>
            <input type="text" name="name" required style="padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px;">

            <label for="email" style="margin-bottom: 5px; font-weight: bold;">Email:</label>
            <input type="email" name="email" required style="padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px;">

            <label for="phone" style="margin-bottom: 5px; font-weight: bold;">Phone:</label>
            <input type="text" name="phone" required style="padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px;">

            <label for="message" style="margin-bottom: 5px; font-weight: bold;">Message:</label>
            <textarea name="message" required style="padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; height: 100px;"></textarea>

            <button type="submit" style="background-color: #007BFF; color: white; padding: 10px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">Submit</button>
        </form>
    </div>
</body>
</html>
