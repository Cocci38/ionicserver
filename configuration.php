<?php

// Connexion à la base de données sécurisées grâce aux htaccess qui empêche d'y accéder (uniquement pour les serveurs Apache)
define('DB_NAME', 'ionicfoundlost');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PWD', '');

// Créer une instance de la classe PDO (connexion à la base)
try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . "; charset=UTF8", DB_USER, DB_PWD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connexion réussi";
} catch (PDOException $exception) {
    echo "Erreur de connexion : " . $exception->getMessage();
}
?>