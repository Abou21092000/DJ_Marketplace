<?php
session_start();
include "config.php";
include "includes/header.php";

// SÉCURITÉ : Admin uniquement
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin'){ 
    header("Location: index.php"); 
    exit(); 
}

// LOGIQUE DE SUPPRESSION
if(isset($_GET['delete_user'])){
    $u_id = mysqli_real_escape_string($conn, $_GET['delete_user']);
    $conn->query("DELETE FROM users WHERE id = '$u_id'");
    header("Location: admin_users.php?msg=deleted");
    exit();
}
?>

<div class="container py-4">
    <div class="bg-white p-2 rounded-pill shadow-sm mb-4 d-inline-flex border">
        <a href="admin_validate.php" class="btn btn-light rounded-pill px-4">Paiements</a>
        <a href="admin_users.php" class="btn btn-primary rounded-pill px-4 ms-2">Utilisateurs</a>
    </div>

    <h2 class="fw-bold mb-4">Gestion des Utilisateurs 👥</h2>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success rounded-4">Utilisateur supprimé avec succès.</div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Inscription</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $me = $_SESSION['user_id'];
                    $result = $conn->query("SELECT * FROM users WHERE id != '$me' ORDER BY created_at DESC");

                    while($user = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                </div>
                                <span class="fw-bold"><?php echo htmlspecialchars($user['name']); ?></span>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="badge rounded-pill <?php echo ($user['role'] == 'merchant') ? 'bg-warning text-dark' : 'bg-light text-muted border'; ?>">
                                <?php echo ($user['role'] == 'merchant') ? 'Vendeur' : 'Acheteur'; ?>
                            </span>
                        </td>
                        <td class="small text-muted"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                        <td class="text-center">
                            <a href="admin_users.php?delete_user=<?php echo $user['id']; ?>" 
                               class="btn btn-outline-danger btn-sm rounded-pill" 
                               onclick="return confirm('Supprimer définitivement cet utilisateur ?')">
                               <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>