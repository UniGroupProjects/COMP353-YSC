-- table creation
-- ------------------------------------------------------
CREATE DATABASE IF NOT EXISTS groupProject1;
USE groupProject1;

-- Dropping tables in the correct order to respect foreign key dependencies
DROP TABLE IF EXISTS EmailLogs;
DROP TABLE IF EXISTS SessionTeams;
DROP TABLE IF EXISTS CoachTeam;
DROP TABLE IF EXISTS TeamMember;
DROP TABLE IF EXISTS Sponsor;
DROP TABLE IF EXISTS FamilyMemberLocation;
DROP TABLE IF EXISTS ClubMemberLocation;
DROP TABLE IF EXISTS ManagerLocation;
DROP TABLE IF EXISTS PersonnelLocation;
DROP TABLE IF EXISTS Session;
DROP TABLE IF EXISTS Team;
DROP TABLE IF EXISTS Location;
DROP TABLE IF EXISTS EmergencyContact;
DROP TABLE IF EXISTS FamilyMember;
DROP TABLE IF EXISTS ClubMember;
DROP TABLE IF EXISTS Personnel;
DROP TABLE IF EXISTS Person;

-- Continue with the CREATE TABLE commands as previously defined.
CREATE TABLE Person (
 personID INT AUTO_INCREMENT PRIMARY KEY,
 firstName VARCHAR(50) NOT NULL,
 lastName VARCHAR(50) NOT NULL,
 email VARCHAR(100) UNIQUE,
 phone VARCHAR(20),
 gender ENUM('Male', 'Female'),
 SSN CHAR(9) UNIQUE NOT NULL,
 medicareID CHAR(11) UNIQUE,
 address VARCHAR(50),
 city VARCHAR(50),
 province VARCHAR(50),
 postalCode VARCHAR(10),
 dateOfBirth DATE NOT NULL
);

CREATE TABLE Personnel (
 personnelID INT AUTO_INCREMENT PRIMARY KEY,
 personID INT UNIQUE NOT NULL,
 role ENUM('Administrator', 'Trainer', 'Other') NOT NULL,
 mandate ENUM('Volunteer', 'Salary') NOT NULL,
 activationDate DATE NOT NULL DEFAULT (CURRENT_DATE),
 terminationDate DATE,
 FOREIGN KEY (personID) REFERENCES Person(personID) ON DELETE CASCADE
);

CREATE TABLE ClubMember (
 clubMemberID INT AUTO_INCREMENT PRIMARY KEY,
 personID INT UNIQUE NOT NULL,
 activationDate DATE NOT NULL DEFAULT (CURRENT_DATE),
 terminationDate DATE,
 FOREIGN KEY (personID) REFERENCES Person(personID) ON DELETE CASCADE
);
-- COULD WE ADD A CHECK TO CLUBMEMBER??  --> CHECK (DATEDIFF(YEAR, dateOfBirth, GETDATE()) BETWEEN 4 AND 10)

-- Sets the delimiter to $$ to allow multiple statements within the trigger creation
DELIMITER $$

-- Creates a new trigger called CheckAgeBeforeInsertClubMember
CREATE TRIGGER CheckAgeBeforeInsertClubMember
BEFORE INSERT ON ClubMember  -- Specifies that the trigger should run before an insert operation on the ClubMember table
FOR EACH ROW  -- Indicates that the trigger should execute for each row being inserted
BEGIN
    DECLARE dob DATE;  -- Declare a variable to hold the date of birth of the person being inserted into ClubMember
    DECLARE age INT;  -- Declare a variable to hold the calculated age

    -- Select the date of birth from the Person table where the personID matches the personID of the new ClubMember row
    SELECT dateOfBirth INTO dob FROM Person WHERE Person.personID = NEW.personID;

    -- Calculate the age by finding the difference in years between the current date and the date of birth
    SET age = TIMESTAMPDIFF(YEAR, dob, CURDATE());

    -- Check if the calculated age is outside the acceptable range (less than 4 or greater than 10)
    IF age < 4 OR age > 10 THEN
        -- If the age is out of range, prevent the insert and raise an error message
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Age must be between 4 and 10 years';
    END IF;
END$$

-- Resets the delimiter back to the default semicolon for regular SQL commands
DELIMITER ;


CREATE TABLE FamilyMember (
 familyMemberID INT AUTO_INCREMENT PRIMARY KEY,
 personID INT UNIQUE NOT NULL,
 FOREIGN KEY (personID) REFERENCES Person(personID) ON DELETE CASCADE
);

CREATE TABLE EmergencyContact (
    familyMemberID INT UNIQUE NOT NULL,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    relType ENUM('Father', 'Mother', 'GrandFather', 'GrandMother', 'Tutor', 'Partner',
'Friend', 'Other') NOT NULL,
    phone VARCHAR(20) NOT NULL,
    PRIMARY KEY (familyMemberID, phone),
    FOREIGN KEY (familyMemberID) REFERENCES FamilyMember(familyMemberID) ON DELETE CASCADE
);

CREATE TABLE Location (
 locationID INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 address VARCHAR(50) NOT NULL,
 city VARCHAR(50) NOT NULL,
 province VARCHAR(50) NOT NULL,
 postalCode VARCHAR(10) NOT NULL,
 phone VARCHAR(20),
 website VARCHAR(100),
 type ENUM('Head', 'Branch') NOT NULL,
 capacity INT NOT NULL,
 CHECK(capacity >= 0)
);
-- ADD UNIQUE TO ADDRESS? WOULD DOUBT IF ANOTHER LOCATION HAS SAME ADDRESS

-- multiple places could have the same name  ^^
CREATE TABLE Team (
    teamID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    gender ENUM('Male','Female'),
    locationID INT,
    activationDate DATE NOT NULL DEFAULT (CURRENT_DATE),
    terminationDate DATE,
    FOREIGN KEY (locationID) REFERENCES Location(locationID) ON DELETE CASCADE
);

CREATE TABLE Session (
    sessionID INT AUTO_INCREMENT PRIMARY KEY,
    address VARCHAR(100) NOT NULL,
    time TIME NOT NULL,
    date DATE NOT NULL DEFAULT (CURRENT_DATE),
    type ENUM('Practice','Game')
);

CREATE TABLE EmailLogs (
    sessionID INT,
    date DATE NOT NULL DEFAULT(CURRENT_DATE),
    sender VARCHAR(50),
    receiver VARCHAR(50),
    subject VARCHAR(100),
    bodyHead TEXT,
    PRIMARY KEY (sessionID, receiver),
    FOREIGN KEY (sessionID) REFERENCES Session(sessionID) ON DELETE CASCADE
);
-- DEFAULTS TO SOME TEAMID? LOCATIONID? RECEIVER DEFAULT TO SOME CLUBMEMBERID?? IDK THE SEMANTICS OF HOW WERE DOING THE AUTOMATIC EMAIL SENDING

CREATE TABLE PersonnelLocation (
 personnelID INT NOT NULL,
 locationID INT NOT NULL,
 activationDate DATE NOT NULL DEFAULT (CURRENT_DATE),
 terminationDate DATE,
 PRIMARY KEY (personnelID, activationDate),
 FOREIGN KEY (personnelID) REFERENCES Personnel(personnelID) ON DELETE CASCADE,
 FOREIGN KEY (locationID) REFERENCES Location(locationID) ON DELETE CASCADE
);

CREATE TABLE ManagerLocation (
 personnelID INT NOT NULL,
 locationID INT NOT NULL,
 activationDate DATE NOT NULL DEFAULT (CURRENT_DATE),
 terminationDate DATE,
 PRIMARY KEY (personnelID, activationDate),
 FOREIGN KEY (personnelID) REFERENCES Personnel(personnelID) ON DELETE CASCADE,
 FOREIGN KEY (locationID) REFERENCES Location(locationID) ON DELETE CASCADE
);

CREATE TABLE ClubMemberLocation (
 clubMemberID INT NOT NULL,
 locationID INT NOT NULL,
 activationDate DATE NOT NULL DEFAULT (CURRENT_DATE),
 terminationDate DATE,
 PRIMARY KEY (clubMemberID, activationDate),
 FOREIGN KEY (clubMemberID) REFERENCES ClubMember(clubMemberID) ON DELETE CASCADE,
 FOREIGN KEY (locationID) REFERENCES Location(locationID) ON DELETE CASCADE
);

CREATE TABLE FamilyMemberLocation (
 familyMemberID INT NOT NULL,
 locationID INT NOT NULL,
 activationDate DATE NOT NULL DEFAULT (CURRENT_DATE),
 terminationDate DATE,
 PRIMARY KEY (familyMemberID, activationDate),
 FOREIGN KEY (familyMemberID) REFERENCES FamilyMember(familyMemberID) ON DELETE CASCADE,
 FOREIGN KEY (locationID) REFERENCES Location(locationID) ON DELETE CASCADE
);

CREATE TABLE Sponsor (
 clubMemberID INT NOT NULL,
 familyMemberID INT NOT NULL,
 relType ENUM('Father', 'Mother', 'GrandFather', 'GrandMother', 'Tutor', 'Partner',
'Friend', 'Other') NOT NULL,
 activationDate DATE NOT NULL DEFAULT (CURRENT_DATE),
 terminationDate DATE,
 PRIMARY KEY (clubMemberID, activationDate),
 FOREIGN KEY (clubMemberID) REFERENCES ClubMember(clubMemberID) ON DELETE CASCADE,
 FOREIGN KEY (familyMemberID) REFERENCES FamilyMember(familyMemberID) ON DELETE CASCADE
);

CREATE TABLE TeamMember (
    clubMemberID INT,
    teamID INT,
    position ENUM('Forward','Midfielder','Defender','Goalkeeper') NOT NULL,
    PRIMARY KEY (clubMemberID, teamID),
    FOREIGN KEY (clubMemberID) REFERENCES ClubMember(clubMemberID ) ON DELETE CASCADE,
    FOREIGN KEY (teamID) REFERENCES Team(teamID) ON DELETE CASCADE
);
DELIMITER $$

CREATE TRIGGER CheckTeamMemberAssignmentBeforeInsert
BEFORE INSERT ON TeamMember
FOR EACH ROW
BEGIN
    DECLARE conflict_count INT;  -- Variable to hold the count of conflicting sessions.

    -- Perform a check to find if there are any session conflicts.
    -- This query counts all existing sessions that conflict in time (less than three hours apart) for the new team member.
    SELECT COUNT(*) INTO conflict_count
    FROM SessionTeams ST
    JOIN Session S1 ON ST.sessionID = S1.sessionID  -- Join SessionTeams to Session to access session details.
    WHERE ST.teamID IN (
        -- Select all team IDs where the new team member is already assigned,
        -- ensuring we check against all teams the member is part of.
        SELECT teamID 
        FROM TeamMember 
        WHERE clubMemberID = NEW.clubMemberID
    )
    AND EXISTS (
        -- Check for any session in the new team that overlaps with other sessions the member is involved in.
        SELECT 1
        FROM Session S2
        WHERE S2.sessionID IN (SELECT sessionID FROM SessionTeams WHERE teamID = NEW.teamID) -- Sessions of the new team.
        AND S2.date = S1.date  -- Only consider sessions on the same day.
        AND ABS(TIMESTAMPDIFF(MINUTE, S1.time, S2.time)) < 180  -- Check if sessions are less than three hours apart.
    );

    -- If any conflict is found, prevent the insertion and raise an error.
    IF conflict_count > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: Assignments must have at least three hours difference.';
    END IF;
END$$

DELIMITER ;




CREATE TABLE CoachTeam (
    personnelID INT,
    activationDate DATE NOT NULL DEFAULT(CURRENT_DATE),
    teamID INT,
    terminationDate DATE,
    PRIMARY KEY (teamID, activationDate),
    FOREIGN KEY (personnelID) REFERENCES Personnel(personnelID) ON DELETE CASCADE,
    FOREIGN KEY (teamID) REFERENCES Team(teamID) ON DELETE CASCADE
);

CREATE TABLE SessionTeams (
    sessionID INT,
    teamID INT,
    score INT,
    PRIMARY KEY (sessionID, teamID),
    FOREIGN KEY (sessionID) REFERENCES Session(sessionID) ON DELETE CASCADE,
    FOREIGN KEY (teamID) REFERENCES Team(teamID) ON DELETE CASCADE
);
-- I LET SCORE BE NULL SINCE THEY SCHEDULE THIS AND PLAY AFTER AND THEN WE UPDATE THE SCORE 

-- ---------------------------------------------------------------
ALTER TABLE TeamMember 
DROP PRIMARY KEY,  -- Removes the existing primary key constraint from the table.
ADD PRIMARY KEY (clubMemberID, teamID, position);  -- Adds a new primary key comprising three columns.
-- By expanding the primary key to include position,
-- it allows a member to hold multiple positions in the same team, each as a separate record.


DELIMITER $$

CREATE TRIGGER EnsureSingleHeadLocation
BEFORE INSERT ON Location
FOR EACH ROW
BEGIN
    IF NEW.type = 'Head' THEN
        IF (SELECT COUNT(*) FROM Location WHERE type = 'Head') >= 1 THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'There can only be one head location.';
        END IF;
    END IF;
END$$

DELIMITER ;
