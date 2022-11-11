<?php
/**
 * Pour gérer l’ajout, la mise à jour et la suppression des enregistrements de la table objects.
 * On peut garder * pour le développement mais il faudra le changer lors du passage en production
 * Les headers permettent de s’affranchir de la « CORS policy » 
 * qui empêche deux serveurs différents de communiquer par défaut.
 */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

// Connexion à la base de données
require_once 'configuration.php';

// Récupérer le paramètre d’action de l’URL du client depuis $_GET[‘key’] 
// et nettoyer la valeur
$key = htmlspecialchars(strip_tags(trim(stripslashes($_GET['key']))));

// error_log(print_r($_GET['key'] == 'login', 1));

/**
 * Récupérer les paramètres envoyés par le client vers l’API
 * La commande file_get_contents('php://input') lit les informations brutes qui sont enregistrées 
 * dans un fichier temporaire avant qu'elles ne soient placées dans $_POST ou $_REQUEST 
 */
$input = file_get_contents('php://input');

// Si les paramètres envoyés par le client ne sont pas vide 
// OU $_GET est déclarée et différente de null ($_GET pour update et delete)
if (!empty($input) || isset($_GET['id'])) {
    $data = json_decode($input, true);
    // error_log(print_r($data,1));
    // En fonction du mode d'action requis
    switch ($key) {
            // Ajoute un nouvel enregistrement
        case 'create':
            $status = htmlspecialchars(trim(strip_tags(stripslashes($data['status']))));
            $description = htmlspecialchars(trim(strip_tags(stripslashes($data['description']))));
            $date = htmlspecialchars(trim(strip_tags(stripslashes($data['date']))));
            $location = htmlspecialchars(trim(strip_tags(stripslashes($data['location']))));
            $firstname = htmlspecialchars(trim(strip_tags(stripslashes($data['firstname']))));
            $lastname = htmlspecialchars(trim(strip_tags(stripslashes($data['lastname']))));
            $email = htmlspecialchars(trim(strip_tags(stripslashes($data['email']))));
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $user_id = htmlspecialchars(trim(strip_tags(stripslashes($data['user_id']))));
            try {
                if (
                    filter_var($email, FILTER_VALIDATE_EMAIL)
                    && preg_match("#^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$#", $email)
                    && preg_match("#^[a-zA-Z0-9-\' ,.?!æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{10,250}$#", $description)
                    && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,100}$#", $location)
                    && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,25}$#", $firstname)
                    && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,25}$#", $lastname)
                ) {
                    $stmt = $conn->prepare("INSERT INTO objects (status, description, date, location, firstname, lastname, email, user_id) 
                                            VALUES(:status, :description, :date, :location, :firstname, :lastname, :email, :user_id)");
                    $stmt->bindParam("status", $status, PDO::PARAM_INT);
                    $stmt->bindParam("description", $description, PDO::PARAM_STR);
                    $stmt->bindParam("date", $date, PDO::PARAM_STR);
                    $stmt->bindParam("location", $location, PDO::PARAM_STR);
                    $stmt->bindParam("firstname", $firstname, PDO::PARAM_STR);
                    $stmt->bindParam("lastname", $lastname, PDO::PARAM_STR);
                    $stmt->bindParam("email", $email, PDO::PARAM_STR);
                    $stmt->bindParam("user_id", $user_id, PDO::PARAM_INT);
                    $stmt->execute();

                    $idObject = $conn->lastInsertId();
                    //error_log($idObject);

                    $login = $conn->prepare("SELECT id_object FROM objects WHERE id_object=:id_object");
                    $login->bindParam("id_object", $idObject, PDO::PARAM_STR);
                    $login->execute();
                    $result = $login->fetch(PDO::FETCH_ASSOC);
                    echo json_encode($result);
                } else {
                    $create = false;
                    echo json_encode($create);
                }
            } catch (PDOException $exception) {
                echo "Erreur de connexion : " . $exception->getMessage();
            }
            break;

            // Mettre à jour le status pour le passé de 0 à 1
        case 'update':
            $id = htmlspecialchars(strip_tags(trim(stripslashes($_GET['id']))));
            $status = "";
            try {
                if ($status == 0) {
                    $update = $conn->prepare("UPDATE objects SET status = 1 WHERE id_object = $id");
                    $update->bindParam(':status', $status, PDO::PARAM_INT);
                    $update->execute();
                }
            } catch (PDOException $exception) {
                echo "Erreur de connexion : " . $exception->getMessage();
            }

            break;

            //Supprimer un enregistrement existant
        case 'delete':
            $id = htmlspecialchars(strip_tags(trim(stripslashes($_GET['id']))));
            try {
                $delete = $conn->prepare("DELETE FROM pictures WHERE object_id = $id");
                $delete->execute();
                $supp = $delete->fetchAll();
            } catch (PDOException $exception) {
                echo "Erreur de connexion : " . $exception->getMessage();
            }
            try {
                $delete = $conn->prepare("DELETE FROM objects WHERE id_object = $id");
                $delete->execute();
                $supp = $delete->fetchAll();
            } catch (PDOException $exception) {
                echo "Erreur de connexion : " . $exception->getMessage();
            }
            break;

            // Créer un nouvel utilisateur
        case 'sign-up':

            $username = htmlspecialchars(trim(strip_tags(stripslashes($data['username']))));
            $user_email = htmlspecialchars(trim(strip_tags(stripslashes($data['user_email']))));
            $user_email = filter_var($user_email, FILTER_SANITIZE_EMAIL);
            $password = htmlspecialchars(trim(strip_tags(stripslashes($data['password']))));
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $login = $conn->prepare("SELECT user_email FROM users");
                $login->execute();
                $result = $login->fetch(PDO::FETCH_ASSOC);

                if ($user_email !== $result['user_email']) {
                    if (filter_var($user_email, FILTER_VALIDATE_EMAIL) 
                    && preg_match("#^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$#", $user_email) 
                    && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,25}$#", $username) 
                    && preg_match("#^[a-zA-Z0-9-?!*+/]{8,25}$#", $password)) {
                        $user = $conn->prepare("INSERT INTO users (username, user_email, password) 
                                                VALUES(:username, :user_email, :password)");
                        $user->bindParam("username", $username, PDO::PARAM_STR);
                        $user->bindParam("user_email", $user_email, PDO::PARAM_STR);
                        $user->bindParam("password", $passwordHash, PDO::PARAM_STR);
                        $user->execute();
                        // error_log("mot de passe : ", $password,1);
                        // error_log("email", $user_email,1);
                        // error_log("nom", $username,1);
                        $nameUser = true;

                        $login = $conn->prepare("SELECT id_user, username, user_email FROM users WHERE user_email=:user_email");
                        $login->bindParam("user_email", $user_email, PDO::PARAM_STR);
                        $login->execute();
                        $result = $login->fetch(PDO::FETCH_ASSOC);
                        echo json_encode($result, $nameUser);
                    } else {
                        $nameUser = false;
                        echo $name = json_encode($nameUser);
                    }
                } else {
                    $email = false;
                    echo $email_user = json_encode($email);
                }
            } catch (PDOException $exception) {
                echo "Erreur de connexion : " . $exception->getMessage();
            }
            break;

            // Connexion d'un utilisateur
        case 'login':

            $user_email = htmlspecialchars(trim(strip_tags(stripslashes($data['user_email']))));
            $user_email = filter_var($user_email, FILTER_SANITIZE_EMAIL);
            $password = htmlspecialchars(trim(strip_tags(stripslashes($data['password']))));
            try {
                if ($user_email !== "" && $password !== "") {
                    if (filter_var($user_email, FILTER_VALIDATE_EMAIL) 
                    && preg_match("#^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$#", $user_email) 
                    && preg_match("#^[a-zA-Z0-9-?!*+/]{8,25}$#", $password)) {
                        $login = $conn->prepare("SELECT id_user, username, user_email, password 
                                                FROM users 
                                                WHERE user_email=:user_email");
                        $login->bindParam("user_email", $user_email, PDO::PARAM_STR);
                        $login->execute();
                        $result = $login->fetch(PDO::FETCH_ASSOC);
                        // error_log(print_r('coucou 0 '), 1);
                        if (empty($result['user_email'])) {
                            $user = false;
                        } else if (count($result) > 0) {
                            // error_log(print_r('coucou 1 '), 1);
                            if (password_verify($password,  $result['password']) && $result['user_email'] == $user_email) {
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
