<?php
include 'config.php';

$errors = [];
$personnelData = [
    'personID' => null,
    'personnelID' => null,
    'role' => 'Administrator',
    'mandate' => 'Volunteer',
    'activationDate' => date('Y-m-d'),
    'locationID' => '',
    'isManager' => '',
];

// Fetch location managers
$managerStmt = $pdo->query("
    SELECT L.locationID, CONCAT(P.firstName, ' ', P.lastName) AS generalManagerName
    FROM
        Location L 
        JOIN ManagerLocation ML on L.locationID = ML.locationID
        JOIN Personnel PE ON ML.personnelID = PE.personnelID
        JOIN Person P ON PE.personID = P.personID
    WHERE 
        PE.role = 'Administrator' AND ML.terminationDate is null");
$managers = $managerStmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch existing data
    $stmt = $pdo->prepare("SELECT * FROM Personnel WHERE personnelID = ?");
    $stmt->execute([$id]);
    $personnelDataFetched = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$personnelDataFetched) {
        die('Personnel not found.');
    }

    //get location data
    $stmt = $pdo->prepare("SELECT L.locationID, CONCAT(L.name, ' : ', L.address, ' : ', L.type) AS locName
                    FROM 
                        Personnel P
                        JOIN PersonnelLocation PL on P.personnelID = PL.personnelID
                        JOIN Location L on PL.locationID = L.locationID
                    WHERE P.personnelID = ?;");
    $stmt->execute([$id]);
    $oldLocationData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($oldLocationData && array_key_exists('locationID', $oldLocationData)) {
        $oldLocationID = $oldLocationData['locationID'];
    } else {
        $oldLocationID = null;
    }

    //get manager data
    $stmt = $pdo->prepare("SELECT PE.personnelID
FROM
    Location L 
    JOIN ManagerLocation ML on L.locationID = ML.locationID
    JOIN Personnel PE ON ML.personnelID = PE.personnelID
    JOIN Person P ON PE.personID = P.personID
WHERE 
    L.locationID = ?
    AND PE.role = 'Administrator'
    AND PE.terminationDate is null");

    $stmt->execute([$id]);
    $oldManagerData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($oldManagerData && array_key_exists('personnelID', $oldManagerData)) {
        $isManagerOfCurLoc = $oldManagerData['personnelID'] == $id;
    } else {
        $isManagerOfCurLoc = false;
    }

    //Get personID from personnelID
    $personStmt = $pdo->prepare("
SELECT P.personID, CONCAT(P.firstName, ' ', P.lastName) AS fullName
FROM Person P
JOIN Personnel PP ON P.personID=PP.personID
WHERE PP.personnelID = ?");
    $personStmt->execute([$id]);
    $persons = $personStmt->fetchAll(PDO::FETCH_ASSOC);

    $personnelData = [
        'personID' => $persons[0]['personID'],
        'role' => $personnelDataFetched['role'],
        'mandate' => $personnelDataFetched['mandate'],
        'activationDate' => $personnelDataFetched['activationDate'],
        'locationID' => $oldLocationID,
        'isManager' => $isManagerOfCurLoc,
        'personnelID' => $id,
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Validation
        if (empty($_POST['role']) && empty($_POST['hiddenRole'])) {
            $errors['role'] = 'Role is required.';
        }
        if (empty($_POST['mandate'])) {
            $errors['mandate'] = 'Mandate is required.';
        }
        if (empty($_POST['activationDate'])) {
            $errors['activationDate'] = 'Activation date is required.';
        }

        // Collect data
        $personnelData = [
            'personID' => $personnelData['personID'],
            'role' => empty($_POST['role']) ? $_POST['hiddenRole'] : $_POST['role'],
            'mandate' => $_POST['mandate'],
            'locationID' => $_POST['locationID'],
            'isManager' => empty($_POST['isManager']) ? null : $_POST['isManager'],
            'activationDate' => $_POST['activationDate'],
        ];

        // If no errors, insert data into the database
        if (empty($errors)) {

            //Start a transaction, and rollback everything in case of errors
            $pdo->beginTransaction();

            //Personnel entry
            $sql = "UPDATE Personnel SET role=?, mandate=?, activationDate=? WHERE PersonnelID=?";
            $stmt = $pdo->prepare($sql);

            try {
                $stmt->execute([
                    $personnelData['role'],
                    $personnelData['mandate'],
                    $personnelData['activationDate'],
                    $id,
                ]);
            } catch (PDOException $e) {
                $errors['database'] = "Error: " . $e->getMessage();
                $pdo->rollBack();
                exit;
            }

            //PersonnelLocation entry

            //If the location has changed
            if ($_POST['locationID'] != $oldLocationID) {

                //First, terminate the old location
                $sql = "UPDATE PersonnelLocation SET terminationDate=CURDATE() WHERE personnelID=? AND locationID=?";
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

                //Second, remove the manager link if the peson was a manager
                if ($isManagerOfCurLoc) {
                    $sql = "UPDATE ManagerLocation SET terminationDate=CURDATE() WHERE personnelID=? AND locationID=?";
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
                }

                //Then, create the new link if needed
                if (!empty($_POST['locationID'])) {
                    $sql = "INSERT INTO PersonnelLocation (personnelID, locationID, activationDate) VALUES (?, ?, CURDATE())";
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

            } else {
                //If location has not changed, but manager status has changed

                if ($isManagerOfCurLoc != $_POST['isManager']) {
                    if ($isManagerOfCurLoc == true) {
                        //Remove manager link
                        if ($isManagerOfCurLoc) {
                            $sql = "UPDATE ManagerLocation SET terminationDate=CURDATE() WHERE personnelID=? AND locationID=?";
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
                        }
                    } else {
                        //Add manager link
                        $sql = "INSERT INTO ManagerLocation (personnelID, locationID, activationDate) VALUES (?, ?, CURDATE())";
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

            if (empty($errors)) {
                $pdo->commit();
                header("Location: index.php");
            }
        }
    }
}

// Fetch locations for dropdown
$locStmt = $pdo->query("SELECT locationID, CONCAT(name, ' : ', address, ' : ', type) AS locName FROM Location");
$locations = $locStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch location managers
$managerStmt = $pdo->query("
SELECT L.locationID, CONCAT(P.firstName, ' ', P.lastName) AS generalManagerName
FROM
    Location L 
    JOIN ManagerLocation ML on L.locationID = ML.locationID
    JOIN Personnel PE ON ML.personnelID = PE.personnelID
    JOIN Person P ON PE.personID = P.personID
WHERE 
    PE.role = 'Administrator'");
$managers = $managerStmt->fetchAll(PDO::FETCH_ASSOC);
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

        .checkbox-container {
            display: flex;
            align-items: center;
            margin: 10px;
        }

        .styled-checkbox {
            position: absolute;
            opacity: 0;
        }

        .styled-checkbox+.checkbox-label {
            position: relative;
            cursor: pointer;
            padding-left: 25px;
        }

        .styled-checkbox+.checkbox-label:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 18px;
            height: 18px;
            border: 2px solid #ccc;
            border-radius: 3px;
            background: white;
        }

        .styled-checkbox:checked+.checkbox-label:before {
            content: '\2714';
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            background-color: #007bff;
            border: none;
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
    <form action="edit_personnel.php?id=<?php echo htmlspecialchars($id); ?>" method="post">
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
            <label for="locationID">Location:</label>
            <select id="locationID" name="locationID">
                <option value="">Select a location</option>
                <?php foreach ($locations as $location): ?>
                    <option value="<?php echo htmlspecialchars($location['locationID']); ?>" <?php echo $location['locationID'] === $personnelData['locationID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($location['locName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span
                class="error"><?php echo isset($errors['locationName']) ? htmlspecialchars($errors['locationName']) : ''; ?></span>
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

        <div class="checkbox-container">
            <input type="checkbox" id="isManager" name="isManager" class="styled-checkbox">
            <label for="isManager" class="checkbox-label">Manager of Location</label>
        </div>
        <span id="managerName" class="manager-name" style="{margin: 15px}"></span>
        <input type="hidden" id="hiddenRole" name="hiddenRole" value="">

        <input type="submit" class="button" value="Update Personnel">
    </form>
    <a href="index.php">Back to List</a>

    <script>
        document.querySelector('form').addEventListener('submit', function (event) {
            console.log("Form submitted");
        });
    </script>

    <script>
        // Fetch managers data from PHP and parse it to JavaScript
        const managers = <?php echo json_encode($managers); ?>;

        document.getElementById('locationID').addEventListener('change', function () {
            const locationID = this.value;
            const isManagerCheckbox = document.getElementById('isManager');
            const checkboxContainer = isManagerCheckbox.parentElement;
            const managerNameSpan = document.getElementById('managerName');

            // Find the manager for the selected location
            const manager = managers.find(m => m.locationID == locationID);

            if (manager) {
                // If a manager exists, hide the checkbox and display the manager's name
                checkboxContainer.style.display = 'none';
                managerNameSpan.textContent = 'Current Manager: ' + manager.generalManagerName;
                managerNameSpan.style.display = 'block'; // Ensure the span is visible
            } else {
                // If no manager exists, show the checkbox and clear the manager's name
                checkboxContainer.style.display = 'block';
                managerNameSpan.textContent = '';
                managerNameSpan.style.display = 'none'; // Hide the span
            }
        });
    </script>

    <script>
        document.getElementById('isManager').addEventListener('change', function () {
            const isChecked = this.checked;
            const roleDropdown = document.getElementById('role'); // Ensure the role dropdown has this id
            const hiddenRoleInput = document.getElementById('hiddenRole'); // Hidden input field for role

            if (isChecked) {
                // If the checkbox is checked, set the role dropdown to 'Administrator' and disable it
                roleDropdown.value = 'Administrator'; // Set to 'Administrator'
                hiddenRoleInput.value = 'Administrator';
                roleDropdown.disabled = true; // Make dropdown unchangeable
            } else {
                // If the checkbox is unchecked, allow the role dropdown to be changed
                roleDropdown.disabled = false; // Make dropdown changeable
            }
        });
    </script>
</body>

</html>