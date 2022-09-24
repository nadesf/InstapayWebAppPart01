<?php

session_start();

# L'URL et les endpoints de notre API.
#$domain = "http://localhost:8000/api/v1/";
$domain = "http://164.92.134.116/api/v1/";
$endpoints = array(
"signup" => "". $domain . "signup/",
"active_account" => "". $domain . "active_my_account/",
"login_token" => "". $domain . "login/token/",
"second_authentication" => "". $domain . "login/second_authentication/",
"ask_reset_password" => "". $domain . "ask_for_reset_password/",
"reset_password" => "". $domain . "reset_password/",
"change_password" => "". $domain . "change_password/",
"edit_profil" => "". $domain . "users/profil/",
"logout" => "". $domain . "logout/",
"edit_security" => "". $domain . "users/security/",
"transactions" => "". $domain . "users/transactions/",
"payment_request" => "". $domain . "users/payment_request/",
"accounts" => "". $domain . "users/accounts/",
"user_infos" => "". $domain . "users/",
);


if (isset($_POST["full_name"]) && isset($_POST["email"]) && isset($_POST["phone_number"])) {

    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $phone_number = $_POST["phone_number"];

    $url = $endpoints['edit_profil'];
    $method = "PUT";
    $use_token = 1;
    $data = array(
        "full_name" => $full_name,
        "email_user" => $email,
        "phone_number" => $phone_number
    );

    # Envoie des données et Analyse de la réponse.
    $result = send_data_to_api($url, $data, $method, $use_token);
    $http_code = (int) $result['http_code'];
    if ($http_code === 200) {
        $_SESSION["check"] = 1;
        $_SESSION["msg"] = "Vos Informations ont été mise à jour !";

        // On récupère les informations 
        $result = get_user_info();
        if ($result[0] === 1) {
        $result = $result[1];
        $_SESSION["full_name"] = $result['response']->full_name;
        $_SESSION["email"] = $result['response']->email;
        $_SESSION["phone_number"] = $result['response']->phone_number;
        $_SESSION["status"]  = $result['response']->status;
        $_SESSION["double_authentication"]  = $result['response']->double_authentication;
        }

        $_SESSION["check_editprofil"] = 1;

        header("Location: ../settings.php");

    }else if ($http_code === 401) {
        session_destroy();
        header("Location: ../login.php");
    }else {
        $_SESSION["check_editprofil"] = 0;
        header("Location: ../settings.php");  
    }

} else if (isset($_POST["old_password"]) && isset($_POST["new_password"]) && isset($_POST["confirm_password"])) {
    if ($_POST["confirm_password"] != $_POST["new_password"]) {
        $_SESSION["check"] = 0;
        $_SESSION["msg"] = "Les deux mot de passe ne correspondent pas";
        header("Location: ../settings_security.html");
    }

    # Récupération et Préparation des données
    $old_password = htmlspecialchars($_POST["old_password"]);
    $new_password = htmlspecialchars($_POST["new_password"]);

    $url = $endpoints["change_password"];
    $method = "PATCH";
    $use_token = 1;
    $data = array(
        "old_password" => $old_password,
        "new_password" => $new_password
    );

    # Envoie de la requête et analyse de la réponse 
    $result = send_data_to_api($url, $data, $method, $use_token);

    $http_code = (int) $result["http_code"];
    if ($http_code === 200) { # Opération réussie
        // Traitement en cas de succès 

        $_SESSION["check_changepassword"] = 1;
        header('Location: ../settings_security.php');
    }else if ($http_code === 401) { # Le Token n'est plus valide
        session_destroy();
        header("Location: ../login.php");
    } else { # Une erreur survenu lors du traitement

        #header("Location: settings_security.");
        $_SESSION["check_changepassword"] = 0;
        header('Location: ../settings_security.php');
    }
} else {

    # Récupération et Préparation des données
    $double_authentication = NULL;
    $email_alert = NULL;
    $account_protection = NULL;
    $account_protection_code = "";
    $url = $endpoints["edit_security"] . "?";
    $method = "PATCH";
    $use_token = 1;


    if ( (int) $_SESSION["double_authentication"] === 1 && !isset($_POST["double_authentication"])) {
        // Todo
        $url = $url . "double_authentication=0";
        // // déactivation de la double authentification
        $data = array("nothing");
        $result = send_data_to_api($url, $data, $method, $use_token);
        $http_code = (int) $result["http_code"];
        if ($http_code === 200) { # Opération réussie
            // Traitement en cas de succès.

            // On récupère les informations 
            $result = get_user_info();
            if ($result[0] === 1) {
            $result = $result[1];
            $_SESSION["full_name"] = $result['response']->full_name;
            $_SESSION["email"] = $result['response']->email;
            $_SESSION["phone_number"] = $result['response']->phone_number;
            $_SESSION["status"]  = $result['response']->status;
            $_SESSION["double_authentication"]  = $result['response']->double_authentication;
            }
            header("Location: ../settings_security.php");

        }else if ($http_code === 401) { # Le Token n'est plus valide
            session_destroy();
            header("Location: ../login.php");
        } else { # Une erreur survenu lors du traitement
            $_SESSION["check_da"] = 0;
            header("Location: ../settings_security.php");
        }

    } else if ((int) $_SESSION["double_authentication"] === 0 && isset($_POST["double_authentication"])) {
        // Activation de la double authentification
        $url = $url . "double_authentication=1";

        // Pour la seconde authentification
        $data = array("nothing");
        $result = send_data_to_api($url, $data, $method, $use_token);
        $http_code = (int) $result["http_code"];
        if ($http_code === 200) { # Opération réussie
            // Traitement en cas de succès 

            // On récupère les informations 
            $result = get_user_info();
            if ($result[0] === 1) {
            $result = $result[1];
            $_SESSION["full_name"] = $result['response']->full_name;
            $_SESSION["email"] = $result['response']->email;
            $_SESSION["phone_number"] = $result['response']->phone_number;
            $_SESSION["status"]  = $result['response']->status;
            $_SESSION["double_authentication"]  = $result['response']->double_authentication;
            }
            header("Location: ../settings_security.php");
        }else if ($http_code === 401) { # Le Token n'est plus valide
            session_destroy();
            header("Location: ../login.php");
        } else { # Une erreur survenu lors du traitement
            $_SESSION["check"] = 0;
            $_SESSION["msg"] = "Impossible d'éffectuer cette opération";
            header("Location: ../settings_security.php");
        }
    } else {
        $_SESSION["check"] = 0;
        $_SESSION["msg"] = "Impossible d'éffectuer cette opération";
        header("Location: ../settings_security.php");
    }

    // Pour la protection du compte
    if (((int) $_SESSION["account_protection"] === 1 && !isset($_POST["account_protection"]) && isset($_POST["protection_code"]))) {
        $url = $url . "account_protection=0";

        // Pour la seconde authentification
        $account_protection_code = htmlspecialchars($_POST["protection_code"]);

        $data = array(
            "account_protection_code" => $account_protection_code
        );
        $result = send_data_to_api($url, $data, $method, $use_token);
        $http_code = (int) $result["http_code"];
        if ($http_code === 200) { # Opération réussie

            // Traitement en cas de succès 
            $result = get_user_accounts_info();
            if ($result[0] === 1) {
                $result = $result[1];
                $_SESSION["status_account"] = $result['response']->status;
                $_SESSION["amount"] = $result['response']->amount;
                $_SESSION["date_created"] = $result['response']->date_created;
                $_SESSION["account_protection"]  = $result['response']->account_protection;
                $_SESSION["provider"]  = $result['response']->provider;
            }
            header("Location: ../settings_security.php");
        } else { # Une erreur survenu lors du traitement
            $_SESSION["check"] = 0;
            $_SESSION["msg"] = "Le code de protection des transactions est incorrecte !";
            header("Location: ../settings_security.php");
        }
    } else if ((int) $_SESSION["account_protection"]===0 && isset($_POST["account_protection"]) && isset($_POST["protection_code"])) {
        $url = $url . "account_protection=1";
        $account_protection_code = htmlspecialchars($_POST["protection_code"]);
        // Pour la seconde authentification
        $data = array(
            "account_protection_code" => $account_protection_code
        );
        $result = send_data_to_api($url, $data, $method, $use_token);
        $http_code = (int) $result["http_code"];
        if ($http_code === 200) { # Opération réussie
            // Traitement en cas de succès 
            
            // Traitement en cas de succès 
            $result = get_user_accounts_info();
            if ($result[0] === 1) {
                $result = $result[1];
                $_SESSION["status_account"] = $result['response']->status;
                $_SESSION["amount"] = $result['response']->amount;
                $_SESSION["date_created"] = $result['response']->date_created;
                $_SESSION["account_protection"]  = $result['response']->account_protection;
                $_SESSION["provider"]  = $result['response']->provider;
            }
            header("Location: ../settings_security.php");
        } else { # Une erreur survenu lors du traitement
            $_SESSION["check"] = 0;
            $_SESSION["msg"] = "Le code de protection des transactions est incorrecte !";
            header("Location: ../settings_security.php");
        }
    } else {
        $_SESSION["check"] = 0;
        $_SESSION["msg"] = "Impossible d'éffectuer cette opération";
        header("Location: ../settings_security.php");
    }


}

# ----------------------------------------------

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

function get_user_info() {

    # Les données
    global $endpoints;
    $url = $endpoints['user_infos'];
    $use_token = 1;

    # Envoie et Analyse de la réponse
    $result = get_data_from_api($url, $use_token);
    $http_code = (int) $result["http_code"];
    if ($http_code === 200) {

        $res = [1, $result];
        return $res;

    } else {
        $res = [0];
        return $res;
    }
}

function get_user_accounts_info() {

    # Les données
    global $endpoints;
    $url = $endpoints['accounts'];
    $use_token = 1;

    # Envoie et Analyse de la réponse
    $result = get_data_from_api($url, $use_token);
    $http_code = (int) $result["http_code"];

    if ($http_code === 200) {

        $res = [1, $result];
        return $res;

    } else {
        $res = [0];
        return $res;
    }
}

?>