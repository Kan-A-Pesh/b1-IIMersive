<?php
if (isset($_GET['token'])) {

    // TODO: Check if token is valid and if it is, activate the account
    $title = "Votre compte a été activé";
    $paragraph = "Votre compte a été activé avec succès.<br>
    Vous pouvez maintenant vous connecter.";
    $resend = false;
} else {
    $title = "Confirmez votre compte";
    $paragraph = "Votre compte a été créé avec succès.
    Veuillez confirmer votre compte en cliquant sur le lien que nous vous avons envoyé par e-mail.<br>
    Si vous n'avez pas reçu d'e-mail, veuillez vérifier votre dossier de spam.<br><br>
    Le compte est valide pendant 24 heures.
    Après cela, vous devrez vous inscrire à nouveau.";
    $resend = true;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IIMersive</title>

    <link rel="stylesheet" href="/css/root.css">

    <script defer src="/js/theme.js"></script>

    <!-- Bootstrap Imports -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</head>

<body class="d-flex align-items-center justify-content-center row text-white m-0 vh-100 bg-black">
    <main class="form-signin col-11 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4 m-auto">
        <h1 class="mb-4 text-center"><span class="color-primary">IIM</span>ersive</h1>
        <h2 class="mb-4"><?php echo $title; ?></h2>
        <p class="mb-4"><?php echo $paragraph; ?></p>
        <?php
        if ($resend) {
            echo '<button id="resend" class="btn btn-primary">Renvoyer l\'e-mail</button>';
        } else {
            echo '<a id="login" class="btn btn-primary" href="/login">Se connecter</a>';
        }
        ?>
    </main>
</body>

</html>