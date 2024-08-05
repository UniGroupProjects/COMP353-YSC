-- data for the queries 
-- ------------------------------------------------

-- Query 2 data 
-- creates person for the data
INSERT INTO Person (firstName, lastName, email, phone, gender, SSN, medicareID, address, city, province, postalCode, dateOfBirth)
VALUES 
('John', 'Doe', 'johndoe@example.com', '123-456-7890', 'Male', '123456789', 'AB1234567', '1234 Main St', 'Montreal', 'QC', 'H3Z2Y7', '1980-01-01'),
('Alice', 'Smith', 'alicesmith@example.com', '234-567-8901', 'Female', '987654321', 'CD9876543', '2345 Side St', 'Toronto', 'ON', 'M5R1K8', '1992-05-15'),
('Bob', 'Johnson', 'bobjohnson@example.com', '345-678-9012', 'Male', '876543219', 'EF8765432', '3456 Avenue Rd', 'Vancouver', 'BC', 'V5K0A1', '1988-08-30'),
('Eve', 'Taylor', 'evetaylor@example.com', '456-789-0123', 'Female', '765432198', 'GH7654321', '5678 Circle Dr', 'Calgary', 'AB', 'T2P4H5', '1990-06-06'),
('Charlie', 'Brown', 'charliebrown@example.com', '567-890-1234', 'Male', '654321987', 'IJ6543210', '6789 Square Pl', 'Ottawa', 'ON', 'K1A0B1', '1985-12-17');



-- Inserting corresponding entries into the Personnel table using last inserted IDs

-- Inserting into Personnel by selecting personID based on email
INSERT INTO Personnel (personID, role, mandate)
SELECT personID, 'Trainer', 'Salary'
FROM Person
WHERE email = 'johndoe@example.com'
UNION ALL
SELECT personID, 'Other', 'Salary'
FROM Person
WHERE email = 'alicesmith@example.com'
UNION ALL
SELECT personID, 'Other', 'Volunteer'
FROM Person
WHERE email = 'bobjohnson@example.com'
UNION ALL
SELECT personID, 'Administrator', 'Volunteer'
FROM Person
WHERE email = 'evetaylor@example.com'
UNION ALL
SELECT personID, 'Trainer', 'Salary'
FROM Person
WHERE email = 'charliebrown@example.com';



-- -------------------------------------------------------
-- Query 4 data
-- creates person for the data


INSERT INTO Person (firstName, lastName, email, phone, gender, SSN, medicareID, address, city, province, postalCode, dateOfBirth)
VALUES 
('Nora', 'Fisher', 'norafisher@example.com', '800-123-4567', 'Female', '221654987', 'OP2216549', '789 Maple St', 'Quebec City', 'QC', 'G1K8M5', '2018-07-07'),
('Miles', 'Bennett', 'milesbennett@example.com', '801-234-5678', 'Male', '332765198', 'QR3327651', '890 Ash St', 'Winnipeg', 'MB', 'R3P2N2', '2017-12-15'),
('Zoe', 'Carter', 'zoecarter@example.com', '802-345-6789', 'Female', '443876309', 'ST4438763', '901 Oak St', 'Saskatoon', 'SK', 'S7K1W2', '2016-06-20'),
('Leo', 'Grant', 'leogrant@example.com', '803-456-7890', 'Male', '554987410', 'UV5549874', '123 Elm St', 'Vancouver', 'BC', 'V5K0B2', '2014-04-11'),
('Ivy', 'Wallace', 'ivywallace@example.com', '804-567-8901', 'Female', '665098521', 'WX6650985', '234 Birch St', 'Halifax', 'NS', 'B3K5M4', '2015-09-26');

-- ------------------------------------------------------------
-- Inserting corresponding entries into the ClubMember table using email to find personID
INSERT INTO ClubMember (personID, activationDate)
SELECT personID, CURDATE()
FROM Person
WHERE email IN ('norafisher@example.com', 'milesbennett@example.com', 'zoecarter@example.com', 'leogrant@example.com', 'ivywallace@example.com');

-- --------------------------------------------------------------

-- Query 6 data

-- -----------------------------------------------------------------
-- creating data for query 65
-- creates location for data 

INSERT INTO Location (locationID, name, address, city, province, postalCode, phone, website, type, capacity)
VALUES 
(1, 'Main HQ', '1234 Main St', 'Metro City', 'MetroState', 'M12345', '123-456-7890', 'http://mainhq.example.com', 'Head', 100),
(2, 'North Branch', '2345 North Rd', 'Northville', 'NorthState', 'N23456', '234-567-8901', 'http://northbranch.example.com', 'Branch', 50),
(3, 'East Branch', '3456 East Ave', 'Eastburg', 'EastState', 'E34567', '345-678-9012', 'http://eastbranch.example.com', 'Branch', 75),
(4, 'South Branch', '4567 South Blvd', 'Southtown', 'SouthState', 'S45678', '456-789-0123', 'http://southbranch.example.com', 'Branch', 60),
(5, 'West Branch', '5678 West Ln', 'Westville', 'WestState', 'W56789', '567-890-1234', 'http://westbranch.example.com', 'Branch', 80);


INSERT INTO Team (teamID, name, gender, locationID, activationDate)
VALUES 
(10, 'Alpha Team', 'Male', 1, CURDATE()),
(11, 'Bravo Team', 'Female', 2, CURDATE()),
(12, 'Charlie Team', 'Male', 3, CURDATE()),
(13, 'Delta Team', 'Female', 4, CURDATE()),
(14, 'Echo Team', 'Male', 5, CURDATE());


-- creates teamMember for data 

INSERT INTO TeamMember (clubMemberID, teamID, position)
VALUES 
(1, 10, 'Forward'),     
(2, 11, 'Goalkeeper'),  
(3, 12, 'Defender'),    
(4, 13, 'Midfielder'),  
(5, 14, 'Forward');     

-- creates the sessions 
INSERT INTO Session (address, time, date, type)
VALUES
('123 Sport Ave', '14:00', '2024-08-01', 'Game'),
('456 Game Rd', '14:00', '2024-08-01', 'Game'),
('789 Match St', '15:00', '2024-08-02', 'Practice');

-- creates session teams

INSERT INTO SessionTeams (sessionID, teamID, score)
VALUES 
(1, 10, NULL),  -- Team 10 participates in session 1 with no score recorded yet
(1, 11, NULL),  -- Team 11 also participates in the same session
(2, 12, NULL),  -- Team 12 participates in session 2
(2, 13, NULL),  -- Team 13 also participates in session 2
(3, 14, NULL); -- Team 14 participates in session 3 alone
-- ---------------------------------------------------------------
-- ----------------------------------------------------------------
-- Q8 data

INSERT INTO Person (firstName, lastName, email, phone, gender, SSN, medicareID, address, city, province, postalCode, dateOfBirth)
VALUES 
('John', 'Doe', 'lol@example.com', '123-456-7890', 'Male', '112233445', 'AB1333567', '1234 Main St', 'Metro City', 'MetroState', 'M12345', '2019-01-01'),
('Jane', 'Doe', 'bye@example.com', '234-567-8901', 'Female', '556677889', 'CD9999543', '2345 Side St', 'Northville', 'NorthState', 'N23456', '2019-02-15'),
('Alice', 'Smith', 'alice@example.com', '555-0101', 'Female', '223344556', 'XY2233445', '4567 Apple St', 'Laketown', 'LakeState', 'L45678', '2018-03-14'),
('Bob', 'Johnson', 'bob@example.com', '555-0102', 'Male', '334455667', 'XY3344556', '7890 Orange Ave', 'Hilltown', 'HillState', 'H56789', '1990-07-22'),
('Carol', 'Taylor', 'carol@example.com', '555-0103', 'Female', '445566778', 'XY4455667', '123 Pine Rd', 'Forest City', 'ForestState', 'F12345', '2017-11-30'),
('Dave', 'Wilson', 'dave@example.com', '555-0104', 'Male', '996677889', 'XY5566778', '456 Maple Ln', 'River City', 'RiverState', 'R23456', '1992-02-17'),
('Eva', 'Brown', 'eva@example.com', '555-0105', 'Female', '667788990', 'XY6677889', '789 Birch Blvd', 'Mountain Town', 'MountainState', 'M34567', '2017-08-08'),
('Frank', 'Hill', 'frankhill@example.com', '555-0202', 'Male', '778899001', 'XY7788990', '890 Winter St', 'Snowville', 'SnowState', 'S67890', '2016-12-12');
-- turning persons into family members

INSERT INTO FamilyMember (personID)
VALUES 
((SELECT personID FROM Person WHERE email = 'lol@example.com')),
((SELECT personID FROM Person WHERE email = 'bye@example.com')),
((SELECT personID FROM Person WHERE email = 'alice@example.com')),
((SELECT personID FROM Person WHERE email = 'bob@example.com')),
((SELECT personID FROM Person WHERE email = 'carol@example.com')),
((SELECT personID FROM Person WHERE email = 'dave@example.com')),
((SELECT personID FROM Person WHERE email = 'eva@example.com')),
((SELECT personID FROM Person WHERE email = 'frankhill@example.com'));


-- turning them into emergency contacts

-- Assuming Jane Doe is the emergency contact for John Doe
INSERT INTO EmergencyContact (familyMemberID, firstName, lastName, relType, phone)
VALUES 
((SELECT familyMemberID FROM FamilyMember WHERE personID = (SELECT personID FROM Person WHERE email = 'lol@example.com')), 'Jane', 'Doe', 'Wife', '234-567-8901'),
((SELECT familyMemberID FROM FamilyMember WHERE personID = (SELECT personID FROM Person WHERE email = 'alice@example.com')), 'Bob', 'Johnson', 'Friend', '555-0102'),
((SELECT familyMemberID FROM FamilyMember WHERE personID = (SELECT personID FROM Person WHERE email = 'bob@example.com')), 'Carol', 'Taylor', 'Friend', '555-0103'),
((SELECT familyMemberID FROM FamilyMember WHERE personID = (SELECT personID FROM Person WHERE email = 'carol@example.com')), 'Dave', 'Wilson', 'Brother', '555-0104'),
((SELECT familyMemberID FROM FamilyMember WHERE personID = (SELECT personID FROM Person WHERE email = 'dave@example.com')), 'Eva', 'Brown', 'Sister', '555-0105'),
((SELECT familyMemberID FROM FamilyMember WHERE personID = (SELECT personID FROM Person WHERE email = 'eva@example.com')), 'Alice', 'Smith', 'Cousin', '555-0101'),
((SELECT familyMemberID FROM FamilyMember WHERE personID = (SELECT personID FROM Person WHERE email = 'frankhill@example.com')), 'Eva', 'Brown', 'Friend', '555-0105');


-- adding john doe as a club member 

-- Assuming John Doe becomes a club member
INSERT INTO ClubMember (personID)
VALUES 
((SELECT personID FROM Person WHERE email = 'lol@example.com')),
((SELECT personID FROM Person WHERE email = 'alice@example.com')),
((SELECT personID FROM Person WHERE email = 'carol@example.com')),
((SELECT personID FROM Person WHERE email = 'eva@example.com')),
((SELECT personID FROM Person WHERE email = 'frankhill@example.com'));

-- ----------------------------------------------------------------
-- ----------------------------------------------------------------
-- Q 10 data

INSERT INTO Person (firstName, lastName, email, phone, gender, SSN, medicareID, address, city, province, postalCode, dateOfBirth)
VALUES
('Alice', 'Smith', 'alice.smith@example.com', '1222567890', 'Female', '123333789', '98765432110', '1234 Elm St', 'Metro', 'Ontario', 'A1A1A1', '2019-01-01'),
('Bob', 'Johnson', 'bob.johnson@example.com', '2333678901', 'Male', '234567890', '87654321100', '5678 Pine St', 'Capital', 'British Columbia', 'B1B1B1', '2019-02-02'),
('Charlie', 'Brown', 'charlie.brown@example.com', '3444567890', 'Male', '345678901', '76543211100', '2345 Maple St', 'Metro', 'Ontario', 'A5A5A5', '2019-03-03'),
('Diana', 'Prince', 'diana.prince@example.com', '4555678901', 'Female', '456789012', '65432112200', '7890 Oak St', 'Capital', 'British Columbia', 'B2B2B2', '2019-04-04'),
('Edward', 'Norton', 'edward.norton@example.com', '5666789012', 'Male', '567890123', '54321013300', '4321 Birch St', 'Metro', 'Ontario', 'A6A6A6', '2019-05-05');




-- Insert into ClubMember using direct SELECT from Person
INSERT INTO ClubMember (personID, activationDate)
SELECT personID, CURDATE() FROM Person WHERE email IN ('alice.smith@example.com', 'bob.johnson@example.com', 'charlie.brown@example.com', 'diana.prince@example.com', 'edward.norton@example.com');



-- Alice associated with four locations
INSERT INTO ClubMemberLocation (clubMemberID, locationID, activationDate)
SELECT cm.clubMemberID, 1, DATE_SUB(CURDATE(), INTERVAL 1 YEAR) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'alice.smith@example.com'
UNION ALL
SELECT cm.clubMemberID, 2, DATE_SUB(CURDATE(), INTERVAL 6 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'alice.smith@example.com'
UNION ALL
SELECT cm.clubMemberID, 3, DATE_SUB(CURDATE(), INTERVAL 3 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'alice.smith@example.com'
UNION ALL
SELECT cm.clubMemberID, 4, DATE_SUB(CURDATE(), INTERVAL 1 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'alice.smith@example.com';


-- Bob associated with four locations
INSERT INTO ClubMemberLocation (clubMemberID, locationID, activationDate)
SELECT cm.clubMemberID, 1, DATE_SUB(CURDATE(), INTERVAL 1 YEAR) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'bob.johnson@example.com'
UNION ALL
SELECT cm.clubMemberID, 2, DATE_SUB(CURDATE(), INTERVAL 8 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'bob.johnson@example.com'
UNION ALL
SELECT cm.clubMemberID, 3, DATE_SUB(CURDATE(), INTERVAL 4 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'bob.johnson@example.com'
UNION ALL
SELECT cm.clubMemberID, 5, DATE_SUB(CURDATE(), INTERVAL 2 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'bob.johnson@example.com';

-- Charlie associated with four locations
INSERT INTO ClubMemberLocation (clubMemberID, locationID, activationDate)
SELECT cm.clubMemberID, 2, DATE_SUB(CURDATE(), INTERVAL 9 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'charlie.brown@example.com'
UNION ALL
SELECT cm.clubMemberID, 3, DATE_SUB(CURDATE(), INTERVAL 5 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'charlie.brown@example.com'
UNION ALL
SELECT cm.clubMemberID, 4, DATE_SUB(CURDATE(), INTERVAL 2 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'charlie.brown@example.com'
UNION ALL
SELECT cm.clubMemberID, 5, DATE_SUB(CURDATE(), INTERVAL 1 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'charlie.brown@example.com';

-- Diana associated with four locations
INSERT INTO ClubMemberLocation (clubMemberID, locationID, activationDate)
SELECT cm.clubMemberID, 1, DATE_SUB(CURDATE(), INTERVAL 1 YEAR) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'diana.prince@example.com'
UNION ALL
SELECT cm.clubMemberID, 2, DATE_SUB(CURDATE(), INTERVAL 7 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'diana.prince@example.com'
UNION ALL
SELECT cm.clubMemberID, 3, DATE_SUB(CURDATE(), INTERVAL 4 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'diana.prince@example.com'
UNION ALL
SELECT cm.clubMemberID, 4, DATE_SUB(CURDATE(), INTERVAL 1 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'diana.prince@example.com';

-- Edward associated with four locations
INSERT INTO ClubMemberLocation (clubMemberID, locationID, activationDate)
SELECT cm.clubMemberID, 1, DATE_SUB(CURDATE(), INTERVAL 10 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'edward.norton@example.com'
UNION ALL
SELECT cm.clubMemberID, 2, DATE_SUB(CURDATE(), INTERVAL 6 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'edward.norton@example.com'
UNION ALL
SELECT cm.clubMemberID, 3, DATE_SUB(CURDATE(), INTERVAL 3 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'edward.norton@example.com'
UNION ALL
SELECT cm.clubMemberID, 4, DATE_SUB(CURDATE(), INTERVAL 2 MONTH) FROM ClubMember cm JOIN Person p ON cm.personID = p.personID WHERE p.email = 'edward.norton@example.com';

-- ----------------------------------------------------------------

-- ---------------------------------------
-- query 14 data
-- Insert sample data into `Person` table
INSERT INTO Person (firstName, lastName, email, phone, gender, SSN, medicareID, address, city, province, postalCode, dateOfBirth)
VALUES
('John', 'Doe', 'john.doe@example.com', '555-1234', 'Male', '111223333', 'A11122333', '123 Elm St', 'Townsville', 'ON', '12345', '2019-01-01'),
('Jane', 'Smith', 'jane.smith@example.com', '555-5678', 'Female', '222334444', 'B22233444', '456 Oak St', 'Villecity', 'BC', '23456', '2019-05-15'),
('Alice', 'Wong', 'alice.wong@example.com', '555-9876', 'Female', '333226666', 'C33322666', '789 Maple St', 'Sportstown', 'ON', '34567', '2018-04-10'),
('Bob', 'Johnson', 'bestMail@example.com', '555-8765', 'Male', '444337777', 'D44433777', '321 Birch St', 'Sportstown', 'ON', '34568', '2017-03-15'),
('Charlie', 'Davis', 'charlie.davis@example.com', '555-7654', 'Male', '555448888', 'E55544888', '654 Palm St', 'Sportstown', 'ON', '34569', '2016-02-20');
;

-- Insert data into `ClubMember`
INSERT INTO ClubMember (personID, activationDate)
SELECT personID, CURDATE() FROM Person WHERE email IN ('john.doe@example.com', 'jane.smith@example.com','alice.wong@example.com', 'bestMail@example.com', 'charlie.davis@example.com');

-- Insert sample data into `Location`
INSERT INTO Location (name, address, city, province, postalCode, phone, website, type, capacity)
VALUES 
('Main Sports Center', '789 Pine St', 'Sportstown', 'ON', '34567', '555-7890', 'http://maincenter.com', 'Branch', 100);

-- Insert sample data into `Team`
INSERT INTO Team (name, gender, locationID)
VALUES 
('Junior Team', 'Male', (SELECT locationID FROM Location WHERE name='Main Sports Center'));

-- Insert sample data into `Session`
INSERT INTO Session (address, time, date, type)
VALUES 
('123 Game Ave', '10:00:00', '2024-01-01', 'Game'),
('456 Practice Blvd', '14:00:00', '2024-01-02', 'Practice'),
('789 Tournament Rd', '16:00:00', '2024-01-03', 'Game');

-- Assign positions to all members for the 'Junior Team'
INSERT INTO TeamMember (clubMemberID, teamID, position)
SELECT CM.clubMemberID, (SELECT teamID FROM Team WHERE name = 'Junior Team'), positions.position
FROM ClubMember CM
JOIN Person P ON CM.personID = P.personID
CROSS JOIN (SELECT 'Forward' AS position UNION ALL SELECT 'Midfielder' UNION ALL SELECT 'Defender' UNION ALL SELECT 'Goalkeeper') positions
WHERE P.email IN ('john.doe@example.com', 'jane.smith@example.com', 'alice.wong@example.com', 'bestMail@example.com', 'charlie.davis@example.com');


-- Insert sessions and link to 'Junior Team' 
INSERT INTO SessionTeams (sessionID, teamID)
SELECT S.sessionID, (SELECT teamID FROM Team WHERE name = 'Junior Team')
FROM Session S
WHERE S.type = 'Game' AND NOT EXISTS (
    SELECT 1 FROM SessionTeams ST WHERE ST.sessionID = S.sessionID AND ST.teamID = (SELECT teamID FROM Team WHERE name = 'Junior Team')
);

-- ---------------------------------------
-- Query 16 data 
-- Insert sample data into `Person` table with details like name, contact, and demographics
INSERT INTO Person (firstName, lastName, email, phone, gender, SSN, medicareID, address, city, province, postalCode, dateOfBirth)
VALUES
('John', 'Doe', 'epicMail@example.com', '555-1234', 'Male', '999223333', 'A99222333', '123 Elm St', 'Townsville', 'ON', '12345', '2019-01-01'),
('Jane', 'Smith', 'WorstMail@example.com', '555-5678', 'Female', '222334554', 'B66233444', '456 Oak St', 'Villecity', 'BC', '23456', '2017-05-15'),
('Alice', 'Wong', 'alice.right@example.com', '555-9876', 'Female', '993226666', 'C97322666', '789 Maple St', 'Sportstown', 'ON', '34567', '2018-04-10'),
('Eva', 'Green', 'eva.green@example.com', '555-4321', 'Female', '555332211', 'D55533221', '101 Cedar St', 'Villecity', 'BC', '23457', '2019-03-23'),
('Liam', 'Brown', 'liam.brown@example.com', '555-8765', 'Male', '666443322', 'E66644332', '202 Pine St', 'Townsville', 'ON', '12346', '2015-07-19');

-- Insert data into `ClubMember` table by selecting person IDs from `Person` table based on their emails
INSERT INTO ClubMember (personID, activationDate)
SELECT personID, CURDATE() FROM Person WHERE email IN ('epicMail@example.com', 'WorstMail@example.com', 'alice.right@example.com','eva.green@example.com', 'liam.brown@example.com');

-- Insert data into `Location` table, specifying facility details
INSERT INTO Location (name, address, city, province, postalCode, phone, website, type, capacity)
VALUES 
('Elite Sports Center', '789 Pine St', 'Sportstown', 'ON', '34567', '555-7890', 'http://maincenter.com', 'Branch', 100);

-- Insert data into `Team` table ensuring the location is unique by using a subquery to fetch the correct locationID
INSERT INTO Team (name, gender, locationID)
VALUES 
('Elite Squad', 'Male', (
    SELECT locationID 
    FROM Location 
    WHERE name = 'Elite Sports Center' AND city = 'Sportstown' AND address = '789 Pine St'
    LIMIT 1
));

-- Insert data into `Session` table for scheduled games with specific address, time, and date
INSERT INTO Session (address, time, date, type)
VALUES 
('999 Game Ave', '10:00:00', '2024-01-01', 'Game'),
('456 Game Blvd', '11:00:00', '2024-01-15', 'Game'),
('789 Game Rd', '12:00:00', '2024-01-30', 'Game');

-- Link `Session` to `Team` by inserting records in `SessionTeams` table, assuming 'Elite Squad' wins every game
INSERT INTO SessionTeams (sessionID, teamID, score)
VALUES 
(
    (SELECT sessionID FROM Session WHERE address = '999 Game Ave' AND date = '2024-01-01' AND time = '10:00:00'),
    (SELECT teamID FROM Team WHERE name = 'Elite Squad'),
    10
),
(
    (SELECT sessionID FROM Session WHERE address = '456 Game Blvd' AND date = '2024-01-15' AND time = '11:00:00'),
    (SELECT teamID FROM Team WHERE name = 'Elite Squad'),
    8
),
(
    (SELECT sessionID FROM Session WHERE address = '789 Game Rd' AND date = '2024-01-30' AND time = '12:00:00'),
    (SELECT teamID FROM Team WHERE name = 'Elite Squad'),
    9
);

-- Assign members to positions within 'Elite Squad' team and they win every game
INSERT INTO TeamMember (clubMemberID, teamID, position)
SELECT CM.clubMemberID, (SELECT teamID FROM Team WHERE name = 'Elite Squad'), 'Forward'
FROM ClubMember CM
JOIN Person P ON CM.personID = P.personID
WHERE P.email IN ('epicMail@example.com', 'WorstMail@example.com', 'alice.right@example.com','eva.green@example.com', 'liam.brown@example.com');

-- ----------------------------------

-- ----------------------------------
-- Query 18 insert data 
-- Insert data into the Person table for new volunteers.
-- These records include personal information such as name, contact details, and demographics.
INSERT INTO Person (firstName, lastName, email, phone, gender, SSN, medicareID, address, city, province, postalCode, dateOfBirth)
VALUES
('Emily', 'Taylor', 'emily.taylor@example.com', '555-0101', 'Female', '444456789', 'M98765432', '101 Oak Lane', 'Metrocity', 'ON', 'M5R1K8', '1985-06-15'),
('David', 'Moore', 'david.moore@example.com', '555-0202', 'Male', '999954321', 'Z12345678', '202 Birch Road', 'Coastside', 'BC', 'V3T1W7', '1990-09-10'),
('Lucas', 'Green', 'lucas.green@example.com', '555-0303', 'Male', '123459876', 'A11122233', '303 Pine St', 'Lakeside', 'ON', 'L5R3K8', '1982-03-12'),
('Sophia', 'Brown', 'sophia.brown@example.com', '555-0404', 'Female', '987650123', 'B22233344', '404 Spruce Rd', 'Hilltown', 'BC', 'H3T2S9', '1988-07-07'),
('Mason', 'White', 'mason.white@example.com', '555-0505', 'Male', '321098765', 'C33344455', '505 Cedar Ln', 'Rivercity', 'AB', 'R4P5T6', '1991-11-15');

-- Insert into Personnel table as volunteers.
-- These records are associated with the persons inserted above and specify their role and mandate as volunteers.
INSERT INTO Personnel (personID, role, mandate)
VALUES
((SELECT personID FROM Person WHERE email = 'emily.taylor@example.com'), 'Trainer', 'Volunteer'),
((SELECT personID FROM Person WHERE email = 'david.moore@example.com'), 'Administrator', 'Volunteer'),
((SELECT personID FROM Person WHERE email = 'lucas.green@example.com'), 'Trainer', 'Volunteer'),
((SELECT personID FROM Person WHERE email = 'sophia.brown@example.com'), 'Trainer', 'Volunteer'),
((SELECT personID FROM Person WHERE email = 'mason.white@example.com'), 'Trainer', 'Volunteer');

-- Insert into Location table.
-- These records define the physical locations of facilities where the volunteers will be stationed.
INSERT INTO Location (name, address, city, province, postalCode, phone, website, type, capacity)
VALUES 
('Community Sports Center', '450 Forest Rd', 'Metrocity', 'ON', 'M5R2P9', '555-3030', 'http://communitysports.org', 'Branch', 50),
('North Sports Facility', '506 North Ave', 'Lakeside', 'ON', 'L5R3L2', '555-6060', 'http://northsports.org', 'Branch', 75),
('Hilltown Fitness Center', '407 Hill St', 'Hilltown', 'BC', 'H3T2T9', '555-7070', 'http://hilltownfitness.com', 'Branch', 65),
('Rivercity Athletics Club', '508 River Rd', 'Rivercity', 'AB', 'R4P5R6', '555-8080', 'http://rivercityathletics.com', 'Branch', 80);

-- Link personnel to their respective locations.
-- This step assigns each personnel to a location where they will perform their volunteer duties.
INSERT INTO PersonnelLocation (personnelID, locationID, activationDate)
SELECT 
    personnelID, 
    (SELECT locationID FROM Location WHERE name = 'Community Sports Center'), 
    CURDATE()
FROM 
    Personnel
WHERE 
    personID IN (
        SELECT personID FROM Person WHERE email IN ('emily.taylor@example.com', 'david.moore@example.com')
    );

INSERT INTO PersonnelLocation (personnelID, locationID, activationDate)
SELECT 
    personnelID, 
    (SELECT locationID FROM Location WHERE name = 'North Sports Facility'), 
    CURDATE()
FROM 
    Personnel
WHERE 
    personID = (SELECT personID FROM Person WHERE email = 'lucas.green@example.com');

INSERT INTO PersonnelLocation (personnelID, locationID, activationDate)
SELECT 
    personnelID, 
    (SELECT locationID FROM Location WHERE name = 'Hilltown Fitness Center'), 
    CURDATE()
FROM 
    Personnel
WHERE 
    personID = (SELECT personID FROM Person WHERE email = 'sophia.brown@example.com');

INSERT INTO PersonnelLocation (personnelID, locationID, activationDate)
SELECT 
    personnelID, 
    (SELECT locationID FROM Location WHERE name = 'Rivercity Athletics Club'), 
    CURDATE()
FROM 
    Personnel
WHERE 
    personID = (SELECT personID FROM Person WHERE email = 'mason.white@example.com');
-- -------------------------------------------


-- -------------------------------------------
-- Insert Sponsor Data
-- Insert new people who will become club members
INSERT INTO Person (firstName, lastName, email, phone, gender, SSN, medicareID, address, city, province, postalCode, dateOfBirth)
VALUES 
('John', 'Doe', 'qwdfqw.doe@example.com', '123-456-7890', 'Male', '123433389', 'ES1234567', '1234 Main St', 'Montreal', 'QC', 'H3Z2Y7', '2016-01-01'),
('Alice', 'Smith', 'wwwce.smith@example.com', '234-567-8901', 'Female', '987654421', 'ES9876543', '2345 Side St', 'Toronto', 'ON', 'M5R1K8', '2016-05-15'),
('Mia', 'Wilson', 'mia.wilson@example.com', '123-456-7891', 'Female', '123433390', 'ES1234577', '1236 Main St', 'Montreal', 'QC', 'H3Z2Y7', '2017-02-01'),
('Noah', 'Brown', 'noah.brown@example.com', '234-567-8902', 'Male', '987654422', 'ES9876544', '2347 Side St', 'Toronto', 'ON', 'M5R1K8', '2018-06-15'),
('Emma', 'Johnson', 'emma.johnson@example.com', '345-678-9013', 'Female', '987654423', 'ES9876545', '2349 Side St', 'Toronto', 'ON', 'M5R1K8', '2019-07-20'),
('Olivia', 'Martin', 'olivia.martin@example.com', '456-789-0124', 'Female', '987654424', 'ES9876546', '2351 Side St', 'Toronto', 'ON', 'M5R1K8', '2016-08-25'),
('Liam', 'Davis', 'liam.davis@example.com', '567-890-1235', 'Male', '987654425', 'ES9876547', '2353 Side St', 'Toronto', 'ON', 'M5R1K8', '2016-09-30');

-- Insert these persons as club members
INSERT INTO ClubMember (personID, activationDate)
SELECT personID, CURDATE()
FROM Person
WHERE email IN ('qwdfqw.doe@example.com', 'wwwce.smith@example.com','mia.wilson@example.com', 
'noah.brown@example.com', 'emma.johnson@example.com', 'olivia.martin@example.com', 'liam.davis@example.com');

-- Insert new people who will become family members
INSERT INTO Person (firstName, lastName, email, phone, gender, SSN, medicareID, address, city, province, postalCode, dateOfBirth)
VALUES 
('Jane', 'Doe', 'jane.doe@example.com', '345-678-9012', 'Female', '666543219', 'ES8765432', '3456 Avenue Rd', 'Vancouver', 'BC', 'V5K0A1', '1988-08-30'),
('Bob', 'Smith', 'bob.smith@example.com', '456-789-0123', 'Male', '666432198', 'ES7654321', '5678 Circle Dr', 'Calgary', 'AB', 'T2P4H5', '1990-06-06'),
('Ethan', 'Clark', 'ethan.clark@example.com', '345-678-9014', 'Male', '666543220', 'ES8765433', '3458 Avenue Rd', 'Vancouver', 'BC', 'V5K0A1', '1989-12-12'),
('Sophia', 'Lee', 'sophia.lee@example.com', '456-789-0125', 'Female', '666432199', 'ES7654322', '5680 Circle Dr', 'Calgary', 'AB', 'T2P4H5', '1991-11-15'),
('Isabella', 'Harris', 'isabella.harris@example.com', '567-890-1236', 'Female', '666432200', 'ES7654323', '5682 Circle Dr', 'Calgary', 'AB', 'T2P4H5', '1992-10-18'),
('James', 'Lewis', 'james.lewis@example.com', '678-901-2345', 'Male', '666432201', 'ES7654324', '5684 Circle Dr', 'Calgary', 'AB', 'T2P4H5', '1993-09-21'),
('Charlotte', 'Walker', 'charlotte.walker@example.com', '789-012-3456', 'Female', '666432202', 'ES7654325', '5686 Circle Dr', 'Calgary', 'AB', 'T2P4H5', '1994-08-24');

-- Insert these persons as family members
INSERT INTO FamilyMember (personID)
SELECT personID
FROM Person
WHERE email IN ('jane.doe@example.com', 'bob.smith@example.com','ethan.clark@example.com', 'sophia.lee@example.com', 'isabella.harris@example.com', 'james.lewis@example.com', 'charlotte.walker@example.com');
-- Insert Sponsor Data
INSERT INTO Sponsor (clubMemberID, familyMemberID, relType, activationDate, terminationDate)
VALUES
  ((SELECT clubMemberID FROM ClubMember WHERE personID = (SELECT personID FROM Person WHERE email = 'qwdfqw.doe@example.com')),
   (SELECT familyMemberID FROM FamilyMember WHERE personID = (SELECT personID FROM Person WHERE email = 'jane.doe@example.com')),
   'Mother', CURDATE(), NULL),
  ((SELECT clubMemberID FROM ClubMember WHERE personID = (SELECT personID FROM Person WHERE email = 'wwwce.smith@example.com')),
   (SELECT familyMemberID FROM FamilyMember WHERE personID = (SELECT personID FROM Person WHERE email = 'bob.smith@example.com')),
   'Father', CURDATE(), NULL),
  ((SELECT clubMemberID FROM ClubMember WHERE personID = (SELECT personID FROM Person WHERE email = 'mia.wilson@example.com')),
   (SELECT familyMemberID FROM FamilyMember WHERE personID = (SELECT personID FROM Person WHERE email = 'ethan.clark@example.com')),
   'Other', CURDATE(), NULL),
  ((SELECT clubMemberID FROM ClubMember WHERE personID = (SELECT personID FROM Person WHERE email = 'noah.brown@example.com')),
   (SELECT familyMemberID FROM FamilyMember WHERE personID = (SELECT personID FROM Person WHERE email = 'sophia.lee@example.com')),
   'Mother', CURDATE(), NULL),
  ((SELECT clubMemberID FROM ClubMember WHERE personID = (SELECT personID FROM Person WHERE email = 'emma.johnson@example.com')),
   (SELECT familyMemberID FROM FamilyMember WHERE personID = (SELECT personID FROM Person WHERE email = 'isabella.harris@example.com')),
   'Father', CURDATE(), NULL),
  ((SELECT clubMemberID FROM ClubMember WHERE personID = (SELECT personID FROM Person WHERE email = 'olivia.martin@example.com')),
   (SELECT familyMemberID FROM FamilyMember WHERE personID = (SELECT personID FROM Person WHERE email = 'james.lewis@example.com')),
   'Father', CURDATE(), NULL),
  ((SELECT clubMemberID FROM ClubMember WHERE personID = (SELECT personID FROM Person WHERE email = 'liam.davis@example.com')),
   (SELECT familyMemberID FROM FamilyMember WHERE personID = (SELECT personID FROM Person WHERE email = 'charlotte.walker@example.com')),
   'Mother', CURDATE(), NULL);