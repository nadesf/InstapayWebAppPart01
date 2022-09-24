<html lang="en">
<head>
    <meta charset="UTF-8">

    <!-- Les Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    
    <!-- Liens vers le fichier de bootstrap CSS et JS -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" href="sign.css"/>
    <script src="bootstrap/js/jquery.js"></script>
    <script src="bootstrap/js/bootstrap.bundle.js"></script>
    <title>Instapay - Connectez vous </title>

</head>
<body style="background-color: white;">

    <div class="container">
        <div class="row">
            <div class="bloc">
                <br><br><br><br>
                <p class="text-center"><img src="4.png" alt=""/></p>
                <h4 class="text-center mb-3">Réinitialisation de mot de passe</h4>
                <form action="php/resetpassword.php" method="POST">
                    <div class="shadow p-3 mb-5 bg-body rounded">
                        <label for="exampleFormControlInput1" class="form-label ">Nom complet</label>
                        <input type="email" class="form-control" id="exampleFormControlInput1" name="email" placeholder="johnedoe@upmail.com">
                        <div class="d-grid gap-2">
                            <button class="btn mt-4">Obtenir un code</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    
</body>
</html>