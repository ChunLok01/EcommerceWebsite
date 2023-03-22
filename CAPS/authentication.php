<?php
require_once("session.php");
if (!(isset($_SESSION["username"]) && $_SESSION["username"] !== "" && isset($_SESSION["userlevel"]) && $_SESSION["userlevel"] !== "" && $_SESSION["authentication"] === 1)) {
    header("location:index.php");
    exit();
}
?>