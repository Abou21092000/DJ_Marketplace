<?php
session_start();
include "config.php";
include "includes/header.php";

// Rediriger si déjà connecté
if(isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

if(isset($_POST['register'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']); // Récupération du téléphone
    $role = $_POST['role']; 
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_email = $conn->query("SELECT id FROM users WHERE email='$email'");
    if($check_email->num_rows > 0){
        $error = "Cet email est déjà utilisé.";
    } else {
        // Insertion incluant le numéro de téléphone
        $sql = "INSERT INTO users (name, email, phone, password, role) 
                VALUES ('$name', '$email', '$phone', '$password', '$role')";
        if($conn->query($sql)){
            $success = "Compte créé avec succès !";
        } else {
            $error = "Une erreur est survenue lors de l'inscription.";
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center mt-5 mb-5">
        <div class="col-md-10 col-lg-9">
            <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 20px;">
                <div class="row g-0">
                    
                    <div class="col-md-6 p-4 p-md-5 bg-white">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary mb-1">Rejoignez-nous</h2>
                            <p class="text-muted small">Créez votre compte en quelques secondes.</p>
                        </div>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger border-0 small p-2 text-center" style="border-radius: 10px;"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if(isset($success)): ?>
                            <div class="alert alert-success border-0 small p-2 text-center" style="border-radius: 10px;"><?php echo $success; ?> <a href="login.php">Connexion</a></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Nom complet</label>
                                <input type="text" name="name" class="form-control bg-light border-0 py-2" placeholder="Ex: Adan Aboubaker" style="border-radius: 10px;" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Adresse Email</label>
                                <input type="email" name="email" class="form-control bg-light border-0 py-2" placeholder="nom@exemple.com" style="border-radius: 10px;" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Numéro de téléphone</label>
                                <input type="tel" name="phone" class="form-control bg-light border-0 py-2" placeholder="Ex: 77 12 34 56" style="border-radius: 10px;" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Mot de passe</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control bg-light border-0 py-2" placeholder="••••••••" style="border-radius: 10px 0 0 10px;" required>
                                    <span class="input-group-text bg-light border-0" style="border-radius: 0 10px 10px 0; cursor: pointer;" onclick="togglePassword()">
                                        <i class="bi bi-eye-slash" id="toggleIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-dark">Je souhaite :</label>
                                <select name="role" class="form-select bg-light border-0 py-2" style="border-radius: 10px;">
                                    <option value="client">🛒 Acheter des produits</option>
                                    <option value="merchant">🏪 Vendre mes produits</option>
                                </select>
                            </div>

                            <button name="register" type="submit" class="btn btn-primary w-100 py-2 shadow-sm fw-bold" style="border-radius: 10px;">Créer mon compte</button>
                            <p class="text-center mt-4 small">Déjà inscrit ? <a href="login.php" class="fw-bold text-primary text-decoration-none">Se connecter</a></p>
                        </form>
                    </div>

                    <div class="col-md-6 d-none d-md-block">
                        <img src="https://images.unsplash.com/photo-1557821552-17105176677c?q=80&w=1000&auto=format&fit=crop" class="img-fluid h-100" style="object-fit: cover;" alt="Register">
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
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