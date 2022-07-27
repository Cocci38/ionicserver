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
// $id = intval($_GET["id_object"]);
// Prépare et exécute la requête de lecture de la table (try/catch)
// switch ($status) {
//     case 'get':
    if (isset($_GET['id'])) {
        try {
            $select = $conn->prepare("SELECT * FROM foundlost WHERE id_object = " . $_GET['id']);
            // $select->bindValue(":id", $_GET['id']);
            $select->execute();
            $result =$select->fetch(PDO::FETCH_ASSOC);
            echo json_encode($result);
            }
            catch(PDOException$e) { 
                echo $e->getMessage(); 
            }
        
    } else {
        try {
            $select = $conn->prepare("SELECT * FROM foundlost ORDER BY date DESC");
            $select->execute();
            // 
            while($row = $select->fetch(PDO::FETCH_OBJ) )
            { // Assign each row of data to associat
                    $data[] = $row;
            }
            // $select->execute();
            // $result =$select->fetchAll(PDO::FETCH_OBJ);
            // $myJSON = json_encode($data);
            // echo "<pre>",print_r($_GET['status'] ,1),"</pre>";die();
                echo json_encode($data);
            }
            catch(PDOException$e) { echo $e->getMessage(); 
            }
    }
    

        // echo $result;
//         break;
//     case '1':
//         $select = $conn->prepare("SELECT * FROM foundlost WHERE id_object = :id");
//         $select->execute();
//         $result =$select->fetchAll(PDO::FETCH_ASSOC);
//         break;
// }
?>