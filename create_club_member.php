<?php
include 'config.php';

$errors = [];
$clubMemberData = [
    'personID' => '',
    'activationDate' => '',
    'terminationDate' => null,
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation
    if (empty($_POST['personID'])) {
        $errors['personID'] = 'Person ID is required.';
    }
    if (empty($_POST['activationDate'])) {
        $errors['activationDate'] = 'Activation Date is required.';
    }

    // Collect data
    $clubMemberData = [
        'personID' => $_POST['personID'],
        'activationDate' => $_POST['activationDate'],
        'terminationDate' => null,
    ];

    // If no errors, insert data into the database
    if (empty($errors)) {
        $sql = "INSERT INTO ClubMember (personID, activationDate, terminationDate) VALUES (:personID, :activationDate, :terminationDate)";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                ':personID' => $clubMemberData['personID'],
                ':activationDate' => $clubMemberData['activationDate'],
                ':terminationDate' => $clubMemberData['terminationDate']
            ]);

            $success = "Club member added successfully!";
        } catch (PDOException $e) {
            $errors['database'] = "Error: " . $e->getMessage();
        }
    }
}

// Fetch persons for dropdown
$personStmt = $pdo->query("SELECT personID, CONCAT(firstName, ' ', lastName) AS fullName FROM Person");
$persons = $personStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Club Member</title>
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

        input, select {
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
            background-color: #5bc0de;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .button:hover {
            background-color: #31b0d5;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #5bc0de;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Add Club Member</h1>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php elseif (!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form action="create_club_member.php" method="post">
        <div class="form-group">
            <label for="personID">Person:</label>
            <select id="personID" name="personID" required>
                <option value="">Select a person</option>
                <?php foreach ($persons as $person): ?>
                    <option value="<?php echo htmlspecialchars($person['personID']); ?>" <?php echo $person['personID'] == $clubMemberData['personID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($person['fullName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="error"><?php echo isset($errors['personID']) ? htmlspecialchars($errors['personID']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="activationDate">Activation Date:</label>
            <input type="date" id="activationDate" name="activationDate" value="<?php echo htmlspecialchars($clubMemberData['activationDate']); ?>" required>
            <span class="error"><?php echo isset($errors['activationDate']) ? htmlspecialchars($errors['activationDate']) : ''; ?></span>
        </div>

        <input type="submit" class="button" value="Add Club Member">
    </form>
    <a href="index.php">Back to List</a>
</body>
</html>
