<?php
require_once __DIR__.'/src/connection.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userToRegister = new User();
    if (!empty($_POST['login']) && !empty($_POST['pass1']) && !empty($_POST['pass2'])) {
        if ($userToRegister->checkLogin($conn, $_POST['login'])) {
            if ($userToRegister->setPassword($_POST['pass1'], $_POST['pass2'])) {
                $userToRegister->setLogin($_POST['login']);
                $userToRegister->activate();
                $registerSucess = $userToRegister->saveToDB($conn);
                if ($registerSucess) {
                    $_SESSION['loggedUserId'] = $userToRegister->getId();
                    header('Location:login.php');
                } else {
                    $error = 'Rejestracja nieudana';
                }
            } else {
                $error = 'Podane hasła różnią się';
            }
        } else {
            $error = 'Podany login już istnieje';
        }
    } else {
        $error = 'Wszystkie pola są obowiązkowe';
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
                <h2>Rejestracja</h2>
                <p>To jest <b>(i zawsze będzie)</b> darmowe.</p>
                <p><a href="index.php" class="btn btn-primary btn-lg" role="button">Powrót</a></p>
            </div>
            <?php
            if ($error != '') {
                echo '<div class="alert alert-danger alert-dismissable">';
                echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                echo $error;
                echo '</div>';
            } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <img src="img/tblack_bird_pl.jpg" class="img-thumbnail">
        </div>
        <div class="col-md-8">
            <form class="form-horizontal" role="form" method="post" action="#">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Login</label>
                    <div class="col-sm-10">
                        <input type="text" name="login" class="form-control" placeholder="Podaj login">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Hasło</label>
                    <div class="col-sm-10">
                        <input type="password" name="pass1" class="form-control" placeholder="Podaj hasło">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Powtórz hasło</label>
                    <div class="col-sm-10">
                        <input type="password" name="pass2" class="form-control" placeholder="Powtórz hasło">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-success btn-lg">Rejestracja</button>
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