<?php
session_start();
require_once __DIR__.'/User.php';
require_once __DIR__.'/Post.php';
require_once __DIR__.'/Comment.php';
require_once __DIR__.'/Message.php';
require_once __DIR__.'/function.php';

$db_host = 'localhost';
$db_user = 'root';
$db_password = 'coderslab';
$db_name = 'Tweeter';

$conn = new mysqli($db_host,$db_user,$db_password,$db_name);

if ($conn->error != 0) {
    die ('Blad polaczenia do bazy danych: {$conn->error}');
}
?>