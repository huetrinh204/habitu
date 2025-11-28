-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2025 at 12:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `habitly_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `cmt_id` int(11) NOT NULL,
  `content_cmt` text NOT NULL,
  `created_cmt` datetime NOT NULL DEFAULT current_timestamp(),
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `feedback_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `status_fb` enum('read','unread') NOT NULL,
  `created_fb` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `habit`
--

CREATE TABLE `habit` (
  `habit_id` int(11) NOT NULL,
  `habit_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(10) DEFAULT NULL,
  `start_date` date NOT NULL,
  `current_streak` int(11) NOT NULL,
  `best_streak` int(11) NOT NULL,
  `status` enum('Người dùng','Mẫu') NOT NULL,
  `created_hb` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `last_completed_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `habit`
--

INSERT INTO `habit` (`habit_id`, `habit_name`, `description`, `icon`, `start_date`, `current_streak`, `best_streak`, `status`, `created_hb`, `user_id`, `last_completed_date`) VALUES
(14, 'Đọc sách', 'Đọc 10 phút', '📚', '0000-00-00', 1, 0, 'Mẫu', '2025-11-22 10:50:31', 17, '2025-11-28'),
(15, ' Nghe nhạc ', 'Nghe 10 phút ', '🎧', '0000-00-00', 1, 0, 'Mẫu', '2025-11-22 10:51:21', 17, '2025-11-28'),
(18, 'Chạy bộ', 'Chạy 500m', '🏃', '0000-00-00', 1, 0, 'Mẫu', '2025-11-22 11:59:17', 17, '2025-11-28'),
(20, 'Uống nước', 'Uống 500ml nước', '💧', '0000-00-00', 1, 0, 'Mẫu', '2025-11-23 12:23:07', 17, '2025-11-28'),
(26, 'mẫu', 's', '💧', '0000-00-00', 2, 0, 'Người dùng', '2025-11-28 16:18:53', 15, NULL),
(27, 'đi chơi', 'hr', '🏃', '0000-00-00', 2, 0, 'Người dùng', '2025-11-28 16:47:51', 15, NULL),
(28, 'tieumytest', 'test', '🏃', '0000-00-00', 1, 0, 'Người dùng', '2025-11-28 17:20:37', 18, NULL),
(29, 'test', '1', '💧', '0000-00-00', 1, 0, 'Người dùng', '2025-11-28 18:55:25', 25, '2025-11-28');

-- --------------------------------------------------------

--
-- Table structure for table `habit_logs`
--

CREATE TABLE `habit_logs` (
  `log_id` int(11) NOT NULL,
  `log_date` date NOT NULL DEFAULT current_timestamp(),
  `completed` enum('done','missed') NOT NULL,
  `habit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `habit_logs`
--

INSERT INTO `habit_logs` (`log_id`, `log_date`, `completed`, `habit_id`, `user_id`) VALUES
(1, '2025-11-28', 'done', 20, 15),
(2, '2025-11-28', 'done', 18, 15),
(3, '2025-11-28', 'done', 15, 15),
(4, '2025-11-28', 'done', 14, 15),
(5, '2025-11-28', 'done', 26, 15),
(6, '2025-11-28', 'done', 27, 15),
(7, '2025-11-28', 'done', 20, 18),
(8, '2025-11-28', 'done', 18, 18),
(9, '2025-11-28', 'done', 15, 18),
(10, '2025-11-28', 'done', 14, 18),
(11, '2025-11-28', 'done', 28, 18),
(12, '2025-11-28', 'done', 20, 19),
(13, '2025-11-28', 'done', 18, 19),
(14, '2025-11-28', 'done', 15, 19),
(15, '2025-11-28', 'done', 14, 19),
(16, '2025-11-28', 'done', 20, 20),
(17, '2025-11-28', 'done', 18, 20),
(18, '2025-11-28', 'done', 15, 20),
(19, '2025-11-28', 'done', 14, 20),
(20, '2025-11-28', 'done', 20, 21),
(21, '2025-11-28', 'done', 18, 21),
(22, '2025-11-28', 'done', 15, 21),
(23, '2025-11-28', 'done', 14, 21),
(24, '2025-11-28', 'done', 20, 22),
(25, '2025-11-28', 'done', 18, 22),
(26, '2025-11-28', 'done', 15, 22),
(27, '2025-11-28', 'done', 14, 22),
(28, '2025-11-28', 'missed', 20, 23),
(29, '2025-11-28', 'done', 18, 23),
(30, '2025-11-28', 'done', 15, 23),
(31, '2025-11-28', 'done', 14, 23),
(32, '2025-11-28', 'done', 20, 24),
(33, '2025-11-28', 'done', 18, 24),
(34, '2025-11-28', 'done', 15, 24),
(35, '2025-11-28', 'done', 14, 24),
(36, '2025-11-28', 'done', 20, 25),
(37, '2025-11-28', 'done', 18, 25),
(38, '2025-11-28', 'done', 15, 25),
(39, '2025-11-28', 'done', 14, 25),
(40, '2025-11-28', 'done', 29, 25);

-- --------------------------------------------------------

--
-- Table structure for table `health_journal`
--

CREATE TABLE `health_journal` (
  `journal_id` int(11) NOT NULL,
  `journal_date` date NOT NULL DEFAULT current_timestamp(),
  `title` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `icon` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `noti_id` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `post_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('Nam','Nữ','Khác') NOT NULL,
  `tel` varchar(10) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `create_acc` datetime NOT NULL DEFAULT current_timestamp(),
  `last_activity` datetime NOT NULL DEFAULT current_timestamp(),
  `total_streak` int(11) NOT NULL DEFAULT 0,
  `last_streak_update` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `gender`, `tel`, `role`, `create_acc`, `last_activity`, `total_streak`, `last_streak_update`) VALUES
(10, 'demo', 'demo@gmail.com', '1', 'Nữ', '0969953014', 'user', '2025-11-17 16:10:22', '2025-11-17 16:10:22', 0, NULL),
(14, 'demo', 'hi@gmail.com', '$2y$10$tUBq9LwxWQGaguXTlT1wXevm4tXf5uptHz2mgCTZzRCALBpaEvo9.', 'Nam', '0969953014', 'user', '2025-11-17 16:27:33', '2025-11-17 17:09:28', 0, NULL),
(15, 'meocute', 'meocute@gmail.com', '$2y$10$F9j0bxoYB3hshK1n5sG8See.aQvmtl7N7QdJCavxmmSPRvQOTuHkm', 'Nam', '0969953014', 'user', '2025-11-17 17:12:03', '2025-11-28 17:51:25', 0, NULL),
(16, 'trinh', 'trinh@gmail.com', '$2y$10$0ixAl7Or66uQVDdKlyMC1eDTSHomhDXCyE4g/ny4hGtGISPaSn38O', 'Nữ', '0969953014', 'user', '2025-11-17 18:02:20', '2025-11-17 18:02:28', 0, NULL),
(17, 'cute', 'cute@gmail.com', '$2y$10$eSv7qRb9J4Yq3JvcfIzvr.Vir2OrrVhJ6637EIdyxgLVE.PaSXXVe', 'Nữ', '0969953014', 'admin', '2025-11-20 16:49:59', '2025-11-27 16:24:44', 0, NULL),
(18, 'tieumy', 'tieumy@gmail.com', '$2y$10$LVVSPIi2ajStDvJtxa/F8.C5wy1/M9mzLRM9A13Ko/yWFspFeMbmK', 'Nữ', '0969953014', 'user', '2025-11-28 17:19:56', '2025-11-28 17:52:14', 0, NULL),
(19, 'habit', 'habit@gmail.com', '$2y$10$.30FJm.6KRqmrJ4hRIvojOejVdXucwe27UUeVyAfWdv0DktA.1CFe', 'Nữ', '0969953014', 'user', '2025-11-28 17:58:42', '2025-11-28 17:58:50', 0, NULL),
(20, 'baby', 'baby@gmail.com', '$2y$10$DXgmQRKH59XE.0hExqVh/.tljL05oLatmbzBQ/z4bTcnxPzcC8OrS', 'Nữ', '0969953014', 'user', '2025-11-28 18:03:14', '2025-11-28 18:03:22', 0, NULL),
(21, 'metnha', 'met@gmail.com', '$2y$10$KNaqtH/VVtumTSpQnwEUieGexufFWX5mPDD2wAimd.JbuEVl/x196', 'Nữ', '0969953014', 'user', '2025-11-28 18:08:33', '2025-11-28 18:08:39', 0, NULL),
(22, 'oh', 'oh@gmail.com', '$2y$10$d1Rfht.SmItHbU0ANH0TKuRPf8gV09ar09LWFkqH6UlZkzvePlsAe', 'Nữ', '0969953014', 'user', '2025-11-28 18:16:28', '2025-11-28 18:16:34', 0, NULL),
(23, 'end', 'end@gmail.com', '$2y$10$KQyPd1D0hguihFcX//8b3uVpREbdtxpNdcsKVJ0fmsss/RnNaXr0y', 'Nữ', '0969953014', 'user', '2025-11-28 18:27:38', '2025-11-28 18:27:50', 0, NULL),
(24, 'a', 'a@gmail.com', '$2y$10$tJT3lA2A0w6DUkB54epAs.K3xnzqSlKkdUSm4KZKSJ/mvYGEUfBUC', 'Nam', '1223467986', 'user', '2025-11-28 18:39:18', '2025-11-28 18:39:28', 6, NULL),
(25, 'b', 'b@gmail.com', '$2y$10$UheCMTas3ooVLs3BT1o9BOCM4wEG.wMNtd/Oe7T6VvhpwLZ5JENkC', 'Khác', '0969953014', 'user', '2025-11-28 18:54:51', '2025-11-28 18:54:59', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`cmt_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `habit`
--
ALTER TABLE `habit`
  ADD PRIMARY KEY (`habit_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `habit_logs`
--
ALTER TABLE `habit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `habit_id` (`habit_id`);

--
-- Indexes for table `health_journal`
--
ALTER TABLE `health_journal`
  ADD PRIMARY KEY (`journal_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`noti_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `cmt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `habit`
--
ALTER TABLE `habit`
  MODIFY `habit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `habit_logs`
--
ALTER TABLE `habit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `health_journal`
--
ALTER TABLE `health_journal`
  MODIFY `journal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `noti_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `habit`
--
ALTER TABLE `habit`
  ADD CONSTRAINT `habit_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `habit_logs`
--
ALTER TABLE `habit_logs`
  ADD CONSTRAINT `habit_logs_ibfk_1` FOREIGN KEY (`habit_id`) REFERENCES `habit` (`habit_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `health_journal`
--
ALTER TABLE `health_journal`
  ADD CONSTRAINT `health_journal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
