-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET NAMES utf8 */;

/*!50503 SET NAMES utf8mb4 */;

/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;

/*!40103 SET TIME_ZONE='+00:00' */;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table dynamicform.answers
CREATE TABLE
  IF NOT EXISTS `answers` (
    `id` int NOT NULL AUTO_INCREMENT,
    `form_id` int NOT NULL,
    `question_id` int NOT NULL,
    `answer` varchar(2000) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `form_id` (`form_id`),
    KEY `question_id` (`question_id`),
    CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`),
    CONSTRAINT `answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- Data exporting was unselected.
-- Dumping structure for table dynamicform.forms
CREATE TABLE
  IF NOT EXISTS `forms` (
    `id` int NOT NULL AUTO_INCREMENT,
    `title` varchar(255) DEFAULT NULL,
    `slug` varchar(255) DEFAULT NULL,
    `is_active` tinyint (1) DEFAULT 0,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- Data exporting was unselected.
-- Dumping structure for table dynamicform.questions
CREATE TABLE
  IF NOT EXISTS `questions` (
    `id` int NOT NULL AUTO_INCREMENT,
    `form_id` int DEFAULT NULL,
    `text` varchar(2000) CHARACTER
    SET
      utf8mb4 COLLATE utf8mb4_0900_ai_ci,
      `type` varchar(50) CHARACTER
    SET
      utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
      `options` varchar(2000),
      `allowed_types` varchar(2000) DEFAULT NULL,
      `is_required` tinyint (1) DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `deleted_at` timestamp NULL DEFAULT NULL,
      KEY `form_id` (`form_id`),
      CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- Data exporting was unselected.
/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;

/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;