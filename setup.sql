

USE `attendance_segregator`;

SET NAMES utf8mb4;
SET time_zone = '+05:30';
SET foreign_key_checks = 0;


CREATE TABLE IF NOT EXISTS `events` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`            VARCHAR(255) NOT NULL,
    `venue`           VARCHAR(255) NOT NULL,
    `faculty_coordinator` VARCHAR(100) NOT NULL DEFAULT '',
    `school`          VARCHAR(100) NOT NULL DEFAULT '',
    `phone_number`    VARCHAR(15)  NOT NULL DEFAULT '',
    `event_type`      VARCHAR(100) NOT NULL DEFAULT '',
    `multiday`        TINYINT(1)   NOT NULL DEFAULT 0,
    `date`            DATE         NOT NULL,
    `end_date`        DATE DEFAULT NULL,
    `time`            VARCHAR(50) DEFAULT NULL,
    `days`            JSON DEFAULT NULL,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_events_date (`date`),
    INDEX idx_events_end_date (`end_date`),
    INDEX idx_events_type (`event_type`(50)),
    INDEX idx_events_multiday (`multiday`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




CREATE TABLE IF NOT EXISTS `segregation_history` (
    `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `run_date_range` VARCHAR(100) NOT NULL DEFAULT '',
    `date_from`      DATE NOT NULL,
    `date_to`        DATE NOT NULL,
    `segregated_on`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `events`         JSON DEFAULT NULL,
    `zips`           JSON DEFAULT NULL,

    INDEX idx_history_seg_on (`segregated_on`),
    INDEX idx_history_date_from (`date_from`),
    INDEX idx_history_date_to (`date_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `segregation_stats` (
    `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `history_id`    INT UNSIGNED NOT NULL,
    `school_name`   VARCHAR(100) NOT NULL,
    `event_name`    VARCHAR(255) NOT NULL,
    `student_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `segregated_on` DATETIME NOT NULL,

    CONSTRAINT `fk_stats_history`
        FOREIGN KEY (`history_id`)
        REFERENCES `segregation_history`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    INDEX idx_stats_history (`history_id`),
    INDEX idx_stats_school (`school_name`),
    INDEX idx_stats_event (`event_name`(100)),
    INDEX idx_stats_seg_on (`segregated_on`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `schools` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `school_name` VARCHAR(100) NOT NULL UNIQUE,
    `codes` JSON NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_schools_name (`school_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `schools` (`school_name`, `codes`) VALUES
('SENSE',   '["BVD","bvd","BEC","bec","BML","bml"]'),
('SCOPE',   '["BBS","bbs","BDS","bds","BCT","bct","BCB","bcb","MIC","mic","BAI","bai","MID","mid","BCI","bci","BKT","bkt","BCE","bce"]'),
('SCORE',   '["BIT","bit","BCA","bca","BCS","bcs","MCA","mca","MAG","mag","BYB","byb","BDE","bde","MIS","mis"]'),
('SAS',     '["MDT","mdt","MSP","msp"]'),
('SELECT',  '["BEE","bee","BEL","bel","BEI","bei","MAC","mac"]'),
('SMEC',    '["MMT","mmt","BMV","bmv","BST","bst","BMA","bma","BME","bme","BMM","bmm"]'),
('SBST',    '["BBT","bbt","MSI","msi","MFI","mfi"]'),
('SCE',     '["BCL","bcl"]'),
('SHINE',   '["BHT","bht"]'),
('SCHEME',  '["BCM","bcm"]'),
('VAIAL',   '["BAG","bag"]'),
('SSL',     '["BFN","bfn","BBC","bbc","BCC","bcc","BBP","bbp"]'),
('VITBS',   '["BBA","bba","MBA","mba"]'),
('HOT',     '["BHA","bha"]'),
('VSMART',  '["BVC","bvc","BAM","bam"]'),
('V-SIGN',  '["BID","bid"]'),
('V-SPARC', '["BARC","barc"]');

SET foreign_key_checks = 1;