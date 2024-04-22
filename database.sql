
- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  jeu. 09 jan. 2020 à 12:07
-- Version du serveur :  5.7.19
-- Version de PHP :  7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `registration`
--

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

ALTER TABLE `users`
ADD COLUMN `active` TINYINT(1) NOT NULL DEFAULT 1;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`) VALUES
(1, 'admin', 'aminekouis@gmail.com', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918'),
(2, 'aaa', 'aaaaa@gmail.com', 'ed02457b5c41d964dbd2f2a609d63fe1bb7528dbe55e1abf5b52c249cd735797'),
(3, 'bb', 'bb@gmail.com', '3b64db95cb55c763391c707108489ae18b4112d783300de38e033b4c98c3deaf'),
(4, 'cc', 'cc@gmail.com', '355b1bbfc96725cdce8f4a2708fda310a80e6d13315aec4e5eed2a75fe8032ce'),
(5, 'cc', 'cc@gmail.com', '355b1bbfc96725cdce8f4a2708fda310a80e6d13315aec4e5eed2a75fe8032ce'),
(6, 'cc', 'cc@gmail.com', '355b1bbfc96725cdce8f4a2708fda310a80e6d13315aec4e5eed2a75fe8032ce');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- Création de la table Genres_musicaux
CREATE TABLE IF NOT EXISTS Genres_musicaux (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100)
);

-- Création de la table Morceaux_de_musique
CREATE TABLE IF NOT EXISTS Morceaux_de_musique (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255),
    artiste VARCHAR(255),
    genre_id INT,
    fichier_audio VARCHAR(255),
    description TEXT,
    date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    utilisateur_id INT,
    FOREIGN KEY (genre_id) REFERENCES Genres_musicaux(id),
    FOREIGN KEY (utilisateur_id) REFERENCES users(id) -- Modifier ici
);

-- Création de la table Événements
CREATE TABLE IF NOT EXISTS Evenements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255),
    lieu VARCHAR(255),
    date DATE,
    description TEXT,
    image VARCHAR(255),
    utilisateur_id INT,
    FOREIGN KEY (utilisateur_id) REFERENCES users(id) -- Modifier ici
);

-- Création de la table Playlists
CREATE TABLE IF NOT EXISTS Playlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255),
    description TEXT,
    utilisateur_id INT,
    FOREIGN KEY (utilisateur_id) REFERENCES users(id) -- Modifier ici
);

-- Création de la table Morceaux_Playlists (table de liaison)
CREATE TABLE IF NOT EXISTS Morceaux_Playlists (
    morceau_id INT,
    playlist_id INT,
    FOREIGN KEY (morceau_id) REFERENCES Morceaux_de_musique(id),
    FOREIGN KEY (playlist_id) REFERENCES Playlists(id),
    PRIMARY KEY (morceau_id, playlist_id)
);

-- Création de la table des rapports 
CREATE TABLE IF NOT EXISTS Reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    report_type VARCHAR(100),
    description TEXT,
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Création de la table des commentaires 
CREATE TABLE IF NOT EXISTS Comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    content TEXT,
    reported BOOLEAN DEFAULT FALSE,
    report_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (report_id) REFERENCES Reports(id)
);

-- Création SiteSettings
CREATE TABLE IF NOT EXISTS SiteSettings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(255) NOT NULL,
    setting_value TEXT NOT NULL
);


-- INSERTION DONNEES --

-- Insertion de données dans la table Genres_musicaux
INSERT INTO Genres_musicaux (nom) VALUES
('Indie Rock'),
('Electro Indie'),
('Folk'),
('Indie Pop'),
('Experimental');

-- Insertion de données dans la table Morceaux_de_musique
INSERT INTO Morceaux_de_musique (titre, artiste, genre_id, fichier_audio, description, utilisateur_id) VALUES
('Lost in the Dream', 'The War on Drugs', 1, 'the_war_on_drugs_lost_in_the_dream.mp3', 'Un morceau de rock indépendant atmosphérique', 1),
('Midnight City', 'M83', 2, 'm83_midnight_city.mp3', 'Un morceau électronique indépendant captivant', 2),
('Holocene', 'Bon Iver', 3, 'bon_iver_holocene.mp3', 'Une chanson folk introspective', 3),
('Oxford Comma', 'Vampire Weekend', 4, 'vampire_weekend_oxford_comma.mp3', 'Un morceau indie pop dynamique', 1),
('Two Weeks', 'Grizzly Bear', 5, 'grizzly_bear_two_weeks.mp3', 'Une piste expérimentale avec des harmonies vocales riches', 2);

-- Insertion de données dans la table Evenements
INSERT INTO Evenements (titre, lieu, date, description, image, utilisateur_id) VALUES
('Festival Indie Rock', 'Parc des Expositions', '2024-07-15', 'Un festival mettant en vedette des artistes indépendants de rock', 'festival_indie_rock.jpg', 1),
('Soirée Electro Indie', 'Warehouse Club', '2024-08-20', 'Une soirée avec des DJs jouant de la musique électro indépendante', 'soiree_electro_indie.jpg', 2);

-- Insertion de données dans la table Playlists
INSERT INTO Playlists (titre, description, utilisateur_id) VALUES
('Indie Vibes', 'Une sélection des meilleurs morceaux indépendants', 1),
('Folk Favorites', 'Des chansons folk inspirantes et émotionnelles', 3);

-- Insertion de données dans la table Morceaux_Playlists
INSERT INTO Morceaux_Playlists (morceau_id, playlist_id) VALUES
(1, 1),
(3, 2),
(5, 1);

-- Insertion de données dans la table Reports
INSERT INTO Reports (user_id, report_type, description, status)
VALUES
(1, 'Harassment', 'User reported harassment in comments.', 'open'),
(2, 'Spam', 'Spamming links in multiple posts.', 'open'),
(3, 'Inappropriate Content', 'Posted content that is not appropriate for the platform.', 'open');

-- Insertion de données dans la table Comments
INSERT INTO Comments (user_id, content, reported, report_id)
VALUES
(1, 'I totally agree with your point!', FALSE, NULL),
(2, 'This is an inappropriate comment.', TRUE, 1),
(3, 'Check out this cool website! www.spam.com', TRUE, 2),
(1, 'Another comment that needs review for being off-topic.', TRUE, 3);

-- Insertion de données dans la table SiteSettings
INSERT INTO SiteSettings (setting_key, setting_value) VALUES
('contact_email', 'contact@example.com'),
('privacy_policy', 'Votre politique de confidentialité ici.');


