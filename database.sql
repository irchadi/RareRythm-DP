-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 26 avr. 2024 à 14:39
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `rare_rythm_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `reported` tinyint(1) DEFAULT 0,
  `report_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `content`, `reported`, `report_id`, `created_at`) VALUES
(1, 1, 'I totally agree with your point!', 0, NULL, '2024-04-22 20:08:38'),
(2, 2, 'This is an inappropriate comment.', 1, 1, '2024-04-22 20:08:38'),
(3, 3, 'Check out this cool website! www.spam.com', 1, 2, '2024-04-22 20:08:38'),
(4, 1, 'Another comment that needs review for being off-topic.', 1, 3, '2024-04-22 20:08:38');

-- --------------------------------------------------------

--
-- Structure de la table `evenements`
--

CREATE TABLE `evenements` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `utilisateur_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `evenements`
--

INSERT INTO `evenements` (`id`, `titre`, `lieu`, `date`, `description`, `image`, `utilisateur_id`) VALUES
(1, 'Festival Indie Rock', 'Parc des Expositions', '2024-07-15', 'Un festival mettant en vedette des artistes indépendants de rock', 'images\\indie rock.jpg', 1),
(2, 'Soirée Electro Indie', 'Warehouse Club', '2024-08-20', 'Une soirée avec des DJs jouant de la musique électro indépendante', 'images\\jxLWCk4.gif', 2);

-- --------------------------------------------------------

--
-- Structure de la table `genres_musicaux`
--

CREATE TABLE `genres_musicaux` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `genres_musicaux`
--

INSERT INTO `genres_musicaux` (`id`, `nom`, `image`) VALUES
(1, 'Indie Rock', 'images\\indie rock.jpg'),
(2, 'Electro Indie', 'images\\6490596579_03cdf42783_z.jpg'),
(3, 'Afro-Cuban', 'images\\14990030881_cb3cd9e0fc - Copie.jpg'),
(4, 'Indie Pop', 'images\\giraffe-2005-giraffe.jpg'),
(5, 'Hard Bop', 'images\\199802_034_depth1 - Copie.jpg'),
(7, 'Swing', 'images/71kxh5HCeDL._SL1200_ - Copie.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `listening_history`
--

CREATE TABLE `listening_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `track_name` varchar(255) NOT NULL,
  `listened_on` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `morceaux_de_musique`
--

CREATE TABLE `morceaux_de_musique` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `artiste` varchar(255) DEFAULT NULL,
  `genre_id` int(11) DEFAULT NULL,
  `fichier_audio` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_upload` timestamp NOT NULL DEFAULT current_timestamp(),
  `utilisateur_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `morceaux_de_musique`
--

INSERT INTO `morceaux_de_musique` (`id`, `titre`, `artiste`, `genre_id`, `fichier_audio`, `description`, `date_upload`, `utilisateur_id`) VALUES
(1, 'Lost in the Dream', 'The War on Drugs', 1, 'the_war_on_drugs_lost_in_the_dream.mp3', 'Un morceau de rock indépendant atmosphérique', '2024-04-22 18:59:58', 1),
(3, 'Holocene', 'Bon Iver', 3, 'bon_iver_holocene.mp3', 'Une chanson folk introspective', '2024-04-22 18:59:58', 3),
(5, 'Two Weeks', 'Grizzly Bear', 5, 'grizzly_bear_two_weeks.mp3', 'Une piste expérimentale avec des harmonies vocales riches', '2024-04-22 18:59:58', 2),
(6, 'Comedy Cha Cha (Make the Drive)', 'AlexGrohl', 3, '662aa820372ae.mp3', 'La musique afro cubaine', '2024-04-25 18:59:44', NULL),
(7, 'Summer Walk', 'Olexy', 1, '662aa963a8457.mp3', 'Indie rock aérien', '2024-04-25 19:05:07', NULL),
(8, 'Stoner Rock - Vocal edit', 'Abydos_Music', 1, '662aaa0fe6bb8.mp3', 'Powerful and energetic ', '2024-04-25 19:07:59', NULL),
(9, 'Powerful Action Indie Rock', 'Rockot', 1, '662aaa91dd9d6.mp3', 'Powerful and motivation indie rock', '2024-04-25 19:10:09', NULL),
(10, 'Stylish Indie Rock', 'ElephantGreen', 1, '662aaafe098d2.mp3', 'Brutal indie Rock', '2024-04-25 19:11:58', NULL),
(11, 'October mood', 'markotopa', 2, '662aab6007f24.mp3', 'indé populaire', '2024-04-25 19:13:36', NULL),
(12, ' Full tech', 'Dubstep-love', 1, '662aabaaddc38.mp3', 'technique electro danse', '2024-04-25 19:14:50', NULL),
(13, 'I Will Take You To The Moon', 'juniorsoundays', 1, '662aabe8cee39.mp3', 'electro cyberpunk', '2024-04-25 19:15:52', NULL),
(14, 'Drive Breakbeat', 'Rockot', 2, '662aac685efda.mp3', 'Drive Breakbeat', '2024-04-25 19:18:00', NULL),
(15, 'New Life', 'saavane', 4, '662aace8c616a.mp3', 'Rythm pop', '2024-04-25 19:20:08', NULL),
(16, 'Jazzy Bop', 'Future_king_creator', 5, '662aad4098f41.mp3', 'Funny jazz Hard Bop', '2024-04-25 19:21:36', NULL),
(17, 'Jazzy Jazz', 'FreeGroove', 5, '662aad9841f5d.mp3', 'Jazz bouncing Be-pop', '2024-04-25 19:23:04', NULL),
(18, 'bebop era 300', 'Darockart', 5, '662aadeebcdf4.mp3', 'Be-pop', '2024-04-25 19:24:30', NULL),
(19, 'Cheerful Electro Swing', 'OpenMusicList', 7, '662aae7cb891c.mp3', 'Elecrto dancing swing', '2024-04-25 19:26:52', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `morceaux_playlists`
--

CREATE TABLE `morceaux_playlists` (
  `morceau_id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `morceaux_playlists`
--

INSERT INTO `morceaux_playlists` (`morceau_id`, `playlist_id`) VALUES
(1, 1),
(3, 2),
(5, 1);

-- --------------------------------------------------------

--
-- Structure de la table `playlists`
--

CREATE TABLE `playlists` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `utilisateur_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `playlists`
--

INSERT INTO `playlists` (`id`, `titre`, `description`, `utilisateur_id`) VALUES
(1, 'Indie Vibes', 'Une sélection des meilleurs morceaux indépendants', 1),
(2, 'Folk Favorites', 'Des chansons folk inspirantes et émotionnelles', 3);

-- --------------------------------------------------------

--
-- Structure de la table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `report_type` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('open','closed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reports`
--

INSERT INTO `reports` (`id`, `user_id`, `report_type`, `description`, `status`, `created_at`) VALUES
(1, 1, 'Harassment', 'User reported harassment in comments.', 'open', '2024-04-22 20:08:38'),
(2, 2, 'Spam', 'Spamming links in multiple posts.', 'open', '2024-04-22 20:08:38'),
(3, 3, 'Inappropriate Content', 'Posted content that is not appropriate for the platform.', 'open', '2024-04-22 20:08:38');

-- --------------------------------------------------------

--
-- Structure de la table `sitesettings`
--

CREATE TABLE `sitesettings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sitesettings`
--

INSERT INTO `sitesettings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'contact_email', 'contact@example.com'),
(2, 'privacy_policy', 'Politique de Confidentialité\nCollecte des informations personnelles\nNous collectons les informations suivantes : nom, prénom, adresse postale, adresse électronique, numéro de téléphone, etc. Les informations personnelles sont collectées par le biais de formulaires et grâce à l\'interactivité établie entre vous et notre site Web.\n\nFormulaires et interactivité:\nVotre information personnelle est collectée par le biais de formulaire, à savoir :\n\nFormulaire d\'inscription au site\nFormulaire de commande\nNous utilisons les informations ainsi collectées pour les finalités suivantes :\n\nSuivi de la commande\nInformations / Offres promotionnelles\nStatistiques');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `profile_pic` varchar(255) DEFAULT NULL,
  `favorite_genre` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `active`, `profile_pic`, `favorite_genre`) VALUES
(1, 'admin', 'irchadi3@hotmail.fr', '$2y$10$wcTSkLpWT238ha1e3wqVU.mAgNBazz3n5kI0za4N9oiL0nisFIWy.', 1, NULL, NULL),
(2, 'aaa', 'aaaaa@gmail.com', '$2y$10$He0VL5k96vFyGPfJl7sEkeEoXh46/iyAeiifAT59TLi79M1qI6s9q', 1, NULL, NULL),
(3, 'bb', 'bb@gmail.com', '$2y$10$F1laEZ6oJfmESLZNnRswj.ilkwhZxMZDKHF01AYGw7JqkqWNufm9q', 1, NULL, NULL),
(4, 'cc', 'cc@gmail.com', '$2y$10$dXRjAxlw1ZcOL/.XluHZKO4Tp6eB9dOXkc4HrRTUPxT.QB9ZcMBEW', 1, NULL, NULL),
(5, 'cc', 'cc@gmail.com', '$2y$10$BJU1vS3NfBZ241be1U5fVeGFAh6r8GJ34NsG0sfmAnNIyf4tys5qa', 1, NULL, NULL),
(6, 'cc', 'cc@gmail.com', '$2y$10$HHpI9aHJ/1eTYzF76ap0XOuB.eVMO1NiRqI6Ud7rOF3Cm9YpUWfnq', 1, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `report_id` (`report_id`);

--
-- Index pour la table `evenements`
--
ALTER TABLE `evenements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `genres_musicaux`
--
ALTER TABLE `genres_musicaux`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `listening_history`
--
ALTER TABLE `listening_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `morceaux_de_musique`
--
ALTER TABLE `morceaux_de_musique`
  ADD PRIMARY KEY (`id`),
  ADD KEY `genre_id` (`genre_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `morceaux_playlists`
--
ALTER TABLE `morceaux_playlists`
  ADD PRIMARY KEY (`morceau_id`,`playlist_id`),
  ADD KEY `playlist_id` (`playlist_id`);

--
-- Index pour la table `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `sitesettings`
--
ALTER TABLE `sitesettings`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `evenements`
--
ALTER TABLE `evenements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `genres_musicaux`
--
ALTER TABLE `genres_musicaux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `listening_history`
--
ALTER TABLE `listening_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `morceaux_de_musique`
--
ALTER TABLE `morceaux_de_musique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `playlists`
--
ALTER TABLE `playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `sitesettings`
--
ALTER TABLE `sitesettings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`);

--
-- Contraintes pour la table `evenements`
--
ALTER TABLE `evenements`
  ADD CONSTRAINT `evenements_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `listening_history`
--
ALTER TABLE `listening_history`
  ADD CONSTRAINT `listening_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `morceaux_de_musique`
--
ALTER TABLE `morceaux_de_musique`
  ADD CONSTRAINT `morceaux_de_musique_ibfk_1` FOREIGN KEY (`genre_id`) REFERENCES `genres_musicaux` (`id`),
  ADD CONSTRAINT `morceaux_de_musique_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `morceaux_playlists`
--
ALTER TABLE `morceaux_playlists`
  ADD CONSTRAINT `morceaux_playlists_ibfk_1` FOREIGN KEY (`morceau_id`) REFERENCES `morceaux_de_musique` (`id`),
  ADD CONSTRAINT `morceaux_playlists_ibfk_2` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`);

--
-- Contraintes pour la table `playlists`
--
ALTER TABLE `playlists`
  ADD CONSTRAINT `playlists_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
