<?php
include 'config.php';

// Get the family member ID from the query string
$familyMemberID = $_GET['id'] ?? null;

if (!$familyMemberID) {
    die('Family member ID is required.');
}

// Prepare the delete statement
$stmt = $pdo->prepare("DELETE FROM FamilyMember WHERE familyMemberID = :familyMemberID");

if ($stmt->execute(['familyMemberID' => $familyMemberID])) {
    header("Location: index.php");  // Redirect to the main page after deletion
    exit();
} else {
    die('Error deleting family member.');
}
?>
