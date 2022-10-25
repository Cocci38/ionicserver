<?php


header('Access-Control-Allow-Origin: *');

// Connexion à la base de données
require_once 'configuration.php';
error_log(print_r($_FILES, 1));
$object_id = htmlspecialchars(strip_tags(trim(stripslashes($_GET['id']))));

error_log(print_r($object_id, 1));

if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
    error_log('je passe ici ');
    $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
    $filename = $_FILES['file']["name"];
    $filetmp = $_FILES['file']["tmp_name"];
    $target_path = "picture/";
    $target_path = $target_path . basename($_FILES['file']['name']);

    // Vérifie l'extension du fichier
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    try {
        if (!array_key_exists($ext, $allowed)) {
            error_log('error format image');
            $data = ['success' => false, 'message' => "Erreur : Veuillez sélectionner un format de fichier valide."];
            echo json_encode($data);
        } else {
            error_log('insertion débuter');
            $stmt = $conn->prepare("INSERT INTO pictures (picture, object_id) VALUES(:picture, :object_id)");
            $stmt->bindParam("picture", $filename, PDO::PARAM_STR);
            $stmt->bindParam("object_id", $object_id, PDO::PARAM_INT);
            $stmt->execute();

            $create = true;

            if ($create) {
                move_uploaded_file($filetmp, $target_path);
                $data = ['success' => true, 'message' => 'Téléchargement et déplacement réussis'];
                echo json_encode($data);
            } else {
                $data = ['success' => false, 'message' => 'Une erreur est survenu pendant l\'upload'];
                echo json_encode($data);
            }
        }
    } catch (PDOException $exception) {
        echo "Erreur de connexion : " . $exception->getMessage();
    }
}
