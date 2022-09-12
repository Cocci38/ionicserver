<?php
// Pour la lecture des données de la table foundlost.

header('Access-Control-Allow-Origin: *');

// Définir les paramètres de connexion

$host = "localhost";
$db_name = "ionicfoundlost";
$username = "root";
$password = "";

// Créer une instance de la classe PDO (connexion à la base)
try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name; charset=UTF8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connexion réussi";
} catch (PDOException $exception) {
    echo "Erreur de connexion : " . $exception->getMessage();
}
// Prépare et exécute la requête de lecture de la table (try/catch)

if (isset($_GET['id'])) {
    // error_log($_GET['id']);
    $id = htmlspecialchars(strip_tags(trim(stripslashes($_GET['id']))));
    try {
        $select = $conn->prepare("SELECT id_object, status, description, date, location, firstname, lastname, email, users_id FROM foundlost WHERE id_object = $id");
        $select->bindParam("id_object", $id, PDO::PARAM_INT);
        $select->execute();
        $result = $select->fetch(PDO::FETCH_ASSOC);
        echo json_encode($result);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else {
    try {
        $select = $conn->prepare("SELECT id_object, status, description, date, location, firstname, lastname, email, users_id FROM foundlost ORDER BY date DESC");
        $select->execute();
        while ($row = $select->fetch(PDO::FETCH_ASSOC)) { 
            $data[] = $row;
        }
        // echo "<pre>",print_r($_GET['status'] ,1),"</pre>";die();
        echo json_encode($data);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

