<?php
    session_start();
    $_SESSION = array();
    session_destroy();
    setcookie("token", "", time()-3600);
    header("location: login.php");
?>