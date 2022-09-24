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
                <h4 class="text-center mb-3">Réinitialiser votre mot de passe</h4>
                <form action="php/resetpassword.php" method="post">
                    <div class="shadow p-3 mb-5 bg-body rounded">
                        <label for="exampleFormControlInput1" class="form-label">Email</label>
                        <input type="email" class="form-control" id="exampleFormControlInput1" name="email" placeholder="Email" required>
                        <label for="exampleFormControlInput1" class="form-label mt-4">Code de réinistalisation</label>
                        <input type="text" class="form-control" id="exampleFormControlInput1" name="reset_code" placeholder="Ex : 12345678" required>
                        <label for="exampleFormControlInput1" class="form-label mt-4">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="exampleFormControlInput1" name="new_password" placeholder="*************" required>
                        <div class="d-grid gap-2">
                            <button class="btn mt-4 ">Réinitialisé votre mot de passe</button>
                        </div>
                    </div>
                </form>
                <?php
                if (isset($_SESSION["check_reset"]) && $_SESSION["check_reset"] === 0) {
                ?>
                <p clas="text-danger text-center fw-bold">Code de confirmation ou Email incorrecte</p><br>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    
</body>
</html>