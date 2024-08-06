<?php
include 'config.php';

$errors = [];
$clubMemberData = [
    'personID' => null,
    'clubMemberID' => null,
    'familyMemberID' => null,
    'relType' => null,
    'position' => '',
    'teamID' => null,
    'activationDate' => date('Y-m-d'),
    'locationID' => '',
];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $clubMemberID = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM ClubMember WHERE clubMemberID = :clubMemberID");
    $stmt->execute([':clubMemberID' => $clubMemberID]);
    $clubMember = $stmt->fetch(PDO::FETCH_ASSOC);

    //get location data
    $stmt = $pdo->prepare("SELECT L.locationID, CONCAT(L.name, ' : ', L.address, ' : ', L.type) AS locName
                    FROM 
                        ClubMember P
                        JOIN ClubMemberLocation PL on P.clubMemberID = PL.clubMemberID
                        JOIN Location L on PL.locationID = L.locationID
                    WHERE P.clubMemberID = ?;");
    $stmt->execute([$id]);
    $oldLocationData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($oldLocationData && array_key_exists('locationID', $oldLocationData)) {
        $oldLocationID = $oldLocationData['locationID'];
    } else {
        $oldLocationID = null;
    }

    //Get personID from clubMemberID
    $personStmt = $pdo->prepare("
SELECT P.personID, CONCAT(P.firstName, ' ', P.lastName) AS fullName
FROM Person P
JOIN ClubMember PP ON P.personID=PP.personID
WHERE PP.clubMemberID = ?");
    $personStmt->execute([$id]);
    $persons = $personStmt->fetchAll(PDO::FETCH_ASSOC);

    //Get role from position
    $positionStmt = $pdo->prepare("SELECT 
    TeamMember.position as position
FROM 
    TeamMember
WHERE 
    TeamMember.clubMemberID = ?;");
    $positionStmt->execute([$id]);

    $oldPositionData = $positionStmt->fetchAll(PDO::FETCH_ASSOC);

    if ($oldPositionData && array_key_exists('position', $oldPositionData)) {
        $oldPositionID = $oldPositionData['position'];
    } else {
        $oldPositionID = null;
    }

    // Get team ID
    $teamIDStmt = $pdo->prepare("SELECT 
    TeamMember.TeamID as TeamID
FROM 
    TeamMember
WHERE 
    TeamMember.clubMemberID = ?;");
    $teamIDStmt->execute([$id]);
    $oldTeamData = $teamIDStmt->fetchAll(PDO::FETCH_ASSOC);

    if ($oldTeamData && array_key_exists('TeamID', $oldTeamData)) {
        $oldTeamID = $oldTeamData['TeamID'];
    } else {
        $oldTeamID = null;
    }

    // Get familyMemberID
    $famMemStmt = $pdo->prepare("
SELECT s.familyMemberID as familyMemberID
FROM Sponsor s
JOIN ClubMember cm ON s.clubMemberID = cm.clubMemberID
WHERE s.clubMemberID = :clubMemberID
  AND s.terminationDate IS NULL;
");
    $famMemStmt->execute(['clubMemberID' => $id]); // Bind parameter properly
    $oldFamilyMemberData = $famMemStmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if the array is not empty and access the value
    if (!empty($oldFamilyMemberData)) {
        $oldFamilyMemberID = $oldFamilyMemberData[0]['familyMemberID']; // Access the correct key
    } else {
        $oldFamilyMemberID = null;
    }

    // Get relType
    $relTypeStmt = $pdo->prepare("
SELECT s.relType as relType
FROM Sponsor s
JOIN ClubMember cm ON s.clubMemberID = cm.clubMemberID
WHERE s.clubMemberID = :clubMemberID
  AND s.terminationDate IS NULL;
");
    $relTypeStmt->execute(['clubMemberID' => $id]); // Bind parameter properly
    $oldFamilyMemberRelData = $relTypeStmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if the array is not empty and access the value
    if (!empty($oldFamilyMemberRelData)) {
        $oldFamilyMemberRelID = $oldFamilyMemberRelData[0]['relType']; // Access the correct key
    } else {
        $oldFamilyMemberRelID = null;
    }

    $clubMemberData = [
        'personID' => $persons[0]['personID'],
        'clubMemberID' => $id,
        'position' => $oldPositionID,
        'teamID' => $oldTeamID,
        'activationDate' => $clubMember['activationDate'],
        'locationID' => $oldLocationID,
        'familyMemberID' => $oldFamilyMemberID,
        'relType' => $oldFamilyMemberRelID,
    ];

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
    }
}

// Fetch persons for dropdown
$personStmt = $pdo->query("SELECT personID, CONCAT(firstName, ' ', lastName) AS fullName FROM Person");
$persons = $personStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch teams for dropdown
$personStmt = $pdo->query("SELECT personID, CONCAT(firstName, ' ', lastName) AS fullName FROM Person");
$persons = $personStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch locations for dropdown
$locStmt = $pdo->query("SELECT locationID, CONCAT(name, ' : ', address, ' : ', type) AS locName FROM Location");
$locations = $locStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch family members for dropdown
$famMemberStmt = $pdo->query("SELECT familyMemberID, CONCAT(firstName, ' ', lastName) AS fullName FROM Person p JOIN FamilyMember fm ON p.personID=fm.personID");
$famMembers = $famMemberStmt->fetchAll(PDO::FETCH_ASSOC);
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
    <h1>Edit Club Member</h1>
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if (isset($clubMember)): ?>
        <form action="edit_club_member.php" method="post">
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
                    <option value="Father" <?php echo isset($clubMemberData['relType']) && $clubMemberData['relType'] === 'Father' ? 'selected' : ''; ?>>Father
                    </option>
                    <option value="Mother" <?php echo isset($clubMemberData['relType']) && $clubMemberData['relType'] === 'Mother' ? 'selected' : ''; ?>>Mother
                    </option>
                    <option value="GrandFather" <?php echo isset($clubMemberData['relType']) && $clubMemberData['relType'] === 'GrandFather' ? 'selected' : ''; ?>>GrandFather</option>
                    <option value="GrandMother" <?php echo isset($clubMemberData['relType']) && $clubMemberData['relType'] === 'GrandMother' ? 'selected' : ''; ?>>GrandMother</option>
                    <option value="Tutor" <?php echo isset($clubMemberData['relType']) && $clubMemberData['relType'] === 'Tutor' ? 'selected' : ''; ?>>Tutor
                    </option>
                    <option value="Partner" <?php echo isset($clubMemberData['relType']) && $clubMemberData['relType'] === 'Partner' ? 'selected' : ''; ?>>Partner
                    </option>
                    <option value="Friend" <?php echo isset($clubMemberData['relType']) && $clubMemberData['relType'] === 'Friend' ? 'selected' : ''; ?>>Friend
                    </option>
                    <option value="Other" <?php echo isset($clubMemberData['relType']) && $clubMemberData['relType'] === 'Other' ? 'selected' : ''; ?>>Other
                    </option>
                </select>
                <span
                    class="error"><?php echo isset($errors['relType']) ? htmlspecialchars($errors['relType']) : ''; ?></span>
            </div>

            <div class="form-group">
                <label for="personID">Club Member:</label>
                <select id="personID" name="personID" required disabled>
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


            <label for="teamID">Team:</label>
            <select id="teamID" name="teamID">
                <option value="">Select a team</option>
            </select>

            <div class="form-group">
                <label for="position">Position:</label>
                <select name="position" id="position">
                    <option value="">Select Position</option>
                    <option value="Forward" <?php echo isset($position) && $position === 'Forward' ? 'selected' : ''; ?>>
                        Forward</option>
                    <option value="Midfielder" <?php echo isset($position) && $position === 'Midfielder' ? 'selected' : ''; ?>>Midfielder</option>
                    <option value="Defender" <?php echo isset($position) && $position === 'Defender' ? 'selected' : ''; ?>>
                        Defender</option>
                    <option value="Goalkeeper" <?php echo isset($position) && $position === 'Goalkeeper' ? 'selected' : ''; ?>>Goalkeeper</option>
                </select>
                <span
                    class="error"><?php echo isset($errors['position']) ? htmlspecialchars($errors['position']) : ''; ?></span>
            </div>


            <input type="submit" class="button" value="Update Club Member">
        </form>
    <?php else: ?>
        <p>Invalid Club Member ID.</p>
    <?php endif; ?>
    <a href="index.php">Back to List</a>

</body>

</html>