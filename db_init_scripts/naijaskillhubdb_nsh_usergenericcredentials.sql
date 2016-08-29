CREATE DATABASE  IF NOT EXISTS `naijaskillhubdb` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `naijaskillhubdb`;
-- MySQL dump 10.13  Distrib 5.6.17, for Win64 (x86_64)
--
-- Host: localhost    Database: naijaskillhubdb
-- ------------------------------------------------------
-- Server version	5.5.8

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `nsh_usergenericcredentials`
--

DROP TABLE IF EXISTS `nsh_usergenericcredentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nsh_usergenericcredentials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `credentialTypeId` int(11) NOT NULL,
  `socialId` varchar(4000) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `modifiedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gencred_credentialTypeId_fk_idx` (`credentialTypeId`),
  KEY `gencred_userId_fk_idx` (`userId`),
  CONSTRAINT `gencred_credentialTypeId_fk` FOREIGN KEY (`credentialTypeId`) REFERENCES `nsh_credentialtypes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `gencred_userId_fk` FOREIGN KEY (`userId`) REFERENCES `nsh_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nsh_usergenericcredentials`
--

LOCK TABLES `nsh_usergenericcredentials` WRITE;
/*!40000 ALTER TABLE `nsh_usergenericcredentials` DISABLE KEYS */;
INSERT INTO `nsh_usergenericcredentials` VALUES (1,15,2,'FBX678rd','2016-05-20 21:04:38','2016-05-20 21:04:38'),(15,16,2,'FBX679rd','2016-05-21 19:05:51','2016-05-21 19:05:51'),(16,23,2,'RTG4475GH','2016-05-22 20:33:51','2016-05-22 20:33:51'),(20,25,2,'NeGGGG1','2016-05-23 00:54:34','2016-05-23 00:54:34'),(21,28,2,'NeGGGG1','2016-05-31 18:59:43','2016-05-31 18:59:43');
/*!40000 ALTER TABLE `nsh_usergenericcredentials` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-20 16:44:25
