<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clubMemberID = $_POST['clubMemberID'];
    $personID = $_POST['personID'];
    $activationDate = $_POST['activationDate'];

    // Validate input
    if (empty($clubMemberID) || empty($personID) || empty($activationDate)) {
        $error = "Club Member ID, Person ID, and Activation Date are required.";
    } else {
        // Prepare and execute SQL statement
        $sql = "UPDATE ClubMember SET personID = :personID, activationDate = :activationDate, terminationDate = :terminationDate WHERE clubMemberID = :clubMemberID";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                ':clubMemberID' => $clubMemberID,
                ':personID' => $personID,
                ':activationDate' => $activationDate,
                ':terminationDate' => null
            ]);

            $success = "Club member updated successfully!";
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
} elseif (isset($_GET['id'])) {
    $clubMemberID = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM ClubMember WHERE clubMemberID = :clubMemberID");
    $stmt->execute([':clubMemberID' => $clubMemberID]);
    $clubMember = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <title>Edit Club Member</title>
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
    <h1>Edit Club Member</h1>
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if (isset($clubMember)): ?>
        <form action="edit_club_member.php" method="post">
            <input type="hidden" name="clubMemberID" value="<?php echo htmlspecialchars($clubMember['clubMemberID']); ?>">

            <div class="form-group">
                <label for="personID">Person:</label>
                <select id="personID" name="personID" required disabled>
                    <option value="">Select a person</option>
                    <?php foreach ($persons as $person): ?>
                        <option value="<?php echo htmlspecialchars($person['personID']); ?>" <?php echo $person['personID'] == $clubMember['personID'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($person['fullName']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="error"><?php echo isset($error['personID']) ? htmlspecialchars($error['personID']) : ''; ?></span>
            </div>

            <div class="form-group">
                <label for="activationDate">Activation Date:</label>
                <input type="date" id="activationDate" name="activationDate" value="<?php echo htmlspecialchars($clubMember['activationDate']); ?>" required>
                <span class="error"><?php echo isset($error['activationDate']) ? htmlspecialchars($error['activationDate']) : ''; ?></span>
            </div>

            <input type="submit" class="button" value="Update Club Member">
        </form>
    <?php else: ?>
        <p>Invalid Club Member ID.</p>
    <?php endif; ?>
    <a href="index.php">Back to List</a>
</body>
</html>
