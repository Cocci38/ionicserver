<?php
// Pour la lecture des données de la table foundlost.

header('Access-Control-Allow-Origin: *');

// Définir les paramètres de connexion

require_once 'configuration.php';

// Créer une instance de la classe PDO (connexion à la base)
try {
    $conn = new PDO("mysql:host=". DB_HOST . ";dbname=".DB_NAME."; charset=UTF8", DB_USER, DB_PWD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connexion réussi";
} catch (PDOException $exception) {
    echo "Erreur de connexion : " . $exception->getMessage();
}

// Si $_GET['id'] est défini on sélectionne par l'id
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
// Sinon je sélectionne tous et je trie par la date en ordre descendant
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

