<?php
require_once __DIR__.'/src/connection.php';

if (isset($_SESSION['loggedUserId']) === false) {
    header('Location:index.php');
}

$loggedUser = new User();
$loggedUser->loadFromDB($conn, $_SESSION['loggedUserId']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $name = $conn->real_escape_string($name); //zabezpiczenie przed SQL Injection
    $loggedUser->setName($name);
    $surname = trim($_POST['surname']);
    $surname = $conn->real_escape_string($surname); //zabezpiczenie przed SQL Injection
    $loggedUser->setSurname($surname);
    $email = trim($_POST['email']);
    $email = $conn->real_escape_string($email); //zabezpiczenie przed SQL Injection
    $loggedUser->setEmail($email);
    $userDescription = trim($_POST['user_description']);
    $userDescription = $conn->real_escape_string($userDescription); //zabezpiczenie przed SQL Injection
    $loggedUser->setDescription($userDescription);
    if (isset($_POST['avatar'])) {
        $loggedUser->setAvatar($_POST['avatar']);
    }

    $loggedUser->saveToDB($conn);
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

        <div class="col-md-offset-2 col-md-4 col-md-offset-2">
            <br><br>
            <h3>Twoje dane:</h3>
            <ul class="list-group">
                <li class="list-group-item"><b>Login: </b> <?php echo "{$loggedUser->getLogin()}"?></li>
                <li class="list-group-item"><b>Imie: </b> <?php echo "{$loggedUser->getName()}"?></li>
                <li class="list-group-item"><b>Nazwisko: </b> <?php echo "{$loggedUser->getSurname()}"?></li>
                <li class="list-group-item"><b>Email: </b> <?php echo "{$loggedUser->getEmail()}"?></li>
                <li class="list-group-item" style="text-align: center"><a href="showUser.php?edit=yes"><button class="btn btn-success">Uzupełnij dane</button></a></li>
            </ul>
        </div>
        <div class="col-md-8">
            <span><b>Opis:</b></span>
            <div class="alert alert-info"><?php echo "{$loggedUser->getDescription()}"?></div>
            <?php  if (isset($_GET['edit'])) { ?>
                <form class="form-horizontal" role="form" method="post" action="showUser.php">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Imie</label>
                        <div class="col-sm-6">
                            <input type="text" name="name" class="form-control" placeholder="Podaj imie" value="<?php echo "{$loggedUser->getName()}" ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Nazwisko</label>
                        <div class="col-sm-6">
                            <input type="text" name="surname" class="form-control" placeholder="Podaj nazwisko" value="<?php echo "{$loggedUser->getSurname()}" ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-6">
                            <input type="text" name="email" class="form-control" placeholder="Podaj email" value="<?php echo "{$loggedUser->getEmail()}" ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Opis</label>
                        <div class="col-sm-6">
                            <textarea rows="4" name="user_description" placeholder="Opisz siebie" class="form-control"><?php echo "{$loggedUser->getDescription()}" ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                    <?php
                    for ($i=1; $i<11; $i++) {
                        echo "<label class='radio-inline'>";
                        echo "<input type='radio' name='avatar' id='inlineRadio{$i}' value='avatar_$i.jpg'><img src='img/avatar_$i.jpg'>";
                        echo "</label>";
                    }
                    ?>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-6">
                            <button type="submit" class="btn btn-success btn-sm">Zapisz</button>
                        </div>
                    </div>
                </form>
            <?php } ?>
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
