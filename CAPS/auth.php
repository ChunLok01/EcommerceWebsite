<?php
    require_once("session.php");
    if (!(isset($_SESSION["username"]) && $_SESSION["username"] !== "" && isset($_SESSION["userlevel"]) && $_SESSION["userlevel"] !== "" && ((isset($_SESSION["auth"]) && $_SESSION["auth"] === 1) || (isset($_SESSION["authentication"]) && $_SESSION["authentication"] === 1)))) {
        header("location:login.php");
        exit();
    }
?>