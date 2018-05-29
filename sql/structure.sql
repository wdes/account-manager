
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

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `accountmanager` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `accountmanager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auths` (
  `id` int(11) unsigned NOT NULL COMMENT 'Id',
  `label` varchar(255) NOT NULL COMMENT 'Label',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auths__websites` (
  `idAuth` int(11) unsigned NOT NULL,
  `idWebsite` int(11) unsigned NOT NULL,
  PRIMARY KEY (`idAuth`,`idWebsite`),
  KEY `auth__website__idWebsite` (`idWebsite`),
  CONSTRAINT `auth__website__idAuth` FOREIGN KEY (`idAuth`) REFERENCES `auths` (`id`),
  CONSTRAINT `auth__website__idWebsite` FOREIGN KEY (`idWebsite`) REFERENCES `websites` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domains` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `domain` varchar(255) NOT NULL COMMENT 'Domain',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `identities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `value` varchar(255) NOT NULL COMMENT 'Value',
  `idTypeIdentity` int(11) unsigned NOT NULL COMMENT 'Id type identity',
  PRIMARY KEY (`id`),
  KEY `identities__idTypeIdentity` (`idTypeIdentity`),
  CONSTRAINT `identities__idTypeIdentity` FOREIGN KEY (`idTypeIdentity`) REFERENCES `identities__types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `identities__types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `label` varchar(255) NOT NULL COMMENT 'Label',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `identities__users` (
  `idIdentity` int(11) unsigned NOT NULL COMMENT 'Id identity',
  `idUser` int(11) unsigned NOT NULL COMMENT 'Id user',
  PRIMARY KEY (`idIdentity`,`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User ID',
  `username` varchar(255) NOT NULL COMMENT 'Username',
  `password` varchar(255) NOT NULL COMMENT 'Pasword',
  `email` varchar(255) NOT NULL COMMENT 'Email',
  `verified` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Verified account',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='Users';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users__websites` (
  `idUser` int(11) unsigned NOT NULL COMMENT 'Id user',
  `idWebsite` int(11) unsigned NOT NULL COMMENT 'Id website',
  PRIMARY KEY (`idUser`,`idWebsite`),
  KEY `users__websites__idWebsite` (`idWebsite`),
  CONSTRAINT `users__websites__idUser` FOREIGN KEY (`idUser`) REFERENCES `users` (`id`),
  CONSTRAINT `users__websites__idWebsite` FOREIGN KEY (`idWebsite`) REFERENCES `websites` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `websites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Website ID',
  `label` varchar(255) NOT NULL COMMENT 'Label',
  `idAuth` int(10) unsigned DEFAULT NULL COMMENT 'Auth  ID',
  `cantDelete` enum('1','0') NOT NULL DEFAULT '0' COMMENT 'User can not delete himself his account',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Websites';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `websites__domains` (
  `idWebsite` int(11) unsigned NOT NULL COMMENT 'Id website',
  `idDomain` int(11) unsigned NOT NULL COMMENT 'Id domain',
  PRIMARY KEY (`idWebsite`,`idDomain`),
  KEY `websites__domains__idDomain` (`idDomain`),
  CONSTRAINT `websites__domains__idDomain` FOREIGN KEY (`idDomain`) REFERENCES `domains` (`id`),
  CONSTRAINT `websites__domains__idWebsite` FOREIGN KEY (`idWebsite`) REFERENCES `websites` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `websites__public` (
  `idWebsite` int(11) unsigned NOT NULL COMMENT 'Id website',
  PRIMARY KEY (`idWebsite`),
  CONSTRAINT `websites__public__idWebsite` FOREIGN KEY (`idWebsite`) REFERENCES `websites` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

