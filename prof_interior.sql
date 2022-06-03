-- MariaDB dump 10.19  Distrib 10.5.9-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: prof_interior
-- ------------------------------------------------------
-- Server version	10.5.9-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `auth_attempts`
--

DROP TABLE IF EXISTS `auth_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_attempts` (
  `attempt_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_time` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`attempt_id`),
  KEY `ip` (`ip`),
  KEY `last_time` (`last_time`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_attempts`
--

LOCK TABLES `auth_attempts` WRITE;
/*!40000 ALTER TABLE `auth_attempts` DISABLE KEYS */;
INSERT INTO `auth_attempts` VALUES (83,'70.69.0.112',1654110357),(84,'70.69.0.112',1654110400);
/*!40000 ALTER TABLE `auth_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (14,'qqq1'),(8,'Ванные комнаты'),(7,'Гостиные'),(9,'Спальни');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category_images`
--

DROP TABLE IF EXISTS `category_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL DEFAULT 0,
  `path` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id` (`category_id`) USING BTREE,
  KEY `created_at` (`created_at`),
  CONSTRAINT `category_images_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category_images`
--

LOCK TABLES `category_images` WRITE;
/*!40000 ALTER TABLE `category_images` DISABLE KEYS */;
INSERT INTO `category_images` VALUES (38,7,'16529452891309.jpg',1652945289),(39,8,'16529454215766.jpg',1652945421),(167,9,'16533854485567.jpg',1653385448),(168,14,'16541101921130.jpeg',1654110192);
/*!40000 ALTER TABLE `category_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `config_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated` int(11) unsigned NOT NULL DEFAULT 0,
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `name` (`name`),
  KEY `updated` (`updated`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pictures`
--

DROP TABLE IF EXISTS `pictures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pictures` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) unsigned NOT NULL DEFAULT 0,
  `path` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `pictures_project_id` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pictures`
--

LOCK TABLES `pictures` WRITE;
/*!40000 ALTER TABLE `pictures` DISABLE KEYS */;
INSERT INTO `pictures` VALUES (36,19,'16533744179199.jpg',1653374417),(37,19,'16533744365543.jpg',1653374436),(38,19,'16533744509896.jpg',1653374450),(39,20,'16533847586559.jpg',1653384758),(40,20,'16533847719361.jpg',1653384771),(41,20,'16533847811053.jpg',1653384781),(42,20,'16533847909247.jpg',1653384790),(43,21,'16533852412580.jpg',1653385241),(44,21,'16533852477611.jpg',1653385247),(45,21,'16533852538420.jpg',1653385253),(46,21,'16533852618740.jpg',1653385261),(48,25,'16538172978566.jpg',1653817297),(49,25,'16538173628924.jpg',1653817362),(50,25,'16538173832412.jpg',1653817383),(51,25,'16538173947975.jpg',1653817394),(52,26,'16538177757213.jpg',1653817775),(53,26,'16538177849217.jpg',1653817784),(54,26,'16538177969748.jpg',1653817796),(55,26,'16538178049868.jpg',1653817804),(56,27,'16538182280248.jpg',1653818228),(57,27,'16538182343647.jpg',1653818234),(58,27,'16538182434682.jpg',1653818243),(59,28,'16538874249958.jpg',1653887424),(60,28,'16538874536055.jpg',1653887453),(61,28,'16538874688876.jpg',1653887468),(62,28,'16538874924997.jpg',1653887492),(63,29,'16538879518892.jpg',1653887951),(64,29,'16538880160083.jpg',1653888016),(65,29,'16538880500925.jpg',1653888050),(66,30,'16538886257924.jpg',1653888625),(67,30,'16538886384327.jpg',1653888638),(71,31,'16538985235250.jpg',1653898523),(72,31,'16538985956957.jpg',1653898595),(73,31,'16538986045493.jpg',1653898604),(74,31,'16538986181882.jpg',1653898618),(75,30,'16539123806450.jpg',1653912380);
/*!40000 ALTER TABLE `pictures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int(11) unsigned NOT NULL DEFAULT 0,
  `created_at` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `category_id` (`category_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `projects_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (19,'Гостиная кухня в загородном доме','Элегантная сдержанность – так можно в двух словах охарактеризовать неоклассическую идеологию. Компромиссный стиль предпочитает простоту, гармонию, естественные приглушенные краски.',7,1653374392),(20,'Ванная комната в загородном доме','Использование керамогранита под белый мрамор делают ванную комнату комнату просторнее, а более насыщенные краски вносят аккуратную графичность. Вставки золотых, бронзовых, серебряных тонов придают аристократичности.',8,1653384659),(21,'Спальня в темных тонах','Оформление спальни в темных цветах требует продуманного дизайна. Такой интерьер предполагает множество вариантов декорирования и цветовых комбинаций. Здесь можно воплощать смелые задумки и экспериментировать. При грамотном подходе вы сможете создать в спальне комфортную обстановку с использованием темной палитры.Тёмный оттенок является отличным фоном, который выгодно подчеркивает другие цвета. Здесь можно смело использовать различные по стилю и форме аксессуары,мебель,предметы освещения.',9,1653385196),(25,'Спальня с картиной','Небольшая уютная спальня в стиле современная классика. Неоклассический стиль отличается от других направлений интерьерного дизайна использованием комфортной мягкой мебели с современными очертаниями и декором.',9,1653817246),(26,'Спальня в стиле современного минимализма','Современный интерьер в стиле минимализма - это прежде всего моделирование пространства и света, это воздух, объем и плоскость. Важным при создании таких интерьеров является грамотно спланированное пространство, много рассеянного, спокойного света и воздуха. В интерьере не должно быть сложных детализированных элементов – они отвлекают, не должно быть и ярких цветовых решений. Большие окна соединяют с окружающим миром, делая его частью интерьера.',9,1653817733),(27,'Гостиная в стиле современный минимализм','Интерьер гостиной в стиле минимализм воплощает в себе простоту и изысканность,демонстрируя гостям прекрасный вкус хозяев. Это идеальное дизайнерское решение для небольшой гостиной.',7,1653818194),(28,'Гостиная в загородном доме','Гостиная в стиле неоклассика – решение для тех, кто ценит изысканность, благородство и респектабельность классического направления. Неоклассика в интерьере гостиной во многом реализуется за счет грамотного выбора палитры. Чтобы создать в комнате атмосферу тепла используются приглушенные оттенки бежевого, молочного, кремового цвета. В качестве акцентов применяются детали зеленых, алых и коричневых тонов.',7,1653887283),(29,'Гостиная в стиле арт-деко','Гостиная в стиле арт-деко отличается своей изысканностью и экспрессивностью. Некоторые черты направления напоминают модерн, но все же арт-деко характерны уникальные элементы. Хозяева, решившие оформить свое жилье в этом стиле, получат оригинальную квартиру или дом, который своим привлекательным видом будет создавать прекрасное настроение.',7,1653887936),(30,'Спальня в стиле арт-деко','Интерьер спальни в стиле арт-деко способен удовлетворить самый изысканный вкус и совместить, казалось бы, несовместимое: сдержанный минимализм и роскошь. Утонченный и элегантный, этот стиль подойдет и тем, кто требует от жилища в первую очередь удобства и любит открытые, не загроможденные пространства, и тем, кто предпочитает уют и негу дворцовых покоев с их излишествами и украшательствами.',9,1653888554),(31,'Гостиная в стиле нео-классика','В интерьере этой гостиной в стиле нео-классика преобладают изящные линии, плавные, перетекающие друг в друга формы, светлая цветовая гамма. Это одно из немногих направлений, работая в котором дизайнеру удается обеспечить вневременную актуальность интерьера, а также создать в доме и квартире комфорт премиального уровня.',7,1653898415);
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `session_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0,
  `ip` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` int(11) unsigned NOT NULL DEFAULT 0,
  `expires` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `access_token` (`access_token`),
  KEY `user_id` (`user_id`),
  KEY `ip` (`ip`),
  KEY `created` (`created`),
  KEY `expires` (`expires`),
  CONSTRAINT `sessions_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pswd_h` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `name` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` int(11) unsigned NOT NULL DEFAULT 0,
  `updated` int(11) unsigned NOT NULL DEFAULT 0,
  `last_activity` int(11) unsigned NOT NULL DEFAULT 0,
  `banned` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `banned` (`banned`),
  KEY `last_activity` (`last_activity`),
  KEY `updated` (`updated`),
  KEY `up` (`pswd_h`) USING BTREE,
  KEY `created` (`created`),
  KEY `role` (`role`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'$2y$10$oZOqLPZFNs3B79zm.lYaBORK/UuVibOD7A4hwbzkwtpO7.xcOZO9u','admin@apple.com','admin','admin',1652370940,0,1654110423,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-06-03 10:25:45
