CREATE TABLE `tbl_bank_accounts` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `vendor_id` int NOT NULL,
  `bank_account_name` varchar(255) NOT NULL,
  `bank_account_number` varchar(255) NOT NULL
);

CREATE TABLE `tbl_requirements` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `vendors_id` int NOT NULL,
  `manufacturers_certificate` varchar(255) NOT NULL,
  `dealership_certificate` varchar(255) NOT NULL,
  `sample_receipt` varchar(255) NOT NULL,
  `warranty` varchar(255) NOT NULL,
  `authorized_service_center` varchar(255) NOT NULL,
  `business_permit` varchar(255) NOT NULL,
  `BIR_permit` varchar(255) NOT NULL,
  `DTI_certificate` varchar(255) NOT NULL,
  `SEC_certificate` varchar(255) NOT NULL,
  `company_profile` varchar(255) NOT NULL,
  `valid_ID_no_1` varchar(255) NOT NULL,
  `valid_ID_no_2` varchar(255) NOT NULL,
  `price_list` varchar(255),
  `philgeps` varchar(255),
  `latest_financial_statement` varchar(255),
  `NTC_permit` varchar(255)
);

CREATE TABLE `tbl_vendors` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `vendor_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) NOT NULL,
  `country_code` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `vendor_status` ENUM ('verified', 'not_verified') NOT NULL,
  `delivery_term` varchar(255) NOT NULL,
  `payment_term` varchar(255) NOT NULL,
  `shipping_preparation_day` ENUM ('2_to_3_business_days', '3_to_5_business_days', '5_to_7_business_days', '1_to_2_weeks') NOT NULL,
  `nature_of_business` ENUM ('corporation', 'partnership', 'sole_proprietorship') NOT NULL,
  `business_tax_type` ENUM ('vat', 'non_vat') NOT NULL,
  `courier` ENUM ('edgardo_ibasco_to_daily_overland', 'lalamove_to_dailyoverland', 'j_and_t', 'lazada', 'shopee_express', 'lbc') NOT NULL,
  `document_expiration_date` date NOT NULL COMMENT 'The document should expire on the set date. After expiration, the vendors status should change to non-verified',
  `is_manufacturer` tinyint NOT NULL COMMENT '0-no, 1-yes',
  `is_distributor` tinyint NOT NULL COMMENT '0-no, 1-yes',
  `is_dealer` tinyint NOT NULL COMMENT '0-no, 1-yes',
  `is_reseller` tinyint NOT NULL COMMENT '0-no, 1-yes',
  `status` ENUM ('active', 'inactive', 'update', 'archive') NOT NULL
);

ALTER TABLE `tbl_bank_accounts` ADD CONSTRAINT `tbl_bank_accounts_fk_1` FOREIGN KEY (`vendor_id`) REFERENCES `tbl_vendors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tbl_requirements` ADD CONSTRAINT `tbl_requirements_fk_1` FOREIGN KEY (`vendors_id`) REFERENCES `tbl_vendors` (`id`);