<?php
    session_start();
    if ((isset($_SESSION["auth"]) && $_SESSION["auth"] == 1) || (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1)) {
        require_once("cartfunction.php");
    }
?>