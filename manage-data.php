<?php
// Pour gérer l’ajout, la mise à jour et la suppression des enregistrements de la table foundlost.
// On peut garder * pour le développement mais il faudra le changer lors du passage en production
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

// Connexion à la base de données
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

// Récupérer le paramètre d’action de l’URL du client depuis $_GET[‘key’] et nettoyer la valeur
$key = htmlspecialchars(strip_tags(trim(stripslashes($_GET['key']))));

// Récupérer les paramètres envoyés par le client vers l’API
// C'est la façon recommandée pour lire le contenu d'un fichier dans une chaîne de caractères.
$input = file_get_contents('php://input');

// Si les paramètres envoyés par le client ne sont pas vide OU $_GET est déclarée et différente de null ($_GET pour update et delete)
if (!empty($input) || isset($_GET)) {
    $data = json_decode($input, true);

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
            $users_id = htmlspecialchars(trim(strip_tags(stripslashes($data['users_id']))));
            try {
                $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                // preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $description);
                if (filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{10,}$#", $description) && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $location) && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $firstname) && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $lastname)) {
                    $stmt = $conn->prepare("INSERT INTO foundlost (status, description, date, location, firstname, lastname, email, users_id) VALUES(:status, :description, :date, :location, :firstname, :lastname, :email, :users_id)");
                    $stmt->bindParam("status", $status, PDO::PARAM_INT);
                    $stmt->bindParam("description", $description, PDO::PARAM_STR);
                    $stmt->bindParam("date", $date, PDO::PARAM_STR);
                    $stmt->bindParam("location", $location, PDO::PARAM_STR);
                    $stmt->bindParam("firstname", $firstname, PDO::PARAM_STR);
                    $stmt->bindParam("lastname", $lastname, PDO::PARAM_STR);
                    $stmt->bindParam("email", $email, PDO::PARAM_STR);
                    $stmt->bindParam("users_id", $users_id, PDO::PARAM_INT);
                    $stmt->execute();

                    $create = true;
                    echo json_encode($create);
                } else {
                    $create = false;
                    echo json_encode($create);
                }
            } catch (PDOException $exception) {
                echo "Erreur de connexion : " . $exception->getMessage();
            }
            break;

            // Mettre à jour les enregistements
        case 'update':
            $id = htmlspecialchars(strip_tags(trim(stripslashes($_GET['id']))));
            $status = "";
            try {
                if ($status == 0) {
                    $update = $conn->prepare("UPDATE foundlost SET status = 1 WHERE id_object = $id");
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
                $delete = $conn->prepare("DELETE FROM foundlost WHERE id_object = $id");
                $delete->execute();
                $supp = $delete->fetchAll();
            } catch (PDOException $exception) {
                echo "Erreur de connexion : " . $exception->getMessage();
            }
            break;
        case 'sign-up':

            // FILTER_SANITIZE_EMAIL : Supprime tous les caractères sauf les lettres, chiffres, et !#$%&'*+-=?^_`{|}~@.[].
            $username = htmlspecialchars(trim(strip_tags(stripslashes($data['username']))));
            $user_email = htmlspecialchars(trim(strip_tags(stripslashes($data['user_email']))));
            $password = htmlspecialchars(trim(strip_tags(stripslashes($data['password']))));
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            try {
                $user_email = filter_var($user_email, FILTER_SANITIZE_EMAIL);
                $login = $conn->prepare("SELECT user_email FROM users");
                $login->execute();
                $result = $login->fetch(PDO::FETCH_ASSOC);

                if ($user_email !== $result['user_email']) {
                    if (filter_var($user_email, FILTER_VALIDATE_EMAIL) && preg_match("#^[a-zA-Z0-9-\' æœçéàèùâêîôûëïüÿÂÊÎÔÛÄËÏÖÜÀÆÇÉÈŒÙ]{3,}$#", $username) && preg_match("#^[a-zA-Z0-9-?!*+/]{8,}$#", $password)) {
                        $user = $conn->prepare("INSERT INTO users (username, user_email, password) VALUES(:username, :user_email, :password)");
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
        case 'login':

            $user_email = htmlspecialchars(trim(strip_tags(stripslashes($data['user_email']))));
            $password = htmlspecialchars(trim(strip_tags(stripslashes($data['password']))));
            try {
                if ($user_email !== "" && $password !== "") {
                    if (filter_var($user_email, FILTER_VALIDATE_EMAIL) && preg_match("#^[a-zA-Z0-9-?!*+/]{8,}$#", $password)) {
                        $login = $conn->prepare("SELECT id_user, username, user_email, password FROM users WHERE user_email=:user_email");
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
