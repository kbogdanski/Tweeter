<?php
require_once __DIR__.'/src/connection.php';

if (isset($_SESSION['loggedUserId'])) {
    $loggedUser = new User();
    $loggedUser->loadFromDB($conn, $_SESSION['loggedUserId']);
    $loggedUserTweet = $loggedUser->loadUserPosts($conn,$_SESSION['loggedUserId']);
    $allTweet = Post::GetAllPosts($conn);
} else {
    header('Location:index.php');
}

$count = 5;
if (isset($_GET['count']) && isset($_GET['add'])) {
    $count = $_GET['count'] + 5;
}
if (isset($_GET['count']) && isset($_GET['sub'])) {
    $count = $_GET['count'] - 5;
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

        <div class="col-md-8">
            <h3>Twój ostatni twitt</h3>
            <?php if (!empty($loggedUserTweet)) {
                if (!isset($_GET['all'])) {
                    $loggedUser->showUserPosts($conn, $loggedUserTweet, 1);
                    echo "<a href='login.php?all=yes'><button class='btn btn-success btn-xs'>Pokaż wszystkie</button></a>";
                }
                if (isset($_GET['all'])) {
                    $loggedUser->showUserPosts($conn, $loggedUserTweet);
                    echo "<a href='login.php'><button class='btn btn-success btn-xs'>Ukryj</button></a>";
                }
            } else {
                echo '<div class="alert alert-info">Brak twittów</div>';
            }?>

            <h3>Najnowsze twitty</h3>
            <?php
            $loggedUser->showUserPosts($conn, $allTweet, $count);
            if ($count > 5) {
                echo "<a href='login.php?sub=yes&count=$count' style='float: left'><button class='btn btn-success btn-lg'>Ukryj starsze</button></a>";
            }
            if (count($allTweet) > $count) {
                echo "<a href='login.php?add=yes&count=$count' style='float: right'><button class='btn btn-success btn-lg'>Pokaż starsze</button></a>";
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

<?php
?>