<?php
include 'config.php';

$errors = [];
$personnelData = [
    'personID' => '',
    'role' => 'Administrator',
    'mandate' => 'Volunteer',
    'activationDate' => date('Y-m-d')
];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch existing data
    $stmt = $pdo->prepare("SELECT * FROM Personnel WHERE personnelID = ?");
    $stmt->execute([$id]);
    $personnelData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$personnelData) {
        die('Personnel not found.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validation
        if (empty($_POST['personID'])) {
            $errors['personID'] = 'Person ID is required.';
        }
        if (empty($_POST['role'])) {
            $errors['role'] = 'Role is required.';
        }
        if (empty($_POST['mandate'])) {
            $errors['mandate'] = 'Mandate is required.';
        }

        // Collect data
        $personnelData = [
            'personID' => $_POST['personID'],
            'role' => $_POST['role'],
            'mandate' => $_POST['mandate'],
            'activationDate' => $_POST['activationDate'],
            'terminationDate' => null
        ];

        // If no errors, update data in the database
        if (empty($errors)) {
            $sql = "UPDATE Personnel SET personID = ?, role = ?, mandate = ?, activationDate = ?, terminationDate = ? WHERE personnelID = ?";
            $stmt = $pdo->prepare($sql);

            try {
                $stmt->execute([
                    $personnelData['personID'],
                    $personnelData['role'],
                    $personnelData['mandate'],
                    $personnelData['activationDate'],
                    $personnelData['terminationDate'],
                    $id
                ]);
                header("Location: index.php");  // Redirect to the main page after insertion
            } catch (PDOException $e) {
                $errors['database'] = "Error: " . $e->getMessage();
            }
        }
    }
}

// Fetch persons for dropdown
$personStmt = $pdo->query("SELECT personID, CONCAT(firstName, ' ', lastName) AS fullName FROM Person");
$persons = $personStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Personnel</title>
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
    <h1>Edit Personnel</h1>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php elseif (!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form action="edit_personnel.php?id=<?php echo htmlspecialchars($personnelData['personnelID']); ?>" method="post">
        <div class="form-group">
            <label for="personID">Person:</label>
            <select id="personID" name="personID" disabled>
                <option value="">Select a person</option>
                <?php foreach ($persons as $person): ?>
                    <option value="<?php echo htmlspecialchars($person['personID']); ?>" <?php echo $person['personID'] === $personnelData['personID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($person['fullName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span
                class="error"><?php echo isset($errors['personID']) ? htmlspecialchars($errors['personID']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="Administrator" <?php echo $personnelData['role'] === 'Administrator' ? 'selected' : ''; ?>>
                    Administrator</option>
                <option value="Trainer" <?php echo $personnelData['role'] === 'Trainer' ? 'selected' : ''; ?>>Trainer
                </option>
                <option value="Other" <?php echo $personnelData['role'] === 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
            <span class="error"><?php echo isset($errors['role']) ? htmlspecialchars($errors['role']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="mandate">Mandate:</label>
            <select id="mandate" name="mandate">
                <option value="Volunteer" <?php echo $personnelData['mandate'] === 'Volunteer' ? 'selected' : ''; ?>>
                    Volunteer</option>
                <option value="Salary" <?php echo $personnelData['mandate'] === 'Salary' ? 'selected' : ''; ?>>Salary
                </option>
            </select>
            <span
                class="error"><?php echo isset($errors['mandate']) ? htmlspecialchars($errors['mandate']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="activationDate">Activation Date:</label>
            <input type="date" id="activationDate" name="activationDate"
                value="<?php echo htmlspecialchars($personnelData['activationDate']); ?>">
            <span
                class="error"><?php echo isset($errors['activationDate']) ? htmlspecialchars($errors['activationDate']) : ''; ?></span>
        </div>

        <input type="submit" class="button" value="Update Personnel">
    </form>
    <a href="index.php">Back to List</a>
</body>

</html>