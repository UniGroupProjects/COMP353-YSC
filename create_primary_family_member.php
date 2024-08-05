<?php
include 'config.php';

// Fetch all persons from the database
$stmt = $pdo->query("SELECT personID, CONCAT(firstName, ' ', lastName) AS personName FROM Person");
$persons = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $personID = $_POST['personID'];

    // Prepare and execute the insert statement
    $stmt = $pdo->prepare("INSERT INTO FamilyMember (personID) VALUES (:personID)");
    $stmt->execute(['personID' => $personID]);

    header("Location: index.php");  // Redirect to the main page after insertion
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Family Member</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            color: #333;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            padding: 10px 20px;
            background-color: #80AD4E;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #5D7D39;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <h1>Add Family Member</h1>
    <form method="POST">
        <label for="personID">Person:</label>
        <select name="personID" id="personID" required>
            <option value="">Select a person</option>
            <?php foreach ($persons as $person): ?>
                <option value="<?php echo htmlspecialchars($person['personID']); ?>"><?php echo htmlspecialchars($person['personName']); ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Add Family Member</button>
    </form>
</body>

</html>
