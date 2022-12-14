<?php
// Pour la lecture des données de la table foundlost.

header('Access-Control-Allow-Origin: *');

// Définir les paramètres de connexion

require_once 'configuration.php';

// Si $_GET['id'] est défini on sélectionne par l'id
if (isset($_GET['id'])) {
    // error_log(print_r($_GET, 1));
    $id = htmlspecialchars(strip_tags(trim(stripslashes($_GET['id']))));
    try {
        $select = $conn->prepare("SELECT id_object, status, description, date, location, firstname, lastname, email, user_id, picture
                                  FROM objects 
                                  LEFT JOIN pictures ON objects.id_object = object_id
                                  WHERE id_object = $id");
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
        $select = $conn->prepare("SELECT id_object, status, description, date, location, firstname, lastname, email, user_id, picture 
                                  FROM objects 
                                  LEFT JOIN pictures ON objects.id_object = object_id 
                                  ORDER BY date DESC");
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

