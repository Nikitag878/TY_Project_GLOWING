<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['skin_image'])) {
    if ($_FILES['skin_image']['error'] === 0) {
        $imagePath = "uploads/" . basename($_FILES["skin_image"]["name"]);

        // Ensure uploads folder exists
        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        // Move uploaded file
        if (move_uploaded_file($_FILES["skin_image"]["tmp_name"], $imagePath)) {
            // Run Python script for skin analysis
            $result = shell_exec("python3 analyze_skin.py " . escapeshellarg($imagePath));

            echo "<h3>Your Skin Type: $result</h3>";
        } else {
            echo "<h3>Error uploading file.</h3>";
        }
    } else {
        echo "<h3>File upload error.</h3>";
    }
} else {
    echo "<h3>No image uploaded.</h3>";
}
?>


<form action="analyze_skin.php" method="post" enctype="multipart/form-data">
    <input type="file" name="skin_image" accept="image/*" required>
    <button type="submit">Analyze Skin</button>
</form>
