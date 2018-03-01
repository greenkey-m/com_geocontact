ALTER TABLE `#__geocontact_geocontacts` ADD COLUMN `alias` VARCHAR(255) NOT NULL AFTER `id`;
ALTER TABLE `#__geocontact_geocontacts` ADD COLUMN `catid` int(11) NOT NULL DEFAULT '0' AFTER `caption`;
ALTER TABLE `#__geocontact_geocontacts` ADD COLUMN `asset_id` int(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `catid`;