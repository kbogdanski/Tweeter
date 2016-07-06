<?php
require_once __DIR__.'/src/connection.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loggedUser = User::LogIn($conn,$_POST['login'],$_POST['pass']);
    if ($loggedUser != null) {
        $_SESSION['loggedUserId'] = $loggedUser->getId();
        header('Location:login.php');
    } else {
        $error = 'Błędny login lub hasło';
    }
}
?>

<!DOCTYPE>
<html lang="PL">
<head>
    <meta charset="UTF-8">
    <title>Tweeter.pl</title>
    <link rel="stylesheet" href="css/style.css" type="text/css" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/jquery-1.12.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron">
                <h1>Witaj na Tweeter.pl</h1>
                <p>Połącz się ze swoimi znajomymi – i innymi ciekawymi ludźmi.
                    Otrzymuj natychmiastowe aktualizacje na tematy, które Cię interesują.
                    Obserwuj rozwój wydarzeń w czasie rzeczywistym, z każdej strony w <b>polskiej instancji Twittera</b>.</p>
                <p><a href="register.php" class="btn btn-primary btn-lg" role="button">Zarejestruj się!</a></p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <img src="img/tblack_bird_pl.jpg" class="img-thumbnail">
        </div>
        <div class="col-md-8">
            <?php
            if ($error != '') {
                echo '<div class="alert alert-danger alert-dismissable">';
                echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                echo $error;
                echo '</div>';
            } ?>
            <form class="form-horizontal" role="form" method="post" action="#">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Login</label>
                    <div class="col-sm-10">
                        <input type="text" name="login" class="form-control" placeholder="Podaj swój login">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Hasło</label>
                    <div class="col-sm-10">
                        <input type="password" name="pass" class="form-control" placeholder="Twoje hasło">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-success btn-lg">Zaloguj</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<nav class="navbar navbar-default navbar-inverse navbar-fixed-bottom" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <span class="text-info">Projekt na zaliczenie Warsztatów nr 2 w CodersLab.</span><br>
            <span class="text-info">Autor: Kamil Bogdański. Create: 06.2016</span>
        </div>
    </div>
</nav>
</body>
</html>


