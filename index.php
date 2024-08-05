<?php
include 'config.php';

// Main CRUD Lists

// Fetch all persons from the database
$stmt = $pdo->query("SELECT * FROM Person");
$persons = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all locations from the database
$stmt = $pdo->query("SELECT * FROM Location");
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all locations from the database
$stmt = $pdo->query("SELECT p.personID, CONCAT(p.firstName, ' ', p.lastName) AS personName, pe.personnelID, pe.role, pe.mandate, pe.activationDate, pe.terminationDate 
                     FROM Personnel pe
                     JOIN Person p ON pe.personID = p.personID");
$personnel = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all club members from the database
$stmt = $pdo->query("SELECT p.personID, p.firstName, p.lastName, cm.clubMemberID, cm.activationDate, cm.terminationDate 
                     FROM ClubMember cm
                     JOIN Person p ON cm.personID = p.personID");
$clubMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all family members from the database
$stmt = $pdo->query("SELECT p.personID, CONCAT(p.firstName, ' ', p.lastName) AS personName, fm.familyMemberID 
                     FROM FamilyMember fm
                     JOIN Person p ON fm.personID = p.personID");
$familyMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all emergency contacts from the database
$stmt = $pdo->query("SELECT ec.familyMemberID, CONCAT(p.firstName, ' ', p.lastName) AS personName, ec.firstName AS contactFirstName, ec.lastName AS contactLastName, ec.relType, ec.phone
                     FROM EmergencyContact ec
                     JOIN FamilyMember fm ON ec.familyMemberID = fm.familyMemberID
                     JOIN Person p ON fm.personID = p.personID");
$emergencyContacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Reports

$stmt = $pdo->query("
    SELECT 
        EC.firstName AS SecondaryFirstName,
        EC.lastName AS SecondaryLastName,
        EC.phone AS SecondaryPhone,
        CM.clubMemberID,
        P.firstName AS ClubMemberFirstName,
        P.lastName AS ClubMemberLastName,
        P.dateOfBirth,
        P.SSN,
        P.medicareID,
        P.phone AS ClubMemberPhone,
        P.address,
        P.city,
        P.province,
        P.postalCode,
        EC.relType AS RelationshipWithSecondary
    FROM 
        FamilyMember FM
    JOIN 
        EmergencyContact EC ON FM.familyMemberID = EC.familyMemberID
    JOIN 
        Person P2 ON FM.personID = P2.personID
    JOIN 
        ClubMember CM ON P2.personID = CM.personID
    JOIN 
        Person P ON CM.personID = P.personID
");
$report8 = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
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
        cm.activationDate >= DATE_SUB(CURRENT_DATE, INTERVAL 2 YEAR)
        AND cm.terminationDate IS NULL
    GROUP BY 
        cm.clubMemberID, p.firstName, p.lastName
    HAVING 
        COUNT(DISTINCT cml.locationID) >= 4
    ORDER BY 
        cm.clubMemberID ASC
");
$report10 = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
   SELECT 
        cm.clubMemberID AS MembershipNumber,
        p.firstName,
        p.lastName,
        TIMESTAMPDIFF(YEAR, p.dateOfBirth, CURDATE()) AS Age,
        p.phone,
        p.email,
        l.name AS LocationName
    FROM 
        ClubMember cm
    JOIN Person p ON cm.personID = p.personID
    LEFT JOIN ClubMemberLocation cml ON cm.clubMemberID = cml.clubMemberID
    LEFT JOIN Location l ON cml.locationID = l.locationID
    LEFT JOIN TeamMember tm ON cm.clubMemberID = tm.clubMemberID
    WHERE 
        cm.terminationDate IS NULL
        AND tm.clubMemberID IS NULL
    GROUP BY 
        cm.clubMemberID, p.firstName, p.lastName, p.phone, p.email, l.name
    ORDER BY 
        l.name ASC, cm.clubMemberID ASC;
");
$report12 = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
   SELECT
        CM.clubMemberID AS MembershipNumber,
        P.firstName,
        P.lastName,
        TIMESTAMPDIFF(YEAR, P.dateOfBirth, CURDATE()) AS Age,
        P.phone,
        P.email,
        L.name AS LocationName
    FROM
        ClubMember CM
    JOIN
        Person P ON CM.personID = P.personID
    JOIN
        TeamMember TM ON CM.clubMemberID = TM.clubMemberID
    JOIN
        Team T ON TM.teamID = T.teamID
    JOIN
        SessionTeams ST ON T.teamID = ST.teamID
    JOIN
        Session S ON ST.sessionID = S.sessionID AND S.type = 'Game'
    JOIN
        Location L ON T.locationID = L.locationID
    WHERE
        CM.terminationDate IS NULL
    GROUP BY
        CM.clubMemberID, P.firstName, P.lastName, P.phone, P.email, L.name
    HAVING
        COUNT(DISTINCT TM.position) = 4
    ORDER BY
        L.name ASC, CM.clubMemberID ASC;
");
$report14 = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
 SELECT
        CM.clubMemberID AS MembershipNumber,
        P.firstName,
        P.lastName,
        TIMESTAMPDIFF(YEAR, P.dateOfBirth, CURDATE()) AS Age,
        P.phone,
        P.email,
        L.name AS LocationName
    FROM
        ClubMember CM
    JOIN
        Person P ON CM.personID = P.personID
    JOIN
        TeamMember TM ON CM.clubMemberID = TM.clubMemberID
    JOIN
        Team T ON TM.teamID = T.teamID
    JOIN
        SessionTeams ST ON T.teamID = ST.teamID
    JOIN
        Session S ON ST.sessionID = S.sessionID AND S.type = 'Game'
    JOIN
        Location L ON T.locationID = L.locationID
    WHERE
        CM.terminationDate IS NULL
        AND NOT EXISTS (
            SELECT 1
            FROM SessionTeams ST2
            JOIN Session S2 ON ST2.sessionID = S2.sessionID AND S2.type = 'Game'
            WHERE ST2.teamID != ST.teamID AND ST2.sessionID = ST.sessionID
            AND (ST2.score > ST.score OR ST.score IS NULL OR ST2.score IS NULL)
        )
    GROUP BY
        CM.clubMemberID, P.firstName, P.lastName, P.phone, P.email, L.name
    HAVING
        COUNT(DISTINCT S.sessionID) > 0
    ORDER BY
        L.name ASC, CM.clubMemberID ASC;
");
$report16 = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Youth Soccer Club - Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1,
        h2 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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
            max-width: 1200px;
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

        .collapsible {
            background-color: #f1f1f1;
            color: #444;
            cursor: pointer;
            padding: 10px;
            width: 100%;
            border: none;
            text-align: left;
            outline: none;
            font-size: 16px;
            border-radius: 4px;
        }

        .active,
        .collapsible:hover {
            background-color: #ddd;
        }

        .content {
            padding: 0 18px;
            display: none;
            overflow: hidden;
            background-color: #f9f9f9;
            overflow: scroll;
        }

        #report-form {
            padding: 20px;
            border-radius: 5px;
            margin: 20px;
            max-width: 600px;
        }

        #report-form h2 {
            color: #333;
            margin-bottom: 20px;
        }

        #report-form form {
            display: flex;
            flex-direction: column;
        }

        #report-form label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        #report-form input[type="date"],
        #report-form input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        #report-form button {
            padding: 10px 20px;
            background-color: #80AD4E;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        #report-form button:hover {
            background-color: #5D7D39;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var coll = document.getElementsByClassName("collapsible");
            for (var i = 0; i < coll.length; i++) {
                coll[i].addEventListener("click", function () {
                    this.classList.toggle("active");
                    var content = this.nextElementSibling;
                    if (content.style.display === "block") {
                        content.style.display = "none";
                    } else {
                        content.style.display = "block";
                    }
                });
            }
        });
    </script>
</head>

<body>
    <div class="container">
        <h1>Youth Soccer Club - Admin Dashboard</h1>
        <hr />

        <h2>Manage</h2>
        <button type="button" class="collapsible">Person List</button>
        <div class="content">
            <a href="create_person.php" class="button">Add New Person</a>
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
                                    <a href="edit_person.php?id=<?php echo $person['personID']; ?>">Edit</a>
                                    <a href="delete_person.php?id=<?php echo $person['personID']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this person and all related data?');">Delete</a>
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

        <button type="button" class="collapsible">Location List</button>
        <div class="content">
            <a href="create_location.php" class="button">Add New Location</a>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>Province</th>
                        <th>Postal Code</th>
                        <th>Phone</th>
                        <th>Website</th>
                        <th>Type</th>
                        <th>Capacity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($locations): ?>
                        <?php foreach ($locations as $location): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($location['name']); ?></td>
                                <td><?php echo htmlspecialchars($location['address']); ?></td>
                                <td><?php echo htmlspecialchars($location['city']); ?></td>
                                <td><?php echo htmlspecialchars($location['province']); ?></td>
                                <td><?php echo htmlspecialchars($location['postalCode']); ?></td>
                                <td><?php echo htmlspecialchars($location['phone']); ?></td>
                                <td><?php echo htmlspecialchars($location['website']); ?></td>
                                <td><?php echo htmlspecialchars($location['type']); ?></td>
                                <td><?php echo htmlspecialchars($location['capacity']); ?></td>
                                <td class="actions">
                                    <a href="edit_location.php?id=<?php echo $location['locationID']; ?>">Edit</a>
                                    <a href="delete_location.php?id=<?php echo $location['locationID']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this location?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button type="button" class="collapsible">Personnel List</button>
        <div class="content">
            <a href="create_personnel.php" class="button">Add New Personnel</a>
            <table>
                <thead>
                    <tr>
                        <th>Person</th>
                        <th>Role</th>
                        <th>Mandate</th>
                        <th>Activation Date</th>
                        <th>Termination Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($personnel): ?>
                        <?php foreach ($personnel as $person): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($person['personName']); ?></td>
                                <td><?php echo htmlspecialchars($person['role']); ?></td>
                                <td><?php echo htmlspecialchars($person['mandate']); ?></td>
                                <td><?php echo htmlspecialchars($person['activationDate']); ?></td>
                                <td><?php echo htmlspecialchars($person['terminationDate'] ? $person['terminationDate'] : 'N/A'); ?>
                                </td>
                                <td class="actions">
                                    <?php if (is_null($person['terminationDate'])): ?>
                                        <a
                                            href="edit_personnel.php?id=<?php echo htmlspecialchars($person['personnelID']); ?>">Edit</a>
                                        <a href="delete_personnel.php?id=<?php echo htmlspecialchars($person['personnelID']); ?>"
                                            onclick="return confirm('Are you sure you want to delete this personnel?');">Deactivate</a>
                                    <?php else: ?>
                                        <a href="reactivate_personnel.php?id=<?php echo htmlspecialchars($person['personnelID']); ?>"
                                            onclick="return confirm('Are you sure you want to reactivate this personnel?');">Reactivate</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button type="button" class="collapsible">Club Member List</button>
        <div class="content">
            <a href="create_club_member.php" class="button">Add New Club Member</a>
            <table>
                <thead>
                    <tr>
                        <th>Person</th>
                        <th>Activation Date</th>
                        <th>Termination Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($clubMembers): ?>
                        <?php foreach ($clubMembers as $clubMember): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($clubMember['firstName'] . ' ' . $clubMember['lastName']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($clubMember['activationDate']); ?></td>
                                <td><?php echo htmlspecialchars($clubMember['terminationDate'] ? $clubMember['terminationDate'] : 'N/A'); ?>
                                </td>
                                <td class="actions">
                                    <?php if (is_null($clubMember['terminationDate'])): ?>
                                        <a href="edit_club_member.php?id=<?php echo $clubMember['clubMemberID']; ?>">Edit</a>
                                        <a href="delete_club_member.php?id=<?php echo $clubMember['clubMemberID']; ?>"
                                            onclick="return confirm('Are you sure you want to deactivate this club member?');">Deactivate</a>
                                    <?php else: ?>
                                        <a href="reactivate_club_member.php?id=<?php echo htmlspecialchars($clubMember['clubMemberID']); ?>"
                                            onclick="return confirm('Are you sure you want to reactivate this club member?');">Reactivate</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button type="button" class="collapsible">Family Member List</button>
        <div class="content">
            <a href="create_primary_family_member.php" class="button">Add New Family Member</a>
            <table>
                <thead>
                    <tr>
                        <th>Person</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($familyMembers): ?>
                        <?php foreach ($familyMembers as $familyMember): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($familyMember['personName']); ?></td>
                                <td class="actions">
                                    <a
                                        href="edit_primary_family_member.php?id=<?php echo $familyMember['familyMemberID']; ?>">Edit</a>
                                    <a href="delete_primary_family_member.php?id=<?php echo $familyMember['familyMemberID']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this family member?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button type="button" class="collapsible">Emergency Contact List</button>
        <div class="content">
            <a href="create_secondary_family_member.php" class="button">Add New Emergency Contact</a>
            <table>
                <thead>
                    <tr>
                        <th>Person</th>
                        <th>Contact First Name</th>
                        <th>Contact Last Name</th>
                        <th>Relation</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($emergencyContacts): ?>
                        <?php foreach ($emergencyContacts as $contact): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($contact['personName']); ?></td>
                                <td><?php echo htmlspecialchars($contact['contactFirstName']); ?></td>
                                <td><?php echo htmlspecialchars($contact['contactLastName']); ?></td>
                                <td><?php echo htmlspecialchars($contact['relType']); ?></td>
                                <td><?php echo htmlspecialchars($contact['phone']); ?></td>
                                <td class="actions">
                                    <a
                                        href="edit_secondary_family_member.php?id=<?php echo $contact['familyMemberID']; ?>">Edit</a>
                                    <a href="delete_secondary_family_member.php?id=<?php echo $contact['familyMemberID']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this emergency contact?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>



        <h2>Report</h2>
        <button type="button" class="collapsible">Family Member Details (8)</button>
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>Secondary First Name</th>
                        <th>Secondary Last Name</th>
                        <th>Secondary Phone</th>
                        <th>Club Member ID</th>
                        <th>Club Member First Name</th>
                        <th>Club Member Last Name</th>
                        <th>Date of Birth</th>
                        <th>SSN</th>
                        <th>Medicare ID</th>
                        <th>Club Member Phone</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>Province</th>
                        <th>Postal Code</th>
                        <th>Relationship With Secondary</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($report8): ?>
                        <?php foreach ($report8 as $detail): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($detail['SecondaryFirstName']); ?></td>
                                <td><?php echo htmlspecialchars($detail['SecondaryLastName']); ?></td>
                                <td><?php echo htmlspecialchars($detail['SecondaryPhone']); ?></td>
                                <td><?php echo htmlspecialchars($detail['clubMemberID']); ?></td>
                                <td><?php echo htmlspecialchars($detail['ClubMemberFirstName']); ?></td>
                                <td><?php echo htmlspecialchars($detail['ClubMemberLastName']); ?></td>
                                <td><?php echo htmlspecialchars($detail['dateOfBirth']); ?></td>
                                <td><?php echo htmlspecialchars($detail['SSN']); ?></td>
                                <td><?php echo htmlspecialchars($detail['medicareID']); ?></td>
                                <td><?php echo htmlspecialchars($detail['ClubMemberPhone']); ?></td>
                                <td><?php echo htmlspecialchars($detail['address']); ?></td>
                                <td><?php echo htmlspecialchars($detail['city']); ?></td>
                                <td><?php echo htmlspecialchars($detail['province']); ?></td>
                                <td><?php echo htmlspecialchars($detail['postalCode']); ?></td>
                                <td><?php echo htmlspecialchars($detail['RelationshipWithSecondary']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="15">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button type="button" class="collapsible">Active Club Members with Multi-Location Association (10)</button>
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>Club Member ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($report10): ?>
                        <?php foreach ($report10 as $formation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($formation['clubMemberID']); ?></td>
                                <td><?php echo htmlspecialchars($formation['firstName']); ?></td>
                                <td><?php echo htmlspecialchars($formation['lastName']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button type="button" class="collapsible">Unassigned Active Club Members (12)</button>
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>Membership Number</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Age</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Location Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($report12): ?>
                        <?php foreach ($report12 as $member): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['MembershipNumber']); ?></td>
                                <td><?php echo htmlspecialchars($member['firstName']); ?></td>
                                <td><?php echo htmlspecialchars($member['lastName']); ?></td>
                                <td><?php echo htmlspecialchars($member['Age']); ?></td>
                                <td><?php echo htmlspecialchars($member['phone']); ?></td>
                                <td><?php echo htmlspecialchars($member['email']); ?></td>
                                <td><?php echo htmlspecialchars($member['LocationName']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button type="button" class="collapsible">Active Club Members with All Four Positions (14)</button>
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>Membership Number</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Age</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Location Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($report14): ?>
                        <?php foreach ($report14 as $member): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['MembershipNumber']); ?></td>
                                <td><?php echo htmlspecialchars($member['firstName']); ?></td>
                                <td><?php echo htmlspecialchars($member['lastName']); ?></td>
                                <td><?php echo htmlspecialchars($member['Age']); ?></td>
                                <td><?php echo htmlspecialchars($member['phone']); ?></td>
                                <td><?php echo htmlspecialchars($member['email']); ?></td>
                                <td><?php echo htmlspecialchars($member['LocationName']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button type="button" class="collapsible">Active Club Members with No Lost Games (16)</button>
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>Membership Number</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Age</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Location Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($report16): ?>
                        <?php foreach ($report16 as $member): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['MembershipNumber']); ?></td>
                                <td><?php echo htmlspecialchars($member['firstName']); ?></td>
                                <td><?php echo htmlspecialchars($member['lastName']); ?></td>
                                <td><?php echo htmlspecialchars($member['Age']); ?></td>
                                <td><?php echo htmlspecialchars($member['phone']); ?></td>
                                <td><?php echo htmlspecialchars($member['email']); ?></td>
                                <td><?php echo htmlspecialchars($member['LocationName']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>


</body>

</html>