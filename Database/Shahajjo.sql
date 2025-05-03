-- MySQL dump 10.13  Distrib 8.0.41, for Linux (x86_64)
--
-- Host: localhost    Database: Shahajjo
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Admin_table`use 
--

DROP TABLE IF EXISTS `Admin_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Admin_table` (
  `Admin_ID` varchar(10) NOT NULL,
  `Admin_pass` varchar(255) DEFAULT NULL,
  `Access_level` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Admin_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Admin_table`
--

LOCK TABLES `Admin_table` WRITE;
/*!40000 ALTER TABLE `Admin_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `Admin_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Donor_table`
--

DROP TABLE IF EXISTS `Donor_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Donor_table` (
  `Donor_UID` varchar(10) NOT NULL,
  `Total_donation` decimal(10,2) DEFAULT NULL,
  `Last_donation` date DEFAULT NULL,
  `Total_income` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`Donor_UID`),
  CONSTRAINT `Donor_table_ibfk_1` FOREIGN KEY (`Donor_UID`) REFERENCES `User_table` (`UID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Donor_table`
--

LOCK TABLES `Donor_table` WRITE;
/*!40000 ALTER TABLE `Donor_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `Donor_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Essential_needs`
--

DROP TABLE IF EXISTS `Essential_needs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Essential_needs` (
  `Donation_no` int NOT NULL,
  `Item_name` varchar(255) DEFAULT NULL,
  `Item_quantity` int DEFAULT NULL,
  PRIMARY KEY (`Donation_no`),
  CONSTRAINT `Essential_needs_ibfk_1` FOREIGN KEY (`Donation_no`) REFERENCES `Total_donations` (`Donation_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Essential_needs`
--

LOCK TABLES `Essential_needs` WRITE;
/*!40000 ALTER TABLE `Essential_needs` DISABLE KEYS */;
/*!40000 ALTER TABLE `Essential_needs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Feedback_table`
--

DROP TABLE IF EXISTS `Feedback_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Feedback_table` (
  `UID` varchar(10) NOT NULL,
  `Review` text,
  `Posting_date` date NOT NULL,
  PRIMARY KEY (`UID`,`Posting_date`),
  CONSTRAINT `Feedback_table_ibfk_1` FOREIGN KEY (`UID`) REFERENCES `User_table` (`UID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Feedback_table`
--

LOCK TABLES `Feedback_table` WRITE;
/*!40000 ALTER TABLE `Feedback_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `Feedback_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Financial_donations`
--

DROP TABLE IF EXISTS `Financial_donations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Financial_donations` (
  `Donation_no` int NOT NULL,
  `Money_amount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`Donation_no`),
  CONSTRAINT `Financial_donations_ibfk_1` FOREIGN KEY (`Donation_no`) REFERENCES `Total_donations` (`Donation_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Financial_donations`
--

LOCK TABLES `Financial_donations` WRITE;
/*!40000 ALTER TABLE `Financial_donations` DISABLE KEYS */;
/*!40000 ALTER TABLE `Financial_donations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Jakat_donation`
--

DROP TABLE IF EXISTS `Jakat_donation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Jakat_donation` (
  `Donation_no` int NOT NULL,
  `Jakat_amount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`Donation_no`),
  CONSTRAINT `Jakat_donation_ibfk_1` FOREIGN KEY (`Donation_no`) REFERENCES `Total_donations` (`Donation_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Jakat_donation`
--

LOCK TABLES `Jakat_donation` WRITE;
/*!40000 ALTER TABLE `Jakat_donation` DISABLE KEYS */;
/*!40000 ALTER TABLE `Jakat_donation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Organization_table`
--

DROP TABLE IF EXISTS `Organization_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Organization_table` (
  `Org_BIN` int NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Branch` varchar(255) DEFAULT NULL,
  `Account` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`Org_BIN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Organization_table`
--

LOCK TABLES `Organization_table` WRITE;
/*!40000 ALTER TABLE `Organization_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `Organization_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Receives`
--

DROP TABLE IF EXISTS `Receives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Receives` (
  `Donation_no` int NOT NULL,
  `Recipient_UID` varchar(10) NOT NULL,
  `Org_BIN` int NOT NULL,
  `Donation_date` date DEFAULT NULL,
  `Donation_amount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`Donation_no`,`Recipient_UID`,`Org_BIN`),
  KEY `Recipient_UID` (`Recipient_UID`),
  KEY `Org_BIN` (`Org_BIN`),
  CONSTRAINT `Receives_ibfk_1` FOREIGN KEY (`Donation_no`) REFERENCES `Total_donations` (`Donation_no`),
  CONSTRAINT `Receives_ibfk_2` FOREIGN KEY (`Recipient_UID`) REFERENCES `User_table` (`UID`),
  CONSTRAINT `Receives_ibfk_3` FOREIGN KEY (`Org_BIN`) REFERENCES `Organization_table` (`Org_BIN`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Receives`
--

LOCK TABLES `Receives` WRITE;
/*!40000 ALTER TABLE `Receives` DISABLE KEYS */;
/*!40000 ALTER TABLE `Receives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Recipient_table`
--

DROP TABLE IF EXISTS `Recipient_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Recipient_table` (
  `Recipient_UID` varchar(10) NOT NULL,
  `Cause` text,
  `Donation_Goal` int DEFAULT NULL,
  `Last_received` date DEFAULT NULL,
  PRIMARY KEY (`Recipient_UID`),
  CONSTRAINT `Recipient_table_ibfk_1` FOREIGN KEY (`Recipient_UID`) REFERENCES `User_table` (`UID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Recipient_table`
--

LOCK TABLES `Recipient_table` WRITE;
/*!40000 ALTER TABLE `Recipient_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `Recipient_table` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Saving_account`
--

DROP TABLE IF EXISTS `Saving_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Saving_account` (
  `Recipient_UID` varchar(10) DEFAULT NULL,
  `Account_no` varchar(10) NOT NULL,
  `Time_limit` date DEFAULT NULL,
  `Money` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`Account_no`),
  KEY `Recipient_UID` (`Recipient_UID`),
  CONSTRAINT `Saving_account_ibfk_1` FOREIGN KEY (`Recipient_UID`) REFERENCES `User_table` (`UID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Saving_account`
--

LOCK TABLES `Saving_account` WRITE;
/*!40000 ALTER TABLE `Saving_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `Saving_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Total_donations`
--

DROP TABLE IF EXISTS `Total_donations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Total_donations` (
  `Donor_ID` varchar(10) DEFAULT NULL,
  `Donation_no` int NOT NULL,
  `Donations_amount` decimal(10,2) DEFAULT NULL,
  `Donation_date` date DEFAULT NULL,
  `Confirmation` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`Donation_no`),
  KEY `Donor_ID` (`Donor_ID`),
  CONSTRAINT `Total_donations_ibfk_1` FOREIGN KEY (`Donor_ID`) REFERENCES `Donor_table` (`Donor_UID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Total_donations`
--

LOCK TABLES `Total_donations` WRITE;
/*!40000 ALTER TABLE `Total_donations` DISABLE KEYS */;
/*!40000 ALTER TABLE `Total_donations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User_table`
--

DROP TABLE IF EXISTS `User_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `User_table` (
  `UID` varchar(10) NOT NULL,
  `F_name` varchar(100) DEFAULT NULL,
  `M_Name` varchar(100) DEFAULT NULL,
  `L_Name` varchar(100) DEFAULT NULL,
  `Phone_no` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `NID` varchar(100) NOT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `Reg_date` date DEFAULT NULL,
  `Street` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `A_Verifier` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`UID`),
  UNIQUE KEY `Phone_no` (`Phone_no`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `NID` (`NID`),
  KEY `A_Verifier` (`A_Verifier`),
  CONSTRAINT `User_table_ibfk_1` FOREIGN KEY (`A_Verifier`) REFERENCES `Admin_table` (`Admin_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User_table`
--

LOCK TABLES `User_table` WRITE;
/*!40000 ALTER TABLE `User_table` DISABLE KEYS */;
/*!40000 ALTER TABLE `User_table` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-27 23:20:44
