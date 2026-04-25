<?php
session_start();
include "config.php";
include "includes/header.php";

// 1. SÉCURITÉ : Vérifier si l'utilisateur est connecté et s'il est ADMIN
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin'){ 
    header("Location: index.php"); 
    exit(); 
}

// 2. RÉCUPÉRATION DES STATISTIQUES POUR LE DASHBOARD
$count_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$count_shops = $conn->query("SELECT COUNT(*) as total FROM shops")->fetch_assoc()['total'];
$count_pending = $conn->query("SELECT COUNT(*) as total FROM payments WHERE status = 'pending'")->fetch_assoc()['total'];
$count_offers = $conn->query("SELECT COUNT(*) as total FROM offers")->fetch_assoc()['total'];
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold mb-0">Tableau de Bord Admin 🛡️</h2>
            <p class="text-muted">Bienvenue, <?php echo htmlspecialchars($_SESSION['user_name']); ?>. Gérez votre marketplace ici.</p>
        </div>
        <a href="index.php" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-eye me-2"></i> Voir le site
        </a>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center h-100">
                <div class="text-primary fs-1 mb-2"><i class="bi bi-people"></i></div>
                <h3 class="fw-bold mb-0"><?php echo $count_users; ?></h3>
                <small class="text-muted text-uppercase fw-bold">Utilisateurs</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center h-100">
                <div class="text-warning fs-1 mb-2"><i class="bi bi-shop"></i></div>
                <h3 class="fw-bold mb-0"><?php echo $count_shops; ?></h3>
                <small class="text-muted text-uppercase fw-bold">Boutiques</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center h-100">
                <div class="text-danger fs-1 mb-2"><i class="bi bi-hourglass-split"></i></div>
                <h3 class="fw-bold mb-0"><?php echo $count_pending; ?></h3>
                <small class="text-muted text-uppercase fw-bold">Attente Paiement</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center h-100">
                <div class="text-success fs-1 mb-2"><i class="bi bi-cart-check"></i></div>
                <h3 class="fw-bold mb-0"><?php echo $count_offers; ?></h3>
                <small class="text-muted text-uppercase fw-bold">Annonces Live</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4 p-4 h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-cash-coin text-success fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Validations</h4>
                        <small class="text-muted">Waafi & D-Money</small>
                    </div>
                </div>
                <p class="text-muted small">Vérifiez les captures d'écran ou les références SMS pour activer les boutiques des vendeurs.</p>
                <a href="admin_validate.php" class="btn btn-success w-100 rounded-pill fw-bold py-2 mt-auto">
                    Gérer les paiements <?php if($count_pending > 0) echo "<span class='badge bg-white text-success ms-2'>$count_pending</span>"; ?>
                </a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4 p-4 h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-person-gear text-primary fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Membres</h4>
                        <small class="text-muted">Clients & Marchands</small>
                    </div>
                </div>
                <p class="text-muted small">Consultez la liste des inscrits, modifiez les rôles ou supprimez les comptes qui ne respectent pas les règles.</p>
                <a href="admin_users.php" class="btn btn-primary w-100 rounded-pill fw-bold py-2 mt-auto">
                    Gérer la communauté
                </a>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Dernières Boutiques créées</h5>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Boutique</th>
                            <th>Propriétaire</th>
                            <th>Statut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_shops = $conn->query("SELECT sh.*, u.name as owner FROM shops sh JOIN users u ON sh.user_id = u.id ORDER BY sh.created_at DESC LIMIT 5");
                        while($s = $recent_shops->fetch_assoc()):
                        ?>
                        <tr>
                            <td class="ps-4 fw-bold"><?php echo htmlspecialchars($s['name']); ?></td>
                            <td><?php echo htmlspecialchars($s['owner']); ?></td>
                            <td>
                                <?php if($s['is_active']): ?>
                                    <span class="badge bg-success-soft text-success rounded-pill px-3">Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-soft text-warning rounded-pill px-3">En attente</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted small"><?php echo date('d/m/Y', strtotime($s['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-success-soft { background-color: #e8f5e9; }
    .bg-warning-soft { background-color: #fff8e1; }
</style>

<?php include "includes/footer.php"; ?>