<?php
session_start();
include "config.php";
include "includes/header.php";

// 1. SÉCURITÉ : Vérification du rôle marchand
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'merchant'){ 
    header("Location: login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];

// 2. RÉCUPÉRATION INFOS BOUTIQUE
// Utilisation de mysqli_real_escape_string par sécurité
$user_id_clean = mysqli_real_escape_string($conn, $user_id);
$shop_res = $conn->query("SELECT * FROM shops WHERE user_id='$user_id_clean'");
$shop = $shop_res->fetch_assoc();
$shop_id = $shop['id'] ?? 0;

// 3. RÉCUPÉRATION ET CALCUL DE L'ABONNEMENT RÉEL
$jours_restants = 0;
$statut_boutique = "Inactif";
$couleur_statut = "#feca57"; // Orange (Inactif/Expiré)

if ($shop_id > 0) {
    // On cherche l'abonnement le plus récent pour cette boutique
    $sub_res = $conn->query("SELECT end_date, is_active FROM subscriptions WHERE shop_id='$shop_id' ORDER BY end_date DESC LIMIT 1");
    $sub = $sub_res->fetch_assoc();

    if ($sub) {
        $date_actuelle = new DateTime();
        $date_fin = new DateTime($sub['end_date']);
        
        if ($date_fin > $date_actuelle && $sub['is_active'] == 1) {
            // CAS ACTIF : Date non dépassée ET validé par l'admin
            $intervalle = $date_actuelle->diff($date_fin);
            $jours_restants = $intervalle->days;
            $statut_boutique = "Actif";
            $couleur_statut = "#1dd1a1"; // Vert
        } elseif ($sub['is_active'] == 0) {
            // CAS EN ATTENTE : Payé mais pas encore validé
            $statut_boutique = "En attente";
            $couleur_statut = "#48dbfb"; // Bleu ciel
        } else {
            // CAS EXPIRÉ : Date dépassée
            $statut_boutique = "Expiré";
            $couleur_statut = "#ff5e57"; // Rouge
        }
    }
}

// 4. STATISTIQUES RÉELLES
$count_offers = 0;
$total_vues_reelles = 0;
$recent_offers = null;

if ($shop_id > 0) {
    // Nombre d'annonces
    $count_offers = $conn->query("SELECT COUNT(*) as total FROM offers WHERE shop_id='$shop_id'")->fetch_assoc()['total'] ?? 0;
    
    // Somme des vues
    $views_data = $conn->query("SELECT SUM(views) as total_vues FROM offers WHERE shop_id='$shop_id'")->fetch_assoc();
    $total_vues_reelles = $views_data['total_vues'] ?? 0;
    
    // Liste des 5 dernières annonces
    $recent_offers = $conn->query("SELECT * FROM offers WHERE shop_id='$shop_id' ORDER BY created_at DESC LIMIT 5");
}
?>

<div class="container-fluid px-4">
    <div class="row mb-4 mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px; background: linear-gradient(90deg, #4B0082, #6A0DAD);">
                <div class="row align-items-center g-0">
                    <div class="col-md-2 text-center p-3">
                        <img src="assets/img/image.webp" class="img-fluid rounded-circle shadow" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid white;" onerror="this.src='https://via.placeholder.com/80'">
                    </div>
                    <div class="col-md-10 p-4 text-white">
                        <h2 class="fw-bold mb-1">Tableau de bord Marchand</h2>
                        <p class="mb-0 opacity-75">Bienvenue, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong> 🇩🇯</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(!$shop): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning border-0 shadow-sm d-flex justify-content-between align-items-center p-3" style="border-radius: 15px;">
                <div>
                    <i class="bi bi-shop-window me-2 h5"></i>
                    <strong>Action requise :</strong> Vous n'avez pas encore configuré votre boutique.
                </div>
                <a href="create_shop.php" class="btn btn-dark fw-bold rounded-pill px-4">Créer ma boutique</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-3 mb-4 text-white text-center">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="background-color: #5758bb; border-radius: 15px;">
                <h3 class="fw-bold mb-0"><?php echo $count_offers; ?></h3>
                <small>Annonces en ligne</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="background-color: #1dd1a1; border-radius: 15px;">
                <h3 class="fw-bold mb-0"><?php echo number_format($total_vues_reelles, 0, '.', ' '); ?></h3>
                <small>Vues totales</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="background-color: <?php echo $couleur_statut; ?>; border-radius: 15px;">
                <h3 class="fw-bold mb-0"><?php echo $statut_boutique; ?></h3>
                <small>Statut Boutique</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3" style="background-color: #48dbfb; border-radius: 15px;">
                <h3 class="fw-bold mb-0"><?php echo $jours_restants; ?>j</h3>
                <small>Abonnement restant</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">Mes articles récents</h5>
                    <?php if($shop_id > 0): ?>
                        <a href="my_offers.php" class="btn btn-sm btn-link text-decoration-none">Voir tout</a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small">
                                <tr>
                                    <th class="ps-4">Image</th>
                                    <th>Titre</th>
                                    <th>Prix</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($shop_id > 0 && $recent_offers && $recent_offers->num_rows > 0): ?>
                                    <?php while($item = $recent_offers->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <img src="assets/img/<?php echo htmlspecialchars($item['image']); ?>" class="rounded shadow-sm" style="width: 50px; height: 40px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/50x40'">
                                        </td>
                                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($item['title']); ?></td>
                                        <td class="text-success fw-bold"><?php echo number_format($item['price'], 0, '.', ' '); ?> FDJ</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="edit_offer.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill me-2">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="delete_offer.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Supprimer cet article ?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center p-5 text-muted">
                                            <i class="bi bi-box2 mb-2 d-block h2 opacity-25"></i>
                                            Aucun produit publié pour le moment.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-center rounded-4 mb-4 bg-white">
                <h5 class="fw-bold mb-3 text-dark">Actions Rapides</h5>
                
                <?php if($shop_id == 0): ?>
                    <a href="create_shop.php" class="btn btn-dark btn-lg w-100 mb-3 rounded-pill py-3 fw-bold">
                        Créer ma Boutique
                    </a>
                <?php elseif($jours_restants <= 0 && $statut_boutique != "En attente"): ?>
                    <div class="alert alert-danger small py-2 mb-3">Accès restreint : Abonnez-vous !</div>
                    <a href="subscribe.php" class="btn btn-warning btn-lg w-100 mb-3 rounded-pill py-3 fw-bold shadow-sm">
                        <i class="bi bi-lightning-fill me-2"></i> Activer mon Pack
                    </a>
                <?php else: ?>
                    <a href="add_offer.php" class="btn btn-primary btn-lg w-100 mb-3 rounded-pill shadow-sm py-3 fw-bold">
                        <i class="bi bi-plus-circle me-2"></i> Ajouter un article
                    </a>
                <?php endif; ?>

                <a href="shop_settings.php" class="btn btn-outline-dark w-100 rounded-pill py-2 mb-2">
                    <i class="bi bi-gear me-2"></i> Paramètres boutique
                </a>
                
                <hr class="my-3">
                
                <div class="p-3 bg-light rounded-3 text-start">
                    <p class="small text-muted mb-1 text-center">Assistance Marchand</p>
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="bi bi-whatsapp text-success fs-4 me-2"></i>
                        <span class="fw-bold">+253 77 47 71 39</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>