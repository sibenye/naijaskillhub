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
-- Table structure for table `nsh_users`
--

DROP TABLE IF EXISTS `nsh_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nsh_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `emailAddress` varchar(250) NOT NULL,
  `isActive` bit(1) NOT NULL DEFAULT b'0',
  `username` varchar(45) NOT NULL,
  `resetToken` varchar(250) DEFAULT NULL,
  `activationToken` varchar(250) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `modifiedDate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nsh_users`
--

LOCK TABLES `nsh_users` WRITE;
/*!40000 ALTER TABLE `nsh_users` DISABLE KEYS */;
INSERT INTO `nsh_users` VALUES (1,'test@mailinator.com','\0','tuser',NULL,NULL,'2016-03-18 00:15:22','2016-03-18 00:15:22'),(2,'test2@mailinator.com','\0','tuser2',NULL,NULL,'2016-03-18 01:18:02','2016-03-18 01:18:02'),(3,'test3@mailinator.com','\0','tuser3',NULL,NULL,'2016-03-18 01:22:52','2016-03-18 01:22:52'),(5,'testG2@mailinator.com','\0','testG3',NULL,NULL,'2016-05-19 20:59:53','2016-05-20 02:38:43'),(6,'testZ@mailinator.com','\0','testZ',NULL,NULL,'2016-05-20 03:10:36','2016-05-20 03:10:36'),(7,'testZT@mailinator.com','\0','testZT',NULL,NULL,'2016-05-20 03:14:19','2016-05-20 03:14:19'),(8,'testS2@mailinator.com','\0','testS2',NULL,NULL,'2016-05-20 03:18:47','2016-05-20 03:18:47'),(9,'testS3@mailinator.com','\0','testS3',NULL,NULL,'2016-05-20 03:22:12','2016-05-20 03:22:12'),(10,'testS4@mailinator.com','\0','testS4',NULL,NULL,'2016-05-20 03:27:17','2016-05-20 03:27:17'),(11,'testGS@mailinator.com','\0','testGS',NULL,NULL,'2016-05-20 20:35:12','2016-05-20 20:35:12'),(12,'testGSX@mailinator.com','\0','testGSX',NULL,NULL,'2016-05-20 20:36:53','2016-05-20 20:36:53'),(13,'testGSX2@mailinator.com','\0','testGSX2',NULL,NULL,'2016-05-20 20:44:06','2016-05-20 20:44:06'),(14,'testGSX23@mailinator.com','\0','testGSX23',NULL,NULL,'2016-05-20 20:58:08','2016-05-20 20:58:08'),(15,'testGSR23@mailinator.com','\0','testGSR23',NULL,NULL,'2016-05-20 21:03:10','2016-05-20 21:04:38'),(16,'testGSR24@mailinator.com','\0','testGSR24',NULL,NULL,'2016-05-21 19:05:51','2016-05-21 19:05:51'),(17,'testGSR25@mailinator.com','\0','testGSR25',NULL,NULL,'2016-05-21 19:20:41','2016-05-21 19:20:41'),(18,'testGSR26@mailinator.com','\0','testGSR26',NULL,NULL,'2016-05-21 19:21:43','2016-05-21 19:21:43'),(19,'testGSR28@mailinator.com','\0','testGSR28',NULL,NULL,'2016-05-22 01:52:38','2016-05-22 01:52:38'),(20,'testGSR29@mailinator.com','\0','testGSR29',NULL,NULL,'2016-05-22 01:57:19','2016-05-22 01:57:19'),(21,'testGSR31@mailinator.com','\0','testGSR30',NULL,NULL,'2016-05-22 02:00:10','2016-05-22 02:00:10'),(22,'testGSR32@mailinator.com','\0','testGSR31',NULL,NULL,'2016-05-22 02:49:41','2016-05-22 02:49:41'),(23,'testGSR33@mailinator.com','\0','testGSR33',NULL,NULL,'2016-05-22 02:54:25','2016-05-22 02:54:25'),(24,'testGSR34@mailinator.com','\0','testGSR34',NULL,NULL,'2016-05-22 14:30:36','2016-05-22 14:30:36'),(25,'testGSR37@mailinator.com','\0','testGSR37',NULL,NULL,'2016-05-23 00:43:00','2016-05-23 00:43:00'),(26,'testSly3@mailinator.com','\0','testSly3',NULL,'$2y$10$L2Nb.HHR86Og9zXBcOtEDOF3ZDBCIY0ycU.odkHQBcm8Y0dqjcSg6','2016-05-30 02:20:02','2016-05-30 02:20:02'),(27,'testsly4change@mailinator.com','','testSly4',NULL,NULL,'2016-05-30 02:22:07','2016-06-19 13:56:09'),(28,'testGSR3766@mailinator.com','\0','testGSR3766',NULL,'$2y$10$ZOSCz7qv/Re7s9ILXi4lEeldwTkw7EaAY.U2rbMz9X5ma24YkeA8S','2016-05-31 18:59:43','2016-05-31 19:00:21'),(29,'testSly5@mailinator.com','\0','testSly5',NULL,'$2y$10$j87If5caqfWrZIp8UapBM.cPgyrd2.xoPt26nUSXyNOjEyWMdm2IO','2016-06-14 13:31:07','2016-06-14 13:31:07');
/*!40000 ALTER TABLE `nsh_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-20 16:44:21
