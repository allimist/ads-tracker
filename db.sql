-- Table: globals
CREATE TABLE `globals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB 
AUTO_INCREMENT=3 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: se
CREATE TABLE `se` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `gclid` varchar(1000) DEFAULT NULL,
  `campaignid` varchar(100) DEFAULT NULL,
  `page_url` varchar(1000) DEFAULT NULL,
  `adgroupid` varchar(100) DEFAULT NULL,
  `keyword` varchar(100) DEFAULT NULL,
  `device` varchar(100) DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB 
AUTO_INCREMENT=811 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_uca1400_ai_ci;

-- Table: pc
CREATE TABLE `pc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `record_source` varchar(20) DEFAULT NULL,
  `event_type` varchar(20) DEFAULT NULL,
  `event_name` varchar(100) DEFAULT NULL,
  `event_id` varchar(200) DEFAULT NULL,
  `date` datetime NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `revenue` int(11) DEFAULT NULL,
  `gclid` varchar(1000) DEFAULT NULL,
  `campaignid` varchar(20) DEFAULT NULL,
  `page_url` varchar(1000) DEFAULT NULL,
  `adgroupid` varchar(100) DEFAULT NULL,
  `keyword` varchar(100) DEFAULT NULL,
  `source` varchar(20) DEFAULT NULL,
  `device` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB 
AUTO_INCREMENT=253 
DEFAULT CHARSET=utf8mb4 
COLLATE=utf8mb4_uca1400_ai_ci;
