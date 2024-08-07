<?php
include 'config.php';

$errors = [];
$familyData = [
    'personID' => null,
    'locationID' => null,
];

$id = intval($_GET['id']);

// Fetch person
$stmt = $pdo->query("
SELECT P.personID, CONCAT(P.firstName, ' ', P.lastName) AS personName
FROM
    Person P
JOIN FamilyMember fm ON P.personID=fm.personID");
$persons = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch locations for dropdown
$locStmt = $pdo->query("SELECT locationID, CONCAT(name, ' : ', address, ' : ', type) AS locName FROM Location");
$locations = $locStmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT L.locationID, CONCAT(L.name, ' : ', L.address, ' : ', L.type) AS locName
                    FROM 
                        FamilyMember P
                        JOIN FamilyMemberLocation PL on P.familyMemberID = PL.familyMemberID
                        JOIN Location L on PL.locationID = L.locationID
                    WHERE P.familyMemberID = ?;");
$stmt->execute([$id]);
$oldLocationData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!empty($oldLocationData)) {
    $oldLocationID = $oldLocationData['locationID'];
} else {
    $oldLocationID = null;
}

phpAlert($oldLocationID);

$familyData = [
    'personID' => $persons[0]['personID'],
    'locationID' => $oldLocationID,
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //If the location has changed
    if ($_POST['locationID'] != $oldLocationID) {

        $pdo->beginTransaction();

        //First, terminate the old location
        $sql = "UPDATE FamilyMemberLocation SET terminationDate=CURDATE() WHERE familyMemberID=? AND locationID=? AND terminationDate is null";
        $stmt = $pdo->prepare($sql);

        try {
            $stmt->execute([
                $id,
                $oldLocationID,
            ]);
        } catch (PDOException $e) {
            $errors['database'] = "Error: " . $e->getMessage();
            $pdo->rollBack();
            exit;
        }

        //Then, create the new link if needed
        if (!empty($_POST['locationID'])) {
            $sql = "INSERT INTO FamilyMemberLocation (familyMemberID, locationID, activationDate) VALUES (?, ?, CURDATE())";
            $stmt = $pdo->prepare($sql);

            try {
                $stmt->execute([
                    $id,
                    $_POST['locationID']
                ]);
            } catch (PDOException $e) {
                $errors['database'] = "Error: " . $e->getMessage();
                $pdo->rollBack();
                exit;
            }
        }
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
        <select id="personID" name="personID" disabled>
            <option value="">Select a person</option>
            <?php foreach ($persons as $person): ?>
                <option value="<?php echo htmlspecialchars($person['personID']); ?>" <?php echo $person['personID'] === $familyData['personID'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($person['personName']); ?>
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
                    <option value="<?php echo htmlspecialchars($location['locationID']); ?>" <?php echo $location['locationID'] === $familyData['locationID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($location['locName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span
                class="error"><?php echo isset($errors['locationName']) ? htmlspecialchars($errors['locationName']) : ''; ?></span>
        </div>

        <input type="submit" class="button" value="Edit Family Member">
    </form>
    <a href="index.php">Back to List</a>
</body>

</html>