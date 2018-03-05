CREATE TABLE IF NOT EXISTS `#__geocontact_geocontacts` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`caption` VARCHAR(255) NOT NULL,
        `alias` VARCHAR(255) NOT NULL,
	`description` LONGTEXT NOT NULL,
	`stand` VARCHAR(255) NOT NULL,
	`address` VARCHAR(255) NOT NULL,
	`name` VARCHAR(100) NOT NULL,
	`phones` VARCHAR(255) NOT NULL,
	`latlong` VARCHAR(100) NOT NULL,
        `catid` int(11) NOT NULL DEFAULT '0',
        `asset_id` int(10) NOT NULL DEFAULT '0',
	`created_by` INT(11) NOT NULL,
	`state` INT(11) NOT NULL,
	`ordering` INT(11) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT="" DEFAULT COLLATE=utf8_general_ci;
