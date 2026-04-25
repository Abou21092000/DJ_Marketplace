<?php
session_start();
include "config.php";
include "includes/header.php";

// Sécurité : Vérifier si l'utilisateur est connecté
$is_logged = isset($_SESSION['user_id']);
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Boostez vos ventes sur Marketplace 🇩🇯</h1>
        <p class="text-muted fs-5">Choisissez le pack qui vous permet de publier en illimité.</p>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-md-5 col-lg-4">
            <div class="card h-100 shadow-sm border-0 rounded-4 p-4">
                <div class="card-body text-center">
                    <h3 class="fw-bold">Standard</h3>
                    <div class="my-4">
                        <span class="display-5 fw-bold">0</span>
                        <span class="text-muted">FDJ</span>
                    </div>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> 1 annonce active</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Visibilité locale</li>
                        <li class="mb-2 text-muted"><i class="bi bi-x-circle me-2"></i> Badge vérifié</li>
                    </ul>
                    <button class="btn btn-light w-100 rounded-pill py-2 disabled">Plan actuel</button>
                </div>
            </div>
        </div>

        <div class="col-md-5 col-lg-4">
            <div class="card h-100 shadow-lg border-primary rounded-4 p-4 position-relative" style="border-width: 2px;">
                <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-primary px-3">
                    POPULAIRE
                </span>
                <div class="card-body text-center">
                    <h3 class="fw-bold">Pack Vendeur</h3>
                    <div class="my-4">
                        <span class="display-5 fw-bold text-primary">1 000</span>
                        <span class="text-muted">FDJ / 7 jours</span>
                    </div>
                    <ul class="list-unstyled text-start mb-4">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> <strong>Annonces illimitées</strong></li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Priorité dans les recherches</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Badge Vendeur Pro</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i> Support direct</li>
                    </ul>
                    
                    <?php if($is_logged): ?>
                        <a href="subscriptions.php" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow">
                            Activer maintenant
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary w-100 rounded-pill py-3">Se connecter</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-md-8 text-center">
            <h5 class="fw-bold mb-4 text-uppercase small text-muted" style="letter-spacing: 1px;">Moyens de paiement acceptés</h5>
            <div class="d-flex justify-content-center gap-4 align-items-center flex-wrap">
                <div class="bg-white px-3 py-2 rounded shadow-sm border"><strong>WAAFI</strong></div>
                <div class="bg-white px-3 py-2 rounded shadow-sm border"><strong>D-MONEY</strong></div>
                <div class="bg-white px-3 py-2 rounded shadow-sm border"><strong>MYCAC (CAC Pay)</strong></div>
            </div>
            <p class="mt-4 text-muted small">
                Le paiement est sécurisé. Votre compte est activé après validation de la référence de transaction.
            </p>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>