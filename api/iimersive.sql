-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : sam. 04 mars 2023 à 13:23
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `iimersive`
--

-- --------------------------------------------------------

--
-- Structure de la table `likes`
--

CREATE TABLE `likes` (
  `PFK_post_id` varchar(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `PFK_user_handle` varchar(15) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `PK_message_id` varchar(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `FK_author_handle` varchar(15) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `FK_recipient_handle` varchar(15) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `PK_notification_id` varchar(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `FK_recipient_handle` varchar(15) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `icon_path` varchar(20) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

CREATE TABLE `posts` (
  `PK_post_id` varchar(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `FK_reply_to` varchar(36) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `FK_author_handle` varchar(15) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `tag` tinyint UNSIGNED NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `media_list_paths` varchar(80) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `views_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `PK_session_id` varchar(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `FK_user_handle` varchar(15) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` text CHARACTER SET ascii COLLATE ascii_bin,
  `user_agent` text CHARACTER SET ascii COLLATE ascii_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `PK_user_handle` varchar(15) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `display_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email` varchar(320) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `passhash` text CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `biography` text,
  `avatar_path` varchar(20) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `banner_path` varchar(20) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`PFK_post_id`,`PFK_user_handle`),
  ADD KEY `C_likes_user_handle` (`PFK_user_handle`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`PK_message_id`),
  ADD KEY `C_message_author_handle` (`FK_author_handle`),
  ADD KEY `C_message_recipient_handle` (`FK_recipient_handle`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`PK_notification_id`),
  ADD KEY `C_recipient_handle` (`FK_recipient_handle`);

--
-- Index pour la table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`PK_post_id`),
  ADD KEY `C_posts_reply_to` (`FK_reply_to`),
  ADD KEY `C_posts_author_handle` (`FK_author_handle`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`PK_session_id`),
  ADD KEY `C_sessions_user_handle` (`FK_user_handle`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`PK_user_handle`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `C_likes_post_id` FOREIGN KEY (`PFK_post_id`) REFERENCES `posts` (`PK_post_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `C_likes_user_handle` FOREIGN KEY (`PFK_user_handle`) REFERENCES `users` (`PK_user_handle`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `C_message_author_handle` FOREIGN KEY (`FK_author_handle`) REFERENCES `users` (`PK_user_handle`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `C_message_recipient_handle` FOREIGN KEY (`FK_recipient_handle`) REFERENCES `users` (`PK_user_handle`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `C_recipient_handle` FOREIGN KEY (`FK_recipient_handle`) REFERENCES `users` (`PK_user_handle`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `C_posts_author_handle` FOREIGN KEY (`FK_author_handle`) REFERENCES `users` (`PK_user_handle`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `C_posts_reply_to` FOREIGN KEY (`FK_reply_to`) REFERENCES `posts` (`PK_post_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `C_sessions_user_handle` FOREIGN KEY (`FK_user_handle`) REFERENCES `users` (`PK_user_handle`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
