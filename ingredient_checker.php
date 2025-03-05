<?php
$servername = "localhost";
$username = "root";  // Change if necessary
$password = "";  // Change if necessary
$dbname = "skincare_db";

// Connect to MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to check ingredient safety
function analyzeIngredients($ingredients, $conn) {
    $results = [];
    $conflicts = [];
    $safeProduct = true; // Assume product is safe initially

    foreach ($ingredients as $ingredient) {
        $ingredient = trim($ingredient);
        $sql = "SELECT * FROM ingredients WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $ingredient);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $results[$ingredient] = $row['category'];
            if ($row['category'] == 'Allergen' || $row['category'] == 'Harmful') {
                $safeProduct = false; // Not safe if harmful/allergen
            }
            if (!empty($row['conflict_with'])) {
                $conflict_ingredients = explode(',', $row['conflict_with']);
                foreach ($conflict_ingredients as $conflict) {
                    $conflict = trim($conflict);
                    if (in_array($conflict, $ingredients)) {
                        $conflicts[] = "$ingredient may conflict with $conflict";
                        $safeProduct = false; // Not safe if conflicts exist
                    }
                }
            }
        } else {
            $results[$ingredient] = "Unknown"; // If ingredient is not in database
        }
    }

    return ["results" => $results, "conflicts" => $conflicts, "safe" => $safeProduct];
}

// Handle form submission
$analysis = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ingredients_input = $_POST['ingredients'];
    $ingredients = explode(",", $ingredients_input);
    $analysis = analyzeIngredients($ingredients, $conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingredient Analyzer</title>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.4.0"></script> <!-- Confetti Library -->
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-image: url("assets/images/hero-banner-1.jpg");
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container Styling */
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 40%;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            width: 400px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
        }

        .modal h3 {
            margin-bottom: 15px;
        }

        .modal ul {
            list-style-type: none;
            padding: 0;
        }

        .modal li {
            padding: 5px 0;
            font-size: 14px;
        }

        .modal .safe {
            color: green;
            font-weight: bold;
        }

        .modal .conflict {
            color: red;
            font-weight: bold;
        }

        .modal .close-btn {
            background: red;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            margin-top: 10px;
            cursor: pointer;
        }

        .modal .close-btn:hover {
            background: darkred;
        }

        /* Overlay */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .container {
                width: 80%;
            }
        }
    </style>
</head>
<body>

    <h2>Ingredient Analyzer & Compatibility Checker</h2>
    <form method="post">
        <label>Enter ingredients (comma-separated):</label><br>
        <textarea name="ingredients" rows="4" cols="50" required></textarea><br><br>
        <button type="submit">Analyze</button> <br><br>
        <div class="button-container">
            <a href="index.php"><button type="button">Back to Main Page</button></a>
        </div>
    </form>

    <!-- Modal Popup -->
    <div class="overlay" id="overlay"></div>
    <div class="modal" id="resultModal">
        <h3>Analysis Results:</h3>
        <ul id="analysisResults"></ul>
        <h3>Potential Conflicts:</h3>
        <ul id="conflictResults"></ul>
        <button class="close-btn" onclick="closeModal()">Close</button>
    </div>

    <script>
        function showModal() {
            document.getElementById("overlay").style.display = "block";
            document.getElementById("resultModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("overlay").style.display = "none";
            document.getElementById("resultModal").style.display = "none";
        }

        function triggerConfetti() {
            let duration = 2 * 1000; // 2 seconds
            let end = Date.now() + duration;

            (function frame() {
                confetti({
                    particleCount: 5,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 }
                });
                confetti({
                    particleCount: 5,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 }
                });

                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            })();
        }

        <?php if (!empty($analysis)): ?>
            let resultsHTML = "";
            let conflictsHTML = "";
            let isSafe = <?= json_encode($analysis["safe"]) ?>;

            <?php foreach ($analysis["results"] as $ingredient => $status): ?>
                resultsHTML += `<li class="<?= ($status == 'Allergen' || $status == 'Harmful') ? 'conflict' : 'safe' ?>"><?= htmlspecialchars($ingredient) ?> - <?= htmlspecialchars($status) ?></li>`;
            <?php endforeach; ?>

            <?php if (!empty($analysis["conflicts"])): ?>
                <?php foreach ($analysis["conflicts"] as $conflict): ?>
                    conflictsHTML += `<li class="conflict"><?= htmlspecialchars($conflict) ?></li>`;
                <?php endforeach; ?>
            <?php else: ?>
                conflictsHTML = "<li class='safe'>No conflicts detected.</li>";
            <?php endif; ?>

            document.getElementById("analysisResults").innerHTML = resultsHTML;
            document.getElementById("conflictResults").innerHTML = conflictsHTML;
            showModal();

            if (isSafe) {
                setTimeout(triggerConfetti, 500); // Slight delay for effect
            }
        <?php endif; ?>
    </script>

</body>
</html>
