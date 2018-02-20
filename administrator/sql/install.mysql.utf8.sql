CREATE TABLE IF NOT EXISTS `#__geocontact_geocontacts` (
	`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`created_by` INT(11) NOT NULL,
	`state` INT(11) NOT NULL,
	`ordering` INT(11) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT="" DEFAULT COLLATE=utf8_general_ci;
