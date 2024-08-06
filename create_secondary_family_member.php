<?php
include 'config.php';

// Fetch all family members from the database with the correct column names
$stmt = $pdo->query("SELECT familyMemberID, CONCAT(firstName, ' ', lastName) AS familyMemberName FROM FamilyMember f JOIN Person p ON (f.personID=p.personID)");
$familyMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $familyMemberID = $_POST['familyMemberID'];
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $relType = trim($_POST['relType']);
    $phone = trim($_POST['phone']);

    // Validate input fields
    if (empty($familyMemberID)) {
        $errors['familyMemberID'] = 'Family member is required.';
    }
    if (empty($firstName)) {
        $errors['firstName'] = 'First name is required.';
    }
    if (empty($lastName)) {
        $errors['lastName'] = 'Last name is required.';
    }
    if (empty($relType)) {
        $errors['relType'] = 'Relationship type is required.';
    }
    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required.';
    }

    // If no errors, proceed with the database insertion
    if (empty($errors)) {
        // Prepare and execute the insert statement
        $stmt = $pdo->prepare("INSERT INTO EmergencyContact (familyMemberID, firstName, lastName, relType, phone) VALUES (:familyMemberID, :firstName, :lastName, :relType, :phone)");

        try {
            $stmt->execute([
                'familyMemberID' => $familyMemberID,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'relType' => $relType,
                'phone' => $phone
            ]);

            $success = "Club member added successfully!";
            header("Location: index.php");  // Redirect to the main page after insertion
        } catch (PDOException $e) {
            $errors['database'] = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Emergency Contact</title>
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
    <h1>Add Emergency Contact</h1>
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
        <div class="form-group">
            <label for="familyMemberID">Family Member:</label>
            <select name="familyMemberID" id="familyMemberID" required>
                <option value="">Select a family member</option>
                <?php foreach ($familyMembers as $member): ?>
                    <option value="<?php echo htmlspecialchars($member['familyMemberID']); ?>" <?php echo (isset($familyMemberID) && $familyMemberID == $member['familyMemberID']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($member['familyMemberName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span
                class="error"><?php echo isset($errors['familyMemberID']) ? htmlspecialchars($errors['familyMemberID']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="firstName">First Name:</label>
            <input type="text" name="firstName" id="firstName"
                value="<?php echo isset($firstName) ? htmlspecialchars($firstName) : ''; ?>" required>
            <span
                class="error"><?php echo isset($errors['firstName']) ? htmlspecialchars($errors['firstName']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="lastName">Last Name:</label>
            <input type="text" name="lastName" id="lastName"
                value="<?php echo isset($lastName) ? htmlspecialchars($lastName) : ''; ?>" required>
            <span
                class="error"><?php echo isset($errors['lastName']) ? htmlspecialchars($errors['lastName']) : ''; ?></span>
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
            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone"
                value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" required>
            <span class="error"><?php echo isset($errors['phone']) ? htmlspecialchars($errors['phone']) : ''; ?></span>
        </div>

        <input type="submit" class="button" value="Add Emergency Contact">
    </form>
    <a href="index.php">Back to List</a>
</body>

</html>