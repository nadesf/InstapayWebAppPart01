<?php

session_start();
// ======================================================
//  CE FICHIER PHP CONTIENT TOUTES LES FONCTIONS
//         D'ECHANGE DE DONNEES AVEC L'API
// ======================================================

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


/*  Création des variables de session 
$_SESSION["check"] = 1;
$_SESSION["msg"] = 1;
$_SESSION["full_name"] = "";
$_SESSION["email"] = "";
$_SESSION["phone_number"] = "";
$_SESSION["status_user"]  = "";
$_SESSION["double_authentication"]  = "";
$_SESSION["status_account"] = "";
$_SESSION["amount"] = "";
$_SESSION["date_created"] = "";
$_SESSION["account_protection"]  = "";
$_SESSION["provider"]  = "";
$_SESSION["sender"] = "";
$_SESSION["recipient"] = "";
*/

// ------------- INSCRIPTIONS DES UTILISATEURS ----------- V

if (isset($_POST["full_name"]) and isset($_POST["email"]) and isset($_POST["password"])) {

    # Récupèration et préparation des données pour l'envoie
    $full_name = htmlspecialchars($_POST["full_name"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);

    $url = $endpoints['signup'];
    $method = "POST";
    $use_token = 0;
    $data = array(
        "full_name" => $full_name,
        "email" => $email,
        "password" => $password
    );

    # Envoie des données
    $result = send_data_to_api($url, $data, $method, $use_token);
    $http_code = (int) $result['http_code'];
    if ($http_code === 201) {

        header("Location:../account_created.html");

    }else {
        $_SESSION["check"] = 0;
        $_SESSION["msg"] = "Cette adresse mail est déjà utilisé";
        header("Location:../sign.php");
    }
} else if (isset($_POST["email"]) and isset($_POST["password"])) { // ------------- CONNEXION DES UTILISATEURS -------------- V

    # Récupération des informations 
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);
    $url = $endpoints['login_token'];
    $method = "POST";
    $data = "email=".$email."&password=".$password."";

    $result = login_user($url, $data, $method);
    $http_code = (int) $result["http_code"];
    #var_dump($http_code);
    #var_dump($result);
    if ($http_code === 200) {
        $_SESSION["refresh_token"] = $result['response']->refresh;
        $_SESSION["access_token"] = $result['response']->access;

        $_SESSION["Authorization"] = "Authorization: Bearer ".$_SESSION["access_token"];

        $result = get_user_info();

        if ($result[0] === 1) {
            $result = $result[1];

            $_SESSION["full_name"] = $result['response']->full_name;
            $_SESSION["email"] = $result['response']->email;
            $_SESSION["phone_number"] = $result['response']->phone_number;
            $_SESSION["status"]  = $result['response']->status;
            $_SESSION["double_authentication"]  = $result['response']->double_authentication;
        }

        $do_second_authentication = (int) $_SESSION["double_authentication"];
        if ($do_second_authentication === 1) {

            $_SESSION["second_auth"] = 1;

            $url = $endpoints["second_authentication"];
            $use_token = 1;
            $result = get_data_from_api($url, $use_token);
            //var_dump($result);
        
            $http_code = $result["http_code"];

            if ($http_code === 200) {
                header("Location:../second_authentication.php");
            } else {
                $_SESSION["check"] = 0;
                $_SESSION["msg"] = "Impossible de faire la seconde authentification";
                header("Location:../login.php");
            }

        } else {

            $result = get_user_accounts_info();
            if ($result[0] === 1) {
                $result = $result[1];
                $_SESSION["status_account"] = $result['response']->status;
                $_SESSION["amount"] = $result['response']->amount;
                $_SESSION["date_created"] = $result['response']->date_created;
                $_SESSION["account_protection"]  = $result['response']->account_protection;
                $_SESSION["provider"]  = $result['response']->provider;
            } else {
                header("Location: ../login.php");
            }

            $result = get_user_transactions_list();
            if ($result[0] === 1) {
                $result = $result[1];
                $_SESSION["sender"] = $result['response']->sender;
                $_SESSION["recipient"] = $result['response']->recipient;

                for ($i = 0; $i < count($_SESSION["sender"]); $i++) {
                    $_SESSION["transactions_move"][] = "s";
                }
                for ($i = 0; $i < count($_SESSION["recipient"]); $i++) {
                    $_SESSION["transactions_move"][] = "r";
                } 
                $_SESSION["all_transactions"] = concatenate_table($_SESSION["sender"], $_SESSION["recipient"]);
                //$_SESSION["user_id_list"] = []
                $tab = [];
                for ($i = 0; $i < count($_SESSION["sender"]); $i++) {
                    $tab[] = $_SESSION["sender"][$i]->recipient;
                    #$_SESSION["users_id"][] = $_SESSION["sender"][$i]->recipient;
                }
                for ($i = 0; $i < count($_SESSION["recipient"]); $i++) {
                    $tab[] = $_SESSION["recipient"][$i]->sender;
                    #$_SESSION["users_id"][] = $_SESSION["recipient"][$i]->sender;
                }

                // Nous allons trier les tableaux avant de les envoyers.
                # on récupère les ID des transactions.
                $tab_id = [];
                for ($i = 0; $i < count($_SESSION["all_transactions"]); $i++) {
                    $tab_id[] = (int) $_SESSION["all_transactions"][$i]->id;
                }
                sort($tab_id);
                $tab_id = array_reverse($tab_id);
                $transactions_list = $_SESSION["all_transactions"];
                $transactions_move = $_SESSION["transactions_move"];
                for ($i = 0; $i < count($tab_id); $i++) {
                    for ($j = 0; $j < count($tab_id); $j++) {
                        $val = (int) $transactions_list[$j]->id;
                        if ($tab_id[$i] === $val) {
                            $aux = $transactions_list[$i];
                            $transactions_list[$i] = $transactions_list[$j];
                            $transactions_list[$j] = $aux;

                            $aux = $tab[$i];
                            $tab[$i] = $tab[$j];
                            $tab[$j] = $aux;

                            $aux = $transactions_move[$i];
                            $transactions_move[$i] = $transactions_move[$j];
                            $transactions_move[$j] = $aux;
                        }
                    }
                }
                $_SESSION["all_transactions"] = $transactions_list;
                $_SESSION["transactions_move"] = $transactions_move;

                // Envoie de la requête
                $url = $endpoints["user_infos"];
                $method = "POST";
                $use_token = 1;
                $data = array(
                    "get_user_info" => $tab
                );
                $res = send_data_to_api($url, $data, $method, $use_token);
                $http_code = $res["http_code"];

                if ($http_code === 200) {
                    $_SESSION["users_address"] = $res["response"]->response;

                    $_SESSION["IsAuthenticate"] = 1;
                    header("Location: ../home.php");
            
                } else {
                    header("Location: ../login.php");
                }

                //var_dump($_SESSION["user_id_info"]);
               //var_dump($_SESSION["all_transactions"]);
            #header("Location: ../home.php");
        } else {
            header("Location: ../login.php");
        }
    }

    } else {
        $_SESSION["check_login"] = 0;
        header("Location:../login.php"); #http://164.92.134.116:12000/login.php
    }

} else if (isset($_GET["logout"])) {

    # Récupération des informations 
    $refresh = $_SESSION["refresh_token"];
    $url = $endpoints['logout'];
    $method = "POST";
    $data = array(
        "refresh" => $refresh
    );

    $result = send_data_to_api($url, $data, $method, 1);
    $http_code = (int) $result["http_code"];

    session_destroy();
    header("Location:../login.php");

} else if (isset($_POST["second_authentication"])) {

    # Récupération des informations 
    $code = htmlspecialchars($_POST["second_authentication"]);
    $url = $endpoints['second_authentication'];
    $method = "POST";
    $data = array(
        "second_authentication_code" => $code
    );

    $result = send_data_to_api($url, $data, $method, 1);
    $http_code = (int) $result["http_code"];

    if ($http_code === 200) {
        
        $result = get_user_accounts_info();
        if ($result[0] === 1) {
            $result = $result[1];
            $_SESSION["status_account"] = $result['response']->status;
            $_SESSION["amount"] = $result['response']->amount;
            $_SESSION["date_created"] = $result['response']->date_created;
            $_SESSION["account_protection"]  = $result['response']->account_protection;
            $_SESSION["provider"]  = $result['response']->provider;
        } else {
            header("Location: ../login.php");
        }

        $result = get_user_transactions_list();
        if ($result[0] === 1) {
            $result = $result[1];
            $_SESSION["sender"] = $result['response']->sender;
            $_SESSION["recipient"] = $result['response']->recipient; 

            // On différencie les transactions où l'on à été l'émetteur et ou l'on à été le recepteur.
            for ($i = 0; $i < count($_SESSION["sender"]); $i++) {
                $_SESSION["transactions_move"][] = "s";
            }
            for ($i = 0; $i < count($_SESSION["recipient"]); $i++) {
                $_SESSION["transactions_move"][] = "r";
            } 
            $_SESSION["all_transactions"] = concatenate_table($_SESSION["sender"], $_SESSION["recipient"]);
            $tab = [];
            for ($i = 0; $i < count($_SESSION["sender"]); $i++) {
                $tab[] = $_SESSION["sender"][$i]->recipient;
                #$_SESSION["users_id"][] = $_SESSION["sender"][$i]->recipient;
            }
            for ($i = 0; $i < count($_SESSION["recipient"]); $i++) {
                $tab[] = $_SESSION["recipient"][$i]->sender;
                #$_SESSION["users_id"][] = $_SESSION["recipient"][$i]->sender;
            }

            // Nous allons trier les tableaux avant de les envoyers.
            # on récupère les ID des transactions.
            $tab_id = [];
            for ($i = 0; $i < count($_SESSION["all_transactions"]); $i++) {
                $tab_id[] = (int) $_SESSION["all_transactions"][$i]->id;
            }
            sort($tab_id);
            $tab_id = array_reverse($tab_id);
            $transactions_list = $_SESSION["all_transactions"];
            $transactions_move = $_SESSION["transactions_move"];
            for ($i = 0; $i < count($tab_id); $i++) {
                for ($j = 0; $j < count($tab_id); $j++) {
                    $val = (int) $transactions_list[$j]->id;
                    if ($tab_id[$i] === $val) {
                        $aux = $transactions_list[$i];
                        $transactions_list[$i] = $transactions_list[$j];
                        $transactions_list[$j] = $aux;

                        $aux = $tab[$i];
                        $tab[$i] = $tab[$j];
                        $tab[$j] = $aux;

                        $aux = $transactions_move[$i];
                        $transactions_move[$i] = $transactions_move[$j];
                        $transactions_move[$j] = $aux;
                    }
                }
            }
            $_SESSION["all_transactions"] = $transactions_list;
            $_SESSION["transactions_move"] = $transactions_move;

            // Envoie de la requête
            $url = $endpoints["user_infos"];
            $method = "POST";
            $use_token = 1;
            $data = array(
                "get_user_info" => $tab
            );
            $res = send_data_to_api($url, $data, $method, $use_token);
            $http_code = $res["http_code"];

            if ($http_code === 200) {
                $_SESSION["users_address"] = $res["response"]->response;
                $_SESSION["IsAuthenticate"] = 1;
                header("Location: ../home.php");
            } else {
                header("Location: ../login.php");
            }

            //var_dump($_SESSION["user_id_info"]);
            //var_dump($_SESSION["all_transactions"]);
        } else {

            header("Location: ../login.php");
        }

        #header("Location: ../home.php");

    }else {
        $_SESSION["check"] = 0;
        $_SESSION["msg"] = "Le code pour la seconde authentification est incorrecte";
        header("Location:../login.php");
    }

}

# -----------------------------------------------------------------

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

function get_user_transactions_list() {
    global $endpoints;
    $url = $endpoints['transactions'];
    $use_token = 1;

    $result = get_data_from_api($url, $use_token);

    $http_code = (int) $result["http_code"];
    if ($http_code === 200) { # Opération réussie

        $res = [1, $result];
        return $res;

    }else {
        $res = [0];
        return $res;
    }
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

function login_user($url, $data) {

    #$data = http_build_query($data);

    # Création et envoie de la requête
    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, $url);
    curl_setopt($request, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($request, CURLOPT_POST, 1);
    curl_setopt($request, CURLOPT_POSTFIELDS,$data);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    $response  = curl_exec($request);
    $response = json_decode($response);
    $httpcode = curl_getinfo($request, CURLINFO_HTTP_CODE);
    curl_close($request); 

    # On retourne le résultat.
    $results = array(
        "http_code" => $httpcode,
        "response" => $response
    );
    return $results;
}

function ask_for_second_authentication() {

    # Récupération et Préparation des données
    $url = $endpoints["double_authentication"];
    $use_token = 1;

    # Envoie de la requête pour la double authentication
    $result = get_data_from_api($url, $use_token);

    $http_code = (int) $result["http_code"];
    if ($http_code === 200) { # Opération réussie
        // Traitement en cas de succès 
        var_dump($result["response"]);
    }else if ($http_code === 401) { # Le Token n'est plus valide
        echo "Le token n'est plus valide !";
    } else { # Une erreur survenu lors du traitement
        var_dump($result["response"]);
    }

}

function concatenate_table($tab1, $tab2) {
    $tab = [];
    for ($i = 0; $i < count($tab1); $i++) {
        $tab[] = $tab1[$i];
    }
    for ($i = 0; $i < count($tab2); $i++) {
        $tab[] = $tab2[$i];
    }
    return $tab;  
}

?>