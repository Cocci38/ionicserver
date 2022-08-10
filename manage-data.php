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
// function validate($valid){
//     $description = preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $valid);
//     $location = preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $valid);
//     $firstname = preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $valid);
//     $lastname = preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $valid);
//     if ($description && $location && $firstname && $lastname){
//         return true;
//     }else{
//         return false;
//     }
// }
if (!empty($input) || isset($_GET)) {
    $data = json_decode($input, true);

    // @$status = strip_tags($data['status']);
    // @$description = strip_tags($data['description']); 
    // @$date = strip_tags($data['date']);
    // @$location = strip_tags($data['location']);
    // @$firstname = strip_tags($data['firstname']);
    // @$lastname = strip_tags($data['lastname']);
    // @$email = strip_tags($data['email']);
    // En fonction du mode d'action requis
    switch ($key) {
            // Ajoute un nouvel enregistrement
        case 'create':
            @$status = htmlspecialchars(trim(strip_tags($data['status'])));
            @$description = htmlspecialchars(trim(strip_tags($data['description'])));
            @$date = htmlspecialchars(trim(strip_tags($data['date'])));
            @$location = htmlspecialchars(trim(strip_tags($data['location'])));
            @$firstname = htmlspecialchars(trim(strip_tags($data['firstname'])));
            @$lastname = htmlspecialchars(trim(strip_tags($data['lastname'])));
            @$email = htmlspecialchars(trim(strip_tags($data['email'])));
            try {
                $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                // preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $description);
                if (filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $description) && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $location) && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $firstname) && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $lastname)) {
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
            $id = htmlspecialchars(strip_tags(trim($_GET['id'])));
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
            $id = htmlspecialchars(strip_tags(trim($_GET['id'])));
            try {
                $delete = $conn->prepare("DELETE FROM foundlost WHERE id_object = $id");
                $delete->execute();
                $supp = $delete->fetchAll();
            } catch (PDOException $exception) {
                echo "Erreur de connexion : " . $exception->getMessage();
            }
            break;
        case 'users':

            @$username = htmlspecialchars(trim(strip_tags($data['username'])));
            @$user_email = htmlspecialchars(trim(strip_tags($data['user_email'])));
            @$password = htmlspecialchars(trim(strip_tags($data['password'])));
            @$passwordHash = password_hash($password, PASSWORD_DEFAULT);

            try {
                $user_email = filter_var($user_email, FILTER_SANITIZE_EMAIL);
                $login = $conn->prepare("SELECT user_email FROM users");
                $login->execute();
                $result = $login->fetch(PDO::FETCH_ASSOC);

                if (filter_var($user_email, FILTER_VALIDATE_EMAIL) && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $username) && preg_match("#^[a-zA-Z0-9-?!*+/]{8,}$#", $password)) {
                    if ($user_email !== $result['user_email']) {
                        $user = $conn->prepare("INSERT INTO users (username, user_email, password) VALUES(:username, :user_email, :password)");
                        $user->bindParam("username", $username, PDO::PARAM_STR);
                        $user->bindParam("user_email", $user_email, PDO::PARAM_STR);
                        $user->bindParam("password", $passwordHash, PDO::PARAM_STR);
                        $user->execute();
                        // error_log("mot de passe : ", $password,1);
                        // error_log("email", $user_email,1);
                        // error_log("nom", $username,1);
                        $nameUser = true;
                        echo $name = json_encode($nameUser);
                    } else {
                        $email = false;
                        echo $email_user = json_encode($email);
                    }
                }
            } catch (PDOException $exception) {
                echo "Erreur de connexion : " . $exception->getMessage();
            }
            break;
        case 'login':

            @$username = htmlspecialchars(trim(strip_tags($data['username'])));
            @$user_email = htmlspecialchars(trim(strip_tags($data['user_email'])));
            @$password = htmlspecialchars(trim(strip_tags($data['password'])));
            try {
                if ($username !== "" && $user_email !== "" && $password !== "") {
                    if (filter_var($user_email, FILTER_VALIDATE_EMAIL) && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $username) && preg_match("#^[a-zA-Z0-9-?!*+/]{8,}$#", $password)) {
                        $login = $conn->prepare("SELECT id_user, username, user_email, password FROM users WHERE user_email=:user_email");
                        $login->bindParam("user_email", $user_email, PDO::PARAM_STR);
                        $login->execute();
                        $result = $login->fetch(PDO::FETCH_ASSOC);
                        // error_log(print_r('coucou 0 '), 1);
                        if (empty($result['user_email'])) {
                            $user = false;
                        } else if (count($result) > 0) {
                            // error_log(print_r('coucou 1 '), 1);
                            if (password_verify($password,  $result['password']) && $result['user_email'] == $user_email && $result['username'] == $username) {
                                $user = true;
                            } else {
                                $user = false;
                            }
                        }
                    }
                }
                if ($user == true) {
                    echo json_encode($result);
                } else {
                    echo json_encode($user);
                }
            } catch (PDOException $exception) {
                echo "Erreur de connexion : " . $exception->getMessage();
            }
            break;
        default:
            echo 'erreur';
            break;
    }
}
