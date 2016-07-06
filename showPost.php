<?php
require_once __DIR__.'/src/connection.php';

$post = null;
$quantityComment = null;
$error = '';
$errorAddComment = '';

if (isset($_SESSION['loggedUserId'])) {
    $loggedUser = new User();
    $loggedUser->loadFromDB($conn, $_SESSION['loggedUserId']);
    $post = Post::GetPostByID($conn, $_GET['id']);
    $quantityComment = ($post->loadAllCommentsForPost($conn, $_GET['id']) == 0) ? 0 : count($post->loadAllCommentsForPost($conn, $_GET['id']));
    $user = new User();
} else {
    header('Location:index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['editPost'])) {
        if (!empty($_POST['post_text'])) {
            $newPostText = trim($_POST['post_text']);
            $newPostText = $conn->real_escape_string($newPostText); //zabezpiczenie przed SQL Injection
            $post->setPostText($newPostText);
            $postSaveSucess = $post->saveToDB($conn);
            if ($postSaveSucess) {
                $error = "Zmiana została zapisana";
            } else {
                $error = 'Błąd zapisu';
            }
        } else {
            $error = 'Treść nie może byc pusta';
        }
    }
    if (isset($_POST['addComment'])) {
        if (!empty($_POST['comment_text'])) {
            $commentText = trim($_POST['comment_text']);
            $commentText = $conn->real_escape_string($commentText); //zabezpiczenie przed SQL Injection
            $newComment = new Comment();
            $newComment->setUserId($_SESSION['loggedUserId']);
            $newComment->setPostId($_GET['id']);
            $newComment->setCommentText($commentText);
            $commentSaveSucess = $newComment->saveToDB($conn);
            if ($commentSaveSucess) {
                $errorAddComment = 'Komentarz został dodany';
            } else {
                $errorAddComment = 'Błąd zapisu';
            }
        } else {
            $errorAddComment = 'Treść komentarza nie może byc pusta';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    Comment::dissableComment($conn, $_GET['delete']);
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
    <script src="js/appComment.js"></script>
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
            <p><a href='addTweet.php'><img class="img-thumbnail" src="img/tweeter.jpg"></a></p
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?php drawRightSideMenu($conn, $loggedUser);?>
        </div>

        <div class="col-md-1">
            <p><img src="img/big_twitt.jpg" class="img-circle"> </p>
        </div>

        <div class="col-md-7">
            <div class='panel panel-primary'>
                <div class='panel-heading'>
                    <span><?php echo "{$post->getPostText()}" ?></span>
                </div>
                <div class='panel-body'>
                    <span style='float: left'>Autor wpisu: <?php $user->loadFromDB($conn,$post->getUserId()); echo "{$user->getLogin()}" ?></span><br>
                    <span style='float: left'>Data utworzenia: <?php echo "{$post->getCreationDate()}"?></span>
                    <span style='float: right'>Komentarze (<?php echo "$quantityComment"?>)</span><br><br>
                    <?php
                    if ($loggedUser->getId() == $post->getUserId()) {
                        if ($error != '') {
                            echo '<span><div class="alert alert-info alert-dismissable">';
                            echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                            echo $error;
                            echo '</div></span>';
                        }
                        if (isset($_GET['edit'])) { ?>
                            <form class="form-horizontal" role="form" method="post" action="showPost.php?id=<?php echo "{$post->getId()}#" ?>">
                                <div class="form-group">
                                    <label>Treść</label>
                                    <textarea id="addPost" rows="4" maxlength="140" name="post_text" placeholder="" class="form-control"><?php echo "{$post->getPostText()}"?></textarea>
                                </div>
                                <p><span id="counterPost">0/140</span></p>
                                <div class="form-group">
                                    <button type="submit" name="editPost" class="btn btn-success btn-xs">Zapisz</button>
                                </div>
                            </form>
                    <?php
                        } else {
                            echo "<a href='showPost.php?id={$post->getId()}&edit=yes'><button class='btn btn-success btn-xs'>Edytuj</button></a>";
                        }
                    }
                    ?>
                </div>
            </div>

            <?php
            if ($errorAddComment != '') {
            echo '<span><div class="alert alert-info alert-dismissable">';
                    echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                    echo $errorAddComment;
                    echo '</div></span>';
            }
            ?>
            <form class="form-horizontal" role="form" method="post" action="showPost.php?id=<?php echo "{$post->getId()}#" ?>">
                <div class="form-group">
                    <label class="col-md-2 control-label">Komentarz: </label>
                    <div class="col-md-5">
                        <textarea id="addComment" rows="3" maxlength="60" name="comment_text" placeholder="Podaj treść komentarza" class="form-control"></textarea>
                    </div>
                    <p><span id="counterComment">0/60</span></p>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-2 col-md-5">
                        <button type="submit" name="addComment" class="btn btn-success btn-xs">Dodaj komentarz</button>
                    </div>
                </div>
            </form>

            <div class="alert alert-success">Komentarze:</div>
            <?php
            $post->showCommentsForPost($conn,$post,$loggedUser);
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
