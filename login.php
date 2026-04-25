<?php
session_start();
include "config.php";
include "includes/header.php";

// 1. Rediriger si l'utilisateur est déjà connecté
if(isset($_SESSION['user_id'])){
    if($_SESSION['user_role'] == 'admin') {
        header("Location: admin_validate.php");
    } elseif($_SESSION['user_role'] == 'merchant') {
        header("Location: dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// 2. Traitement du formulaire de connexion
if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    $user = $result->fetch_assoc();

    if($user && password_verify($password, $user['password'])){
        // Stockage des informations en session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        // 3. REDIRECTIONS SELON LE RÔLE
        if($_SESSION['user_role'] == 'admin'){
            header("Location: admin_validate.php"); // Redirige l'admin ici
        } elseif($_SESSION['user_role'] == 'merchant'){
            header("Location: dashboard.php"); // Redirige le marchand ici
        } else {
            header("Location: index.php"); // Redirige le client ici
        }
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>

<div class="container">
    <div class="row justify-content-center mt-5 mb-5">
        <div class="col-md-10 col-lg-9">
            <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 20px;">
                <div class="row g-0">
                    <div class="col-md-6 d-none d-md-block">
                        <img src="assets/img/login-bg.webp" class="img-fluid h-100" style="object-fit: cover; min-height: 500px;" alt="Login" onerror="this.src='https://via.placeholder.com/500x700'">
                    </div>
                    
                    <div class="col-md-6 p-4 p-md-5 bg-white">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary mb-1">MARKETPLACE</h2>
                            <p class="text-muted small">Connectez-vous pour continuer 🇩🇯</p>
                        </div>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger border-0 small p-2 text-center" style="border-radius: 10px;">
                                <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Adresse Email</label>
                                <input type="email" name="email" class="form-control bg-light border-0 py-2" placeholder="nom@exemple.com" style="border-radius: 10px;" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Mot de passe</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="loginPassword" class="form-control bg-light border-0 py-2" style="border-radius: 10px 0 0 10px;" required>
                                    <span class="input-group-text bg-light border-0" style="border-radius: 0 10px 10px 0; cursor: pointer;" onclick="toggleLoginPassword()">
                                        <i class="bi bi-eye-slash" id="loginToggleIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <button name="login" type="submit" class="btn btn-primary w-100 mb-3 py-2 shadow-sm fw-bold" style="border-radius: 10px;">
                                Se connecter
                            </button>
                            
                            <div class="text-center">
                                <a href="forgot_password.php" class="text-muted small text-decoration-none">Mot de passe oublié ?</a>
                                <div class="d-flex align-items-center my-4">
                                    <hr class="flex-grow-1"><span class="mx-2 text-muted small">OU</span><hr class="flex-grow-1">
                                </div>
                                <p class="small mb-0">Nouveau ? <a href="register.php" class="fw-bold text-primary text-decoration-none">Créer un compte</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Fonction pour afficher/cacher le mot de passe
function toggleLoginPassword() {
    const passwordInput = document.getElementById('loginPassword');
    const toggleIcon = document.getElementById('loginToggleIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.replace('bi-eye-slash', 'bi-eye');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.replace('bi-eye', 'bi-eye-slash');
    }
}
</script>

<?php include "includes/footer.php"; ?>