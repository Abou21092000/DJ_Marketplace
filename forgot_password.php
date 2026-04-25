<?php
include "config.php";
include "includes/header.php";

if(isset($_POST['reset_request'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $check = $conn->query("SELECT id FROM users WHERE email='$email'");

    if($check->num_rows > 0){
        // 1. Générer un code aléatoire à 6 chiffres (plus pro que 1234)
        $code = rand(100000, 999999); 
        
        // 2. Définir une expiration (ex: valide pendant 30 minutes)
        $expiry = date("Y-m-d H:i:s", strtotime("+30 minutes"));

        // 3. Mise à jour de la table avec les bonnes colonnes
        $sql = "UPDATE users SET reset_token='$code', token_expiry='$expiry' WHERE email='$email'";
        
        if($conn->query($sql)){
            // Simulation d'envoi d'email (en attendant PHPMailer)
            echo "<div class='container mt-3'>
                    <div class='alert alert-success border-0 shadow-sm text-center' style='border-radius:15px;'>
                        <i class='bi bi-envelope-check fs-2 d-block mb-2'></i>
                        Un code de vérification a été généré.<br>
                        <strong>Votre code secret est : <span class='badge bg-dark'>$code</span></strong><br>
                        <small class='text-muted'>Ce code expirera dans 30 minutes.</small><br>
                        <a href='reset_password.php?email=$email' class='btn btn-primary btn-sm mt-3 px-4 rounded-pill'>Saisir le code</a>
                    </div>
                  </div>";
        }
    } else {
        echo "<div class='container mt-3'><div class='alert alert-danger text-center'>Désolé, cette adresse email n'existe pas.</div></div>";
    }
}
?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 p-4" style="border-radius: 20px;">
                <div class="text-center mb-4">
                    <div class="bg-light d-inline-block p-3 rounded-circle mb-3">
                        <i class="bi bi-shield-lock text-primary fs-1"></i>
                    </div>
                    <h3 class="fw-bold">Mot de passe oublié</h3>
                    <p class="text-muted small">Entrez votre email pour recevoir un code de récupération.</p>
                </div>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Adresse Email</label>
                        <input type="email" name="email" class="form-control py-2" placeholder="nom@exemple.com" style="border-radius: 10px;" required>
                    </div>
                    <button name="reset_request" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius: 10px;">
                        Envoyer le code
                    </button>
                    <div class="text-center mt-4">
                        <a href="login.php" class="text-decoration-none small text-secondary">
                            <i class="bi bi-arrow-left"></i> Retour à la connexion
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>