<?php
session_start();
include "config.php";
include "includes/header.php";

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // 1. Récupération du produit, de la boutique ET du téléphone du vendeur via les jointures
    $res = $conn->query("SELECT o.*, s.name as shop_name, s.user_id as seller_id, u.phone as seller_phone 
                         FROM offers o 
                         JOIN shops s ON o.shop_id = s.id 
                         JOIN users u ON s.user_id = u.id
                         WHERE o.id = '$id'");
    $item = $res->fetch_assoc();

    if (!$item) {
        header("Location: index.php");
        exit();
    }

    // --- LOGIQUE DE VUE UNIQUE ---
    if (isset($_SESSION['user_id'])) {
        $current_user_id = $_SESSION['user_id'];
        $seller_id = $item['seller_id'];

        // On n'incrémente pas si c'est le vendeur qui regarde son propre produit
        if ($current_user_id != $seller_id) {
            $check_history = $conn->query("SELECT id FROM view_history WHERE user_id = '$current_user_id' AND offer_id = '$id'");
            
            if ($check_history->num_rows == 0) {
                $conn->query("UPDATE offers SET views = views + 1 WHERE id = '$id'");
                $conn->query("INSERT INTO view_history (user_id, offer_id) VALUES ('$current_user_id', '$id')");
                $item['views'] += 1;
            }
        }
    } 
    else {
        // Pour les visiteurs non-connectés (système de cookie)
        $cookie_name = "guest_view_" . $id;
        if (!isset($_COOKIE[$cookie_name])) {
            $conn->query("UPDATE offers SET views = views + 1 WHERE id = '$id'");
            setcookie($cookie_name, "1", time() + (365 * 24 * 3600), "/");
            $item['views'] += 1;
        }
    }

    // Préparation du numéro WhatsApp
    $seller_phone = $item['seller_phone'] ?? '';
    $phone_to_whatsapp = str_replace(' ', '', $seller_phone);
    
    // Formatage pour Djibouti (8 chiffres -> ajout du 253)
    if (strlen($phone_to_whatsapp) == 8) {
        $phone_to_whatsapp = "253" . $phone_to_whatsapp;
    }

} else {
    header("Location: index.php");
    exit();
}
?>

<div class="container py-5">
    <div class="row g-5">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <img src="assets/img/<?php echo $item['image']; ?>" class="img-fluid" style="width: 100%; height: 450px; object-fit: cover;" alt="<?php echo htmlspecialchars($item['title']); ?>">
            </div>
        </div>

        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
                    <li class="breadcrumb-item active text-capitalize"><?php echo htmlspecialchars($item['type']); ?></li>
                </ol>
            </nav>

            <h1 class="fw-bold text-dark mb-3"><?php echo htmlspecialchars($item['title']); ?></h1>
            
            <div class="d-flex align-items-center mb-4">
                <h2 class="text-primary fw-bold mb-0"><?php echo number_format($item['price'], 0, '.', ' '); ?> FDJ</h2>
                <span class="ms-3 badge bg-light text-muted border py-2 px-3 rounded-pill">
                    <i class="bi bi-eye me-1"></i> <?php echo $item['views']; ?> vues
                </span>
            </div>
            
            <div class="p-4 bg-light rounded-4 mb-4">
                <h6 class="fw-bold text-muted small text-uppercase mb-2">Description du produit</h6>
                <p class="mb-0 text-secondary"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
            </div>

            <div class="card border-0 bg-white shadow-sm p-3 mb-4 rounded-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-shop fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($item['shop_name']); ?></h6>
                        <small class="text-muted"><i class="bi bi-geo-alt"></i> Djibouti</small>
                    </div>
                </div>
            </div>

            <?php if (!empty($phone_to_whatsapp)): ?>
                <div class="d-grid gap-2">
                    <a href="https://wa.me/<?php echo $phone_to_whatsapp; ?>?text=Bonjour, je suis intéressé par votre produit : <?php echo urlencode($item['title']); ?>" 
                       class="btn btn-success btn-lg rounded-pill py-3 shadow-sm fw-bold">
                        <i class="bi bi-whatsapp me-2"></i> Contacter le vendeur
                    </a>
                    
                    <a href="tel:<?php echo $phone_to_whatsapp; ?>" 
                       class="btn btn-outline-primary btn-lg rounded-pill py-3 fw-bold">
                        <i class="bi bi-telephone me-2"></i> Appeler directement
                    </a>
                </div>
            <?php else: ?>
                <div class="alert alert-warning rounded-4 small">
                    <i class="bi bi-exclamation-triangle me-2"></i> Le vendeur n'a pas renseigné de numéro de téléphone.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>