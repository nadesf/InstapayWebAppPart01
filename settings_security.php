<?php 

session_start();

if (isset($_SESSION["IsAuthenticate"]) && $_SESSION["IsAuthenticate"] === 1) {
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

    // On récupère les informations du user 
    $result = get_user_info();
    if ($result[0] === 1) {
    $result = $result[1];
    $_SESSION["full_name"] = $result['response']->full_name;
    $_SESSION["email"] = $result['response']->email;
    $_SESSION["phone_number"] = $result['response']->phone_number;
    $_SESSION["status"]  = $result['response']->status;
    $_SESSION["double_authentication"]  = $result['response']->double_authentication;
    }

    // On récupère les informations du compte instapay de l'utilisateur 
    $result = get_user_accounts_info();
    if ($result[0] === 1) {
        $result = $result[1];
        $_SESSION["status_account"] = $result['response']->status;
        $_SESSION["amount"] = $result['response']->amount;
        $_SESSION["date_created"] = $result['response']->date_created;
        $_SESSION["account_protection"]  = $result['response']->account_protection;
        $_SESSION["provider"]  = $result['response']->provider;
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instapay</title>

    <!-- Les Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    
    <!-- Liens vers le fichier de bootstrap CSS et JS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="main.css"/>
    <script src="bootstrap/js/jquery.js"></script>
    <script src="bootstrap/js/bootstrap.bundle.js"></script>

    <style>
        h1 {
            color: #613de6;
        }
    </style>
</head>
<body>

    <!-- Header de la page -->>
    <div class="header bg-light">
        <div class="container">
            <div class="row">
                <div class="header-content d-flex justify-content-between">
                    <div class="header-left">
                        <h1><img src="4-removebg-preview.png" alt=""/>Instapay</h1>
                    </div>
                    <div class="right d-flex">

                        <div class="dropdown align-self-center">
                            <span class="mx-2 mt-1 mt-3 fs-4 dropdown-toggle" id="notification" data-bs-toggle="dropdown" aria-expanded="False">
                                <i class="bi bi-bell"></i>
                            </span>
                            
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Aucun message</a></li>
                            </ul>
                        </div>

                        <div class="dropdown align-self-center">
                            <span class="rounded-circle dropdown-toggle account" id="notification" data-bs-toggle="dropdown" aria-expanded="False">
                                <i class="bi bi-person-fill text-white"></i>
                            </span>
                            <ul class="dropdown-menu" style="width: 270px;">
                                <li><a class="dropdown-item name fw-bold" href="home.php"><?php echo $_SESSION["full_name"]; ?></a></li>
                                <li><a class="dropdown-item text-muted" href="#"><?php echo $_SESSION["email"]; ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item fs-6" href="home.php"><i class="bi bi-person mx-2 fs-6"></i>Portefeuil</a></li>
                                <li><a class="dropdown-item fs-6" href="#"><i class="bi bi-wallet2 mx-2 fs-6"></i>Mes comptes</a></li>
                                <li><a class="dropdown-item fs-6" href="settings.php"><i class="bi bi-gear mx-2 fs-6"></i>Paramêtre</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item fs-6 text-danger" href="php/signuser.php?logout=1"><i class="bi bi-box-arrow-left mx-2 fs-6 text-danger"></i>Deconnexion</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Header de la page -->>
    
    <div class="container"> <!-- Div principale -->
        <div class="row">
            <div class="col-md-9 mt-md-3 mb-md-2 welcome_message">
                <h4>Mon Profil</h4>
            </div> <!-- Le message de bonne arrivé -->

            <div class="col-12 col-md-3 mt-md-3">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="home.php" class="link_breadcrumb">Home</a></li>
                    <li class="breadcrumb-item"><a href="#" class="link_breadcrumb">paramètre</a></li>
                    <li class="breadcrumb-item active">sécurité</li>  
                </ul>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <nav class="nav">
                            <ul class="list-unstyled d-flex">
                                <a href="settings.php", class="nav-link fs-5">Profil</a>
                                <a href="settings_security.php" class="nav-link fs-5 active">Securité</a>
                                <a href="#" class="nav-link fs-5">Methode de paiment</a>
                            </ul>
                        </nav>
                    </div>

                    <div class="card-body">
                        <div class="row">

                            <div class="col-12 col-md-6 my-2"> <!-- Changement de mot de passe -->
                                <h4 class="mb-3 info_update">Changer de mot de passe</h4>
                                <hr>
                                <form action="php/edit_user.php" method="post">
                                    <div class="mb-3">
                                        <label for="Old Password" class="form-label">Ancien mot de passe (*)</label>
                                        <input type="password" class="form-control" name="old_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="New Password" class="form-label">Nouveau mot de passe (*)</label>
                                        <input type="password" class="form-control" name="new_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="Confirm Mot de passe" class="form-label">Confirmation mot de passe (*)</label>
                                        <input type="password" class="form-control" name="confirm_password" required>
                                    </div>
                                    <button type="submit" class="btn btn-success submit">Envoyer</button>
                                </form>
                                <?php 
                                if (isset($_SESSION["check_changepassword"]) && (int) $_SESSION["check_changepassword"] === 1) {
                                ?>
                                <p class="text-success fw-bold text-center">Votre mot de passe à été mis à jour</p>
                                <?php    
                                }else if(isset($_SESSION["check_changepassword"]) && (int) $_SESSION["check_changepassword"] === 0) {
                                ?> 
                                <p class="text-danger fw-bold"> Impossible de mettre à jour le mot de passe.</p>
                                <?php  
                                }
                                $_SESSION["check_changepassword"] = NULL;
                                ?>
                            </div>


                            <div class="col-12 col-md-6 my-2"> 
                                <h4 class="mb-3 info_update">Sécurité du compte</h4>
                                <hr>
                                <form action="php/edit_user.php" method="POST">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name= "email_alert" role="switch" id="flexSwitchCheckChecked" checked>
                                        <label class="form-check-label" for="flexSwitchCheckChecked">M'avertir à chaque nouvelle connexion</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name= "double_authentication" role="switch" id="flexSwitchCheckChecked" 
                                        <?php
                                        if (isset($_SESSION["double_authentication"]) && (int) $_SESSION["double_authentication"] === 1) {
                                        ?>
                                        checked
                                        <?php
                                        }
                                        ?>
                                        >
                                        <label class="form-check-label" for="flexSwitchCheckChecked">Authentification à double facteur</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" name="account_protection" id="flexSwitchCheckChecked"
                                        <?php
                                        if (isset($_SESSION["account_protection"]) && (int) $_SESSION["account_protection"] === 1) {
                                        ?>
                                        checked
                                        <?php
                                        }
                                        ?>
                                        >
                                        <label class="form-check-label" for="flexSwitchCheckChecked">Protéger les transactions</label>
                                    </div>
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Code pin pour les transactions</label>
                                        <input type="password" class="form-control" id="confirm_password" name= "protection_code">
                                    </div>
                                    <button type="submit" class="btn btn-success submit">Envoyer</button>
                                </form>
                            </div>

                        </div>
                    </div>
                  </div>
            </div>

            
        </div>
    </div> <!-- div princiaple -->

    <!-- Ajout de notre propre script JS -->
</body>
</html>

<?php
} else {
    header("Location: login.php");
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
function concatenate_table($tab1, $tab2) {
    for ($i = 0; $i < count($tab1); $i++) {
        $tab[] = $tab1[$i];
    }
    for ($i = 0; $i < count($tab2); $i++) {
        $tab[] = $tab2[$i];
    }
    
    return $tab;
    
}

$_SESSION["check"] = NULL;
$_SESSION["msg"] = NULL; 
?>