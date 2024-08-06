<?php
include 'config.php';

$errors = [];
$clubMemberData = [
    'personID' => '',
    'familyMemberID' => null,
    'relType' => null,
    'locationID' => null,
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation
    if (empty($_POST['personID'])) {
        $errors['personID'] = 'Person ID is required.';
    }
    if (empty($_POST['familyMemberID'])) {
        $errors['familyMemberID'] = 'Family member is required.';
    }
    if (empty($_POST['relType'])) {
        $errors['relType'] = 'Relation type is required.';
    }

    // Collect data
    $clubMemberData = [
        'personID' => $_POST['personID'],
        'familyMemberID' => $_POST['familyMemberID'],
        'relType' => $_POST['relType'],
        'locationID' => $_POST['locationID'],
    ];

    // If no errors, insert data into the database
    if (empty($errors)) {

        //Start a transaction, and rollback everything in case of errors
        $pdo->beginTransaction();

        try {
            $sql = "INSERT INTO ClubMember (personID, activationDate, terminationDate) VALUES (:personID, CURDATE(), null)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':personID' => $clubMemberData['personID'],
            ]);
        } catch (PDOException $e) {
            phpAlert($e->getMessage());
            $errors['database'] = "Error: " . $e->getMessage();
            $pdo->rollBack();
            exit;
        } catch (Exception $e) {
            phpAlert($e->getMessage());
        }

        $clubMemberID = $pdo->lastInsertId();

        try {
            $sql = "INSERT INTO Sponsor (clubMemberID, familyMemberID, relType, activationDate, terminationDate) VALUES (?, ?, ?, CURDATE(), null)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $clubMemberID,
                $clubMemberData['familyMemberID'],
                $clubMemberData['relType'],
            ]);
        } catch (PDOException $e) {
            phpAlert($e->getMessage());
            $errors['database'] = "Error: " . $e->getMessage();
            $pdo->rollBack();
            exit;
        }

        if (!empty($_POST['locationID'])) {

            $sql = "INSERT INTO ClubMemberLocation (clubMemberID, locationID, activationDate, terminationDate) VALUES (?, ?, CURDATE(), null)";
            $stmt = $pdo->prepare($sql);

            try {
                $stmt->execute([
                    $clubMemberID,
                    $_POST['locationID']
                ]);
            } catch (PDOException $e) {
                phpAlert($e->getMessage());
                $errors['database'] = "Error: " . $e->getMessage();
                $pdo->rollBack();
                exit;
            }
        }

        if (empty($errors)) {
            $pdo->commit();
            header("Location: index.php");
        }
    }
}

// Fetch persons for dropdown - not already club members
$personStmt = $pdo->query("
SELECT personID, CONCAT(P.firstName, ' ', P.lastName) AS fullName
FROM
    Person P
WHERE P.personID NOT IN (
    SELECT personID
    FROM ClubMember
) AND DATE_ADD(dateOfBirth, INTERVAL 4 YEAR) <= CURDATE()
    AND DATE_ADD(dateOfBirth, INTERVAL 10 YEAR) > CURDATE();;");
$persons = $personStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch family members for dropdown
$famMemberStmt = $pdo->query("SELECT familyMemberID, CONCAT(firstName, ' ', lastName) AS fullName FROM Person p JOIN FamilyMember fm ON p.personID=fm.personID");
$famMembers = $famMemberStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch locations for dropdown
$locStmt = $pdo->query("SELECT locationID, CONCAT(name, ' : ', address, ' : ', type) AS locName FROM Location");
$locations = $locStmt->fetchAll(PDO::FETCH_ASSOC);
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
            <label for="familyMemberID">Family Member:</label>
            <select id="familyMemberID" name="familyMemberID" required>
                <option value="">Select a person</option>
                <?php foreach ($famMembers as $famMember): ?>
                    <option value="<?php echo htmlspecialchars($famMember['familyMemberID']); ?>" <?php echo $famMember['familyMemberID'] == $clubMemberData['familyMemberID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($famMember['fullName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span
                class="error"><?php echo isset($errors['familyMemberID']) ? htmlspecialchars($errors['familyMemberID']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="relType">Relationship Type:</label>
            <select name="relType" id="relType" required>
                <option value="">Select Relationship Type</option>
                <option value="Father" <?php echo isset($relType) && $relType === 'Father' ? 'selected' : ''; ?>>Father
                </option>
                <option value="Mother" <?php echo isset($relType) && $relType === 'Mother' ? 'selected' : ''; ?>>Mother
                </option>
                <option value="GrandFather" <?php echo isset($relType) && $relType === 'GrandFather' ? 'selected' : ''; ?>>GrandFather</option>
                <option value="GrandMother" <?php echo isset($relType) && $relType === 'GrandMother' ? 'selected' : ''; ?>>GrandMother</option>
                <option value="Tutor" <?php echo isset($relType) && $relType === 'Tutor' ? 'selected' : ''; ?>>Tutor
                </option>
                <option value="Partner" <?php echo isset($relType) && $relType === 'Partner' ? 'selected' : ''; ?>>Partner
                </option>
                <option value="Friend" <?php echo isset($relType) && $relType === 'Friend' ? 'selected' : ''; ?>>Friend
                </option>
                <option value="Other" <?php echo isset($relType) && $relType === 'Other' ? 'selected' : ''; ?>>Other
                </option>
            </select>
            <span
                class="error"><?php echo isset($errors['relType']) ? htmlspecialchars($errors['relType']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="personID">Club Member:</label>
            <select id="personID" name="personID" required>
                <option value="">Select a person</option>
                <?php foreach ($persons as $person): ?>
                    <option value="<?php echo htmlspecialchars($person['personID']); ?>" <?php echo $person['personID'] == $clubMemberData['personID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($person['fullName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span
                class="error"><?php echo isset($errors['personID']) ? htmlspecialchars($errors['personID']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="locationID">Location:</label>
            <select id="locationID" name="locationID">
                <option value="">Select a location</option>
                <?php foreach ($locations as $location): ?>
                    <option value="<?php echo htmlspecialchars($location['locationID']); ?>" <?php echo $location['locationID'] === $clubMemberData['locationID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($location['locName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span
                class="error"><?php echo isset($errors['locationName']) ? htmlspecialchars($errors['locationName']) : ''; ?></span>
        </div>

        <input type="submit" class="button" value="Add Club Member">
    </form>
    <a href="index.php">Back to List</a>
</body>

</html>