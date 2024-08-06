<?php
include 'config.php';

// Fetch all persons from the database
$stmt = $pdo->query("SELECT personID, CONCAT(firstName, ' ', lastName) AS personName FROM Person");
$persons = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $personID = $_POST['personID'];

    // Prepare and execute the insert statement
    $stmt = $pdo->prepare("INSERT INTO FamilyMember (personID) VALUES (:personID)");

    try {
        $stmt->execute(['personID' => $personID]);
        header("Location: index.php");  // Redirect to the main page after insertion
    } catch (PDOException $e) {
        $errors['database'] = "Error: " . $e->getMessage();
    }
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
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input,
        select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .error {
            color: #d9534f;
            font-size: 0.9em;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group span {
            display: block;
            margin-top: 5px;
        }

        .form-group span.error {
            color: #d9534f;
        }

        .button {
            padding: 10px 20px;
            background-color: #80AD4E;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .button:hover {
            background-color: #5D7D39;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #80AD4E;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <h1>Add Family Member</h1>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php elseif (!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form method="POST">
        <label for="personID">Person:</label>
        <select name="personID" id="personID" required>
            <option value="">Select a person</option>
            <?php foreach ($persons as $person): ?>
                <option value="<?php echo htmlspecialchars($person['personID']); ?>">
                    <?php echo htmlspecialchars($person['personName']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="submit" class="button" value="Add Family Member">
    </form>
    <a href="index.php">Back to List</a>
</body>

</html>