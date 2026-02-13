-- Tshijuka RDP – single database schema (everything in this file).
-- Run this once to create all tables and seed data.

USE u628771162_dndala;

-- User table
CREATE TABLE `User` (
    `userID` INT PRIMARY KEY AUTO_INCREMENT,
    `userName` VARCHAR(255) NOT NULL,
    `userContact` VARCHAR(20),
    `userEmail` VARCHAR(255) UNIQUE NOT NULL,
    `userPassword` VARCHAR(255) NOT NULL,
    `userRole` ENUM('Document Seeker', 'Document Issuer', 'Admin') NOT NULL
);

-- Subscribe table (Document Issuer subscription info)
CREATE TABLE `Subscribe` (
    `subscribeID` INT AUTO_INCREMENT PRIMARY KEY,
    `userID` INT NOT NULL,
    `documentIssuerName` VARCHAR(255) NOT NULL,
    `documentIssuerContact` VARCHAR(50),
    `documentIssuerEmail` VARCHAR(255),
    `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`userID`) REFERENCES `User`(`userID`) ON DELETE CASCADE
);

-- DocumentType table (document types e.g. State exams, P6, etc.)
CREATE TABLE `DocumentType` (
    `documentTypeID` INT PRIMARY KEY AUTO_INCREMENT,
    `typeName` VARCHAR(255) NOT NULL
);

-- Status table
CREATE TABLE `Status` (
    `statusID` INT PRIMARY KEY AUTO_INCREMENT,
    `statusName` VARCHAR(50) NOT NULL
);

-- Document table
CREATE TABLE `Document` (
    `documentID` VARCHAR(50) PRIMARY KEY,
    `userID` INT,
    `schoolID` INT,
    `documentTypeID` INT,
    `statusID` INT,
    `description` TEXT NOT NULL,
    `location` VARCHAR(255),
    `imagePath` VARCHAR(255),
    `submissionDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `completionDate` DATE,
    FOREIGN KEY (`userID`) REFERENCES `User`(`userID`) ON DELETE CASCADE,
    FOREIGN KEY (`schoolID`) REFERENCES `User`(`userID`) ON DELETE CASCADE,
    FOREIGN KEY (`documentTypeID`) REFERENCES `DocumentType`(`documentTypeID`),
    FOREIGN KEY (`statusID`) REFERENCES `Status`(`statusID`)
);

-- PrelossDocuments table (deprecated - removed for Document Seeker users)
CREATE TABLE `PrelossDocuments` (
    `prelossID` INT AUTO_INCREMENT PRIMARY KEY,
    `userID` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `filePath` VARCHAR(255) NOT NULL,
    `uploadedOn` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`userID`) REFERENCES `User`(`userID`) ON DELETE CASCADE
);

-- TshijukaPackHistory table
CREATE TABLE `TshijukaPackHistory` (
    `packID` INT AUTO_INCREMENT PRIMARY KEY,
    `userID` INT NOT NULL,
    `documentIDs` TEXT NOT NULL,
    `classification` VARCHAR(255) NOT NULL,
    `institutionEmail` VARCHAR(255) NOT NULL,
    `sharedOn` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`userID`) REFERENCES `User`(`userID`) ON DELETE CASCADE
);

-- Chat table (NEW, for private document seeker-issuer conversations)
CREATE TABLE `Chat` (
    `chatID` INT AUTO_INCREMENT PRIMARY KEY,
    `documentID` VARCHAR(50) NOT NULL,
    `senderID` INT NOT NULL,
    `message` TEXT NOT NULL,
    `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`documentID`) REFERENCES `Document`(`documentID`) ON DELETE CASCADE,
    FOREIGN KEY (`senderID`) REFERENCES `User`(`userID`) ON DELETE CASCADE
);

-- UserMfa: email OTP only for Document Seekers (no app, no QR)
CREATE TABLE IF NOT EXISTS `UserMfa` (
    `userID` INT PRIMARY KEY,
    `mfaEnabled` TINYINT(1) NOT NULL DEFAULT 1,
    `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`userID`) REFERENCES `User`(`userID`) ON DELETE CASCADE
);

-- Insert initial data
INSERT INTO `DocumentType` (`typeName`) VALUES 
('State exams'), ('P6'), ('P5'), ('P4'), ('P1 to P3');

INSERT INTO `Status` (`statusName`) VALUES 
('Pending'), ('In Progress'), ('Completed'), ('Cancelled');

-- Users (passwords stored as bcrypt hashes only; never plain text)
INSERT INTO `User` (`userName`, `userContact`, `userEmail`, `userPassword`, `userRole`) VALUES 
('John Doe', '123456789', 'student1@example.com', '$2y$10$dhMRIr7g.obQYOiFKJIFzecHO75NnN9dOqwvzZXEeg4s7QG3zKQgG', 'Document Seeker'),
('ABC Institute', '111222333', 'school@example.com', '$2y$10$CXFe..fqSVApR7ql5orEceMAOtfrPqEY4elP2nKYgh5gyNQR4xHUm', 'Document Issuer'),
('Tresor Ndala', '999888777', 'ndalabuzangu@gmail.com', '$2y$10$kQI0uEvnKg9rpexEYwpWr.Q7xQYNSoFjGdn3D1HxWdGRxBN0xfUJy', 'Admin');

-- Example subscription (Document Issuer must subscribe)
INSERT INTO `Subscribe` (`userID`, `documentIssuerName`, `documentIssuerContact`, `documentIssuerEmail`) VALUES
(2, 'ABC Institute', '111222333', 'school@example.com');

-- Documents (fixed imagePath without leading /)
INSERT INTO `Document` 
(`documentID`, `userID`, `schoolID`, `documentTypeID`, `statusID`, `description`, `location`, `imagePath`) VALUES 
('document_001', 1, 2, 2, 1, 'document in the main hall', 'Main Hall', 'uploads/images/RPT001.jpg'),
('document_002', 1, 2, 1, 3, 'document in the library', 'Library', 'uploads/images/RPT002.jpg');

-- PrelossDocuments example
INSERT INTO `PrelossDocuments` (`userID`, `title`, `filePath`) VALUES
(1, 'Birth Certificate', 'uploads/preloss/birth_certificate.pdf'),
(1, 'Primary School Completion', 'uploads/preloss/primary_completion.pdf');

-- Example chat messages
INSERT INTO `Chat` (`documentID`, `senderID`, `message`) VALUES
('document_001', 1, 'Hello, I submitted my document request.'),
('document_001', 2, 'We received your request and are working on it.');
