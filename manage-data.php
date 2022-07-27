<?php
// Pour gérer l’ajout, la mise à jour et la suppression des enregistrements de la table foundlost.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

$host = "localhost";
$db_name = "ionicfoundlost";
$username = "root";
$password = "";

try {
$conn = new PDO("mysql:host=$host;dbname=$db_name; charset=UTF8", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// echo "Connexion réussi";
} catch (PDOException $exception) {
    echo "Erreur de connexion : " . $exception->getMessage();
}

// Récupérer le paramètre d’action de l’URL du client depuis $_GET[‘key’] 
// et nettoyer la valeur
$key = strip_tags($_GET['key']);

// Récupérer les paramètres envoyés par le client vers l’API
$input = file_get_contents('php://input');

if (!empty($input) || isset($_GET)) {
    $data = json_decode($input, true);
    
    @$status = strip_tags($data['status']);
    @$description = strip_tags($data['description']); 
    @$date = strip_tags($data['date']);
    @$location = strip_tags($data['location']);
    @$firstname = strip_tags($data['firstname']);
    @$lastname = strip_tags($data['lastname']);
    @$email = strip_tags($data['email']);
    // En fonction du mode d'action requis
    switch ($key) {
        // Ajoute un nouvel enregistrement
        case 'create':
                $status = htmlspecialchars($status);
                $description = htmlspecialchars($description);
                $date = htmlspecialchars($date);
                $location = htmlspecialchars($location);
                $firstname = htmlspecialchars($firstname);
                $lastname = htmlspecialchars($lastname);
                $email = htmlspecialchars($email);
            try {
                $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $stmt = $conn->prepare("INSERT INTO foundlost (status, description, date, location, firstname, lastname, email) VALUES(:status, :description, :date, :location, :firstname, :lastname, :email)");
                    $stmt->bindParam("status", $status, PDO::PARAM_INT);
                    $stmt->bindParam("description", $description, PDO::PARAM_STR);
                    $stmt->bindParam("date", $date, PDO::PARAM_STR);
                    $stmt->bindParam("location", $location, PDO::PARAM_STR);
                    $stmt->bindParam("firstname", $firstname, PDO::PARAM_STR);
                    $stmt->bindParam("lastname", $lastname, PDO::PARAM_STR);
                    $stmt->bindParam("email", $email, PDO::PARAM_STR);
                    $stmt->execute();
                }
            } catch (PDOException $exception) {
                echo "Erreur de connexion : " . $exception->getMessage();
            }
            break;

        // Mettre à jour les enregistements
        case 'update':
            $id = htmlspecialchars(strip_tags($_GET['id']));
            if ($status == 0) {
                try {
                    $update = $conn->prepare("UPDATE foundlost SET status = 1 WHERE id_object = $id");
                    $update->bindParam(':status', $status, PDO::PARAM_INT);
                    $update->execute();
                } catch (PDOException $exception) {
                    echo "Erreur de connexion : " . $exception->getMessage();
                }
            }
            if ($status == 1) {
                try {
                    $update = $conn->prepare("UPDATE foundlost SET status = 0 WHERE id_object = $id");
                    $update->bindParam(':status', $status, PDO::PARAM_INT); 
                    $update->execute();
                } catch (PDOException $exception) {
                    echo "Erreur de connexion : " . $exception->getMessage();
                }
            }

            break;

        //Supprimer un enregistrement existant
        case 'delete':
            
            $id = htmlspecialchars(strip_tags($_GET['id']));
            try {
                $delete = $conn->prepare("DELETE FROM foundlost WHERE id_object = $id");
                $delete->execute();
                $supp = $delete->fetchAll();
            } catch (PDOException $exception) {
                echo "Erreur de connexion : " . $exception->getMessage();
            }
            break;
            default:
            echo 'erreur';
            break;
    }
}
