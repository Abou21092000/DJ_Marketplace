<?php
session_start();
include "config.php";
include "includes/header.php";

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){ header("Location: login.php"); exit(); }

$id = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user_id'];

// Récupérer l'offre actuelle
$res = $conn->query("SELECT o.* FROM offers o JOIN shops s ON o.shop_id = s.id WHERE o.id='$id' AND s.user_id='$user_id'");
$offer = $res->fetch_assoc();

if(!$offer) exit("Annonce introuvable ou accès refusé.");

if(isset($_POST['update'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    
    $image_query = "";
    if(!empty($_FILES['product_image']['name'])){
        $image_name = time() . "_" . $_FILES['product_image']['name'];
        move_uploaded_file($_FILES['product_image']['tmp_name'], "assets/img/" . $image_name);
        $image_query = ", image='$image_name'";
    }

    $sql = "UPDATE offers SET title='$title', description='$desc', price='$price', location='$loc' $image_query WHERE id='$id'";
    if($conn->query($sql)){
        header("Location: dashboard.php?msg=modifie");
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow border-0 p-4">
            <h4 class="fw-bold mb-4">Modifier l'annonce</h4>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label fw-bold">Titre</label>
                    <input type="text" name="title" class="form-control" value="<?php echo $offer['title']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Prix (FDJ)</label>
                    <input type="number" name="price" class="form-control" value="<?php echo $offer['price']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Localisation</label>
                    <input type="text" name="location" class="form-control" value="<?php echo $offer['location']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo $offer['description']; ?></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Changer la photo (optionnel)</label>
                    <input type="file" name="product_image" class="form-control">
                </div>
                <button name="update" class="btn btn-success w-100">Enregistrer les modifications</button>
                <a href="dashboard.php" class="btn btn-light w-100 mt-2">Annuler</a>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>