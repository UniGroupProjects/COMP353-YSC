<?php
include 'config.php';

if (isset($_GET['locationID'])) {
    $locationID = $_GET['locationID'];

    try {
        $stmt = $pdo->prepare("SELECT teamID, teamName FROM Team WHERE locationID = :locationID");
        $stmt->execute([':locationID' => $locationID]);
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the teams as JSON
        echo json_encode($teams);
    } catch (PDOException $e) {
        // Handle the error appropriately
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Location ID not provided.']);
}
?>
