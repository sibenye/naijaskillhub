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
-- Table structure for table `nsh_usersessions`
--

DROP TABLE IF EXISTS `nsh_usersessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nsh_usersessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `authorizationKey` varchar(512) DEFAULT NULL,
  `firstLoginDate` datetime NOT NULL,
  `lastLoginDate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `us_userId_fk_idx` (`userId`),
  CONSTRAINT `us_userId_fk` FOREIGN KEY (`userId`) REFERENCES `nsh_users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nsh_usersessions`
--

LOCK TABLES `nsh_usersessions` WRITE;
/*!40000 ALTER TABLE `nsh_usersessions` DISABLE KEYS */;
INSERT INTO `nsh_usersessions` VALUES (1,29,'KSWqVufkz2lXZjzvRB2/X2dZ2Hh0G9Ds+hmYc0DiwI/LJCBtsV5IFvOGJS2frNw0N13WvJGq1nv0zjyWCkUS2IivC6xTISPia3prdA+8Xis1YkUmsYxV0xa70uwzkkLc','2016-06-14 13:32:20','2016-06-14 13:33:41'),(2,27,'Q+wRPo5ymx3dOcyABw6eDwyIOEjt/5jHnC5ri9RqWZYuErv667R2FEB+rJJtfCdoxCgoOo7v6RGWGV/iELwWgfaRjxbqmwfs+9SnGRtYCHLuizIL9LoM+UcXzRLEpPXz','2016-06-14 13:34:32','2016-06-19 13:55:01');
/*!40000 ALTER TABLE `nsh_usersessions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-20 16:44:18
