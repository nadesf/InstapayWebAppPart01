<?php 
session_start();

if (isset($_SESSION["IsAuthenticate"]) && $_SESSION["IsAuthenticate"] === 1) {

    $domain = "http://164.92.134.116/api/v1/";
    $endpoints = array(
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
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
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
                    <li class="breadcrumb-item active">profil</li>  
                </ul>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <nav class="nav">
                            <ul class="list-unstyled d-flex">
                                <a href="settings.php", class="nav-link fs-5  active">Profil</a>
                                <a href="settings_security.php" class="nav-link fs-5">Securité</a>
                                <a href="#" class="nav-link fs-5">Methode de paiment</a>
                            </ul>
                        </nav>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-12 my-2"> <!-- Modifier Son Email -->
                                <h4 class="mb-3 info_update">Informations de l'utilisateur</h4>
                                <hr>
                                <form action="php/edit_user.php" method="POST">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Votre nom</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="<?php echo $_SESSION["full_name"];?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Email address</label>
                                        <input type="text" class="form-control" id="email_update" name="email" placeholder="<?php echo $_SESSION["email"];?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Numéro de téléphone </label>
                                        <input type="text" class="form-control" id="describe" name="phone_number" placeholder="<?php echo $_SESSION["phone_number"];?>">
                                    </div>
                                    <button type="submit" class="btn btn-success submit">Envoyer</button>
                                </form>
                                <?php 
                                if (isset($_SESSION["check_editprofil"]) && (int) $_SESSION["check_editprofil"] === 1) {
                                ?>
                                <p class="text-success fw-bold text-center">Vos informations ont été mis à jour</p>
                                <?php    
                                }else if(isset($_SESSION["check_payreq"]) && (int) $_SESSION["check_payreq"] === 0) {
                                ?> 
                                <p class="text-danger fw-bold"> Impossible de mettre à jour.</p>
                                <?php  
                                }
                                $_SESSION["check_editprofil"] = NULL;
                                ?>
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

$_SESSION["check"] = NULL;
$_SESSION["msg"] = NULL; 
?>