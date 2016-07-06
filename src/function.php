<?php

function drawRightSideMenu (mysqli $conn, User $loggedUser) {
    echo "<h2>Login: {$loggedUser->getLogin()}</h2>";
    echo "<img src='img/{$loggedUser->getAvatar()}' class='img-thumbnail'><br><br>";
    echo "<div class='list-group'>";
        echo "<a href='login.php' class='list-group-item list-group-item-success'>Strona główna</a>";
        echo "<a href='showUser.php' class='list-group-item list-group-item-success'>Pokaż profil</a>";
        echo "<a href='showAllUsers.php' class='list-group-item list-group-item-success'>Pokaż użytkowników</a>";
        echo "<a href='showMessage.php' class='list-group-item list-group-item-success'>Wiadomości
              ({$loggedUser->countNoReadUserMessages($conn,$loggedUser->getId())})</a>";
        echo "<a href='addTweet.php' class='list-group-item list-group-item-success'>Dodaj Twitta</a>";
        echo "<a href='logout.php' class='list-group-item list-group-item-success'>Wyloguj</a>";
    echo "</div>";
    echo "<p>Twoje twitty: {$loggedUser->countUserPosts($conn,$loggedUser->getId())}</p>";
    echo "<p>Twoje komentarze: {$loggedUser->countUserComments($conn,$loggedUser->getId())}</p>";
    echo "<p>Wysłane wiadomości: {$loggedUser->countSendMessages($conn,$loggedUser->getId())}</p>";
    echo "<p>Otrzymane Wiadomości: {$loggedUser->countRecieverMessages($conn,$loggedUser->getId())}</p>";
}

?>