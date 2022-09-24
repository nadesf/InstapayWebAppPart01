<?php 

session_start();

#$domain = "http://localhost:8000/api/v1/";
$domain = "http://164.92.134.116/api/v1/";
$endpoints = array(
    "ask_reset_password" => "". $domain . "ask_for_reset_password/",
    "reset_password" => "". $domain . "reset_password/",
);

if (isset($_POST["email"]) && isset($_POST["reset_code"]) && isset($_POST["new_password"])) {

    // ------------- RESTAURATION DE MOT DE PASSE ------------ V
    
        # Récupération des données
        $email = htmlspecialchars($_POST["email"]);
        $reset_code = htmlspecialchars($_POST["reset_code"]);
        $new_password = htmlspecialchars($_POST["new_password"]);
    
        $url = $endpoints['reset_password'];
        $method = "POST";
        $use_token = 0;
        $data = array(
            "email" => $email,
            "reset_code" => $reset_code,
            "new_password" => $new_password
        );
    
        # Envoie de la requête et Analyse de la réponse
        $result = send_data_to_api($url, $data, $method, $use_token);
        $http_code = (int) $result['http_code'];
        if ($http_code === 200) {
            // $msg = "Votre mot de passe à bien été réinitialisé !";
            //echo $msg;
            header("Location: ../reset_password_success.html");
        }else if (($http_code === 404) or ($http_code === 406)){
            $_SESSION["check"] === 0;
            header("Location: ../resetting.php");
        }else if ($http_code === 401) {
            echo "Le Token n'est plus valide";
        } else {
            $_SESSION["show_reset_code"] === 0;
            $_SESSION["msg"] = "Impossible d'éffectuer l'action demandé";
            echo $_SESSION["msg"];
        }
    
} else if (isset($_POST["email"])) {

    $url = $endpoints['ask_reset_password'];
    $email = htmlspecialchars($_POST["email"]);
    $data = array(
        "email" => $email
    );
    $method = "POST";
    $use_token = 0;

    $result = send_data_to_api($url, $data, $method, $use_token);
    $http_code = (int) $result['http_code'];

    # Analyse et Réponse au données reçue
    if ($http_code === 200) {

        $_SESSION["check"] = 1;
        $_SESSION["msg"] = "Un code de restauration vous à été envoyé par mail";
        echo $_SESSION["msg"];
        header('Location: ../resetting.php');

    } else if ($http_code === 404) {
        $msg = "L'utilisateur n'est pas reconnu !";
        echo $msg;

    } else {
        $msg = "Impossible d'éffectuer l'opération";
        echo $msg;
        var_dump($result["response"]);
    }

} else {
    echo "Onpération inconnu";
}

// DEMO - GET, POST, PUT, ET PATCH.
function get_data_from_api($url, $use_token) {

    # La requête
    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $url);
    if ($use_token === 1) {
        curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $_SESSION["Authorization"]));
    }else {
        curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    }
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($request);
    $response = json_decode($response);
    $httpcode = curl_getinfo($request, CURLINFO_HTTP_CODE);
    curl_close($request);

    # Traitement de la réponse.
    # On retourne le résultat.
    $result = array(
        "http_code" => $httpcode,
        "response" => $response
    );
    return $result;
    #var_dump($response->success)
}

function send_data_to_api($url, $data, $request_type, $use_token) {

    # Encodage des données
    $data_json = json_encode($data);

    # Création et envoie de la requête
    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $url);
    if ($use_token === 1) {
        curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $_SESSION["Authorization"]));
    }else {
        curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    }
    if ($request_type === "POST") {
        curl_setopt($request, CURLOPT_POST, 1);
    }else {
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $request_type);
    }
    curl_setopt($request, CURLOPT_POSTFIELDS,$data_json);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    $response  = curl_exec($request);
    $response = json_decode($response);
    $httpcode = curl_getinfo($request, CURLINFO_HTTP_CODE);
    curl_close($request); 

    # On retourne le résultat.
    $result = array(
        "http_code" => $httpcode,
        "response" => $response
    );
    return $result;
}

?>