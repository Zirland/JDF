-- MySQL dump 10.13  Distrib 5.7.19, for Linux (x86_64)
--
-- Host: localhost    Database: JDF
-- ------------------------------------------------------
-- Server version	5.7.19-0ubuntu0.17.04.1

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
-- Current Database: `JDF`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `JDF` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci */;

USE `JDF`;

--
-- Table structure for table `agency`
--

DROP TABLE IF EXISTS `agency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agency` (
  `agency_id` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `agency_name` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `agency_url` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `agency_timezone` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `agency_lang` varchar(2) COLLATE utf8_czech_ci DEFAULT NULL,
  `agency_phone` varchar(30) COLLATE utf8_czech_ci DEFAULT NULL,
  `agency_fare_url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `agency_email` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`agency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `area_enum`
--

DROP TABLE IF EXISTS `area_enum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `area_enum` (
  `kod` varchar(1) COLLATE utf8_czech_ci NOT NULL,
  `popis` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`kod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cal_use`
--

DROP TABLE IF EXISTS `cal_use`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cal_use` (
  `trip_id` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `kalendar` smallint(3) DEFAULT NULL,
  PRIMARY KEY (`trip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar`
--

DROP TABLE IF EXISTS `calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar` (
  `service_id` tinyint(3) unsigned NOT NULL,
  `monday` tinyint(1) unsigned NOT NULL,
  `tuesday` tinyint(1) unsigned NOT NULL,
  `wednesday` tinyint(1) unsigned NOT NULL,
  `thursday` tinyint(1) unsigned NOT NULL,
  `friday` tinyint(1) unsigned NOT NULL,
  `saturday` tinyint(1) unsigned NOT NULL,
  `sunday` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calendar_dates`
--

DROP TABLE IF EXISTS `calendar_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendar_dates` (
  `service_id` tinyint(3) unsigned NOT NULL,
  `date` int(8) unsigned NOT NULL,
  `exception_type` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exter`
--

DROP TABLE IF EXISTS `exter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exter` (
  `linka` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `poradi` varchar(4) COLLATE utf8_czech_ci DEFAULT NULL,
  `kod_dopravy` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `kod_linky` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `prefer` varchar(1) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`linka`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exter_enum`
--

DROP TABLE IF EXISTS `exter_enum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exter_enum` (
  `kod` varchar(10) CHARACTER SET utf8 NOT NULL,
  `nazev` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`kod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fare_attribute`
--

DROP TABLE IF EXISTS `fare_attribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fare_attribute` (
  `fare_id` varchar(5) COLLATE utf8_czech_ci NOT NULL,
  `price` decimal(6,2) unsigned NOT NULL,
  `currency_type` varchar(3) COLLATE utf8_czech_ci NOT NULL,
  `payment_method` tinyint(1) NOT NULL,
  `transfers` varchar(1) COLLATE utf8_czech_ci NOT NULL,
  `transfer_duration` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`fare_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fare_rule`
--

DROP TABLE IF EXISTS `fare_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fare_rule` (
  `fare_id` varchar(5) COLLATE utf8_czech_ci NOT NULL,
  `route_id` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `origin_id` varchar(5) COLLATE utf8_czech_ci DEFAULT NULL,
  `destination_id` varchar(5) COLLATE utf8_czech_ci DEFAULT NULL,
  `contains_id` varchar(5) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`fare_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `frequency`
--

DROP TABLE IF EXISTS `frequency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `frequency` (
  `trip_id` int(10) unsigned NOT NULL,
  `start_time` varchar(8) COLLATE utf8_czech_ci NOT NULL,
  `end_time` varchar(8) COLLATE utf8_czech_ci NOT NULL,
  `headway_secs` int(10) unsigned NOT NULL,
  `exact_times` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`trip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `linestopsDB`
--

DROP TABLE IF EXISTS `linestopsDB`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `linestopsDB` (
  `stop_id` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `stop_name` varchar(150) COLLATE utf8_czech_ci DEFAULT NULL,
  `stop_pk` varchar(30) COLLATE utf8_czech_ci DEFAULT NULL,
  `stop_smer` tinyint(1) DEFAULT NULL,
  `stop_vazba` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `stop_linka` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `stop_poradi` int(3) DEFAULT NULL,
  PRIMARY KEY (`stop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `parent_use`
--

DROP TABLE IF EXISTS `parent_use`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `parent_use` (
  `stop_id` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pevnykod`
--

DROP TABLE IF EXISTS `pevnykod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pevnykod` (
  `kod_cislo` tinyint(2) NOT NULL,
  `kod_znak` varchar(1) COLLATE utf8_czech_ci DEFAULT NULL,
  `kod_pozn` varchar(45) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`kod_cislo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pomoc`
--

DROP TABLE IF EXISTS `pomoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pomoc` (
  `zastav_id` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `trip_id` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `trip_pk` varchar(30) COLLATE utf8_czech_ci DEFAULT NULL,
  `prijezd` varchar(4) COLLATE utf8_czech_ci DEFAULT NULL,
  `odjezd` varchar(4) COLLATE utf8_czech_ci DEFAULT NULL,
  `km` int(4) DEFAULT NULL,
  `stop_vazba` varchar(15) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pomstop`
--

DROP TABLE IF EXISTS `pomstop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pomstop` (
  `pom_cislo` int(3) NOT NULL,
  `stop_name` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `stop_PK` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`pom_cislo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `route`
--

DROP TABLE IF EXISTS `route`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `route` (
  `route_id` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `agency_id` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `route_short_name` varchar(30) COLLATE utf8_czech_ci NOT NULL,
  `route_long_name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `route_desc` varchar(600) COLLATE utf8_czech_ci DEFAULT NULL,
  `route_type` smallint(5) DEFAULT NULL,
  `route_url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `route_color` varchar(6) COLLATE utf8_czech_ci DEFAULT NULL,
  `route_text_color` varchar(6) COLLATE utf8_czech_ci DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`route_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `route_enum`
--

DROP TABLE IF EXISTS `route_enum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `route_enum` (
  `kod` varchar(1) COLLATE utf8_czech_ci NOT NULL,
  `popis` varchar(20) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`kod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shape`
--

DROP TABLE IF EXISTS `shape`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shape` (
  `shape_id` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `shape_pt_lat` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `shape_pt_lon` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `shape_pt_sequence` int(10) unsigned NOT NULL,
  `shape_dist_traveled` decimal(10,3) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shapecheck`
--

DROP TABLE IF EXISTS `shapecheck`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shapecheck` (
  `trip_id` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `shape_id` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`trip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shapetvary`
--

DROP TABLE IF EXISTS `shapetvary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shapetvary` (
  `shape_id` int(11) NOT NULL AUTO_INCREMENT,
  `tvartrasy` varchar(1000) CHARACTER SET utf8 NOT NULL,
  `complete` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`shape_id`),
  UNIQUE KEY `trasa` (`tvartrasy`)
) ENGINE=InnoDB AUTO_INCREMENT=290 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stop`
--

DROP TABLE IF EXISTS `stop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stop` (
  `stop_id` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `stop_code` varchar(3) COLLATE utf8_czech_ci DEFAULT NULL,
  `stop_name` varchar(150) CHARACTER SET utf8 NOT NULL,
  `stop_desc` varchar(600) COLLATE utf8_czech_ci DEFAULT NULL,
  `stop_lat` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `stop_lon` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `zone_id` varchar(5) COLLATE utf8_czech_ci DEFAULT NULL,
  `stop_url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `location_type` tinyint(1) DEFAULT NULL,
  `parent_station` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `stop_timezone` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `wheelchair_boarding` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `fullname` varchar(150) COLLATE utf8_czech_ci NOT NULL DEFAULT '',
  `pomcode` varchar(3) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`stop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stop_use`
--

DROP TABLE IF EXISTS `stop_use`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stop_use` (
  `trip_id` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `stop_id` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stoptime`
--

DROP TABLE IF EXISTS `stoptime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stoptime` (
  `trip_id` varchar(15) CHARACTER SET utf8 NOT NULL,
  `arrival_time` varchar(8) COLLATE utf8_czech_ci NOT NULL,
  `departure_time` varchar(8) COLLATE utf8_czech_ci NOT NULL,
  `stop_id` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `stop_sequence` tinyint(3) unsigned NOT NULL,
  `stop_headsign` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `pickup_type` tinyint(1) unsigned DEFAULT NULL,
  `drop_off_type` tinyint(1) unsigned DEFAULT NULL,
  `shape_dist_traveled` int(5) unsigned DEFAULT NULL,
  `timepoint` tinyint(1) unsigned DEFAULT NULL,
  `zastav_id` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transfer`
--

DROP TABLE IF EXISTS `transfer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transfer` (
  `from_stop_id` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `to_stop_id` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `transfer_type` tinyint(1) unsigned NOT NULL,
  `min_transfer_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`from_stop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trip`
--

DROP TABLE IF EXISTS `trip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trip` (
  `route_id` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `matice` varchar(553) COLLATE utf8_czech_ci NOT NULL,
  `trip_id` varchar(15) CHARACTER SET utf8 NOT NULL,
  `trip_headsign` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `trip_short_name` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `direction_id` tinyint(1) unsigned DEFAULT NULL,
  `block_id` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `shape_id` varchar(1000) CHARACTER SET utf8 DEFAULT NULL,
  `wheelchair_accessible` tinyint(1) unsigned DEFAULT NULL,
  `bikes_allowed` tinyint(1) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`trip_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `triptimesDB`
--

DROP TABLE IF EXISTS `triptimesDB`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `triptimesDB` (
  `zastav_id` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `trip_id` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `trip_pk` varchar(30) COLLATE utf8_czech_ci DEFAULT NULL,
  `prijezd` varchar(4) COLLATE utf8_czech_ci DEFAULT NULL,
  `odjezd` varchar(4) COLLATE utf8_czech_ci DEFAULT NULL,
  `km` int(4) DEFAULT NULL,
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  UNIQUE KEY `UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15988986 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tripvazba`
--

DROP TABLE IF EXISTS `tripvazba`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tripvazba` (
  `zastav_id` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `trip_id` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `stop_vazba` varchar(15) COLLATE utf8_czech_ci DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-09-20 11:49:24
