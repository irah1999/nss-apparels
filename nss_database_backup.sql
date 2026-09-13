-- NSS Database Backup
-- Generated: 2026-09-13 10:07:15
-- Host: localhost
-- Database: nss

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `bulk_imports`;
CREATE TABLE `bulk_imports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `total_count` int DEFAULT '0',
  `inserted_count` int DEFAULT '0',
  `failed_count` int DEFAULT '0',
  `status` varchar(20) DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `bulk_imports` VALUES("1","employees.csv","imp_1773858994_3132.csv","99","99","0","completed","2026-03-18 18:36:34","2026-03-18 18:36:35");
INSERT INTO `bulk_imports` VALUES("2","Preview_Upload_2026-03-19_17-44","imp_1773922471_1875.csv","1","0","0","failed","2026-03-19 17:44:31","2026-03-19 17:48:03");
INSERT INTO `bulk_imports` VALUES("3","Preview_Upload_2026-03-19_17-51","imp_1773922866_3306.csv","1","1","0","completed","2026-03-19 17:51:06","2026-03-19 17:51:06");
INSERT INTO `bulk_imports` VALUES("4","Preview_Upload_2026-03-19_17-51","imp_1773922918_3069.csv","1","0","1","failed","2026-03-19 17:51:58","2026-03-19 17:51:58");
INSERT INTO `bulk_imports` VALUES("5","Preview_Upload_2026-03-19_17-58","imp_1773923298_9250.csv","1","1","0","completed","2026-03-19 17:58:18","2026-03-19 17:58:18");


DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` VALUES("1","T-shirts","t-shirts","uploads/1773574599_be1506129bae4e97488e.jpeg","active","2026-03-15 11:36:39","2026-03-15 11:36:39");


DROP TABLE IF EXISTS `configurations`;
CREATE TABLE `configurations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `config_key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `config_value` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_key` (`config_key`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `configurations` VALUES("1","whatsapp_api_id","",NULL,NULL);
INSERT INTO `configurations` VALUES("2","whatsapp_token","",NULL,NULL);
INSERT INTO `configurations` VALUES("3","meta_app_id","",NULL,NULL);
INSERT INTO `configurations` VALUES("4","meta_app_secret","",NULL,NULL);


DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `joining_date` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `customers` VALUES("1","Hari","hari@gmail.com","916383455764","2026-02-24","1","2026-02-22 05:29:22","2026-02-22 05:29:22",NULL);
INSERT INTO `customers` VALUES("2","Saran","saran@gmail.com","919080865344","2026-02-26","1","2026-02-22 07:39:01","2026-03-14 05:35:30",NULL);
INSERT INTO `customers` VALUES("3","Balaji","irah7708@gmail.com","918124183337","2026-02-26","1","2026-02-22 07:55:22","2026-03-14 05:34:57",NULL);
INSERT INTO `customers` VALUES("5","Aravind Kumar","aravind@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-19 09:00:11","2026-03-19 09:00:11");
INSERT INTO `customers` VALUES("6","Bala Chandran","bala@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("7","Chitra Devi","chitra@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("8","Deepak Raj","deepak@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("9","Eswari Ram","eswari@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("10","Farook Khan","farook@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("11","Ganesh Mani","ganesh@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("12","Hari Prasad","hari@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("13","Induja S","induja@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("14","Jeeva K","jeeva@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("15","Karthik R","karthik@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("16","Latha M","latha@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("17","Mani G","mani@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("18","Naveen P","naveen@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("19","Oviya S","oviya@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("20","Prakash J","prakash@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("21","Qadir A","qadir@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("22","Ramesh V","ramesh@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("23","Suresh T","suresh@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("24","Thara K","thara@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("25","Umar F","umar@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("26","Vimal R","vimal@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("27","Wilson D","wilson@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("28","Xavier S","xavier@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("29","Yuvraj N","yuvraj@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("30","Zakir H","zakir@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("31","Anand L","anand@example.com","9.19E+11","0000-00-00","0","2026-03-18 18:36:35","2026-03-19 09:06:34","2026-03-19 09:06:34");
INSERT INTO `customers` VALUES("32","Bharathi P","bharathi@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("33","Chandru M","chandru@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("34","Dinesh K","dinesh@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("35","Elango V","elango@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("36","Faizal R","faizal@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("37","Gita S","gita@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("38","Hemant N","hemant@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("39","Ishwarya M","ishwarya@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("40","Jegan P","jegan@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("41","Kavitha D","kavitha@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("42","Logu S","logu@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("43","Mohan R","mohan@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("44","Nisha K","nisha@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("45","Prabhu A","prabhu@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("46","Ravi S","ravi@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("47","Selvam P","selvam@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("48","Tamil M","tamil@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("49","Uma K","uma@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("50","Venu G","venu@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("51","Waseem R","waseem@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("52","Yasin M","yasin@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("53","Zoya S","zoya@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("54","Ajith K","ajith@example.com","9.19E+11","0000-00-00","0","2026-03-18 18:36:35","2026-03-19 09:06:21","2026-03-19 09:06:21");
INSERT INTO `customers` VALUES("55","Bhuvan S","bhuvan@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("56","Charan R","charan@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("57","Dhaya M","dhaya@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("58","Ezhil P","ezhil@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("59","Feroz K","feroz@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("60","Gowri D","gowri@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("61","Hema L","hema@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("62","Iniyan V","iniyan@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("63","Janani R","janani@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("64","Kishore B","kishore@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("65","Leela M","leela@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("66","Madan S","madan@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("67","Nivetha P","nivetha@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("68","Omkar R","omkar@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("69","Priya G","priya@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("70","Rahul K","rahul@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("71","Sneha M","sneha@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("72","Tarun V","tarun@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("73","Udhaya S","udhaya@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("74","Varun P","varun@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("75","Yuvani R","yuvani@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("76","Arun J","arun@example.com","9.19E+11","0000-00-00","0","2026-03-18 18:36:35","2026-03-19 09:04:24","2026-03-19 09:04:24");
INSERT INTO `customers` VALUES("77","Babu L","babu@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("78","Devi K","devi@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("79","Eashwar M","eashwar@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("80","Guna S","guna@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("81","Heera P","heera@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("82","Irfan R","irfan@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("83","Jothi D","jothi@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("84","Kala V","kala@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("85","Lucky S","lucky@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("86","Meena R","meena@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("87","Nitesh K","nitesh@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("88","Pavan M","pavan@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("89","Ritu G","ritu@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("90","Siva P","siva@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("91","Tanya S","tanya@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("92","Vicky K","vicky@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("93","Wasim M","wasim@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("94","Yogesh R","yogesh@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("95","Aakash S","aakash@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("96","Bina P","bina@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("97","Chetan K","chetan@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("98","Dipa M","dipa@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("99","Esha V","esha@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("100","Falak R","falak@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("101","Gyan S","gyan@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("102","Hana P","hana@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("103","Ivaan K","ivaan@example.com","9.19E+11","0000-00-00","1","2026-03-18 18:36:35","2026-03-18 18:36:35",NULL);
INSERT INTO `customers` VALUES("104","Aravind Kumar","aravind@example.com","911234567890","0000-00-00","1","2026-03-19 17:44:31","2026-03-19 17:44:31",NULL);
INSERT INTO `customers` VALUES("105","Aravind Kumar1","aravind@example.com","911023456789","0000-00-00","0","2026-03-19 17:51:06","2026-03-19 17:57:29","2026-03-19 17:57:29");
INSERT INTO `customers` VALUES("106","Aravind kumar1","","91911023456789","2026-03-20","0","2026-03-19 17:57:50","2026-03-19 17:58:00","2026-03-19 17:58:00");
INSERT INTO `customers` VALUES("107","Aravind Kumar1","aravind@example.com","91911023456789","0000-00-00","1","2026-03-19 17:58:18","2026-03-19 17:58:18",NULL);


DROP TABLE IF EXISTS `enquiries`;
CREATE TABLE `enquiries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

INSERT INTO `enquiries` VALUES("1","Hari","irah7708@gmail.com","dfjj jb fdj k bf dkn ihjfk kjfd bjk khdf","2026-03-08 18:27:08");


DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `migrations` VALUES("1","2026-02-22-051952","App\\Database\\Migrations\\CreateUsersTable","default","App","1771737655","1");
INSERT INTO `migrations` VALUES("2","2026-02-22-052012","App\\Database\\Migrations\\CreateConfigurationsTable","default","App","1771737655","1");
INSERT INTO `migrations` VALUES("3","2026-02-22-052012","App\\Database\\Migrations\\CreateCustomersTable","default","App","1771737655","1");
INSERT INTO `migrations` VALUES("4","2026-02-22-052012","App\\Database\\Migrations\\CreateWhatsappLogsTable","default","App","1771737655","1");
INSERT INTO `migrations` VALUES("5","2026-02-22-073517","App\\Database\\Migrations\\AddAttachmentToWhatsappLogs","default","App","1771745741","2");
INSERT INTO `migrations` VALUES("6","2026-02-22-074632","App\\Database\\Migrations\\ModifyAttachmentToLongerText","default","App","1771746412","3");
INSERT INTO `migrations` VALUES("7","2026-03-15-111839","App\\Database\\Migrations\\CreateCategoriesTable","default","App","1773573684","4");


DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `colors` text COLLATE utf8mb4_general_ci,
  `sizes` text COLLATE utf8mb4_general_ci,
  `main_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `additional_images` text COLLATE utf8mb4_general_ci,
  `whatsapp_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` VALUES("1","1","Peter England Men\'s Cotton Regular Fit Polo T-Shirt","peter-england-mens-cotton-regular-fit-polo-t-shirt","Colour	Medium Blue\r\nFitting type	Regular Fit\r\nOccasion description	Halloween, New Year, Valentine\'s Day, Wedding\r\nStyle Name	Modern\r\nNeck Style	Collared Neck\r\nSleeve Type	Half Sleeve\r\nShirt Form Type	Polo Shirt\r\nSport Type	Golf\r\ncharacter	Other\r\nCollar Style	Polo Collar\r\nPattern	Striped\r\nBack Style	T Back\r\nStrap Type	Basic\r\nCuff Style	Round Cut Cuff","undefined","undefined","uploads/1773577685_18ed9c264278df5b21ca.jpeg",NULL,"6383455764","active","2026-03-15 12:28:05","2026-03-15 12:28:05");
INSERT INTO `products` VALUES("2","1","XYXX Men\'s Nova 100% Combed Cotton Regular Fit Polo T-Shirt","xyxx-mens-nova-100-combed-cotton-regular-fit-polo-t-shirt","Material composition100% Combed Cotton\r\nPatternSolid\r\nFit typeRegular Fit\r\nSleeve typeHalf Sleeve\r\nCollar stylePolo Collar\r\nLengthStandard Length\r\nCountry of OriginBangladesh","undefined","undefined","uploads/1773577999_ae150f4925aca310806a.jpeg",NULL,"6383455764","active","2026-03-15 12:33:19","2026-03-15 12:33:19");
INSERT INTO `products` VALUES("3","1","Allen Solly Men’s Polo T Shirt | Comfortable Rich Cotton Blend, Band Collar, Regular Fit | Stylish & Premium All Day Wear","allen-solly-mens-polo-t-shirt-comfortable-rich-cotton-blend-band-collar-regular-fit-stylish-premium-all-day-wear","Material composition60% Cotton, 40% Polyester\r\nPatternSolid\r\nFit typeRegular Fit\r\nSleeve typeHalf Sleeve\r\nCollar styleBand Collar\r\nLengthStandard Length\r\nCountry of OriginIndia","undefined","undefined","uploads/1773578377_fca80f852cf08e09010b.jpeg",NULL,"","active","2026-03-15 12:39:37","2026-03-15 12:39:37");
INSERT INTO `products` VALUES("4","1","Lyca T-shirt","lyca-t-shirt","","[]","[]","uploads/1773578583_96bd5c6b1c50cceb0e48.jpeg","[\"uploads\\/1773724995_85f396380a8149043848.png\",\"uploads\\/1773724995_3781a8cdeb5b7b6221ca.png\",\"uploads\\/1773724995_5ff1eac2060c13fd4722.jpeg\"]","6383455764","active","2026-03-15 12:43:03","2026-03-17 05:23:15");
INSERT INTO `products` VALUES("5","1","Lycra T-shirts","lycra-t-shirts","Material composition60% Cotton, 40% Polyester\r\nPatternSolid\r\nFit typeRegular Fit\r\nSleeve typeHalf Sleeve\r\nCollar styleBand Collar\r\nLengthStandard Length\r\nCountry of OriginIndia","[]","[]","uploads/1773579168_e0a38a991fa636133181.jpeg","[\"uploads\\/1773724585_cfdccec6c1b48743fa24.png\",\"uploads\\/1773724743_e06faaf252634dfc0768.png\"]","","active","2026-03-15 12:52:48","2026-03-17 05:19:03");


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES("1","Super Admin","admin@nss.com","$2y$10$xkqqulOvKT1Pz0qG8bMVbuDtsl/JA.AMrkGgpnNBNt5JGf4wMviim","admin","1","2026-02-22 05:21:16","2026-02-22 05:21:16");
INSERT INTO `users` VALUES("2","Hari","hari@gmail.com","$2y$10$bZ3YNPufugVI/Er.Jq8wX.MzVDeiXXGMSCMoXyowomqr8Mmif.pnG","user","1","2026-02-22 05:33:32","2026-02-22 05:33:32");


DROP TABLE IF EXISTS `webhook_debug`;
CREATE TABLE `webhook_debug` (
  `id` int NOT NULL AUTO_INCREMENT,
  `raw_body` longtext,
  `received_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `webhook_debug` VALUES("1","{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"0\",\"changes\":[{\"field\":\"messages\",\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"16505551111\",\"phone_number_id\":\"123456123\"},\"contacts\":[{\"profile\":{\"name\":\"test user name\",\"username\":\"\\u0040testusername\"},\"wa_id\":\"16315551181\",\"user_id\":\"US.13491208655302741918\"}],\"messages\":[{\"id\":\"ABGGFlA5Fpa\",\"timestamp\":\"1504902988\",\"from\":\"16315551181\",\"from_user_id\":\"US.13491208655302741918\",\"type\":\"text\",\"text\":{\"body\":\"this is a text message\"}}]}}]}]}","2026-03-14 07:31:11");
INSERT INTO `webhook_debug` VALUES("2","{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"0\",\"changes\":[{\"field\":\"history\",\"value\":{\"messaging_product\":\"whatsapp\",\"history\":[{\"metadata\":{\"phase\":1,\"chunk_order\":131,\"progress\":30},\"threads\":[{\"id\":\"1234567890\",\"context\":{\"wa_id\":\"1234567890\",\"user_id\":\"user.abc.efghijklmn123\",\"username\":\"\\u0040alice\",\"logical_id\":\"1234567890\"},\"messages\":[{\"from\":\"16505551111\",\"from_user_id\":\"user.abc.efghijklmn123\",\"id\":\"ABGGFlA5Fpa\",\"timestamp\":\"1504902988\",\"type\":\"media_placeholder\",\"history_context\":{\"status\":\"read\",\"from_me\":true}},{\"from\":\"16315551181\",\"from_user_id\":\"user.abc.efghijklmn123\",\"id\":\"ABGGFlA5Fpa\",\"timestamp\":\"1504902988\",\"type\":\"text\",\"text\":{\"body\":\"this is a text message\"},\"history_context\":{\"status\":\"delivered\",\"from_me\":false}}]}]}],\"metadata\":{\"display_phone_number\":\"16505551111\",\"phone_number_id\":\"123123123\"}}}]}]}","2026-03-14 07:38:02");
INSERT INTO `webhook_debug` VALUES("3","{\"object\":\"whatsapp_business_account\",\"entry\":[{\"id\":\"0\",\"changes\":[{\"field\":\"messages\",\"value\":{\"messaging_product\":\"whatsapp\",\"metadata\":{\"display_phone_number\":\"16505551111\",\"phone_number_id\":\"123456123\"},\"statuses\":[{\"id\":\"ABGGFlA5Fpa\",\"status\":\"sent\",\"timestamp\":\"1504902988\",\"recipient_id\":\"16315551181\",\"conversation\":{\"id\":\"CONVERSATION_ID\",\"expiration_timestamp\":\"1504903988\",\"origin\":{\"type\":\"marketing\"}},\"pricing\":{\"billable\":true,\"pricing_model\":\"PMP\",\"type\":\"regular\",\"category\":\"marketing\"}}],\"contacts\":[{\"profile\":{\"name\":\"test user name\"},\"wa_id\":\"16315551181\",\"user_id\":\"US.13491208655302741918\"}]}}]}]}","2026-03-14 07:38:44");


DROP TABLE IF EXISTS `whatsapp_logs`;
CREATE TABLE `whatsapp_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int unsigned NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `attachment` text COLLATE utf8mb4_general_ci,
  `attachment_type` text COLLATE utf8mb4_general_ci,
  `status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `response_log` text COLLATE utf8mb4_general_ci,
  `sent_at` datetime DEFAULT NULL,
  `message_id` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `direction` enum('outbound','inbound') COLLATE utf8mb4_general_ci DEFAULT 'outbound',
  `read_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'sent',
  PRIMARY KEY (`id`),
  KEY `whatsapp_logs_customer_id_foreign` (`customer_id`),
  CONSTRAINT `whatsapp_logs_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `whatsapp_logs` VALUES("1","1","Welcome to NSS Business! We are glad to have you.",NULL,NULL,"failed","Missing API Configuration","2026-02-22 05:30:51",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("2","1","Welcome to NSS Business! We are glad to have you.",NULL,NULL,"failed","Missing API Configuration","2026-02-22 07:24:06",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("3","1","hi",NULL,NULL,"failed","Missing API Configuration","2026-02-22 07:38:19",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("4","1","hgdftgfdc",NULL,NULL,"failed","Missing API Configuration","2026-02-22 07:39:41",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("5","2","hgdftgfdc",NULL,NULL,"failed","Missing API Configuration","2026-02-22 07:39:41",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("6","2","hi",NULL,NULL,"failed","Missing API Configuration","2026-02-22 07:40:55",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("7","1","hi","[\"1771746522_f993e251f3a679dd0c7b.png\",\"1771746522_97c6f42caba90c3747ea.pdf\",\"1771746522_dff3258b27917859b2ba.jpg\"]","[\"image\\/png\",\"application\\/pdf\",\"image\\/jpeg\"]","failed","Missing API Configuration","2026-02-22 07:48:42",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("8","1","HI saran you wastwe peice",NULL,NULL,"failed","Missing API Configuration","2026-02-22 07:49:53",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("9","2","HI saran you wastwe peice",NULL,NULL,"failed","Missing API Configuration","2026-02-22 07:49:53",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("10","2","https://goviralhost.com/clientarea/store/web-hosting/advanced?billingcycle=annually",NULL,NULL,"failed","Missing API Configuration","2026-02-22 07:59:47",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("11","1","https://goviralhost.com/clientarea/store/web-hosting/advanced?billingcycle=annually",NULL,NULL,"failed","Missing API Configuration","2026-02-22 08:00:11",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("12","2","https://goviralhost.com/clientarea/store/web-hosting/advanced?billingcycle=annually",NULL,NULL,"failed","Missing API Configuration","2026-02-22 08:00:11",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("13","3","https://goviralhost.com/clientarea/store/web-hosting/advanced?billingcycle=annually",NULL,NULL,"failed","Missing API Configuration","2026-02-22 08:00:11",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("14","1","Hi hari nee enna pandra",NULL,NULL,"failed","Missing API Configuration (Check .env)","2026-03-14 05:43:08",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("15","3","Hi da what r u doing.",NULL,NULL,"sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"8124183337\",\"wa_id\":\"918124183337\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE4MTI0MTgzMzM3FQIAERgSRDQyMkQxQTg5Q0RGREZFMTRFAA==\"}]}}]","2026-03-14 05:44:41",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("16","1","Hi da what r u doing.",NULL,NULL,"sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"6383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSN0NDNkNCMkZDQUEwNkQ2NzdGAA==\"}]}}]","2026-03-14 05:45:50",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("17","1","hi",NULL,NULL,"sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"6383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSMDdGQzE0QjJEM0E0N0YyMTM4AA==\"}]}}]","2026-03-14 05:59:01",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("18","1","dai",NULL,NULL,"sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSNjVENjdFNjYzNTc1RjNCNDM2AA==\"}]}}]","2026-03-14 06:01:46",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("19","1","hi",NULL,NULL,"sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSMDc3Q0M0OTZGMjcyRDMzMUY5AA==\"}]}}]","2026-03-14 06:51:16",NULL,"outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("20","1","dai",NULL,NULL,"sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSQkZDOEQyRkZFMDEwMUJCMzQwAA==\"}]}}]","2026-03-14 07:13:25","wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSQkZDOEQyRkZFMDEwMUJCMzQwAA==","outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("21","1","Media Message","[\"1773472453_731c52f5b7ffe7f178b2.png\"]","[\"image\\/png\"]","sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSNDY1MTVFQjVBMkMwNkY5OTVDAA==\"}]}}]","2026-03-14 07:14:14","wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSNDY1MTVFQjVBMkMwNkY5OTVDAA==","outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("22","1","Hi","[\"1773472538_cd4ad641f4fbce69a2a8.png\"]","[\"image\\/png\"]","sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSQkE2QzUxQjk5NDc3RjI1RThCAA==\"}]}},{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSMjEyRDdGMDUyOUZBNUUwNDAzAA==\"}]}}]","2026-03-14 07:15:40","wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSQkE2QzUxQjk5NDc3RjI1RThCAA==,wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSMjEyRDdGMDUyOUZBNUUwNDAzAA==","outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("23","1","Template: nss_offer_notification",NULL,NULL,"sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSOURBNzQ3QjJEODlGQUZFQkQ4AA==\",\"message_status\":\"accepted\"}]}}]","2026-03-14 13:37:20","wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSOURBNzQ3QjJEODlGQUZFQkQ4AA==","outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("24","2","Template: nss_offer_notification",NULL,NULL,"sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"919080865344\",\"wa_id\":\"919080865344\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE5MDgwODY1MzQ0FQIAERgSMEQ5OThCRUJDNDk4NTg5QTgyAA==\",\"message_status\":\"accepted\"}]}}]","2026-03-15 10:32:03","wamid.HBgMOTE5MDgwODY1MzQ0FQIAERgSMEQ5OThCRUJDNDk4NTg5QTgyAA==","outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("25","2","Template: nss_offer_notification",NULL,NULL,"sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"919080865344\",\"wa_id\":\"919080865344\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE5MDgwODY1MzQ0FQIAERgSOEE1MTU1QTk3N0JDNTA2MzdDAA==\",\"message_status\":\"accepted\"}]}}]","2026-03-15 10:33:30","wamid.HBgMOTE5MDgwODY1MzQ0FQIAERgSOEE1MTU1QTk3N0JDNTA2MzdDAA==","outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("26","1","Template: nss_offer_notification",NULL,NULL,"sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSQzAwRTIyQzg0NEVGQUZDRTlEAA==\",\"message_status\":\"accepted\"}]}}]","2026-03-15 10:52:20","wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSQzAwRTIyQzg0NEVGQUZDRTlEAA==","outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("27","1","Template: nss_wholesale_notification",NULL,NULL,"sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSQzhGNzVFMjQ2NDUwODc4RjIwAA==\",\"message_status\":\"accepted\"}]}}]","2026-03-18 17:54:06","wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSQzhGNzVFMjQ2NDUwODc4RjIwAA==","outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("28","1","hi",NULL,NULL,"sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSMTE2NjMwNUQwMEI4RDI4NTJBAA==\"}]}}]","2026-03-18 18:58:41","wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSMTE2NjMwNUQwMEI4RDI4NTJBAA==","outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("29","1","please check","[\"1773860368_89e3d8c6fc66e86955fd.png\"]","[\"image\\/png\"]","sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSQ0YyMjBBMzMyMjk0MDIzMjA4AA==\"}]}},{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSREU5OTlCRDEyQ0UwRTcxNTNCAA==\"}]}}]","2026-03-18 18:59:31","wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSQ0YyMjBBMzMyMjk0MDIzMjA4AA==,wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSREU5OTlCRDEyQ0UwRTcxNTNCAA==","outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("30","1","hi","[\"1773860406_c5f44135111fba236a61.xlsx\"]","[\"application\\/vnd.openxmlformats-officedocument.spreadsheetml.sheet\"]","sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSODNERTQ0QjJFNkFFRTMyQzhFAA==\"}]}},{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSMjY5Q0NDMTMxNjRBNkM2NDZFAA==\"}]}}]","2026-03-18 19:00:08","wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSODNERTQ0QjJFNkFFRTMyQzhFAA==,wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSMjY5Q0NDMTMxNjRBNkM2NDZFAA==","outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("31","1","Media Message","[\"1773860426_393b413366daf8568e6c.mp4\"]","[\"video\\/mp4\"]","sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSOTdCQ0MxQzkzMUIwRUEzMTQ5AA==\"}]}}]","2026-03-18 19:00:27","wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSOTdCQ0MxQzkzMUIwRUEzMTQ5AA==","outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("32","1","🤩hi",NULL,NULL,"sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSNTQ3NDk3MzZCMzQ4ODBBM0I3AA==\"}]}}]","2026-03-18 19:05:17","wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSNTQ3NDk3MzZCMzQ4ODBBM0I3AA==","outbound","sent");
INSERT INTO `whatsapp_logs` VALUES("33","1","hi",NULL,NULL,"sent","[{\"status\":\"success\",\"data\":{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"916383455764\",\"wa_id\":\"916383455764\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSN0QxNjg3OTdGNUI0NzdCMUY5AA==\"}]}}]","2026-03-19 01:09:48","wamid.HBgMOTE2MzgzNDU1NzY0FQIAERgSN0QxNjg3OTdGNUI0NzdCMUY5AA==","outbound","sent");


DROP TABLE IF EXISTS `whatsapp_templates`;
CREATE TABLE `whatsapp_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL,
  `language` varchar(50) DEFAULT 'en_US',
  `category` varchar(50) DEFAULT 'MARKETING',
  `status` varchar(50) DEFAULT 'PENDING',
  `body_text` text,
  `header_text` text,
  `footer_text` text,
  `buttons` text,
  `meta_template_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `whatsapp_templates` VALUES("1","hello_world","en_US","UTILITY","APPROVED","Welcome and congratulations!! This message demonstrates your ability to send a WhatsApp message notification from the Cloud API, hosted by Meta. Thank you for taking the time to test with us.","{\"header_type\":\"TEXT\",\"header_url\":\"\",\"button_text\":\"\",\"button_url\":\"\"}","WhatsApp Business Platform sample message",NULL,"933219345842144","2026-03-14 06:40:05","2026-03-18 17:53:33");
INSERT INTO `whatsapp_templates` VALUES("2","nss_offer_notification","en_US","MARKETING","APPROVED","Hello {{1}},\n\nNew offer from NSS Apparels.🔥🔥\n\n*Product:* {{2}}\n*Discount:* {{3}}\nPrice: {{4}}.\n**\nGrab the deal before it ends!","{\"header_type\":\"IMAGE\",\"header_url\":\"https:\\/\\/scontent.whatsapp.net\\/v\\/t61.29466-34\\/534423498_1470342671154443_3425077577088659059_n.jpg?ccb=1-7&_nc_sid=8b1bef&_nc_ohc=ZUk5vhdMj0IQ7kNvwF4c3Vq&_nc_oc=Ado34DAquOg3_SfaqV41ibIwuY97GgwAwv9ihpIv4AqUahXODBLdgsl0THce2dBB5rdlvy0CJ-OzhJq3scSu4DYb&_nc_zt=3&_nc_ht=scontent.whatsapp.net&edm=AH51TzQEAAAA&_nc_gid=dB0VEDocqo3S3e3lE0S-zg&_nc_tpa=Q5bMBQHyCx5E4ddkVYmVrDannoVvMdKVx8RZ0NywtjWt3s89T47VkxB_lGORW0bA4d6-uXUPWaHeo306nw&oh=01_Q5Aa4AF1Vr8SRg585rOVrzCtXc-ifiCz8v6xFFg-E6ZB9wvG5Q&oe=69E23DBD\",\"button_text\":\"View website\",\"button_url\":\"https:\\/\\/nssapparels.shop\\/\"}","©2026 NSS APPARELS",NULL,"1470342667821110","2026-03-14 12:47:06","2026-03-18 17:53:33");
INSERT INTO `whatsapp_templates` VALUES("3","nss_product_info","en","MARKETING","APPROVED","Hello 👋\n\nThis is NSS APPARELS – SALEM\n\nWe manufacture & wholesale:\n* Dry Fit T-Shirts\n* Tracks & Shorts\n\n✅ Best wholesale price\n✅ Bulk supply available across India\n\n🛍️ View full product photos & details on our website:\nhttps://nssapparels.shop/\n\n📢 For daily new arrivals & stock updates, please join our WhatsApp broadcast:\nhttps://chat.whatsapp.com/LSRiyk1xdAmFNaIrVhybO4\n\n📱 Kindly save our number for regular updates:\n80987 60720\n\nAfter saving our number, please send a message like “CATALOG” to receive latest product photos.","{\"header_type\":\"IMAGE\",\"header_url\":\"https:\\/\\/scontent.whatsapp.net\\/v\\/t61.29466-34\\/534421849_930050939778028_7150261696699904230_n.jpg?ccb=1-7&_nc_sid=8b1bef&_nc_ohc=h7BbYK7u9c8Q7kNvwGAoRWA&_nc_oc=AdrEjvxjHjCX580rWUsvF1Z9Cpqm5llD68P4jjmahxx_ojxhdIQSvkVz7LeKvVwOBf2xiHdzvMW38_jse1utAQuH&_nc_zt=3&_nc_ht=scontent.whatsapp.net&edm=AH51TzQEAAAA&_nc_gid=dB0VEDocqo3S3e3lE0S-zg&_nc_tpa=Q5bMBQHReepXJ5pYJXnt9RnuwhY2d0x7MYDwlvogs3w4Nll7tKUG2-eHFrBIIMNBQQd4ihNnfEP7RViw1A&oh=01_Q5Aa4AFnLDh9CavGlhptFo1E7st7LdAHNdfrFvW8PIXB1uXHSA&oe=69E2511E\",\"button_text\":\"Visit website\",\"button_url\":\"https:\\/\\/nssapparels.shop\\/\"}","",NULL,"930050936444695","2026-03-18 17:53:33","2026-03-18 17:53:33");
INSERT INTO `whatsapp_templates` VALUES("4","nss_wholesale_notification","en","MARKETING","APPROVED","Hello 👋\n\nThis is NSS APPARELS – SALEM\n\nWe manufacture & wholesale:\n* Dry Fit T-Shirts\n* Tracks & Shorts\n\n✅ Best wholesale price\n✅ Bulk supply available across India\n\n🛍️ View full product photos & details on our website:\nhttps://nssapparels.shop/\n\n📢 For daily new arrivals & stock updates, please join our WhatsApp broadcast:\nhttps://chat.whatsapp.com/LSRiyk1xdAmFNaIrVhybO4\n\n📱 Kindly save our number for regular updates:\n80987 60720\n\nAfter saving our number, please send a message like “CATALOG” to receive latest product photos.","{\"header_type\":\"IMAGE\",\"header_url\":\"https:\\/\\/scontent.whatsapp.net\\/v\\/t61.29466-34\\/588701537_1437834391357095_846792065001220421_n.jpg?ccb=1-7&_nc_sid=8b1bef&_nc_ohc=9SnRQA0Jy1UQ7kNvwGVkbMG&_nc_oc=AdortVwX6_McCqrAJqMvK-vizjSw_RZiu3WgLWW0sEMd1OA_f0tKe66pndvh7u578_Er5Ba3LiHRibTguFRxz6zv&_nc_zt=3&_nc_ht=scontent.whatsapp.net&edm=AH51TzQEAAAA&_nc_gid=dB0VEDocqo3S3e3lE0S-zg&_nc_tpa=Q5bMBQEQBQFT-1mvFugzSFUibErlwsq4eE9WHr473A2ylJq9PrOb6j9Qr-pmfZTp3RoYt5G4MsLFmOgXGw&oh=01_Q5Aa4AFultfn9BH0pyRVV8ILFSaAk0QhGcdUHznTbzInpeWUKQ&oe=69E26E05\",\"button_text\":\"Visit website\",\"button_url\":\"https:\\/\\/nssapparels.shop\\/\"}","",NULL,"1437834388023762","2026-03-18 17:53:33","2026-03-18 17:53:33");


SET FOREIGN_KEY_CHECKS=1;
