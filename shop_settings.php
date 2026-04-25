<?php
session_start();
include "config.php";
include "includes/header.php";

// 1. Vérification de connexion
if(!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];
$message = "";

// 2. RÉCUPÉRATION DES DONNÉES ACTUELLES
$query = "SELECT * FROM shops WHERE user_id = '$user_id'";
$result = $conn->query($query);
$shop = $result->fetch_assoc();

if(!$shop) {
    echo "<div class='container mt-5'><div class='alert alert-warning'>Boutique introuvable.</div></div>";
    exit();
}

// 3. MISE À JOUR DES PARAMÈTRES
if(isset($_POST['update_settings'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $update_sql = "UPDATE shops SET 
                   name = '$name', 
                   description = '$description', 
                   phone = '$phone', 
                   address = '$address' 
                   WHERE user_id = '$user_id'";

    if($conn->query($update_sql)){
        $message = "<div class='alert alert-success'>Paramètres mis à jour avec succès !</div>";
        // Rafraîchir les données locales
        $shop['name'] = $name;
        $shop['description'] = $description;
        $shop['phone'] = $phone;
        $shop['address'] = $address;
    } else {
        $message = "<div class='alert alert-danger'>Erreur : " . $conn->error . "</div>";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h3 class="fw-bold mb-4"><i class="bi bi-gear-fill me-2 text-primary"></i>Paramètres de la Boutique</h3>
                
                <?php echo $message; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom de la boutique</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($shop['name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($shop['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Téléphone (WhatsApp)</label>
                            <input type="text" name="phone" class="form-control" placeholder="77xxxxxx" value="<?php echo htmlspecialchars($shop['phone'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Adresse (Ville/Quartier)</label>
                            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($shop['address']); ?>">
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-4">
                        <p class="mb-0 small text-muted">
                            <i class="bi bi-info-circle me-1"></i> 
                            Le statut de votre boutique (<strong><?php echo ($shop['is_active'] == 1) ? 'Actif' : 'Inactif'; ?></strong>) dépend de votre abonnement. 
                            Pour renouveler, allez dans la section <a href="subscribe.php" class="text-primary fw-bold text-decoration-none">Abonnement</a>.
                        </p>
                    </div>

                    <button name="update_settings" class="btn btn-primary w-100 fw-bold py-2">Enregistrer les modifications</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>