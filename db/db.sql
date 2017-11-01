-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 31, 2017 at 08:29 PM
-- Server version: 5.6.35
-- PHP Version: 7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `petits_comptes_entre_amis`
--
CREATE DATABASE IF NOT EXISTS `petits_comptes_entre_amis` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `petits_comptes_entre_amis`;

-- --------------------------------------------------------

--
-- Stand-in structure for view `t_coeffsbytransaction`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `t_coeffsbytransaction`;
CREATE TABLE `t_coeffsbytransaction` (
   `transaction_id` int(11)
  ,`sum` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `t_currencies`
--

DROP TABLE IF EXISTS `t_currencies`;
CREATE TABLE `t_currencies` (
  `currency_code` varchar(3) NOT NULL,
  `rounding_multiple` double NOT NULL,
  `full_name` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `t_currencies`
--

INSERT INTO `t_currencies` (`currency_code`, `rounding_multiple`, `full_name`) VALUES
  ('AUD', 0.05, 'Australian Dollar'),
  ('CAD', 0.05, 'Canadian Dollar'),
  ('CHF', 0.05, 'Swiss Franc'),
  ('EUR', 0.01, 'Euro'),
  ('GBP', 0.01, 'Pound sterling'),
  ('JPY', 1, 'Japanese Yen'),
  ('USD', 0.01, 'US Dollar');

-- --------------------------------------------------------

--
-- Table structure for table `t_events`
--

DROP TABLE IF EXISTS `t_events`;
CREATE TABLE `t_events` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(256) NOT NULL,
  `event_description` text,
  `currency_code` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_expenses`
--

DROP TABLE IF EXISTS `t_expenses`;
CREATE TABLE `t_expenses` (
  `transaction_id` int(11) NOT NULL,
  `title` varchar(256) NOT NULL,
  `description` text,
  `amount` double NOT NULL,
  `date` date NOT NULL,
  `buyer_username` varchar(256) NOT NULL,
  `event_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_expense_membership`
--

DROP TABLE IF EXISTS `t_expense_membership`;
CREATE TABLE `t_expense_membership` (
  `transaction_id` int(11) NOT NULL,
  `username` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_group_membership`
--

DROP TABLE IF EXISTS `t_group_membership`;
CREATE TABLE `t_group_membership` (
  `username` varchar(256) NOT NULL,
  `event_id` int(11) NOT NULL,
  `coefficient` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_reimbursement`
--

DROP TABLE IF EXISTS `t_reimbursement`;
CREATE TABLE `t_reimbursement` (
  `reimbursement_id` int(11) NOT NULL,
  `paying_username` varchar(256) NOT NULL,
  `payed_username` varchar(256) NOT NULL,
  `event_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `t_users`
--

DROP TABLE IF EXISTS `t_users`;
CREATE TABLE `t_users` (
  `username` varchar(256) NOT NULL,
  `first_name` varchar(256) DEFAULT NULL,
  `last_name` varchar(256) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `password` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `t_users` (`username`, `first_name`, `last_name`, `email`, `password`) VALUES
  ('maximelovino', 'Maxime', 'Lovino', 'maximelovino@gmail.com',
   '$2y$10$XFsHflm31t8PVP8rMu92Aud9ZlR18jCDnSxoJ9pZ3rUNE6zThVvKi'),
  ('thomasibanez', 'Thomas', 'Ibanez', 'maximelovino@gmail.com',
   '$2y$10$XFsHflm31t8PVP8rMu92Aud9ZlR18jCDnSxoJ9pZ3rUNE6zThVvKi'),
  ('marcolopes', 'Marco', 'Lopes', 'maximelovino@gmail.com',
   '$2y$10$XFsHflm31t8PVP8rMu92Aud9ZlR18jCDnSxoJ9pZ3rUNE6zThVvKi'),
  ('vincenttournier', 'Vincent', 'Tournier', 'maximelovino@gmail.com',
   '$2y$10$XFsHflm31t8PVP8rMu92Aud9ZlR18jCDnSxoJ9pZ3rUNE6zThVvKi'),
  ('francoiseichinger', 'Francois', 'Eichinger', 'maximelovino@gmail.com',
   '$2y$10$XFsHflm31t8PVP8rMu92Aud9ZlR18jCDnSxoJ9pZ3rUNE6zThVvKi'),
  ('matthieuconstant', 'Matthieu', 'Constant', 'maximelovino@gmail.com',
   '$2y$10$XFsHflm31t8PVP8rMu92Aud9ZlR18jCDnSxoJ9pZ3rUNE6zThVvKi'),
  ('cyriliseli', 'Cyril', 'Iseli', 'maximelovino@gmail.com',
   '$2y$10$XFsHflm31t8PVP8rMu92Aud9ZlR18jCDnSxoJ9pZ3rUNE6zThVvKi');

-- --------------------------------------------------------

--
-- Structure for view `t_coeffsbytransaction`
--
DROP TABLE IF EXISTS `t_coeffsbytransaction`;

CREATE ALGORITHM=UNDEFINED DEFINER=`php`@`localhost` SQL SECURITY DEFINER VIEW `t_coeffsbytransaction`  AS  select `t_expenses`.`transaction_id` AS `transaction_id`,sum(`t_group_membership`.`coefficient`) AS `sum` from ((`t_expenses` join `t_expense_membership` on((`t_expenses`.`transaction_id` = `t_expense_membership`.`transaction_id`))) join `t_group_membership` on(((`t_expense_membership`.`username` = `t_group_membership`.`username`) and (`t_expenses`.`event_id` = `t_group_membership`.`event_id`)))) group by `t_expenses`.`transaction_id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_currencies`
--
ALTER TABLE `t_currencies`
  ADD PRIMARY KEY (`currency_code`),
  ADD UNIQUE KEY `t_currencies_currency_code_uindex` (`currency_code`);

--
-- Indexes for table `t_events`
--
ALTER TABLE `t_events`
  ADD PRIMARY KEY (`event_id`),
  ADD UNIQUE KEY `t_events_event_id_uindex` (`event_id`),
  ADD KEY `t_events_t_currencies_currency_code_fk` (`currency_code`);

--
-- Indexes for table `t_expenses`
--
ALTER TABLE `t_expenses`
  ADD PRIMARY KEY (`transaction_id`),
  ADD UNIQUE KEY `t_expenses_transaction_id_uindex` (`transaction_id`),
  ADD KEY `t_expenses_t_users_username_fk` (`buyer_username`),
  ADD KEY `t_expenses_t_events_event_id_fk` (`event_id`);

--
-- Indexes for table `t_expense_membership`
--
ALTER TABLE `t_expense_membership`
  ADD PRIMARY KEY (`transaction_id`,`username`),
  ADD KEY `t_expense_membership_t_users_username_fk` (`username`);

--
-- Indexes for table `t_group_membership`
--
ALTER TABLE `t_group_membership`
  ADD PRIMARY KEY (`username`,`event_id`),
  ADD KEY `t_group_membership_t_events_event_id_fk` (`event_id`);

--
-- Indexes for table `t_reimbursement`
--
ALTER TABLE `t_reimbursement`
  ADD PRIMARY KEY (`reimbursement_id`),
  ADD UNIQUE KEY `t_reimbursement_reimbursement_id_uindex` (`reimbursement_id`),
  ADD KEY `t_reimbursement_t_events_event_id_fk` (`event_id`),
  ADD KEY `t_reimbursement_t_users_username_fk` (`paying_username`),
  ADD KEY `t_reimbursement_t_users_username_payed_fk` (`payed_username`);

--
-- Indexes for table `t_users`
--
ALTER TABLE `t_users`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `t_users_username_uindex` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t_events`
--
ALTER TABLE `t_events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `t_expenses`
--
ALTER TABLE `t_expenses`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `t_reimbursement`
--
ALTER TABLE `t_reimbursement`
  MODIFY `reimbursement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `t_events`
--
ALTER TABLE `t_events`
  ADD CONSTRAINT `t_events_t_currencies_currency_code_fk` FOREIGN KEY (`currency_code`) REFERENCES `t_currencies` (`currency_code`) ON UPDATE CASCADE;

--
-- Constraints for table `t_expenses`
--
ALTER TABLE `t_expenses`
  ADD CONSTRAINT `t_expenses_t_events_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `t_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `t_expenses_t_users_username_fk` FOREIGN KEY (`buyer_username`) REFERENCES `t_users` (`username`) ON UPDATE CASCADE;

--
-- Constraints for table `t_expense_membership`
--
ALTER TABLE `t_expense_membership`
  ADD CONSTRAINT `t_expense_membership_t_expenses_transaction_id_fk` FOREIGN KEY (`transaction_id`) REFERENCES `t_expenses` (`transaction_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `t_expense_membership_t_users_username_fk` FOREIGN KEY (`username`) REFERENCES `t_users` (`username`) ON UPDATE CASCADE;

--
-- Constraints for table `t_group_membership`
--
ALTER TABLE `t_group_membership`
  ADD CONSTRAINT `t_group_membership_t_events_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `t_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `t_group_membership_t_users_username_fk` FOREIGN KEY (`username`) REFERENCES `t_users` (`username`) ON UPDATE CASCADE;

--
-- Constraints for table `t_reimbursement`
--
ALTER TABLE `t_reimbursement`
  ADD CONSTRAINT `t_reimbursement_t_events_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `t_events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `t_reimbursement_t_users_username_fk` FOREIGN KEY (`paying_username`) REFERENCES `t_users` (`username`) ON UPDATE CASCADE,
  ADD CONSTRAINT `t_reimbursement_t_users_username_payed_fk` FOREIGN KEY (`payed_username`) REFERENCES `t_users` (`username`) ON UPDATE CASCADE;

CREATE USER 'php'@'localhost' IDENTIFIED WITH mysql_native_password AS '';
GRANT USAGE ON *.* TO 'php'@'localhost';
GRANT ALL PRIVILEGES ON `petits\_comptes\_entre\_amis`.* TO 'php'@'localhost' WITH GRANT OPTION;
