<?php 
if(session_status() === PHP_SESSION_NONE) session_start(); 

// Détecter la page actuelle
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace DJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
        .navbar { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .card { border-radius: 12px; border: none; }
        .main-container { flex: 1; padding-top: 2rem; padding-bottom: 2rem; }
        .navbar-brand { letter-spacing: 1px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="index.php">MARKETPLACE</a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
            <?php if($_SESSION['user_role'] == 'merchant'): ?>
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Mon Tableau de bord</a></li>
                <li class="nav-item"><a class="btn btn-primary btn-sm ms-lg-2 px-3 rounded-pill" href="add_offer.php">Vendre +</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link text-danger ms-lg-3" href="logout.php"><i class="bi bi-box-arrow-right"></i></a></li>

        <?php else: ?>
            <?php if($current_page != 'login.php' && $current_page != 'register.php'): ?>
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'login.php') ? 'active fw-bold' : ''; ?>" href="login.php">Connexion</a>
            </li>
            
            <li class="nav-item">
                <a class="btn <?php echo ($current_page == 'register.php') ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm ms-lg-2 px-3 rounded-pill" href="register.php">
                    S'inscrire
                </a>
            </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<div class="container main-container">