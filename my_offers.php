<?php
session_start();
include "config.php";
include "includes/header.php";

// Sécurité : Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];

// 1. Récupérer l'ID de la boutique
$shop_res = $conn->query("SELECT id FROM shops WHERE user_id = '$user_id'");
$shop = $shop_res->fetch_assoc();
$shop_id = $shop['id'] ?? null;

// 2. Logique de suppression
if(isset($_GET['delete_id'])){
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM offers WHERE id = '$delete_id' AND shop_id = '$shop_id'");
    header("Location: my_offers.php?msg=Supprimé");
}
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Mes Annonces</h2>
        <a href="add_offer.php" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-1"></i> Ajouter une offre
        </a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success rounded-4 shadow-sm border-0">L'annonce a été supprimée.</div>
    <?php endif; ?>

    <div class="row g-4">
        <?php
        // On récupère les offres de la boutique avec le compte des vues
        $query = "SELECT * FROM offers WHERE shop_id = '$shop_id' ORDER BY created_at DESC";
        $result = $conn->query($query);

        if($result && $result->num_rows > 0):
            while($row = $result->fetch_assoc()):
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <img src="assets/img/<?php echo $row['image']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Produit">
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title fw-bold mb-0"><?php echo htmlspecialchars($row['title']); ?></h5>
                        <span class="badge bg-light text-primary border"><?php echo number_format($row['price'], 0, '.', ' '); ?> FDJ</span>
                    </div>
                    <p class="text-muted small mb-3 text-truncate"><?php echo htmlspecialchars($row['description']); ?></p>
                    
                    <div class="d-flex align-items-center text-muted small mb-3">
                        <span class="me-3"><i class="bi bi-eye me-1"></i> <?php echo $row['views'] ?? 0; ?> vues</span>
                        <span><i class="bi bi-geo-alt me-1"></i> <?php echo htmlspecialchars($row['location']); ?></span>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="edit_offer.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-dark btn-sm flex-grow-1 rounded-pill">
                            <i class="bi bi-pencil me-1"></i> Modifier
                        </a>
                        <a href="my_offers.php?delete_id=<?php echo $row['id']; ?>" 
                           class="btn btn-outline-danger btn-sm rounded-pill px-3"
                           onclick="return confirm('Voulez-vous vraiment supprimer cette annonce ?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; else: ?>
        <div class="col-12 text-center py-5">
            <div class="bg-light d-inline-block p-4 rounded-circle mb-3">
                <i class="bi bi-card-list text-muted fs-1"></i>
            </div>
            <p class="text-muted">Vous n'avez pas encore publié d'annonces.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include "includes/footer.php"; ?>