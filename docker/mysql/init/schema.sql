CREATE DATABASE IF NOT EXISTS loan_engine;

USE loan_engine;

CREATE TABLE `internal_users`
(
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id` VARCHAR(50)  NOT NULL UNIQUE,
    `name`        VARCHAR(100) NOT NULL,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `customer_users`
(
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `id_number`  VARCHAR(50)  NOT NULL UNIQUE COMMENT 'Borrower KTP/ID number',
    `name`       VARCHAR(100) NOT NULL,
    `email`      VARCHAR(100) NOT NULL UNIQUE,
    `role`       ENUM('borrower', 'investor') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `loans`
(
    `id`                      INT AUTO_INCREMENT PRIMARY KEY,
    `borrower_id`             INT            NOT NULL,
    `principal_amount`        DECIMAL(15, 2) NOT NULL,
    `rate`                    DECIMAL(5, 2)  NOT NULL COMMENT 'Interest rate for borrower',
    `roi`                     DECIMAL(5, 2)  NOT NULL COMMENT 'Return on Investment for investors',
    `status`                  ENUM('proposed', 'approved', 'invested', 'disbursed') NOT NULL DEFAULT 'proposed',
    `agreement_letter_link`   VARCHAR(255) NULL,
    `validator_employee_id`   INT NULL,
    `approval_date`           DATETIME NULL,
    `proof_picture`           LONGTEXT NULL COMMENT 'Base64 encoded image',
    `officer_employee_id`     INT NULL,
    `disbursement_date`       DATETIME NULL,
    `signed_agreement_letter` LONGTEXT NULL COMMENT 'Base64 encoded pdf/jpeg',
    `created_at`              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`              TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (`borrower_id`) REFERENCES `customer_users` (`id`),
    FOREIGN KEY (`validator_employee_id`) REFERENCES `internal_users` (`id`),
    FOREIGN KEY (`officer_employee_id`) REFERENCES `internal_users` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `investments`
(
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `loan_id`     INT            NOT NULL,
    `investor_id` INT            NOT NULL,
    `amount`      DECIMAL(15, 2) NOT NULL,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`investor_id`) REFERENCES `customer_users` (`id`),
    UNIQUE KEY `loan_investor_unique` (`loan_id`, `investor_id`)
) ENGINE=InnoDB;

-- Seed some initial data for testing
INSERT INTO `internal_users` (`employee_id`, `name`)
VALUES ('EMP-001', 'Validator Staff'),
       ('EMP-002', 'Field Officer');
INSERT INTO `customer_users` (`id_number`, `name`, `email`, `role`)
VALUES ('3201234567890001', 'Budi Borrower', 'budi@example.com', 'borrower'),
       ('3201234567890002', 'Indah Investor', 'indah@example.com', 'investor'),
       ('3201234567890003', 'Cahyo Investor', 'cahyo@example.com', 'investor');