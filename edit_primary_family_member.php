<?php
include 'config.php';

// Get the family member ID from the query string
$familyMemberID = $_GET['id'] ?? null;

if (!$familyMemberID) {
    die('Family member ID is required.');
}

// Fetch the existing family member details
$stmt = $pdo->prepare("SELECT personID FROM FamilyMember WHERE familyMemberID = :familyMemberID");
$stmt->execute(['familyMemberID' => $familyMemberID]);
$familyMember = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$familyMember) {
    die('Family member not found.');
}

// Fetch all persons from the database
$stmt = $pdo->query("SELECT personID, CONCAT(firstName, ' ', lastName) AS personName FROM Person");
$persons = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPersonID = $_POST['personID'];

    // Update the family member record
    $stmt = $pdo->prepare("UPDATE FamilyMember SET personID = :personID WHERE familyMemberID = :familyMemberID");

    try {
        $stmt->execute(['personID' => $newPersonID, 'familyMemberID' => $familyMemberID]);
        header("Location: index.php");  // Redirect to the main page after insertion
    } catch (PDOException $e) {
        $errors['database'] = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Family Member</title>
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

        input,
        select {
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
    <h1>Edit Family Member</h1>
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
                <option value="<?php echo htmlspecialchars($person['personID']); ?>" <?php echo ($person['personID'] == $familyMember['personID']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($person['fullName']); ?>
                </option>
            <?php endforeach; ?>

        </select>
        <button type="submit">Update Family Member</button>
    </form>
</body>

</html>