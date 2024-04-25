<?php
session_start();
require_once '../includes/config.php';  // Connexion BDD

// Vérification si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: login.php");
    exit;
}

//  STATISTIQUES //
// Récupération des statistiques
try {
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalMorceaux = $pdo->query("SELECT COUNT(*) FROM Morceaux_de_musique")->fetchColumn();
    $totalEvenements = $pdo->query("SELECT COUNT(*) FROM Evenements")->fetchColumn();
} catch (PDOException $e) {
    die("Erreur lors de la récupération des statistiques : " . $e->getMessage());
}


//  GESTION USERS //
// Gestion de l'activation/désactivation des utilisateurs
if (isset($_POST['toggle_status'])) {
    $userId = $_POST['user_id'];
    $newStatus = $_POST['new_status'];
    $stmt = $pdo->prepare("UPDATE users SET active = ? WHERE id = ?");
    $stmt->execute([$newStatus, $userId]);
    header("Location: admin_dashboard.php");  // Refresh the page to see changes
    exit;
}

// Recherche et affichage des utilisateurs
$search = $_GET['search'] ?? '';
$stmt = $pdo->prepare("SELECT id, username, email, active FROM users WHERE username LIKE ? OR email LIKE ?");
$stmt->execute(["%$search%", "%$search%"]);
$users = $stmt->fetchAll();

//  GESTION CONTENU  //
// Gestion des actions sur les morceaux
if (isset($_POST['delete_morceau'])) {
    $morceauId = $_POST['morceau_id'];
    $stmt = $pdo->prepare("DELETE FROM Morceaux_de_musique WHERE id = ?");
    $stmt->execute([$morceauId]);
}

// Récupération des morceaux de musique
$morceaux = $pdo->query("SELECT id, titre, artiste FROM Morceaux_de_musique")->fetchAll();

// Ajout / Mise à jour des événements
if (isset($_POST['save_event'])) {
    $titre = $_POST['titre'];
    $lieu = $_POST['lieu'];
    $date = $_POST['date'];
    $description = $_POST['description'];
    if ($_POST['event_id']) {
        // Mise à jour de l'événement
        $stmt = $pdo->prepare("UPDATE Evenements SET titre = ?, lieu = ?, date = ?, description = ? WHERE id = ?");
        $stmt->execute([$titre, $lieu, $date, $description, $_POST['event_id']]);
    } else {
        // Ajout d'un nouvel événement
        $stmt = $pdo->prepare("INSERT INTO Evenements (titre, lieu, date, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titre, $lieu, $date, $description]);
    }
}

// Suppression d'un événement
if (isset($_POST['delete_event'])) {
    $eventId = $_POST['event_id'];
    $stmt = $pdo->prepare("DELETE FROM Evenements WHERE id = ?");
    $stmt->execute([$eventId]);
}

// Récupération des événements existants
$evenements = $pdo->query("SELECT id, titre, lieu, date, description FROM Evenements")->fetchAll();

// GESTION RAPPORT ET COMMENTAIRES //
// Récupération des rapports
$reports = $pdo->query("SELECT Reports.id, Reports.description, Reports.status, Users.username, Reports.created_at FROM Reports JOIN Users ON Reports.user_id = Users.id WHERE Reports.status = 'open'")->fetchAll();

// Récupération des commentaires signalés
$reportedComments = $pdo->query("SELECT Comments.id, Comments.content, Users.username, Comments.created_at FROM Comments JOIN Users ON Comments.user_id = Users.id WHERE Comments.reported = TRUE")->fetchAll();

// GESTION CONTACT ET CONFIDENTIALITE  //
// Récupérer les paramètres
$stmt = $pdo->query("SELECT setting_key, setting_value FROM SiteSettings");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Mettre à jour les paramètres
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $stmt = $pdo->prepare("UPDATE SiteSettings SET setting_value = ? WHERE setting_key = ?");
        $stmt->execute([$value, $key]);
    }
    header("Location: admin_dashboard.php");
    exit;
}



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord administrateur - RareRythm</title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">
    <img src="images/RareRythm logo/logo-transparent-png.png" alt="Logo RareRythm" style="width: 175px;">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="#">Genres</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="musique.php">Musiques</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Évènements</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Partager ma musique</a>
            </li>
            <li class="nav-item">
                <?php if (isset($_SESSION['username'])): ?>
                    <a class="btn btn-primary" href="user_dashboard.php" role="button">
                        <?= htmlspecialchars($_SESSION['username']) ?>
                    </a>
                    <!-- Lien supplémentaire pour l'administrateur -->
                    <?php if ($_SESSION['username'] === 'admin'): ?>
                        <a class="btn btn-warning" href="admin_dashboard.php" role="button">Admin Dashboard</a>
                    <?php endif; ?>
                    <a class="btn btn-secondary" href="logout.php" role="button">Déconnexion</a>
                <?php else: ?>
                    <a class="btn btn-primary" href="login.php" role="button">Connexion</a>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</nav>
    </header>
    
    <main class="container mt-4">
        <h1>Tableau de bord administrateur</h1>
        <div class="row text-center">
            <div class="col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-header">Utilisateurs</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $totalUsers ?></h5>
                        <p class="card-text">Total utilisateurs inscrits</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-header">Morceaux</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $totalMorceaux ?></h5>
                        <p class="card-text">Morceaux de musique téléchargés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-header">Événements</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $totalEvenements ?></h5>
                        <p class="card-text">Événements planifiés</p>
                    </div>
                </div>
            </div>
        </div>
        <h2>Gestion des utilisateurs</h2>
        <form action="" method="get">
            <input type="text" name="search" placeholder="Rechercher un utilisateur" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Rechercher</button>
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom d'utilisateur</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= $user['active'] ? 'Actif' : 'Inactif' ?></td>
                    <td>
                        <form action="" method="post">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <input type="hidden" name="new_status" value="<?= $user['active'] ? 0 : 1 ?>">
                            <button type="submit" name="toggle_status" class="btn btn-<?= $user['active'] ? 'warning' : 'success' ?>">
                                <?= $user['active'] ? 'Désactiver' : 'Activer' ?>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h2>Gestion des morceaux de musique</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Artiste</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($morceaux as $morceau): ?>
                    <tr>
                        <td><?= htmlspecialchars($morceau['titre']) ?></td>
                        <td><?= htmlspecialchars($morceau['artiste']) ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="morceau_id" value="<?= $morceau['id'] ?>">
                                <button type="submit" name="delete_morceau" class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <section>
                <h2>Gestion des événements</h2>
                <form method="post">
                    <input type="hidden" name="event_id" value="">
                    <input type="text" name="titre" placeholder="Titre de l'événement" required>
                    <input type="text" name="lieu" placeholder="Lieu">
                    <input type="date" name="date" required>
                    <textarea name="description" placeholder="Description"></textarea>
                    <button type="submit" name="save_event">Enregistrer</button>
                </form>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Lieu</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($evenements as $event): ?>
                            <tr>
                                <td><?= htmlspecialchars($event['titre']) ?></td>
                                <td><?= htmlspecialchars($event['lieu']) ?></td>
                                <td><?= htmlspecialchars($event['date']) ?></td>
                                <td><?= htmlspecialchars($event['description']) ?></td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                        <button type="submit" name="delete_event" class="btn btn-danger">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
                <section>
    <h2>Rapports d'utilisateurs</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Statut</th>
                <th>Signalé par</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report): ?>
            <tr>
                <td><?= $report['id'] ?></td>
                <td><?= htmlspecialchars($report['description']) ?></td>
                <td><?= $report['status'] ?></td>
                <td><?= htmlspecialchars($report['username']) ?></td>
                <td><?= $report['created_at'] ?></td>
                <td>
                    <button class="btn btn-success">Fermer</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section>
    <h2>Commentaires signalés</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Contenu</th>
                <th>Signalé par</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reportedComments as $comment): ?>
            <tr>
                <td><?= $comment['id'] ?></td>
                <td><?= htmlspecialchars($comment['content']) ?></td>
                <td><?= htmlspecialchars($comment['username']) ?></td>
                <td><?= $comment['created_at'] ?></td>
                <td>
                    <button class="btn btn-warning">Réviser</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<section>
    <h2>Paramètres du site</h2>
    <form method="post">
        <div>
            <label for="contact_email">Email de contact :</label>
            <input type="email" name="contact_email" id="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">
        </div>
        <div>
            <label for="privacy_policy">Politique de confidentialité :</label>
            <textarea name="privacy_policy" id="privacy_policy"><?= htmlspecialchars($settings['privacy_policy'] ?? '') ?></textarea>
        </div>
        <button type="submit">Sauvegarder les modifications</button>
    </form>
</section>


    </main>

    <footer class="bg-light text-center text-lg-start">
    <div class="container p-4">
        <div class="row">
            <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase">Contactez-nous</h5>
                <p>Email : <a href="mailto:<?= htmlspecialchars($settings['contact_email']) ?>"><?= htmlspecialchars($settings['contact_email']) ?></a></p>
            </div>
            <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase">Politique de confidentialité</h5>
                <a href="privacy_policy.php">Lire notre politique</a>
            </div>
        </div>
    </div>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © 2024 RareRythm - Tous droits réservés
    </div>
</footer>

</body>
</html>

