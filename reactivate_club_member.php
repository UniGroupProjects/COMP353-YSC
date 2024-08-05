<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Prepare and execute the update statement
    $stmt = $pdo->prepare("UPDATE ClubMember SET terminationDate=null WHERE clubMemberID = ?");
    $stmt->execute([$id]);
    
    // Redirect to index after deletion
    header('Location: index.php');
    exit;
} else {
    die('Invalid ID');
}
