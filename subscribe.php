<?php
session_start();
include "config.php";
include "includes/header.php";

// 1. Sécurité : Vérifier la connexion
if(!isset($_SESSION['user_id'])){ 
    header("Location: login.php"); exit(); 
}

// 2. Récupération sécurisée de l'ID boutique
$shop_id = $_GET['shop_id'] ?? $_POST['shop_id'] ?? null;
$message = "";

// 3. Traitement du formulaire UNIQUEMENT si le bouton est cliqué
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['receipt'])) {
    
    // On vérifie si les clés existent dans $_POST pour éviter le "Warning"
    $method = isset($_POST['method']) ? mysqli_real_escape_string($conn, $_POST['method']) : '';
    $trans_id = isset($_POST['transaction_id']) ? mysqli_real_escape_string($conn, $_POST['transaction_id']) : '';
    $phone = isset($_POST['sender_phone']) ? mysqli_real_escape_string($conn, $_POST['sender_phone']) : '';
    
    if (empty($shop_id)) {
        $message = "<div class='alert alert-danger rounded-4'>Erreur : ID boutique manquant.</div>";
    } else {
        $target_dir = "assets/uploads/receipts/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

        $file_ext = strtolower(pathinfo($_FILES["receipt"]["name"], PATHINFO_EXTENSION));
        $filename = "RECUT_" . time() . "_" . $shop_id . "." . $file_ext;

        if (move_uploaded_file($_FILES["receipt"]["tmp_name"], $target_dir . $filename)) {
            // Insertion avec les nouvelles colonnes
            $sql = "INSERT INTO payments (shop_id, method, transaction_id, sender_phone, amount, status, receipt_img) 
                    VALUES ('$shop_id', '$method', '$trans_id', '$phone', '1000', 'pending', '$filename')";
            
            if($conn->query($sql)) {
                $message = "<div class='alert alert-success rounded-4 shadow-sm animate__animated animate__fadeIn'>
                                <i class='bi bi-check-circle-fill me-2'></i> Reçu envoyé ! Validation par Aboubaker en cours.
                            </div>";
            } else {
                $message = "<div class='alert alert-danger'>Erreur SQL : " . $conn->error . "</div>";
            }
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="bg-primary p-4 text-white text-center">
                    <h4 class="fw-bold mb-0">Validation de l'Abonnement</h4>
                    <p class="small opacity-75 mb-0">Boutique ID : <?php echo htmlspecialchars($shop_id); ?></p>
                </div>
                
                <div class="card-body p-4">
                    <?php echo $message; ?>

                    <div class="bg-light p-3 rounded-4 mb-4 border-start border-primary border-4 shadow-sm">
                        <p class="small text-muted mb-1 fw-bold text-uppercase">Envoyez 1000 FDJ à :</p>
                        <h4 class="fw-bold text-dark mb-0">77 47 71 39</h4>
                        <span class="badge bg-primary mt-2">Nom : ABOUBAKER ADAN MAIADANE</span>
                    </div>

                    <form action="subscribe.php?shop_id=<?php echo htmlspecialchars($shop_id); ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="shop_id" value="<?php echo htmlspecialchars($shop_id); ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Votre numéro Waafi / D-Money</label>
                            <input type="text" name="sender_phone" class="form-control rounded-pill border-2" placeholder="77xxxxxx" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Mode de paiement</label>
                            <select name="method" class="form-select rounded-pill border-2" required>
                                <option value="" selected disabled>Choisir...</option>
                                <option value="Waafi">Waafi</option>
                                <option value="D-Money">D-Money</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">ID Transaction (SMS)</label>
                            <input type="text" name="transaction_id" class="form-control rounded-pill border-2" placeholder="Ex: 85421..." required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Capture du reçu (Image)</label>
                            <input type="file" name="receipt" class="form-control border-2" accept="image/*" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-3 shadow">
                            VALIDER MON PAIEMENT
                        </button>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="dashboard.php" class="text-muted small text-decoration-none">Retour au tableau de bord</a>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>