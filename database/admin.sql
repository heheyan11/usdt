-- MySQL dump 10.13  Distrib 5.7.24, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: app
-- ------------------------------------------------------
-- Server version	5.7.24-0ubuntu0.18.04.1

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
-- Dumping data for table `admin_menu`
--

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;
INSERT INTO `admin_menu` VALUES (1,0,1,'面板','fa-bar-chart','/',NULL,NULL,NULL),(2,0,2,'管理员','fa-user-secret',NULL,NULL,NULL,'2019-12-17 11:56:08'),(3,2,3,'用户','fa-users','auth/users',NULL,NULL,'2019-12-17 11:56:08'),(4,2,4,'角色','fa-user','auth/roles',NULL,NULL,'2019-12-17 11:56:08'),(5,2,5,'权限','fa-ban','auth/permissions',NULL,NULL,'2019-12-17 11:56:08'),(6,2,6,'菜单','fa-bars','auth/menu',NULL,NULL,'2019-12-17 11:56:08'),(7,2,7,'操作日志','fa-history','auth/logs',NULL,NULL,'2019-12-17 11:56:08'),(8,0,11,'内容管理','fa-delicious',NULL,NULL,'2019-10-21 02:32:29','2019-12-17 11:56:08'),(9,8,13,'新闻分类','fa-info','article-cates',NULL,'2019-10-21 02:33:44','2019-12-17 11:56:08'),(10,8,14,'新闻列表','fa-info-circle','articles',NULL,'2019-10-21 11:30:44','2019-12-17 11:56:08'),(12,8,15,'幻灯列表','fa-sliders','slides',NULL,'2019-10-21 16:12:41','2019-12-17 11:56:08'),(13,0,9,'理财计划','fa-btc','crowd-fundings',NULL,'2019-12-02 09:55:14','2019-12-17 11:56:08'),(14,0,8,'项目配置','fa-connectdevelop','configs/1/edit',NULL,'2019-12-03 04:11:20','2019-12-17 11:56:08'),(15,8,12,'通知公告','fa-leanpub','notices',NULL,'2019-12-03 07:24:26','2019-12-17 11:56:08'),(16,18,20,'用户列表','fa-user','users',NULL,'2019-12-09 07:39:34','2019-12-17 11:56:08'),(17,18,21,'用户树状图','fa-bars','users-tree',NULL,'2019-12-09 08:53:02','2019-12-17 11:56:08'),(18,0,19,'用户管理','fa-users',NULL,NULL,'2019-12-10 00:55:29','2019-12-17 11:56:08'),(19,0,16,'用户记录','fa-camera-retro',NULL,NULL,'2019-12-17 08:55:18','2019-12-17 11:56:08'),(20,19,17,'提币记录','fa-bars','orderti',NULL,'2019-12-17 08:59:24','2019-12-17 11:56:08'),(21,19,18,'充币记录','fa-bars','orderchong',NULL,'2019-12-17 11:53:28','2019-12-17 11:56:08'),(22,0,10,'统计','fa-diamond','form-crow',NULL,'2019-12-17 11:55:00','2019-12-17 12:41:56'),(24,19,0,'收入记录','fa-bars','orderincome',NULL,'2019-12-17 13:59:15','2019-12-17 13:59:31'),(25,19,0,'等级变化记录','fa-bars','orderlevel',NULL,'2019-12-17 14:06:31','2019-12-17 14:08:24');
/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_permissions`
--

LOCK TABLES `admin_permissions` WRITE;
/*!40000 ALTER TABLE `admin_permissions` DISABLE KEYS */;
INSERT INTO `admin_permissions` VALUES (1,'All permission','*','','*',NULL,NULL),(2,'Dashboard','dashboard','GET','/',NULL,NULL),(3,'Login','auth.login','','/auth/login\r\n/auth/logout',NULL,NULL),(4,'User setting','auth.setting','GET,PUT','/auth/setting',NULL,NULL),(5,'Auth management','auth.management','','/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs',NULL,NULL);
/*!40000 ALTER TABLE `admin_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_menu`
--

LOCK TABLES `admin_role_menu` WRITE;
/*!40000 ALTER TABLE `admin_role_menu` DISABLE KEYS */;
INSERT INTO `admin_role_menu` VALUES (1,2,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_permissions`
--

LOCK TABLES `admin_role_permissions` WRITE;
/*!40000 ALTER TABLE `admin_role_permissions` DISABLE KEYS */;
INSERT INTO `admin_role_permissions` VALUES (1,1,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_users`
--

LOCK TABLES `admin_role_users` WRITE;
/*!40000 ALTER TABLE `admin_role_users` DISABLE KEYS */;
INSERT INTO `admin_role_users` VALUES (1,1,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_roles`
--

LOCK TABLES `admin_roles` WRITE;
/*!40000 ALTER TABLE `admin_roles` DISABLE KEYS */;
INSERT INTO `admin_roles` VALUES (1,'Administrator','administrator','2019-11-30 07:44:48','2019-11-30 07:44:48');
/*!40000 ALTER TABLE `admin_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_user_permissions`
--

LOCK TABLES `admin_user_permissions` WRITE;
/*!40000 ALTER TABLE `admin_user_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin','$2y$10$/zKoTQxF/G7RLoxX2rOPi.7EnBPpBMGkTxAEc6IS0ZEhlT8gHnJiq','Administrator',NULL,'hrORfEgYv3tYrBPkHMeeKCCHr3OcCJljcpcJe62u88GGsuY01VeYhDEnBgS8','2019-11-30 07:44:48','2019-11-30 07:44:48');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `configs`
--

LOCK TABLES `configs` WRITE;
/*!40000 ALTER TABLE `configs` DISABLE KEYS */;
INSERT INTO `configs` VALUES (1,100.0000,5.0000,2.0000,100.0000,1000.0000);
/*!40000 ALTER TABLE `configs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-12-17  6:33:09
