<?php

session_start();

# L'URL et les endpoints de notre API.
#$domain = "http://localhost:8000/api/v1/";
$domain = "http://164.92.134.116/api/v1/";
$endpoints = array(

    "transactions" => "". $domain . "users/transactions/",
    "payment_request" => "". $domain . "users/payment_request/",
);

// ------------- INSCRIPTIONS DES UTILISATEURS ----------- V

if (isset($_POST["recipient"]) && isset($_POST["amount"]) && isset($_POST["date"])) {


    # Récupération et Préparation des données
    $recipient = htmlspecialchars($_POST["recipient"]);
    $amount = (float) htmlspecialchars($_POST["amount"]);
    $date = htmlspecialchars($_POST["date"]);

    if ($date === "") {
        $date = "20" . date('y-m-d');
    }

    if ((int) $_SESSION["account_protection"] === 1) {
        if (!isset($_POST["protection_code"])) {
            header("Location: ../home.php");
        }else {
            $protection_code = htmlspecialchars($_POST["protection_code"]);
        }
    } else {
        $protection_code = "";
    }

    $url = $endpoints["transactions"];
    $method = "POST";
    $use_token = 1;
    $data = array(
        "receiver" => $recipient,
        "amount" => $amount,
        "date" => $date,
        "account_protection_code" => $protection_code
    );

    # Envoie de la requête et Analyse de la réponse 
    $result = send_data_to_api($url, $data, $method, $use_token);

    $http_code = (int) $result["http_code"];
    if ($http_code === 200) { # Opération réussie
        // Traitement en cas de succès 
        $_SESSION["check_pay"] = 1;
        header("Location: ../transfer.php");
    }else if ($http_code === 401) { # Le Token n'est plus valide
        echo "Le token n'est plus valide !";
        var_dump($result);
    } else { # Une erreur survenu lors du traitement

        $_SESSION["check_pay"] = 0;
        header("Location: ../transfer.php");
    }

} else if (isset($_POST["recipient"]) && isset($_POST["amount"]) && isset($_POST["reason"])) {
    # Récupération et Préparation des données
    $receiver_email = htmlspecialchars($_POST["recipient"]);
    $amount = htmlspecialchars($_POST["amount"]);
    $reason = htmlspecialchars($_POST["reason"]);

    $url = $endpoints["payment_request"];
    $method = "POST";
    $use_token = 1;
    $data = array(
        "receiver" => $receiver_email,
        "amount" => $amount,
        "reason" => $reason
    );

    # Envoie de la requête et Analyse de la réponse 
    $result = send_data_to_api($url, $data, $method, $use_token);

    $http_code = (int) $result["http_code"];
    if ($http_code === 200) { # Opération réussie
        // Traitement en cas de succès 
        
        $_SESSION["check_payreq"] = 1;
        header("Location: ../transfer.php");
    }else if ($http_code === 401) { # Le Token n'est plus valide
        echo "Le token n'est plus valide !";
    } else { # Une erreur survenu lors du traitement
        $_SESSION["check_payreq"] = 0;
        $_SESSION["msg"] = "Impossible d'envoyer la reqête de paiement";
        header("Location: ../transfer.php");
    }
}else {
    echo "Nothing";
}

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