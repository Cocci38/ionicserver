<?php


header('Access-Control-Allow-Origin: *');

// Connexion à la base de données
require_once 'configuration.php';

// error_log(print_r($_FILES, 1));

if (!empty($_FILES['file'])) {

    $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
    $filename = $_FILES['file']["name"];
    $filetmp = $_FILES['file']["tmp_name"];
    $target_path = "picture/";
    $target_path = $target_path . basename($_FILES['file']['name']);

    // Vérifie l'extension du fichier
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if (!array_key_exists($ext, $allowed)) die("Erreur : Veuillez sélectionner un format de fichier valide.");
    
        $stmt = $conn->prepare("INSERT INTO pictures (picture) VALUES(:picture)");
        $stmt->bindParam("picture", $filename, PDO::PARAM_STR);
        $stmt->execute();
        
        $create = true;
        
        if ($create) {
            move_uploaded_file($filetmp, $target_path);
            $data = ['success' =>true, 'message' => 'Téléchargement et déplacement réussis'];
            echo json_encode($data);
        } else {
        $data = ['success' => false, 'message' => 'Une erreur est survenu pendant l\'upload'];
        echo json_encode($data);
    }
}
