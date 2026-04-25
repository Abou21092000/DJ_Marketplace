<?php
session_start();
include "config.php";

// Sécurité : Vérifier si l'utilisateur est un marchand connecté
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'merchant'){ 
    exit("Accès refusé"); 
}

if(isset($_GET['id'])){
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    // On vérifie que l'annonce appartient bien à ce vendeur avant de supprimer
    $check = $conn->query("SELECT o.id FROM offers o 
                           JOIN shops s ON o.shop_id = s.id 
                           WHERE o.id='$id' AND s.user_id='$user_id'");

    if($check->num_rows > 0){
        $conn->query("DELETE FROM offers WHERE id='$id'");
        header("Location: dashboard.php?msg=supprime");
    } else {
        header("Location: dashboard.php?error=non_autorise");
    }
}
?>