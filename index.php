<?php
session_start();
include "config.php";
include "includes/header.php";

// Sécurité : Optionnel - Rediriger si non connecté, mais souvent index est public
// if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit(); }
?>

<?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
    <div class="bg-dark py-2 shadow-sm border-bottom border-primary">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="text-white small">
                <i class="bi bi-shield-lock-fill me-2 text-primary"></i>
                <span class="d-none d-md-inline">Mode Admin : <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></span>
            </div>
            <div class="d-flex gap-2">
                <a href="admin_dashboard.php" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">Dashboard</a>
                <a href="admin_validate.php" class="btn btn-outline-light btn-sm rounded-pill px-3">Paiements</a>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="container py-5">
    
    <div class="row mb-5 align-items-center bg-white shadow-sm p-4 rounded-4 border mx-0">
        <div class="col-md-2 text-center mb-3 mb-md-0">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; font-size: 2.5rem;">
                <i class="bi bi-bag-heart"></i>
            </div>
        </div>
        <div class="col-md-10 text-center text-md-start">
            <h1 class="fw-bold mb-1" style="color: #4B0082;">Marché de Djibouti 🇩🇯</h1>
            <p class="text-muted mb-0">Découvrez les meilleures offres publiées par nos marchands certifiés.</p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php 
        // Récupération des offres uniquement des boutiques actives (abonnement payé)
        $sql = "SELECT o.*, s.name as shop_name, s.address as shop_address 
                FROM offers o 
                JOIN shops s ON o.shop_id = s.id 
                WHERE s.is_active = 1
                ORDER BY o.created_at DESC";
        $result = $conn->query($sql);

        if($result && $result->num_rows > 0): 
            while($row = $result->fetch_assoc()): 
                // Vérification de l'image
                $image_path = "assets/img/" . $row['image'];
                $photo_affichee = (!empty($row['image']) && file_exists($image_path)) ? $image_path : "assets/img/default-product.png";
        ?>
                
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 product-card" style="border-radius: 20px; overflow: hidden;">
                        
                        <div class="position-relative">
                            <img src="<?php echo $photo_affichee; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($row['title']); ?>"
                                 style="height: 200px; object-fit: cover;">
                            <span class="position-absolute top-0 end-0 m-3 badge bg-dark bg-opacity-75 shadow-sm rounded-pill">
                                <?php echo ucfirst(htmlspecialchars($row['type'])); ?>
                            </span>
                        </div>

                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-primary fw-bold text-truncate" style="max-width: 120px;">
                                    <i class="bi bi-shop me-1"></i><?php echo htmlspecialchars($row['shop_name']); ?>
                                </small>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i><?php echo htmlspecialchars($row['location']); ?>
                                </small>
                            </div>
                            
                            <h6 class="card-title fw-bold text-dark text-truncate-2 mb-2" title="<?php echo htmlspecialchars($row['title']); ?>">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </h6>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <h5 class="text-success fw-bold mb-0">
                                    <?php echo number_format($row['price'], 0, '.', ' '); ?> <small style="font-size: 0.7rem;">FDJ</small>
                                </h5>
                                <a href="details.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                                    Voir <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm bg-light p-5 rounded-5">
                    <div class="display-1 text-muted mb-3"><i class="bi bi-search"></i></div>
                    <h4 class="fw-bold">Aucune offre disponible</h4>
                    <p class="text-muted">Revenez plus tard ou soyez le premier à publier une annonce !</p>
                    <div class="mt-3">
                        <a href="add_offer.php" class="btn btn-primary rounded-pill px-4">Publier une annonce</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* Design des cartes */
    .product-card { 
        transition: all 0.3s ease; 
        background: #ffffff;
    }
    
    .product-card:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 15px 30px rgba(0,0,0,0.12) !important; 
    }
    
    /* Le fameux code pour couper le texte sur 2 lignes proprement */
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        height: 2.8rem; /* Hauteur fixe pour aligner les boutons en bas */
        line-height: 1.4rem;
    }

    /* Couleurs personnalisées */
    .btn-primary {
        background-color: #4B0082;
        border-color: #4B0082;
    }
    .btn-primary:hover {
        background-color: #3a0066;
        border-color: #3a0066;
    }
    .text-primary { color: #4B0082 !important; }
    .bg-primary { background-color: #4B0082 !important; }
</style>

<?php include "includes/footer.php"; ?>