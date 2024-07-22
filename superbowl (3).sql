-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 22 juil. 2024 à 22:40
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";






CREATE TABLE `bets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `bets` (`id`, `user_id`, `match_id`, `team_id`, `amount`, `created_at`) VALUES
(3, 1, 1, 1, 1.00, '2024-06-06 20:25:18'),
(7, 6, 1, 1, 3.00, '2024-06-09 00:50:25'),
(8, 9, 3, 7, 20.00, '2024-07-17 00:24:01'),
(9, 9, 3, 6, 20.00, '2024-07-20 20:56:39'),
(10, 9, 1, 1, 1.00, '2024-07-21 00:32:35'),
(14, 9, 12, 25, 10.00, '2024-07-21 19:07:45'),
(16, 16, 5, 10, 0.00, '2024-07-22 18:37:07'),
(17, 16, 9, 19, 10.00, '2024-07-22 18:43:14');





CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `team1` varchar(100) NOT NULL,
  `team2` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` varchar(50) NOT NULL,
  `score` varchar(20) DEFAULT NULL,
  `weather` varchar(50) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `odds_team1` decimal(10,2) DEFAULT NULL,
  `odds_team2` decimal(10,2) DEFAULT NULL,
  `league` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `matches` (`id`, `team1`, `team2`, `date`, `start_time`, `end_time`, `status`, `score`, `weather`, `comments`, `odds_team1`, `odds_team2`, `league`) VALUES
(1, '1', '2', '2026-06-11', '15:00:00', '16:00:00', 'Terminé', '6-0', 'Ensoleillé', 'Match entre New England Patriots et Green Bay Packers', 6.00, 3.60, 'NFL'),
(2, '3', '4', '2022-06-09', '14:00:00', '16:00:00', 'Terminé', '6-0', 'Ensoleillé', 'Match entre San Francisco 49ers et Dallas Cowboys', 1.50, 2.50, 'NFL'),
(3, '5', '6', '2024-08-29', '15:00:00', '17:00:00', 'Terminé', '4-1', 'Nuageux', 'Match entre Seattle Seahawks et Pittsburgh Steelers', 1.80, 2.20, 'NFL'),
(4, '7', '8', '2024-06-07', '16:00:00', '18:00:00', 'Terminé', '2-0', 'Pluvieux', 'Match entre Baltimore Ravens et New Orleans Saints', 1.60, 2.40, 'NFL'),
(5, '9', '10', '2025-06-05', '17:00:00', '19:00:00', 'À venir', 'A VENIR', 'Venté', 'Match entre Kansas City Chiefs et Minnesota Vikings', 1.70, 2.30, 'NFL'),
(6, '11', '12', '2023-09-06', '18:00:00', '20:00:00', 'Terminé', '9-4', 'Ensoleillé', 'Match entre Philadelphia Eagles et Los Angeles Rams', 1.40, 2.30, 'NFL'),
(7, '15', '16', '2023-06-01', '15:00:00', '16:00:00', 'Terminé', '11-2', 'Ensoleillé', 'Match entre New England Patriots et Buffalo Bills', 1.90, 2.10, 'AFC East'),
(9, '19', '20', '2025-06-06', '16:00:00', '18:00:00', 'À venir', 'A VENIR', 'Pluvieux', 'Match entre Baltimore Ravens et Pittsburgh Steelers', 1.75, 2.25, 'AFC North'),
(12, '25', '26', '2025-06-09', '15:00:00', '17:00:00', 'À venir', 'A VENIR', 'Nuageux', 'Match entre Dallas Cowboys et Philadelphia Eagles', 1.70, 2.30, 'NFC East'),
(17, 'Buffalo Bills', 'Baltimore Ravens', '2024-07-22', '20:00:00', '21:00:00', 'A VENIR', NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'Buffalo Bills', 'Baltimore Ravens', '2024-07-22', '18:30:00', '20:00:00', 'A venir', NULL, NULL, NULL, NULL, NULL, NULL),
(24, '9', '11', '2021-05-05', '16:00:00', '20:00:00', 'TERMINE', NULL, NULL, NULL, 3.00, 2.00, NULL);





CREATE TABLE `odds` (
  `id` int(11) NOT NULL,
  `match_id` int(11) DEFAULT NULL,
  `odds_team1` decimal(10,2) DEFAULT NULL,
  `odds_team2` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;





CREATE TABLE `players` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `number` int(11) NOT NULL,
  `team_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `players` (`id`, `first_name`, `last_name`, `number`, `team_id`) VALUES
(1, 'John', 'Doe', 10, 1),
(2, 'Jane', 'Smith', 12, 1),
(3, 'Tom', 'Brown', 9, 2),
(4, 'Emily', 'Davis', 8, 2),
(14, 'Tom', 'Brady', 12, 15),
(15, 'Julian', 'Edelman', 11, 15),
(16, 'Josh', 'Allen', 17, 16),
(17, 'Stefon', 'Diggs', 14, 16),
(18, 'Tua', 'Tagovailoa', 1, 17),
(19, 'Davante', 'Parker', 11, 17),
(20, 'Zach', 'Wilson', 2, 18),
(21, 'C.J.', 'Uzomah', 87, 18),
(22, 'Lamar', 'Jackson', 8, 19),
(23, 'Mark', 'Andrews', 89, 19),
(24, 'Ben', 'Roethlisberger', 7, 20),
(25, 'T.J.', 'Watt', 90, 20),
(26, 'Baker', 'Mayfield', 6, 21),
(27, 'Nick', 'Chubb', 24, 21),
(28, 'Joe', 'Burrow', 9, 22),
(29, 'Jamar', 'Chase', 1, 22),
(30, 'Patrick', 'Mahomes', 15, 23),
(31, 'Travis', 'Kelce', 87, 23),
(32, 'Russell', 'Wilson', 3, 34),
(33, 'DK', 'Metcalf', 14, 34),
(34, 'John', 'Doe', 10, 1),
(35, 'Jane', 'Smith', 9, 1),
(36, 'Emily', 'Johnson', 8, 1),
(37, 'Michael', 'Brown', 7, 1),
(38, 'Sarah', 'Davis', 6, 1),
(39, 'Alice', 'Williams', 11, 2),
(40, 'Bob', 'Miller', 12, 2),
(41, 'Carol', 'Taylor', 13, 2),
(42, 'David', 'Anderson', 14, 2),
(43, 'Eve', 'Thomas', 15, 2),
(44, 'John', 'Doe', 10, 11),
(45, 'Jane', 'Smith', 9, 11),
(46, 'Emily', 'Johnson', 8, 11),
(47, 'Michael', 'Brown', 7, 11),
(48, 'Sarah', 'Davis', 6, 11),
(49, 'Alice', 'Williams', 11, 12),
(50, 'Bob', 'Miller', 12, 12),
(51, 'Carol', 'Taylor', 13, 12),
(52, 'David', 'Anderson', 14, 12),
(53, 'Eve', 'Thomas', 15, 12);





CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `teams` (`id`, `name`, `country`) VALUES
(1, 'San Francisco 49ers', 'USA'),
(2, 'Seattle Seahawks', 'USA'),
(5, 'Green Bay Packers', 'USA'),
(6, 'Minnesota Vikings', 'USA'),
(7, 'Atlanta Falcons', 'USA'),
(8, 'Chicago Bears', 'USA'),
(9, 'New Orleans Saints', 'USA'),
(10, 'Tampa Bay Buccaneers', 'USA'),
(11, 'Los Angeles Chargers', 'USA'),
(12, 'Arizona Cardinals', 'USA'),
(13, 'Baltimore Ravens', 'USA'),
(14, 'Tennessee Titans', 'USA'),
(15, 'New England Patriots', 'USA'),
(16, 'Buffalo Bills', 'USA'),
(17, 'Miami Dolphins', 'USA'),
(18, 'New York Jets', 'USA'),
(19, 'Baltimore Ravens', 'USA'),
(20, 'Pittsburgh Steelers', 'USA'),
(21, 'Cleveland Browns', 'USA'),
(22, 'Cincinnati Bengals', 'USA'),
(23, 'Kansas City Chiefs', 'USA'),
(24, 'Denver Broncos', 'USA'),
(25, 'Dallas Cowboys', 'USA'),
(26, 'Philadelphia Eagles', 'USA'),
(27, 'Washington Commanders', 'USA'),
(28, 'New York Giants', 'USA'),
(29, 'Green Bay Packers', 'USA'),
(30, 'Chicago Bears', 'USA'),
(31, 'Minnesota Vikings', 'USA'),
(32, 'Detroit Lions', 'USA'),
(33, 'San Francisco 49ers', 'USA'),
(34, 'Seattle Seahawks', 'USA');





CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `email`, `role`) VALUES
(1, 'user1', '3549458efc6a3e8c48e5c3d4c7c603e3', '2024-06-05 01:02:35', '', 'admin'),
(2, 'user2', '$2y$10$VbI8yGJbZdxX0JkBG/8quO5pU4O8oR7KU5e1oN1Gu5vKxGz5eiVxO', '2024-06-05 01:05:38', '', ''),
(3, 'user4', 'b51248fc2b7d052c96681b34240e2fbd', '2024-06-05 01:06:11', '', 'user'),
(4, 'user3', '7ae9dab119314a361aa5990e2c70c261', '2024-06-05 01:07:04', '', 'user'),
(5, 'nomuser1', '$2y$10$dsVzhcJAqMzR87MRrgGSw.qZ0VP5rzXD4Xn3.8702yjRzK9GnDGWW', '2024-06-05 01:24:53', '', 'users'),
(6, 'parieur57', '$2y$10$l4dAeqXntDIccfQ4PhirVeqVR2lJzPvbq.FQwYvd7d5RXB0mW7uE.', '2024-06-05 11:00:49', 'parieur@gmail.com', 'admin'),
(7, 'Admin', '$2y$10$JDlUIWdbHwxBfuHI6p22Quzl7kGFMUppWuryIOYz.q4bk8mZnUu8W', '2024-06-29 23:21:45', '', ''),
(8, 'Admin57', '$2y$10$HACGTtw0dwlr75yQa8In9e20lkLSo7vHLxXw3qkX64TsQtOExnvZ.', '2024-06-29 23:22:25', '', 'admin'),
(9, 'parieur573', '$2y$10$cCiY.IJpneFY5m3dkM4myOkeZqjus9nE8YsvkJgHEVyyipKPu7r.y', '2024-07-17 00:22:53', '', 'admin'),
(10, 'MaitreGims', '$2y$10$9BFb/UIaHXi.xE6nfSIfJ.gjvhU/hXJeXqbiIhwKjU7Jm06ciXiFW', '2024-07-20 21:20:11', 'MaitreGimsSuperBet5767@hotmail.fr', ''),
(11, 'MikeTyson', '$2y$10$E56003yO8.8R0RGWAVPBkO8to77p2tTx3LsRiOMF5Nnz7YPGKD.7G', '2024-07-21 01:48:28', 'Miketyson@hotmail.com', 'commentateur'),
(13, 'MikeTyson1', 'MikeTyson1', '2024-07-21 02:08:34', 'commentator@example.com', 'user'),
(14, 'jackson', '$2y$10$78XptQCpyS6fQNLD85GXvO1UY1IQD3s4WODJwR2Yfm/VtKnvHyh2G', '2024-07-21 20:28:06', 'jackson@gmail.com', 'user'),
(15, 'testuser57', '$2y$10$DeWWT17EKdElVAB7GiZV.uLaOOBNrK1RhQalbnTMgXL86uBvPFRnG', '2024-07-21 20:33:26', 'testuser57@hotmail.fr', 'user'),
(16, 'simpleutilisateur', '$2y$10$5DaiZJzk.7vUr69/KNZMYuPBa17b5Jvcob3fnZeiRDILvBmM5VZAa', '2024-07-22 18:36:39', 'simple@gmail.com', 'user');




ALTER TABLE `bets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `team_id` (`team_id`),
  ADD KEY `bets_ibfk_2` (`match_id`);


ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `odds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_id` (`match_id`);


ALTER TABLE `players`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_id` (`team_id`);


ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);




ALTER TABLE `bets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;


ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;


ALTER TABLE `odds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;


ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;


ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;




ALTER TABLE `bets`
  ADD CONSTRAINT `bets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bets_ibfk_2` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bets_ibfk_3` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`);


ALTER TABLE `odds`
  ADD CONSTRAINT `odds_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`);


ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`);
COMMIT;

 