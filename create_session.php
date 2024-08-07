<?php
include 'config.php';

$errors = [];
$sessionData = [
    'sessionDate' => '',
    'sessionTime' => '',
    'sessionType' => '',
    'team1ID' => '',
    'team2ID' => '',
    'team1Score' => '',
    'team2Score' => '',
    'address' => ''
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation
    if (empty($_POST['sessionDate'])) {
        $errors['sessionDate'] = 'Session date is required.';
    }
    if (empty($_POST['sessionTime'])) {
        $errors['sessionTime'] = 'Session time is required.';
    }
    if (empty($_POST['sessionType'])) {
        $errors['sessionType'] = 'Session type is required.';
    }
    if (empty($_POST['team1ID'])) {
        $errors['team1ID'] = 'Team 1 is required.';
    }
    if (empty($_POST['team2ID'])) {
        $errors['team2ID'] = 'Team 2 is required.';
    }
    if (empty($_POST['address'])) {
        $errors['address'] = 'Address is required.';
    }

    // Collect data
    $sessionData = [
        'sessionDate' => $_POST['sessionDate'],
        'sessionTime' => $_POST['sessionTime'],
        'sessionType' => $_POST['sessionType'],
        'team1ID' => $_POST['team1ID'],
        'team2ID' => $_POST['team2ID'],
        'team1Score' => $_POST['team1Score'],
        'team2Score' => $_POST['team2Score'],
        'address' => $_POST['address']
    ];

    //Check latest team 1 session time
    // $stmt = $pdo->prepare("SELECT MAX(date) as latestDate, MAX(time) as latestTime FROM Session s JOIN SessionTeams st ON s.sessionID=st.sessionID WHERE st.teamID=?");
    // $stmt->execute([$_POST['team1ID']]);
    // $latestT1Session= $stmt->fetch(PDO::FETCH_ASSOC);

    // $stmt->execute([$_POST['team2ID']]);
    // $latestT2Session= $stmt->fetch(PDO::FETCH_ASSOC);


    // $headLocationID = null;
    // if (!empty($headLocation)) {
    //     $headLocationID = $headLocation['locationID'];
    // }

    // If no errors, insert data into the database
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $sql = "INSERT INTO Session (date, time, type, address) VALUES (:sessionDate, :sessionTime, :sessionType, :address)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':sessionDate' => $sessionData['sessionDate'],
                ':sessionTime' => $sessionData['sessionTime'],
                ':sessionType' => $sessionData['sessionType'],
                ':address' => $sessionData['address']
            ]);

            $sessionID = $pdo->lastInsertId();

            $sql = "INSERT INTO SessionTeams (sessionID, teamID, score) VALUES (:sessionID, :teamID, :score)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':sessionID' => $sessionID,
                ':teamID' => $sessionData['team1ID'],
                ':score' => empty($sessionData['team1Score']) ? null : $sessionData['team1Score']
            ]);

            $stmt->execute([
                ':sessionID' => $sessionID,
                ':teamID' => $sessionData['team2ID'],
                ':score' => empty($sessionData['team2Score']) ? null : $sessionData['team2Score']
            ]);

            $pdo->commit();
            header("Location: index.php");
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors['database'] = "Error: " . $e->getMessage();
        }
    }
}

// Fetch teams for dropdown
$teamStmt = $pdo->query("SELECT teamID, name FROM Team");
$teams = $teamStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Session</title>
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
    </style>
</head>

<body>
    <h1>Create Session</h1>
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="create_session.php" method="post">
        <div class="form-group">
            <label for="sessionDate">Session Date:</label>
            <input type="date" id="sessionDate" name="sessionDate"
                value="<?php echo htmlspecialchars($sessionData['sessionDate']); ?>" required>
            <span
                class="error"><?php echo isset($errors['sessionDate']) ? htmlspecialchars($errors['sessionDate']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="sessionTime">Session Time:</label>
            <input type="time" id="sessionTime" name="sessionTime"
                value="<?php echo htmlspecialchars($sessionData['sessionTime']); ?>" required>
            <span
                class="error"><?php echo isset($errors['sessionTime']) ? htmlspecialchars($errors['sessionTime']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="sessionType">Session Type:</label>
            <select id="sessionType" name="sessionType" required>
                <option value="">Select Session Type</option>
                <option value="Practice" <?php echo isset($sessionData['sessionType']) && $sessionData['sessionType'] === 'Training' ? 'selected' : ''; ?>>Training</option>
                <option value="Game" <?php echo isset($sessionData['sessionType']) && $sessionData['sessionType'] === 'Game' ? 'selected' : ''; ?>>Game</option>
            </select>
            <span
                class="error"><?php echo isset($errors['sessionType']) ? htmlspecialchars($errors['sessionType']) : ''; ?></span>
        </div>


        <div class="form-group">
            <label for="team1ID">Team 1:</label>
            <select id="team1ID" name="team1ID" required>
                <option value="">Select a team</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?php echo htmlspecialchars($team['teamID']); ?>" <?php echo $team['teamID'] == $sessionData['team1ID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($team['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span
                class="error"><?php echo isset($errors['team1ID']) ? htmlspecialchars($errors['team1ID']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="team2ID">Team 2:</label>
            <select id="team2ID" name="team2ID" required>
                <option value="">Select a team</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?php echo htmlspecialchars($team['teamID']); ?>" <?php echo $team['teamID'] == $sessionData['team2ID'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($team['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span
                class="error"><?php echo isset($errors['team2ID']) ? htmlspecialchars($errors['team2ID']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="team1Score">Team 1 Score:</label>
            <input type="number" id="team1Score" name="team1Score"
                value="<?php echo htmlspecialchars($sessionData['team1Score']); ?>">
            <span
                class="error"><?php echo isset($errors['team1Score']) ? htmlspecialchars($errors['team1Score']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="team2Score">Team 2 Score:</label>
            <input type="number" id="team2Score" name="team2Score"
                value="<?php echo htmlspecialchars($sessionData['team2Score']); ?>">
            <span
                class="error"><?php echo isset($errors['team2Score']) ? htmlspecialchars($errors['team2Score']) : ''; ?></span>
        </div>

        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address"
                value="<?php echo htmlspecialchars($sessionData['address']); ?>" required>
            <span
                class="error"><?php echo isset($errors['address']) ? htmlspecialchars($errors['address']) : ''; ?></span>
        </div>

        <button type="submit" class="button">Create Session</button>
    </form>
    <a href="index.php">Back to List</a>
</body>

</html>