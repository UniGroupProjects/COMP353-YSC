<?php
include 'config.php';

$errors = [];
$personData = [
    'firstName' => '',
    'lastName' => '',
    'email' => '',
    'phone' => '',
    'gender' => 'Male',
    'SSN' => '',
    'medicareID' => '',
    'address' => '',
    'city' => '',
    'province' => '',
    'postalCode' => '',
    'dateOfBirth' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation
    if (empty($_POST['firstName'])) {
        $errors['firstName'] = 'First Name is required.';
    }
    if (empty($_POST['lastName'])) {
        $errors['lastName'] = 'Last Name is required.';
    }
    if (empty($_POST['SSN']) || strlen($_POST['SSN']) != 9) {
        $errors['SSN'] = 'SSN is required and must be exactly 9 characters.';
    }
    if (empty($_POST['dateOfBirth'])) {
        $errors['dateOfBirth'] = 'Date of Birth is required.';
    }

    // Collect data
    $personData = [
        'firstName' => $_POST['firstName'],
        'lastName' => $_POST['lastName'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'gender' => $_POST['gender'],
        'SSN' => $_POST['SSN'],
        'medicareID' => $_POST['medicareID'],
        'address' => $_POST['address'],
        'city' => $_POST['city'],
        'province' => $_POST['province'],
        'postalCode' => $_POST['postalCode'],
        'dateOfBirth' => $_POST['dateOfBirth']
    ];

    // If no errors, insert data into the database
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO Person (firstName, lastName, email, phone, gender, SSN, medicareID, address, city, province, postalCode, dateOfBirth) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        try {
            $stmt->execute([
                $personData['firstName'],
                $personData['lastName'],
                $personData['email'],
                $personData['phone'],
                $personData['gender'],
                $personData['SSN'],
                $personData['medicareID'],
                $personData['address'],
                $personData['city'],
                $personData['province'],
                $personData['postalCode'],
                $personData['dateOfBirth']
            ]);
            header('Location: index.php');
        } catch (PDOException $e) {
            $errors['database'] = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Person</title>
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
    <h1>Add New Person</h1>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php elseif (!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <form action="create_person.php" method="post">
        <div class="form-group">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName"
                value="<?php echo htmlspecialchars($personData['firstName']); ?>">
            <span
                class="error"><?php echo isset($errors['firstName']) ? htmlspecialchars($errors['firstName']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName"
                value="<?php echo htmlspecialchars($personData['lastName']); ?>">
            <span
                class="error"><?php echo isset($errors['lastName']) ? htmlspecialchars($errors['lastName']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($personData['email']); ?>">
        </div>

        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($personData['phone']); ?>">
        </div>

        <div class="form-group">
            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="Male" <?php echo $personData['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo $personData['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>

        <div class="form-group">
            <label for="SSN">SSN:</label>
            <input type="text" id="SSN" name="SSN" value="<?php echo htmlspecialchars($personData['SSN']); ?>">
            <span class="error"><?php echo isset($errors['SSN']) ? htmlspecialchars($errors['SSN']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="medicareID">Medicare ID:</label>
            <input type="text" id="medicareID" name="medicareID"
                value="<?php echo htmlspecialchars($personData['medicareID']); ?>">
        </div>

        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address"
                value="<?php echo htmlspecialchars($personData['address']); ?>">
        </div>

        <div class="form-group">
            <label for="city">City:</label>
            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($personData['city']); ?>">
        </div>

        <div class="form-group">
            <label for="province">Province:</label>
            <input type="text" id="province" name="province"
                value="<?php echo htmlspecialchars($personData['province']); ?>">
        </div>

        <div class="form-group">
            <label for="postalCode">Postal Code:</label>
            <input type="text" id="postalCode" name="postalCode"
                value="<?php echo htmlspecialchars($personData['postalCode']); ?>">
        </div>

        <div class="form-group">
            <label for="dateOfBirth">Date of Birth:</label>
            <input type="date" id="dateOfBirth" name="dateOfBirth"
                value="<?php echo htmlspecialchars($personData['dateOfBirth']); ?>">
            <span
                class="error"><?php echo isset($errors['dateOfBirth']) ? htmlspecialchars($errors['dateOfBirth']) : ''; ?></span>
        </div>

        <input type="submit" class="button" value="Add Person">
    </form>
    <a href="index.php">Back to List</a>
</body>

</html>