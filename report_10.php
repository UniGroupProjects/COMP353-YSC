<?php
include 'config.php';

// Initialize variables for filtering
$startDate = null;
$locationID = null;
$isGenerated = false;
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate inputs
    if (empty($_POST['startDate'])) {
        $errors[] = 'Start date is required.';
    } else {
        $startDate = $_POST['startDate'];
    }

    if (empty($_POST['locationID'])) {
        $errors[] = 'Location ID is required.';
    } else {
        $locationID = $_POST['locationID'];
    }

    // If no errors, execute the prepared statement
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            SELECT 
                cm.clubMemberID,
                p.firstName,
                p.lastName
            FROM 
                ClubMember cm
            JOIN 
                Person p ON cm.personID = p.personID
            JOIN 
                ClubMemberLocation cml ON cm.clubMemberID = cml.clubMemberID
            WHERE 
                cm.activationDate >= :startDate
                AND cm.terminationDate IS NULL
                AND cml.locationID = :locationID
            GROUP BY 
                cm.clubMemberID, p.firstName, p.lastName
            HAVING 
                COUNT(DISTINCT cml.locationID) >= 4
            ORDER BY 
                cm.clubMemberID ASC
        ");
        $stmt->execute(['startDate' => $startDate, 'locationID' => $locationID]);
        $teamFormations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $isGenerated = true;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Team Formations Report</title>
    <style>
        /* Your existing styles */
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
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            padding: 10px 20px;
            background-color: #80AD4E;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #5D7D39;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .reset-button {
            background-color: #f44336;
        }

        .reset-button:hover {
            background-color: #c62828;
        }
    </style>
</head>

<body>
    <h1>Team Formations for Given Location and Day Report</h1>
    <form method="POST">
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <label for="startDate">Start Date:</label>
        <input type="date" name="startDate" id="startDate" value="<?php echo htmlspecialchars($startDate); ?>" required>

        <label for="locationID">Location ID:</label>
        <input type="number" name="locationID" id="locationID" value="<?php echo htmlspecialchars($locationID); ?>"
            required>

        <button type="submit">Generate</button>
        <button type="button" class="reset-button" onclick="resetForm()">Reset</button>
    </form>

    <?php if (!empty($teamFormations)): ?>
        <h2>Report Results</h2>
        <table>
            <thead>
                <tr>
                    <th>Club Member ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teamFormations as $formation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($formation['clubMemberID']); ?></td>
                        <td><?php echo htmlspecialchars($formation['firstName']); ?></td>
                        <td><?php echo htmlspecialchars($formation['lastName']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif (empty($teamFormations) && $isGenerated): ?>
        <p>No data found.</p>
    <?php endif; ?>

    <script>
        function resetForm() {
            document.querySelector('form').reset();
            // Set the isGenerated variable to false on the client-side (this part needs server-side handling)
            window.location.href = window.location.href.split('?')[0]; // Reload the page to reset PHP variables
        }
    </script>
</body>

</html>