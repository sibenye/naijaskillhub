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
-- Table structure for table `nsh_userstandardcredentials`
--

DROP TABLE IF EXISTS `nsh_userstandardcredentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nsh_userstandardcredentials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `credentialTypeId` int(11) NOT NULL,
  `password` varchar(512) DEFAULT NULL,
  `createdDate` datetime NOT NULL,
  `modifiedDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stdcred_credentialTypeId_fk_idx` (`credentialTypeId`),
  KEY `stdcred_userId_fk_idx` (`userId`),
  CONSTRAINT `stdcred_credentialTypeId_fk` FOREIGN KEY (`credentialTypeId`) REFERENCES `nsh_credentialtypes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `stdcred_userId_fk` FOREIGN KEY (`userId`) REFERENCES `nsh_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nsh_userstandardcredentials`
--

LOCK TABLES `nsh_userstandardcredentials` WRITE;
/*!40000 ALTER TABLE `nsh_userstandardcredentials` DISABLE KEYS */;
INSERT INTO `nsh_userstandardcredentials` VALUES (1,1,1,NULL,'2016-03-18 00:18:13','2016-03-18 00:18:13'),(2,2,1,'New1111','2016-03-18 01:22:52','2016-03-18 01:22:52'),(3,5,1,'New1111','2016-05-19 20:59:53','2016-05-19 20:59:53'),(4,6,1,'New1111','2016-05-20 03:10:37','2016-05-20 03:10:37'),(5,7,1,'New1111','2016-05-20 03:14:19','2016-05-20 03:14:19'),(6,8,1,'New1111','2016-05-20 03:18:47','2016-05-20 03:18:47'),(7,9,1,'New1111','2016-05-20 03:22:12','2016-05-20 03:22:12'),(8,10,1,'New1111','2016-05-20 03:27:18','2016-05-20 03:27:18'),(9,11,1,'New1111','2016-05-20 20:35:13','2016-05-20 20:35:13'),(10,12,1,'New1111','2016-05-20 20:36:53','2016-05-20 20:36:53'),(11,13,1,'New1111','2016-05-20 20:44:06','2016-05-20 20:44:06'),(12,14,1,NULL,'2016-05-20 20:58:08','2016-05-20 20:58:08'),(13,15,1,'New1111','2016-05-20 21:03:10','2016-05-20 21:03:10'),(15,18,1,'fram679rd','2016-05-21 19:21:43','2016-05-21 19:21:43'),(16,21,1,'$2y$10$UvuhAuHE0d1MIP3oK7vcweDGBIIRrogau2rvshWJHV2SFymKolZJm','2016-05-22 02:00:10','2016-05-29 23:04:07'),(17,22,1,'$2y$10$pNBPlvxZrmlG.m6XQ5QIr.LlqY1NID9p7z2Sk8ps3jpq9GWPswjPG','2016-05-22 02:49:41','2016-05-22 02:49:41'),(18,23,1,'$2y$10$f2clnl8d7MbXkS2Z57XRquHtDSdppx4DQh0iA/ojo8PkeZDNPY1HW','2016-05-22 02:54:25','2016-05-22 02:54:25'),(19,24,1,'$2y$10$FKzu6X0rC5buy9tUGpbAkut/xhVP4f7pwuYJgnzPlxxdH0RYIe426','2016-05-22 14:30:36','2016-05-22 14:30:36'),(20,26,1,'$2y$10$mycXfzjIgCbdO2DmGpf16O9y97URDlkJu2fLDdMs9831EMqWWRmkS','2016-05-30 02:20:03','2016-05-30 02:20:03'),(21,27,1,'$2y$10$9TNaNAL7VBPtqIW21Vrfvek43LXkzOBk7d7S8ECXwZ0LlOTf.Scv.','2016-05-30 02:22:07','2016-05-30 02:22:07'),(22,29,1,'$2y$10$hXUBkjXSgyu.i9odOD8oueMvM2lvztyp/edbWKhma9hWiMfZVez5a','2016-06-14 13:31:07','2016-06-14 13:31:07');
/*!40000 ALTER TABLE `nsh_userstandardcredentials` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-20 16:44:23
