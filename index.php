<?php
include 'config.php';

// Fetch all persons from the database
$stmt = $pdo->query("SELECT * FROM Person");
$persons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Person List</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #80AD4E;
            color: #fff;
        }
        td {
            background-color: #fff;
        }
        .actions {
            text-align: center;
        }
        .actions a {
            display: inline-block;
            margin: 0 5px;
            padding: 5px 10px;
            background-color: #80AD4E;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
        .actions a:hover {
            background-color: #5D7D39;
        }
        .container {
            max-width: 1000px;
            margin: auto;
        }
        .button {
            padding: 10px 20px;
            background-color: #80AD4E;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 0;
        }
        .button:hover {
            background-color: #5D7D39;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Person List</h1>
        <a href="create.php" class="button">Add New Person</a>
        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>SSN</th>
                    <th>Medicare ID</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Province</th>
                    <th>Postal Code</th>
                    <th>Date of Birth</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($persons): ?>
                    <?php foreach ($persons as $person): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($person['firstName']); ?></td>
                            <td><?php echo htmlspecialchars($person['lastName']); ?></td>
                            <td><?php echo htmlspecialchars($person['email']); ?></td>
                            <td><?php echo htmlspecialchars($person['phone']); ?></td>
                            <td><?php echo htmlspecialchars($person['gender']); ?></td>
                            <td><?php echo htmlspecialchars($person['SSN']); ?></td>
                            <td><?php echo htmlspecialchars($person['medicareID']); ?></td>
                            <td><?php echo htmlspecialchars($person['address']); ?></td>
                            <td><?php echo htmlspecialchars($person['city']); ?></td>
                            <td><?php echo htmlspecialchars($person['province']); ?></td>
                            <td><?php echo htmlspecialchars($person['postalCode']); ?></td>
                            <td><?php echo htmlspecialchars($person['dateOfBirth']); ?></td>
                            <td class="actions">
                                <a href="edit.php?id=<?php echo $person['personID']; ?>">Edit</a>
                                <a href="delete.php?id=<?php echo $person['personID']; ?>" onclick="return confirm('Are you sure you want to delete this person?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="13">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
