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
$message = "";

// 1. RÉCUPÉRATION DE LA BOUTIQUE ET VÉRIFICATION DE L'ABONNEMENT
// On cherche d'abord la boutique de l'utilisateur
$shop_res = $conn->query("SELECT id FROM shops WHERE user_id = '$user_id' LIMIT 1");
$shop_data = $shop_res->fetch_assoc();
$shop_id = $shop_data['id'] ?? null;

$is_authorized = false;

if($shop_id) {
    // On vérifie si cette boutique a un abonnement ACTIF et NON EXPIRÉ
    $check_sub = $conn->query("SELECT id FROM subscriptions 
                               WHERE shop_id = '$shop_id' 
                               AND is_active = 1 
                               AND end_date >= CURDATE() 
                               LIMIT 1");
    
    if($check_sub->num_rows > 0) {
        $is_authorized = true;
    }
}

// 2. TRAITEMENT DU FORMULAIRE
if(isset($_POST['add'])){
    if(!$shop_id) {
        $message = "<div class='alert alert-danger rounded-4 shadow-sm'>
                        <i class='bi bi-exclamation-triangle-fill me-2'></i>
                        Vous devez d'abord créer une boutique dans votre profil.
                    </div>";
    } elseif (!$is_authorized) {
        $message = "<div class='alert alert-warning rounded-4 shadow-sm'>
                        <strong>Abonnement requis :</strong> Votre abonnement est expiré ou en attente de validation. 
                        <a href='subscribe.php' class='alert-link'>Cliquez ici pour régulariser</a>.
                    </div>";
    } else {
        // RÉCUPÉRATION ET SÉCURISATION DES DONNÉES
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $desc = mysqli_real_escape_string($conn, $_POST['description']);
        $price = mysqli_real_escape_string($conn, $_POST['price']);
        $type = mysqli_real_escape_string($conn, $_POST['type']);
        $loc = mysqli_real_escape_string($conn, $_POST['location']);

        // GESTION DE L'IMAGE
        $image_name = "default.jpg"; 
        
        if(!empty($_FILES['product_image']['name'])){
            $file_extension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
            $extensions_autorisees = array("jpg", "jpeg", "png", "webp");

            if(in_array($file_extension, $extensions_autorisees)){
                $image_name = time() . "_" . bin2hex(random_bytes(4)) . "." . $file_extension;
                $target = "assets/img/" . $image_name;
                
                if(!move_uploaded_file($_FILES['product_image']['tmp_name'], $target)){
                    $image_name = "default.jpg";
                }
            }
        }

        // INSERTION DANS LA TABLE OFFERS
        $sql = "INSERT INTO offers (shop_id, title, description, price, type, location, image) 
                VALUES ('$shop_id', '$title', '$desc', '$price', '$type', '$loc', '$image_name')";
        
        if($conn->query($sql)){
            $message = "<div class='alert alert-success shadow-sm rounded-4 animate__animated animate__fadeIn'>
                            <i class='bi bi-check-circle-fill me-2'></i> 
                            Félicitations ! Votre annonce est maintenant en ligne. 
                        </div>";
        } else {
            $message = "<div class='alert alert-danger rounded-4'>Erreur technique : " . $conn->error . "</div>";
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 p-4" style="border-radius: 20px;">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 d-inline-block p-3 rounded-circle mb-3">
                        <i class="bi bi-megaphone text-primary fs-2"></i>
                    </div>
                    <h3 class="fw-bold">Publier une annonce</h3>
                    <p class="text-muted small">Ciblez des clients à Djibouti dès maintenant.</p>
                </div>
                
                <?php echo $message; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Nom du produit / service</label>
                        <input type="text" name="title" class="form-control form-control-lg shadow-sm" placeholder="Ex: iPhone 15 Pro Max" required style="border-radius: 10px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Description détaillée</label>
                        <textarea name="description" class="form-control shadow-sm" rows="4" placeholder="Détails, état, garantie..." required style="border-radius: 10px;"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Prix (FDJ)</label>
                            <input type="number" name="price" class="form-control shadow-sm" placeholder="0" required style="border-radius: 10px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Catégorie</label>
                            <select name="type" class="form-select shadow-sm" style="border-radius: 10px;">
                                <option value="vente">💰 Vente Directe</option>
                                <option value="location">🏠 Location</option>
                                <option value="livraison">🚚 Service de Livraison</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-muted">Localisation</label>
                        <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-geo-alt text-primary"></i></span>
                            <input type="text" name="location" class="form-control border-start-0" placeholder="Ex: Balbala, Heron, Gabode..." required>
                        </div>
                    </div>

                    <div class="mb-4 p-3 bg-light rounded-4 border border-dashed text-center">
                        <label class="form-label fw-bold small text-uppercase d-block mb-2">Photo du produit</label>
                        <input type="file" name="product_image" class="form-control shadow-sm" accept="image/*" style="border-radius: 8px;">
                        <div class="form-text mt-2 small">JPG, PNG ou WEBP uniquement.</div>
                    </div>

                    <button name="add" class="btn btn-primary w-100 btn-lg shadow-sm fw-bold py-3" style="border-radius: 15px;">
                        LANCER L'ANNONCE
                    </button>
                </form>
            </div>
            
            <div class="text-center mt-4">
                <a href="dashboard.php" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i> Retour à mon tableau de bord
                </a>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>