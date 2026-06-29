CREATE TABLE IF NOT EXISTS `#__geocontact_geocontacts` (
	`id` int unsigned NOT NULL AUTO_INCREMENT,
	`description` LONGTEXT NOT NULL,
	`stand` VARCHAR(255) NOT NULL,
	`address` VARCHAR(255) NOT NULL,
	`name` VARCHAR(100) NOT NULL,
	`phones` VARCHAR(255) NOT NULL,
	`latlong` VARCHAR(100) NOT NULL,
    `catid` INT(11) NOT NULL DEFAULT 0,
    `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT 0,
	`caption` VARCHAR(255) NOT NULL,
    `alias` VARCHAR(255) NOT NULL,
	`created_by` INT(11) NOT NULL,
	`state` INT(11) NOT NULL DEFAULT 1,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
