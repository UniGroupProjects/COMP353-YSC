<?php
include 'config.php';

$errors = [];
$locationData = [
    'name' => '',
    'address' => '',
    'city' => '',
    'province' => '',
    'postalCode' => '',
    'phone' => '',
    'website' => '',
    'type' => 'Head',
    'capacity' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation
    if (empty($_POST['name'])) {
        $errors['name'] = 'Name is required.';
    }
    if (empty($_POST['address'])) {
        $errors['address'] = 'Address is required.';
    }
    if (empty($_POST['city'])) {
        $errors['city'] = 'City is required.';
    }
    if (empty($_POST['province'])) {
        $errors['province'] = 'Province is required.';
    }
    if (empty($_POST['postalCode'])) {
        $errors['postalCode'] = 'Postal Code is required.';
    }
    if (!is_numeric($_POST['capacity']) || intval($_POST['capacity']) < 0) {
        $errors['capacity'] = 'Capacity must be a non-negative integer.';
    }

    // Collect data
    $locationData = [
        'name' => $_POST['name'],
        'address' => $_POST['address'],
        'city' => $_POST['city'],
        'province' => $_POST['province'],
        'postalCode' => $_POST['postalCode'],
        'phone' => $_POST['phone'],
        'website' => $_POST['website'],
        'type' => $_POST['type'],
        'capacity' => $_POST['capacity']
    ];

    // If no errors, insert data into the database
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO Location (name, address, city, province, postalCode, phone, website, type, capacity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $locationData['name'],
            $locationData['address'],
            $locationData['city'],
            $locationData['province'],
            $locationData['postalCode'],
            $locationData['phone'],
            $locationData['website'],
            $locationData['type'],
            $locationData['capacity']
        ]);
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Location</title>
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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
    <h1>Create Location</h1>
    <form action="create_location.php" method="post">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($locationData['name']); ?>">
            <span class="error"><?php echo isset($errors['name']) ? htmlspecialchars($errors['name']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($locationData['address']); ?>">
            <span class="error"><?php echo isset($errors['address']) ? htmlspecialchars($errors['address']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="city">City:</label>
            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($locationData['city']); ?>">
            <span class="error"><?php echo isset($errors['city']) ? htmlspecialchars($errors['city']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="province">Province:</label>
            <input type="text" id="province" name="province" value="<?php echo htmlspecialchars($locationData['province']); ?>">
            <span class="error"><?php echo isset($errors['province']) ? htmlspecialchars($errors['province']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="postalCode">Postal Code:</label>
            <input type="text" id="postalCode" name="postalCode" value="<?php echo htmlspecialchars($locationData['postalCode']); ?>">
            <span class="error"><?php echo isset($errors['postalCode']) ? htmlspecialchars($errors['postalCode']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($locationData['phone']); ?>">
        </div>

        <div class="form-group">
            <label for="website">Website:</label>
            <input type="text" id="website" name="website" value="<?php echo htmlspecialchars($locationData['website']); ?>">
        </div>

        <div class="form-group">
            <label for="type">Type:</label>
            <select id="type" name="type">
                <option value="Head" <?php echo $locationData['type'] === 'Head' ? 'selected' : ''; ?>>Head</option>
                <option value="Branch" <?php echo $locationData['type'] === 'Branch' ? 'selected' : ''; ?>>Branch</option>
            </select>
        </div>

        <div class="form-group">
            <label for="capacity">Capacity:</label>
            <input type="number" id="capacity" name="capacity" value="<?php echo htmlspecialchars($locationData['capacity']); ?>" min="0">
            <span class="error"><?php echo isset($errors['capacity']) ? htmlspecialchars($errors['capacity']) : ''; ?></span>
        </div>

        <input type="submit" class="button" value="Create Location">
    </form>
    <a href="index.php">Back to List</a>
</body>
</html>
