<?php
include "config.php";
include "includes/header.php";

$email_get = $_GET['email'] ?? '';

if(isset($_POST['update_pass'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $now = date("Y-m-d H:i:s");

    // 1. Vérifier si le code est bon ET s'il n'est pas expiré
    $res = $conn->query("SELECT id FROM users 
                         WHERE email='$email' 
                         AND reset_token='$code' 
                         AND token_expiry > '$now'");
    
    if($res->num_rows > 0){
        // 2. Mise à jour du mot de passe et nettoyage des jetons
        $conn->query("UPDATE users SET 
                      password='$new_pass', 
                      reset_token=NULL, 
                      token_expiry=NULL 
                      WHERE email='$email'");
        
        echo "<div class='container mt-3'>
                <div class='alert alert-success text-center border-0 shadow-sm' style='border-radius:15px;'>
                    <i class='bi bi-check-circle-fill fs-3 d-block mb-2'></i>
                    Succès ! Votre mot de passe a été mis à jour. <br>
                    <a href='login.php' class='btn btn-dark btn-sm mt-3 px-4 rounded-pill'>Se connecter maintenant</a>
                </div>
              </div>";
    } else {
        echo "<div class='container mt-3'>
                <div class='alert alert-danger text-center border-0 shadow-sm' style='border-radius:15px;'>
                    Code incorrect ou expiré. Veuillez refaire une demande.
                </div>
              </div>";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 p-4" style="border-radius: 20px;">
                <div class="text-center mb-4">
                    <div class="bg-light d-inline-block p-3 rounded-circle mb-3">
                        <i class="bi bi-key text-success fs-1"></i>
                    </div>
                    <h4 class="fw-bold">Nouveau mot de passe</h4>
                    <p class="text-muted small">Finalisez la récupération de votre compte Marketplace.</p>
                </div>

                <form method="POST">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email_get); ?>">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase">Code de vérification</label>
                        <input type="text" name="code" class="form-control py-2 text-center fw-bold text-primary" 
                               placeholder="Entrez le code à 6 chiffres" 
                               style="letter-spacing: 3px; border-radius: 10px;" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase">Nouveau mot de passe</label>
                        <input type="password" name="password" class="form-control py-2" 
                               placeholder="••••••••" style="border-radius: 10px;" required>
                        <div class="form-text mt-2" style="font-size: 0.75rem;">
                            Utilisez un mélange de lettres et chiffres pour plus de sécurité.
                        </div>
                    </div>

                    <button name="update_pass" class="btn btn-success w-100 py-3 fw-bold shadow-sm" style="border-radius: 10px;">
                        Changer le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>