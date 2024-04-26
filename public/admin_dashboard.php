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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    $userId = $_POST['user_id'];
    $newStatus = $_POST['new_status'];
    $stmt = $pdo->prepare("UPDATE users SET active = ? WHERE id = ?");
    $stmt->execute([$newStatus, $userId]);
    header("Location: admin_dashboard.php");  // Refresh the page to see changes
    exit;
}

// Recherche et affichage des utilisateurs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$stmt = $pdo->prepare("SELECT id, username, email, active FROM users WHERE username LIKE ? OR email LIKE ?");
$stmt->execute(["%$search%", "%$search%"]);
$users = $stmt->fetchAll();

//  GESTION CONTENU  //
// Gestion des actions sur les morceaux
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_morceau'])) {
    $morceauId = $_POST['morceau_id'];
    $stmt = $pdo->prepare("DELETE FROM Morceaux_de_musique WHERE id = ?");
    $stmt->execute([$morceauId]);
}

// Récupération des morceaux de musique
$morceaux = $pdo->query("SELECT id, titre, artiste FROM Morceaux_de_musique")->fetchAll();

// Ajout / Mise à jour des événements
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_event'])) {
        $titre = $_POST['titre'];
        $lieu = $_POST['lieu'];
        $date = $_POST['date'];
        $description = $_POST['description'];

        // Vérifier si un fichier a été téléversé
        if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image']['name'];
            $target_dir = "images/";
            $target_file = $target_dir . basename($image);
            
            // Déplacer le fichier téléversé vers le dossier d'images
            if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Fichier téléversé avec succès, procéder à l'insertion/mise à jour de l'événement
                if ($_POST['event_id']) {
                    // Mise à jour de l'événement
                    $stmt = $pdo->prepare("UPDATE Evenements SET titre = ?, lieu = ?, date = ?, description = ?, image = ? WHERE id = ?");
                    $stmt->execute([$titre, $lieu, $date, $description, $image, $_POST['event_id']]);
                } else {
                    // Ajout d'un nouvel événement
                    $stmt = $pdo->prepare("INSERT INTO Evenements (titre, lieu, date, description, image) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$titre, $lieu, $date, $description, $image]);
                }
            } else {
                // Erreur lors du téléversement du fichier
                echo "Une erreur s'est produite lors du téléversement de l'image.";
            }
        } else {
            // Aucun fichier téléversé ou erreur lors du téléversement
            echo "Veuillez sélectionner une image.";
        }
    } elseif (isset($_POST['delete_event'])) {
        $eventId = $_POST['event_id'];
        $stmt = $pdo->prepare("DELETE FROM Evenements WHERE id = ?");
        $stmt->execute([$eventId]);
    }
}

// Récupération des événements existants
$evenements = $pdo->query("SELECT id, titre, lieu, date, description, image FROM Evenements")->fetchAll();

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
                <a class="nav-link" href="genres.php">Genres</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="musique.php">Musiques</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="events.php">Évènements</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="partager_musique.php">Partager ma musique</a>
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
        <h1 class="mb-4">Tableau de bord administrateur</h1>
        <div class="row">
            <!-- Statistiques -->
            <div class="col-md-4">
                <div class="card bg-light mb-4">
                    <div class="card-header">Utilisateurs</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $totalUsers ?></h5>
                        <p class="card-text">Total utilisateurs inscrits</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light mb-4">
                    <div class="card-header">Morceaux</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $totalMorceaux ?></h5>
                        <p class="card-text">Morceaux de musique téléchargés</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light mb-4">
                    <div class="card-header">Événements</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $totalEvenements ?></h5>
                        <p class="card-text">Événements planifiés</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestion des utilisateurs -->
        <h2 class="mb-4">Gestion des utilisateurs</h2>
        <form action="" method="get" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Rechercher un utilisateur" value="<?= htmlspecialchars($search) ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Rechercher</button>
                </div>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-striped">
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
                                <button type="submit" name="toggle_status" class="btn <?= $user['active'] ? 'btn-warning' : 'btn-success' ?>">
                                    <?= $user['active'] ? 'Désactiver' : 'Activer' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Gestion des morceaux de musique -->
        <h2 class="mb-4">Gestion des morceaux de musique</h2>
        <div class="table-responsive">
            <table class="table table-striped">
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
        </div>

        <!-- Gestion des événements -->
<h2 class="mb-4">Gestion des événements</h2>
<div class="container mt-4">
    <h3>Ajouter un événement</h3>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="titre">Titre de l'événement</label>
            <input type="text" class="form-control" id="titre" name="titre" required>
        </div>
        <div class="form-group">
            <label for="lieu">Lieu</label>
            <input type="text" class="form-control" id="lieu" name="lieu">
        </div>
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" id="date" name="date" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <div class="form-group">
            <label for="image">Image de l'événement</label>
            <input type="file" class="form-control-file" id="image" name="image">
        </div>
        <button type="submit" class="btn btn-primary" name="save_event">Ajouter</button>
    </form>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Lieu</th>
                <th>Date</th>
                <th>Description</th>
                <th>Image</th>
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
                <td><img src="<?= htmlspecialchars($event['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($event['titre']) ?>" style="max-width: 100px;"></td>
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
</div>

        <!-- Rapports d'utilisateurs -->
        <h2 class="mb-4">Rapports d'utilisateurs</h2>
        <div class="table-responsive">
            <table class="table table-striped">
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
        </div>

        <!-- Commentaires signalés -->
        <h2 class="mb-4">Commentaires signalés</h2>
        <div class="table-responsive">
            <table class="table table-striped">
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
        </div>

        <!-- Paramètres du site -->
        <h2 class="mb-4">Paramètres du site</h2>
        <form method="post">
            <div class="form-group">
                <label for="contact_email">Email de contact :</label>
                <input type="email" name="contact_email" id="contact_email" class="form-control" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="privacy_policy">Politique de confidentialité :</label>
                <textarea name="privacy_policy" id="privacy_policy" class="form-control"><?= htmlspecialchars($settings['privacy_policy'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Sauvegarder les modifications</button>
        </form>
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

