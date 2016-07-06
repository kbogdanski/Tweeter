<?php
require_once __DIR__.'/src/connection.php';

unset($_SESSION['loggedUserId']);
header('Location:index.php');

?>