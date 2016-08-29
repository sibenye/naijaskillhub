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
-- Table structure for table `nsh_userattributevalues`
--

DROP TABLE IF EXISTS `nsh_userattributevalues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nsh_userattributevalues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userAttributeId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `attributeValue` varchar(5000) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `modifiedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userAttributeId_fk_idx` (`userAttributeId`),
  KEY `userId_fk_idx` (`userId`),
  CONSTRAINT `uav_userattributeId_fk` FOREIGN KEY (`userAttributeId`) REFERENCES `nsh_userattributes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `uav_userId_fk` FOREIGN KEY (`userId`) REFERENCES `nsh_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nsh_userattributevalues`
--

LOCK TABLES `nsh_userattributevalues` WRITE;
/*!40000 ALTER TABLE `nsh_userattributevalues` DISABLE KEYS */;
INSERT INTO `nsh_userattributevalues` VALUES (1,1,12,'test','2016-05-20 20:36:53','2016-05-20 20:36:53'),(2,2,12,'GSX','2016-05-20 20:36:53','2016-05-20 20:36:53'),(3,1,13,'test','2016-05-20 20:44:06','2016-05-20 20:44:06'),(4,2,13,'GSX2','2016-05-20 20:44:06','2016-05-20 20:44:06'),(5,1,14,'test','2016-05-20 20:58:08','2016-05-20 20:58:08'),(6,2,14,'GSX23','2016-05-20 20:58:08','2016-05-20 20:58:08'),(7,1,15,'test_change','2016-05-20 21:03:10','2016-05-21 01:27:24'),(8,2,15,'GSR23','2016-05-20 21:03:10','2016-05-21 01:27:24'),(9,3,15,'Dg','2016-05-21 01:27:24','2016-05-21 01:27:24'),(10,1,16,'test','2016-05-21 19:05:52','2016-05-21 19:05:52'),(11,2,16,'GSR24','2016-05-21 19:05:52','2016-05-21 19:05:52'),(12,1,18,'test','2016-05-21 19:21:43','2016-05-21 19:21:43'),(13,2,18,'GSR26','2016-05-21 19:21:43','2016-05-21 19:21:43'),(14,1,21,'test','2016-05-22 02:00:10','2016-05-22 02:00:10'),(15,2,21,'GSR31','2016-05-22 02:00:10','2016-05-22 02:00:10'),(16,1,22,'test_changex','2016-05-22 02:49:41','2016-05-22 02:51:54'),(17,2,22,'justcos','2016-05-22 02:49:41','2016-05-22 02:51:54'),(18,3,22,'Darg','2016-05-22 02:51:54','2016-05-22 02:51:54'),(19,1,23,'test','2016-05-22 02:54:25','2016-05-22 02:54:25'),(20,2,23,'GSR33','2016-05-22 02:54:25','2016-05-22 02:54:25'),(21,1,24,'test','2016-05-22 14:30:37','2016-05-22 14:30:37'),(22,2,24,'GSR34','2016-05-22 14:30:37','2016-05-22 14:30:37'),(23,1,25,'test','2016-05-23 00:43:01','2016-05-23 00:43:01'),(24,2,25,'GSR37','2016-05-23 00:43:01','2016-05-23 00:43:01'),(25,1,28,'test','2016-05-31 18:59:43','2016-05-31 19:00:21'),(26,2,28,'GSR37','2016-05-31 18:59:43','2016-05-31 19:00:21');
/*!40000 ALTER TABLE `nsh_userattributevalues` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-20 16:44:17
