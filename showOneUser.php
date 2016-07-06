<?php
require_once __DIR__.'/src/connection.php';

if (isset($_SESSION['loggedUserId']) === false) {
    header('Location:index.php');
}

$loggedUser = new User();
$loggedUser->loadFromDB($conn, $_SESSION['loggedUserId']);
$userToShowTweet = [];

if (isset($_GET['idToShow'])) {
    $userToShow = new User();
    $userToShow->loadFromDB($conn,$_GET['idToShow']);
    $userToShowTweet = $userToShow->loadUserPosts($conn,$_GET['idToShow']);
}
?>

<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tweeter.pl</title>
    <link rel="stylesheet" href="css/style.css" type="text/css" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/jquery-1.12.4.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <!-- Grupowanie "marki" i przycisku rozwijania mobilnego menu -->
        <div class="navbar-header">
            <a class="navbar-brand" href="login.php"><img class="img-rounded" src="img/tbird_pl.png"></a>
        </div>
        <!-- Grupowanie elementów menu w celu lepszego wyświetlania na urządzeniach moblinych -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><h3 class="text-uppercase" style="color: white"><?php echo "{$loggedUser->getLogin()}";?> Witaj na Twitter.pl</h3></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php"><img class="img-circle" src="img/off.jpg"></a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <a href='addTweet.php'><img class="img-thumbnail" src="img/tweeter.jpg"></a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?php drawRightSideMenu($conn, $loggedUser);?>
        </div>

        <div class="col-md-offset-2 col-md-4">
            <br><br>
            <h3>Dane użytkownika:</h3>
            <ul class="list-group">
                <li class="list-group-item"><b>Login: </b> <?php echo "{$userToShow->getLogin()}"?></li>
                <li class="list-group-item"><b>Imie: </b> <?php echo "{$userToShow->getName()}"?></li>
                <li class="list-group-item"><b>Nazwisko: </b> <?php echo "{$userToShow->getSurname()}"?></li>
                <li class="list-group-item"><b>Email: </b> <?php echo "{$userToShow->getEmail()}"?></li>
                <li class="list-group-item"><b>Ilość twittów: </b> <?php echo "{$userToShow->countUserPosts($conn,$_GET['idToShow'])}"?></li>
                <li class="list-group-item"><b>Ilość komentarzy: </b> <?php echo "{$userToShow->countUserComments($conn,$_GET['idToShow'])}"?></li>
                <li class="list-group-item" style="text-align: center"><a href="sendMessage.php?recieverId=<?php echo "{$userToShow->getId()}"?>"><button class="btn btn-success">Wyślij wiadomość</button></a></li>
            </ul>
        </div>
        <div class="col-md-2">
            <h3><?php echo "{$userToShow->getLogin()}"?></h3>
            <img src='img/<?php echo "{$userToShow->getAvatar()}"?>' class="img-thumbnail">
        </div>
        <div class="col-md-8">
            <span><b>Opis:</b></span>
            <div class="alert alert-info"><?php echo "{$userToShow->getDescription()}"?></div>
            <div class="well well-sm">
                Twitty użytkownika:
            </div>
            <?php
            if (!empty($userToShowTweet)) {
                $userToShow->showUserPosts($conn, $userToShowTweet);
            } else {
                echo "<div class='alert alert-info'>Ten użytkownik nie ma jeszcze swoich twittów</div>";
            }
            ?>
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

