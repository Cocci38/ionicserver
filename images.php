<?php


header('Access-Control-Allow-Origin: *');
error_log(print_r($_FILES,1));
$target_path = "picture/";

                $target_path = $target_path . basename($_FILES['file']['name']);
                error_log(print_r($_FILES,1));
                if(move_uploaded_file($_FILES['file']['tmp_name'], $target_path)){
                    header('Content-type: application/json');
                    $data = ['success' =>true, 'message' => 'Upload and move succes'];
                    echo json_encode($data);
                } else {
                    header('Content-type: application/json');
                    $data = ['success' =>false, 'message' => 'Une erreur est survenu pendant l\'upload'];
                    echo json_encode($data);
                }

?>