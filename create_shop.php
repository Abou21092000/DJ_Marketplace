<?php
session_start();
include "config.php";
include "includes/header.php";

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'merchant'){ 
    header("Location: login.php"); exit(); 
}

$user_id = $_SESSION['user_id'];

// Vérifier si l'utilisateur a déjà une boutique
$check = $conn->query("SELECT id FROM shops WHERE user_id = '$user_id'");
if($check->num_rows > 0){
    header("Location: dashboard.php"); exit();
}

if(isset($_POST['create'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $sql = "INSERT INTO shops (user_id, name, address, is_active) VALUES ('$user_id', '$name', '$address', 0)";
    
    if($conn->query($sql)){
        $new_shop_id = $conn->insert_id; 
        header("Location: subscribe.php?shop_id=" . $new_shop_id);
        exit(); 
    }
}
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg p-4" style="border-radius: 20px;">
                <h3 class="fw-bold text-center mb-4">Créer ma Boutique</h3>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nom de l'enseigne</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Ex: Aboubaker Tech" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Adresse à Djibouti</label>
                        <input type="text" name="address" class="form-control rounded-3" placeholder="Quartier, Rue" required>
                    </div>
                    <button name="create" class="btn btn-primary btn-lg w-100 fw-bold shadow" style="border-radius: 15px;">
                        Suivant : Paiement <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>