<?php
require_once __DIR__.'/src/connection.php';

if (isset($_SESSION['loggedUserId']) === false) {
    header('Location:index.php');
} else {
    $loggedUser = new User();
    $loggedUser->loadFromDB($conn, $_SESSION['loggedUserId']);
    $userRecieverMessage = Message::GetAllMessageForUser($conn,$loggedUser->getId());
    $userSendMessage = Message::GetAllSendMessage($conn,$loggedUser->getId());
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
    <script src="js/appPost.js"></script>
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

        <div class="col-md-8">
            <?php
            if (!isset($_GET['idToShow'])) {
                echo "<h3>Skrzynka Odbiorcza:</h3>";
                Message::showMyMessage($conn, $userRecieverMessage);
                echo "<h3>Skrzynka Nadawcza:</h3>";
                Message::ShowSendMessage($conn, $userSendMessage);
            } else {
                $message = new Message();
                $message->loadOneMessageFromDB($conn,$_GET['idToShow']);
                $message->setIsRead('true');
                $message->saveToDB($conn);
                $senderUser = new User();
                $senderUser->loadFromDB($conn, $message->getSenderId());
                echo "<p></p>";
                echo "<div class='panel panel-info'>";
                echo "<div class='panel-heading'>";
                echo "Nadawca: {$senderUser->getLogin()}"."<br>";
                echo "Data otrzymania: {$message->getCreationDate()}";
                echo "</div>";
                echo "<div class='panel-body'>";
                echo "{$message->getMassageText()}";
                echo "</div>";
                echo "</div>";

                echo "<p><a href='sendMessage.php?recieverId={$message->getSenderId()}'><button class='btn btn-success btn-sm'>Odpowiedz</button></a>
                      <a href='showMessage.php'><button class='btn btn-danger btn-sm'>Wstecz</button></a></p>";
            } ?>
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
