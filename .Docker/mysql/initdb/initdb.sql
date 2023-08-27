CREATE DATABASE IF NOT EXISTS `cushon` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cushon_api'@'%' IDENTIFIED BY 'T3st1ng';
GRANT ALL ON `cushon`.* to 'cushon_api'@'%';
FLUSH PRIVILEGES;

use cushon;

CREATE TABLE IF NOT EXISTS `retail_customers` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `email_address` VARCHAR(100) NOT NULL,
    `telephone` VARCHAR(20) NULL DEFAULT NULL,
    `mobile` VARCHAR(20) NULL DEFAULT NULL,
    `ni_number` VARCHAR(10) NULL DEFAULT NULL,
    `dob` DATE NOT NULL,
    `address_1` VARCHAR(200) NULL DEFAULT NULL,
    `address_2` VARCHAR(200) NULL DEFAULT NULL,
    `city` VARCHAR(100) NULL DEFAULT NULL,
    `county` VARCHAR(100) NULL DEFAULT NULL,
    `post_code` VARCHAR(20) NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `retail_customers_email_address` (`email_address`)
);

CREATE TABLE IF NOT EXISTS `isas` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `type` VARCHAR(10) NOT NULL,
    `risk_details` TEXT NULL DEFAULT NULL,
    `charge_details` TEXT NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `isas_type` (`type`)
);

CREATE TABLE IF NOT EXISTS `investments` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `retail_customer_id` BIGINT NOT NULL,
    `isa_id` BIGINT NOT NULL,
    `invested_at` DATETIME NOT NULL,
    `lump_sum` DECIMAL (10,2) NOT NULL DEFAULT 0,
    `monthly_sum` DECIMAL (10,2) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`retail_customer_id`) REFERENCES `retail_customers`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (`isa_id`) REFERENCES `isas`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT
);



-- Insert some retail customer details
INSERT INTO `retail_customers` (`id`, `first_name`, `last_name`, `email_address`, `dob`)
VALUES (1, 'Ian', 'Graham', 'ian@igraham.me', '1990-01-01');

INSERT INTO `retail_customers` (`id`, `first_name`, `last_name`, `email_address`, `dob`)
VALUES (2, 'Jess', 'Graham', 'jess@igraham.me', '2015-01-01');


-- Insert the details of the ISAs
INSERT INTO `isas` (`id`, `name`, `type`, `risk_details`, `charge_details`)
VALUES (1, 'Cushon Equities Fund', 'ISA', 'Medium risk, high return', 'Platform charge 0.79%\nFund management 0.20%');

INSERT INTO `isas` (`id`, `name`, `type`, `risk_details`, `charge_details`)
VALUES (2, 'Top Rated Ethical CushonMix', 'ISA', 'Medium risk, medium return', 'Platform charge 0.79%\nFund management 0.21%');

INSERT INTO `isas` (`id`, `name`, `type`, `risk_details`, `charge_details`)
VALUES (3, 'Top Rated CushonMix', 'ISA', 'Medium risk, good return', 'Platform charge 0.79%\nFund management 0.22%');

INSERT INTO `isas` (`id`, `name`, `type`, `risk_details`, `charge_details`)
VALUES (4, 'Junior ISA', 'JISA', 'Low Risk', 'Platform charge 0.79%\nFund management 0.15%');

INSERT INTO `isas` (`id`, `name`, `type`, `risk_details`, `charge_details`)
VALUES (5, 'Lifetime ISA', 'LISA', 'Low Risk, Government contribution matched 25%', 'Platform charge 0.79%\nFund management 0.20%');
