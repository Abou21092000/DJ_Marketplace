<?php
session_start();
include "config.php";
include "includes/header.php";

// SÉCURITÉ : Admin uniquement
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$message = "";

// --- ACTION : VALIDER ---
if (isset($_GET['approve_id']) && isset($_GET['shop_id'])) {
    $payment_id = mysqli_real_escape_string($conn, $_GET['approve_id']);
    $shop_id = mysqli_real_escape_string($conn, $_GET['shop_id']);
    $date_expiration = date('Y-m-d', strtotime('+30 days'));

    $conn->begin_transaction();
    try {
        $conn->query("UPDATE payments SET status = 'success' WHERE id = '$payment_id'");
        $conn->query("UPDATE shops SET is_active = 1, date_expiration = '$date_expiration' WHERE id = '$shop_id'");
        $conn->query("INSERT INTO subscriptions (shop_id, start_date, end_date, is_active) 
                      VALUES ('$shop_id', CURDATE(), '$date_expiration', 1)
                      ON DUPLICATE KEY UPDATE end_date = '$date_expiration', is_active = 1");
        $conn->commit();
        $message = "<div class='alert alert-success'>Boutique activée avec succès !</div>";
    } catch (Exception $e) {
        $conn->rollback();
        $message = "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
    }
}

// --- ACTION : REJETER ---
if (isset($_GET['reject_id'])) {
    $reject_id = mysqli_real_escape_string($conn, $_GET['reject_id']);
    
    // On change le statut en 'failed' au lieu de supprimer pour garder une trace
    $sql = "UPDATE payments SET status = 'failed' WHERE id = '$reject_id'";
    
    if ($conn->query($sql)) {
        $message = "<div class='alert alert-warning'>Demande rejetée et classée.</div>";
    }
}
?>

<div class="container py-5">
    <h2 class="fw-bold">Validation des Paiements</h2>
    <p class="text-muted mb-4">Gérez les demandes d'abonnement Waafi et D-Money.</p>

    <?php echo $message; ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Boutique</th>
                        <th>Méthode</th>
                        <th>Transaction ID</th>
                        <th>Téléphone</th>
                        <th>Preuve</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // SQL corrigé pour ne prendre que les paiements 'pending'
                    $sql = "SELECT p.*, s.name as shop_name 
                            FROM payments p 
                            JOIN shops s ON p.shop_id = s.id 
                            WHERE p.status = 'pending' 
                            ORDER BY p.created_at DESC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                    ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?php echo htmlspecialchars($row['shop_name']); ?></div>
                                    <small class="text-muted">ID: #<?php echo $row['shop_id']; ?></small>
                                </td>
                                <td><span class="badge bg-info rounded-pill"><?php echo $row['method']; ?></span></td>
                                <td class="small font-monospace"><?php echo $row['transaction_id']; ?></td>
                                <td><?php echo $row['sender_phone']; ?></td>
                                <td>
                                    <a href="assets/uploads/receipts/<?php echo $row['receipt_img']; ?>" target="_blank" class="btn btn-sm btn-light border">Voir reçu</a>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="?approve_id=<?php echo $row['id']; ?>&shop_id=<?php echo $row['shop_id']; ?>" 
                                       class="btn btn-success btn-sm rounded-pill px-3 me-1">
                                        Valider
                                    </a>
                                    <a href="?reject_id=<?php echo $row['id']; ?>" 
                                       class="btn btn-danger btn-sm rounded-pill px-3"
                                       onclick="return confirm('Rejeter ce paiement ? (Le marchand devra recommencer)');">
                                        Rejeter
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5">Aucun paiement en attente.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>