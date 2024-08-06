<?php
$host = 'mnc353.encs.concordia.ca'; // Database host
$db = 'mnc353_1'; // Database name
$user = 'mnc353_1'; // Database username
$pass = 'f3h6rEF0'; // Database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}

function phpAlert($message)
{
    echo "<script type='text/javascript'>alert('$message');</script>";
}
?>