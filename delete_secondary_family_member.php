<?php
include 'config.php';

// Check if ID is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare and execute the delete statement
    $stmt = $pdo->prepare("DELETE FROM EmergencyContact WHERE familyMemberID = :id");
    $stmt->execute(['id' => $id]);

    header("Location: index.php");  // Redirect to the main page after deletion
    exit();
} else {
    echo "No ID specified.";
    exit();
}
?>
