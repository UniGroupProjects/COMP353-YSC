<?php
$host = 'mnc353.encs.concordia.ca'; // Database host
$db = 'mnc353_1'; // Database name
$user = 'mnc353_1'; // Database username
$pass = 'f3h6rEF0'; // Database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

//Get sessions for next week
$sql = "
   SELECT 
    s.sessionID as sessionID, t.teamID as teamID, t.name as name, s.date as date, s.time as time, s.type as type, s.address as address
FROM 
    Session s 
    JOIN SessionTeams st ON s.sessionID=st.sessionID 
    JOIN Team t ON t.teamID=st.teamID
WHERE s.date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 WEEK)
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare to log emails
$logStmt = $pdo->prepare("
    INSERT INTO EmailLogs (sessionID, date, sender, receiver, subject, bodyHead)
    VALUES (:sessionID, :date, :sender, :receiver, :subject, :bodyHead)
");

// Send emails and log them
foreach ($sessions as $session) {
    $subject = $session['name'] . " " . $session['date'] . " " . $session['time'] . " " . $session['type'] . " session";

    //Get all members in team with their position
    $sql = "
   SELECT 
    p.firstName as firstName, p.lastName as lastName, tm.position as position, p.email as email
    FROM SessionTeams st
    JOIN TeamMember tm ON tm.teamID=st.teamID
    JOIN ClubMember cm ON tm.clubMemberID=cm.clubMemberID
    JOIN Person p ON p.personID=cm.personID
    WHERE st.sessionID=:sessionID AND st.teamID=:teamID
";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['sessionID' => $session['sessionID'], 'teamID' => $session['teamID']]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //Get head coach info for team
    $sql = "
    SELECT 
     p.firstName as firstName, p.lastName as lastName, p.email as email
     FROM Person p
     INNER JOIN Personnel pl ON p.personID=pl.personID
     INNER JOIN CoachTeam ct ON pl.personnelID=ct.personnelID
     WHERE ct.teamID=:teamID
 ";
     $stmt = $pdo->prepare($sql);
     $stmt->execute(['teamID' => $session['teamID']]);
     $coaches = $stmt->fetchAll(PDO::FETCH_ASSOC);

     $coach = [
        'firstName' => 'None',
        'lastName' => ' ',
        'email' => 'support@ysm.ca',
    ];
     if (!empty($coaches)) {
        $coach = $coach[0];
     }

     //Send email
     foreach ($members as $member) {
        $to = $member['email']; // Assuming the email is available in the club member data
        $body = "Dear {$member['firstName']} {$member['lastName']},\n\n"
              . "You are scheduled for the {$session['type']} on {$session['date']} at {$session['time']}. "
              . "The session will be held at {$session['address']}.\n\n"
              . "Head Coach: {$coach['firstName']} {$coach['lastName']} ({$coach['email']})\n\n"
              . "Your role in the game: {$member['position']}\n\n"
              . "Best regards,\nYour Team";

        // Send the email
        $headers = "From: no-reply@example.com\r\n";
        $headers .= "Cc: robatto.jeanmarie@gmail.com\r\n";
        mail($to, $subject, $body, $headers);

        // Log the email
        $logStmt->execute([
            'sessionID' => $session['sessionID'],
            'date' => date('Y-m-d'),
            'sender' => $session['address'], // Assuming sender is the location address
            'receiver' => $to,
            'subject' => $subject,
            'bodyHead' => substr($body, 0, 100)
        ]);
     }
}

echo "Emails sent and logged successfully.";
?>
