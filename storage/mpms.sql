-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: mpms
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `collections`
--

DROP TABLE IF EXISTS `collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `collections` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `schema` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collections_tenant_id_key_unique` (`tenant_id`,`key`),
  CONSTRAINT `collections_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collections`
--

LOCK TABLES `collections` WRITE;
/*!40000 ALTER TABLE `collections` DISABLE KEYS */;
INSERT INTO `collections` VALUES ('019e963a-7ac0-738c-929f-19bcd2657d2c','019e963a-798e-71a1-a410-763a051a58ed','products','Products','{\"fields\": [{\"name\": \"name\", \"type\": \"string\"}, {\"name\": \"price\", \"type\": \"decimal\"}, {\"name\": \"inventory\", \"type\": \"integer\"}]}','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7ad9-7394-a2be-6003275a0e40','019e963a-7991-72ab-8c78-6f9c80c10fca','patients','Patient Records','{\"fields\": [{\"name\": \"first_name\", \"type\": \"string\"}, {\"name\": \"last_name\", \"type\": \"string\"}, {\"name\": \"condition\", \"type\": \"string\"}]}','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7aec-702a-b99b-f6b9beff6af4','019e963a-7993-72b2-9c5d-7e102e58619b','shipments','Shipments','{\"fields\": [{\"name\": \"tracking_number\", \"type\": \"string\"}, {\"name\": \"destination\", \"type\": \"string\"}, {\"name\": \"status\", \"type\": \"string\"}]}','2026-06-04 23:51:15','2026-06-04 23:51:15');
/*!40000 ALTER TABLE `collections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `memberships`
--

DROP TABLE IF EXISTS `memberships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `memberships` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tenant_key` varchar(36) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (ifnull(`tenant_id`,_utf8mb4'00000000-0000-0000-0000-000000000000')) VIRTUAL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `memberships_user_id_tenant_id_role_id_unique` (`user_id`,`tenant_id`,`role_id`),
  UNIQUE KEY `memberships_user_id_role_id_tenant_key_unique` (`user_id`,`role_id`,`tenant_key`),
  KEY `memberships_tenant_id_foreign` (`tenant_id`),
  KEY `memberships_role_id_foreign` (`role_id`),
  CONSTRAINT `memberships_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `memberships_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `memberships_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `memberships`
--

LOCK TABLES `memberships` WRITE;
/*!40000 ALTER TABLE `memberships` DISABLE KEYS */;
INSERT INTO `memberships` (`id`, `user_id`, `tenant_id`, `role_id`, `created_at`, `updated_at`) VALUES ('019e963a-7aa4-70b6-8aaf-bdba542dc0c9','019e963a-7aa0-729e-9eda-0c09ebcfd10b',NULL,'019e963a-796f-72c2-83d8-15e7915c53d4','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7aaa-7022-b4a5-4f382538e0dc','019e963a-7aa7-70e6-95cf-ab8968ac402e','019e963a-798e-71a1-a410-763a051a58ed','019e963a-7972-723c-be75-8f44d073247b','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7ace-7017-8a78-93527096772f','019e963a-7acb-73fb-a769-6d32f8b3338b','019e963a-7991-72ab-8c78-6f9c80c10fca','019e963a-7972-723c-be75-8f44d073247b','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7ae2-7130-a6ae-91214deee743','019e963a-7ae0-723b-a639-97a662ed1ab5','019e963a-7993-72b2-9c5d-7e102e58619b','019e963a-7972-723c-be75-8f44d073247b','2026-06-04 23:51:15','2026-06-04 23:51:15');
/*!40000 ALTER TABLE `memberships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_06_03_000001_create_tenants_table',1),(5,'2026_06_03_000002_create_roles_table',1),(6,'2026_06_03_000003_create_permissions_table',1),(7,'2026_06_03_000004_create_memberships_table',1),(8,'2026_06_03_000005_create_role_permission_table',1),(9,'2026_06_03_000006_create_pages_table',1),(10,'2026_06_03_000007_create_page_blocks_table',1),(11,'2026_06_03_000008_create_collections_table',1),(12,'2026_06_03_000009_create_records_table',1),(13,'2026_06_05_000001_add_content_to_page_blocks_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_blocks`
--

DROP TABLE IF EXISTS `page_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `page_blocks` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('kpi','chart','table','list','form') COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL DEFAULT '0',
  `config` json DEFAULT NULL,
  `data_source` json DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `page_blocks_page_id_foreign` (`page_id`),
  CONSTRAINT `page_blocks_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_blocks`
--

LOCK TABLES `page_blocks` WRITE;
/*!40000 ALTER TABLE `page_blocks` DISABLE KEYS */;
INSERT INTO `page_blocks` VALUES ('019e963a-7ab8-71b0-be11-b50e611893d4','019e963a-7aaf-713d-ac2b-8ef70b30d0c0','kpi','hero',1,'{\"label\": \"Monthly Revenue\", \"value\": \"$64.2K\"}','{\"metric\": \"revenue\"}',NULL,'2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7aba-701a-9cf3-d067da335f56','019e963a-7aaf-713d-ac2b-8ef70b30d0c0','chart','body',2,'{\"title\": \"Sales by Category\"}','{\"source\": \"orders\"}',NULL,'2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7abd-710f-a569-b0ff6cbb1a4e','019e963a-7aaf-713d-ac2b-8ef70b30d0c0','table','footer',3,'{\"columns\": [\"SKU\", \"Name\", \"Stock\"]}','{\"collection\": \"products\"}',NULL,'2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7ad4-724a-a341-6d2b97e06dd9','019e963a-7ad1-738e-a5f7-6a3fa43cf4e9','kpi','hero',1,'{\"label\": \"Active Patients\", \"value\": 214}','{\"metric\": \"patients\"}',NULL,'2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7ad7-7044-9874-70e2752ec7d0','019e963a-7ad1-738e-a5f7-6a3fa43cf4e9','list','body',2,'{\"title\": \"Today’s Appointments\"}','{\"source\": \"appointments\"}',NULL,'2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7aea-7195-a8f9-94c882085bee','019e963a-7ae8-708f-b950-5325edf0fbdd','table','body',1,'{\"columns\": [\"Tracking\", \"Destination\", \"Status\"]}','{\"collection\": \"shipments\"}',NULL,'2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e97d6-66c7-73f2-b889-3260c78c8b06','019e963a-7aaf-713d-ac2b-8ef70b30d0c0','list','body',9,'[]','[]','<p>Our finest <strong>single malt</strong> selection.</p>','2026-06-05 07:21:11','2026-06-05 07:21:11');
/*!40000 ALTER TABLE `page_blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `layout` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'single',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `config` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_tenant_id_slug_unique` (`tenant_id`,`slug`),
  KEY `pages_parent_id_foreign` (`parent_id`),
  CONSTRAINT `pages_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `pages` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pages_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES ('019e963a-7aaf-713d-ac2b-8ef70b30d0c0','019e963a-798e-71a1-a410-763a051a58ed',NULL,'dashboard','Dashboard','mdi-view-dashboard','single',1,1,'{\"hero\": true}','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7ad1-738e-a5f7-6a3fa43cf4e9','019e963a-7991-72ab-8c78-6f9c80c10fca',NULL,'dashboard','Dashboard','mdi-view-dashboard','single',1,1,'{\"hero\": true}','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7ae5-72e3-9ea6-c4108e62926c','019e963a-7993-72b2-9c5d-7e102e58619b',NULL,'dashboard','Dashboard','mdi-view-dashboard','single',1,1,'{\"hero\": true}','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7ae8-708f-b950-5325edf0fbdd','019e963a-7993-72b2-9c5d-7e102e58619b',NULL,'shipments','Shipments','mdi-truck','single',1,1,'{\"subtitle\": \"Live shipment status\"}','2026-06-04 23:51:15','2026-06-04 23:51:15');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES ('019e963a-795d-727f-a5c2-28d792afeec1','manage_tenants','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7965-730d-bbe9-9ea96ebf37bd','manage_users','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7968-72df-858c-f0162afd5000','manage_pages','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-796a-73a3-a629-fceda59bac34','manage_collections','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-796c-7115-809c-61af15589134','view_dashboard','2026-06-04 23:51:15','2026-06-04 23:51:15');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `records`
--

DROP TABLE IF EXISTS `records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `records` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenant_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `collection_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `records_collection_id_foreign` (`collection_id`),
  KEY `records_tenant_id_collection_id_index` (`tenant_id`,`collection_id`),
  CONSTRAINT `records_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `records_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `records`
--

LOCK TABLES `records` WRITE;
/*!40000 ALTER TABLE `records` DISABLE KEYS */;
INSERT INTO `records` VALUES ('019e963a-7ac5-73d0-86d6-fd88732580ed','019e963a-798e-71a1-a410-763a051a58ed','019e963a-7ac0-738c-929f-19bcd2657d2c','{\"name\": \"Blue Widget\", \"price\": 29.99, \"inventory\": 132}','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7ac8-7045-9b46-5a694cc094c0','019e963a-798e-71a1-a410-763a051a58ed','019e963a-7ac0-738c-929f-19bcd2657d2c','{\"name\": \"Sale Widget\", \"price\": 18.5, \"inventory\": 48}','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7adb-7332-9344-cc6668afe597','019e963a-7991-72ab-8c78-6f9c80c10fca','019e963a-7ad9-7394-a2be-6003275a0e40','{\"condition\": \"Diabetes\", \"last_name\": \"Khan\", \"first_name\": \"Asha\"}','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7add-7055-b9d4-a4514152cb20','019e963a-7991-72ab-8c78-6f9c80c10fca','019e963a-7ad9-7394-a2be-6003275a0e40','{\"condition\": \"Hypertension\", \"last_name\": \"Lopez\", \"first_name\": \"Martin\"}','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7aef-7248-af2a-a00ca85d692e','019e963a-7993-72b2-9c5d-7e102e58619b','019e963a-7aec-702a-b99b-f6b9beff6af4','{\"status\": \"In Transit\", \"destination\": \"Dallas\", \"tracking_number\": \"FLEET-0031\"}','2026-06-04 23:51:15','2026-06-04 23:51:15');
/*!40000 ALTER TABLE `records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permission` (
  `role_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `role_permission_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permission_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permission_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permission`
--

LOCK TABLES `role_permission` WRITE;
/*!40000 ALTER TABLE `role_permission` DISABLE KEYS */;
INSERT INTO `role_permission` VALUES ('019e963a-796f-72c2-83d8-15e7915c53d4','019e963a-795d-727f-a5c2-28d792afeec1'),('019e963a-796f-72c2-83d8-15e7915c53d4','019e963a-7965-730d-bbe9-9ea96ebf37bd'),('019e963a-7972-723c-be75-8f44d073247b','019e963a-7965-730d-bbe9-9ea96ebf37bd'),('019e963a-796f-72c2-83d8-15e7915c53d4','019e963a-7968-72df-858c-f0162afd5000'),('019e963a-7972-723c-be75-8f44d073247b','019e963a-7968-72df-858c-f0162afd5000'),('019e963a-796f-72c2-83d8-15e7915c53d4','019e963a-796a-73a3-a629-fceda59bac34'),('019e963a-7972-723c-be75-8f44d073247b','019e963a-796a-73a3-a629-fceda59bac34'),('019e963a-796f-72c2-83d8-15e7915c53d4','019e963a-796c-7115-809c-61af15589134'),('019e963a-7972-723c-be75-8f44d073247b','019e963a-796c-7115-809c-61af15589134');
/*!40000 ALTER TABLE `role_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` enum('platform','tenant') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES ('019e963a-796f-72c2-83d8-15e7915c53d4','super_admin','platform','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7972-723c-be75-8f44d073247b','tenant_admin','tenant','2026-06-04 23:51:15','2026-06-04 23:51:15');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('3CcTihAOZ2dZ04Gsv9MPcUpm4ifkl6r1RAniCuXk','019e963a-7aa7-70e6-95cf-ab8968ac402e','127.0.0.1','curl/8.16.0','eyJfdG9rZW4iOiJXTFlrSDl3TVZBSTNJU0RuM2NTZzB1bHFydXYzUmZpeExhZVRjSHpPIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MTIzXC9hZG1pblwvc291dGhzZWFzXC9wYWdlLWJsb2Nrc1wvY3JlYXRlIiwicm91dGUiOiJ0ZW5hbnQucGFnZS1ibG9ja3MuY3JlYXRlIn0sIl9mbGFzaCI6eyJvbGQiOlsic3dlZXRhbGVydCJdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOiIwMTllOTYzYS03YWE3LTcwZTYtOTVjZi1hYjg5NjhhYzQwMmUiLCJzd2VldGFsZXJ0Ijp7Imljb24iOiJzdWNjZXNzIiwidGl0bGUiOiJCbG9jayBjcmVhdGVkIiwidGV4dCI6Ikxpc3QgYmxvY2sifX0=',1780663871),('3lL8owZ0Vld3cpB6R4pNVeHlu0h8JeOA9uZfvkgq',NULL,'127.0.0.1','curl/8.16.0','eyJfdG9rZW4iOiI5YnNhT25rbjY0bU44TmtYMXZERUZjU3VwNnp5eDRIaTlMMXRoMk4yIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvbG9naW4iLCJyb3V0ZSI6ImFkbWluLmxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1780664323),('5ihNJT9U3YRmjBXicVSjtQlPynOqUPNKRA4ikoy2','019e963a-7aa0-729e-9eda-0c09ebcfd10b','127.0.0.1','curl/8.16.0','eyJfdG9rZW4iOiJmajZCQlU1VjlyU2FKbWJoMXYxWEg2YU9OMlYwRjNWU3FJUjlxVWpFIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pbiIsInJvdXRlIjoiYWRtaW4uZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOiIwMTllOTYzYS03YWEwLTcyOWUtOWVkYS0wYzA5ZWJjZmQxMGIifQ==',1780664454),('d3VXfnUKC3NhneQror59I4ZBjDymLSeX5bv449u5','019e963a-7aa7-70e6-95cf-ab8968ac402e','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI2UzhYalR3d0NyN3VoMjdhS1hZSTBqMDVBRlRla01pS2g5TXVLcDBvIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvc291dGhzZWFzIiwicm91dGUiOiJ0ZW5hbnQuZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOiIwMTllOTYzYS03YWE3LTcwZTYtOTVjZi1hYjg5NjhhYzQwMmUifQ==',1780664583),('EaxrLAOL6QbyERMgij4AUCyr907jENLroP5xW5SU','019e963a-7aa7-70e6-95cf-ab8968ac402e','127.0.0.1','curl/8.16.0','eyJfdG9rZW4iOiIyUVJudFpxQzhnYmRoNmRyR3FlaFhiREJ3OWhyc2t0VnBWN3RPcFNjIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvc291dGhzZWFzIiwicm91dGUiOiJ0ZW5hbnQuZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOiIwMTllOTYzYS03YWE3LTcwZTYtOTVjZi1hYjg5NjhhYzQwMmUifQ==',1780664326),('Gyhok03iap6P3YLH9EBOpTTBZ208V97jb65FcjXc','019e963a-7aa0-729e-9eda-0c09ebcfd10b','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJ3TTRIdU0wd2R3MFRtbjFlN0JKUFFjdWhkYkx4WGphbERrMW43QjhSIiwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOiIwMTllOTYzYS03YWEwLTcyOWUtOWVkYS0wYzA5ZWJjZmQxMGIiLCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2FkbWluXC90ZW5hbnRzIiwicm91dGUiOiJhZG1pbi50ZW5hbnRzLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1780894074),('hyer0lY12qQuz6XuloJ8llohFTNBZpFjzs468HTI',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.122.1 Chrome/142.0.7444.265 Electron/39.8.8 Safari/537.36','eyJfdG9rZW4iOiJ4bGp3bkNaUUNxNWZSQTJWbFZ0M1Q5RU5OZkFRdUwzNk1BZmxxY0RPIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1780663313),('KViVpF7V412cFCuBzyNpVwvqNlA08AsFylCNKU6u',NULL,'127.0.0.1','curl/8.16.0','eyJfdG9rZW4iOiJYdzdyTkRxTWFzR1pVazFJUVdkN0JkS0ZtNHZkVWZJdzdYa0VTUTVLIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MTIzXC9hZG1pblwvbG9naW4iLCJyb3V0ZSI6ImFkbWluLmxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOlsiX29sZF9pbnB1dCIsImVycm9ycyJdLCJuZXciOltdfSwiX29sZF9pbnB1dCI6eyJlbWFpbCI6InNob3BtYXJ0QG1wbXMubG9jYWwifSwiZXJyb3JzIjp7ImRlZmF1bHQiOnsiZm9ybWF0IjoiOm1lc3NhZ2UiLCJtZXNzYWdlcyI6eyJlbWFpbCI6WyJUaGVzZSBjcmVkZW50aWFscyBkbyBub3QgbWF0Y2ggb3VyIHJlY29yZHMuIl19fX19',1780663811),('mzVFbFn47yZK5BNG3QoZMmqSm3vDVROIvKUlUSqA',NULL,'127.0.0.1','curl/8.16.0','eyJfdG9rZW4iOiJ1ZmNTVVNTemxTdTNnWjkwWjB3VUxvYnBzOVdKSEh0QzhyN0R4ZVNxIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvbG9naW4iLCJyb3V0ZSI6ImFkbWluLmxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1780664011),('pIaIdY8LtdC7K8zS4fmrqcYcNKzrizwJuSvbx5YH',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.123.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36','eyJfdG9rZW4iOiJtd05VdmZQTWxUQ2EwZUhTSWlPVlhhbE1PODRKSHc0a2tORXNuY3lUIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvbG9naW4iLCJyb3V0ZSI6ImFkbWluLmxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1780893784),('TcXOBm6LHmOChptD5mKq4OAyvH4Z9jRDft3hfwIE','019e963a-7aa0-729e-9eda-0c09ebcfd10b','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJONk5DR0Y4dVc2V3NOT1RyTnJYVE5sbDdUazJHdDJLZkRFRUphTTNQIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvdGVuYW50cyIsInJvdXRlIjoiYWRtaW4udGVuYW50cy5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoiMDE5ZTk2M2EtN2FhMC03MjllLTllZGEtMGMwOWViY2ZkMTBiIn0=',1780664589),('xpeoON3shQA9jO6G52HFAfj69XX95aXmW7hQ0GI6',NULL,'127.0.0.1','curl/8.16.0','eyJfdG9rZW4iOiJCUzI2Rjc1R3ZHOG1YWERxN09RNncwMW5LbklXVndVZE12Y0V6M3p4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvbG9naW4iLCJyb3V0ZSI6ImFkbWluLmxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1780664452);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `settings` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
INSERT INTO `tenants` VALUES ('019e963a-798e-71a1-a410-763a051a58ed','Southseas Distilleries','southseas','active','{\"branding\": {\"theme\": \"default\"}}','2026-06-04 23:51:15','2026-06-05 07:13:34'),('019e963a-7991-72ab-8c78-6f9c80c10fca','MediCare','medicare','active','{\"branding\": {\"theme\": \"default\"}}','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7993-72b2-9c5d-7e102e58619b','FleetGo','fleetgo','active','{\"branding\": {\"theme\": \"default\"}}','2026-06-04 23:51:15','2026-06-04 23:51:15');
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('019e963a-7aa0-729e-9eda-0c09ebcfd10b','Priya','priya@mpms.local','active',1,'2026-06-04 23:51:15','$2y$12$ThcNTCcC4biWuCKO/jIpDOpB7APfL33Ji1lprDjfrhC103dZ/Xzg.','we0oakVRfx','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7aa7-70e6-95cf-ab8968ac402e','Southseas Distilleries Admin','southseas@mpms.local','active',0,'2026-06-04 23:51:15','$2y$12$ThcNTCcC4biWuCKO/jIpDOpB7APfL33Ji1lprDjfrhC103dZ/Xzg.','dI0kRvd7Qh','2026-06-04 23:51:15','2026-06-05 07:13:34'),('019e963a-7acb-73fb-a769-6d32f8b3338b','MediCare Admin','medicare@mpms.local','active',0,'2026-06-04 23:51:15','$2y$12$ThcNTCcC4biWuCKO/jIpDOpB7APfL33Ji1lprDjfrhC103dZ/Xzg.','jPUwY21gfe','2026-06-04 23:51:15','2026-06-04 23:51:15'),('019e963a-7ae0-723b-a639-97a662ed1ab5','FleetGo Admin','fleetgo@mpms.local','active',0,'2026-06-04 23:51:15','$2y$12$ThcNTCcC4biWuCKO/jIpDOpB7APfL33Ji1lprDjfrhC103dZ/Xzg.','RcAFa4Uq0h','2026-06-04 23:51:15','2026-06-04 23:51:15');
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

-- Dump completed on 2026-06-08 10:29:24
