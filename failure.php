<?php
session_start();
echo "<h2>Payment Failed</h2>";
echo "<p>Reason: " . $_SESSION['error_message'] . "</p>";
?>
