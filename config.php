<?php
// Configuration pour la base de données en ligne (Railway)
$host = "shortline.proxy.rlwy.net";
$user = "root";
$pass = "rFmaCdILzPKAUAJTANFUIIkioRRJlKVO";
$dbname = "railway";
$port = 18188;

// Connexion MySQLi
$conn = new mysqli($host, $user, $pass, $dbname, $port);

// Vérification de la connexion
if ($conn->connect_error) {
    die("La connexion au serveur Railway a échoué : " . $conn->connect_error);
}

// Support des caractères spéciaux (accents, etc.)
$conn->set_charset("utf8mb4");

// Echo pour test (à supprimer une fois que ça marche)
// echo "Félicitations Aboubaker ! Connexion au Cloud réussie.";
?>