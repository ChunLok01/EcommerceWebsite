<?php
require_once("authentication.php");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <?php
        if ((isset($_SESSION["auth"]) && $_SESSION["auth"] == 1) || (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1)) {
            echo '<link rel="stylesheet" href="css/style.css">';
        }
    ?>
</head>
<style>
    body {
        max-width: 1920px;
        margin: auto;
    }

    #content-bg {
        background-color: #0f4c75;
        min-height: 700px;
    }

    #sidebar {
        background-color: #393e46;
        min-height: 700px;
    }

    #brand {
        color: red;
    }
</style>

<body id="content">
    <?php require("header.php"); ?>
    <div class="container-fluid">
        <div class="row" id="row-main">
            <div class="col-2 text-center text-white" id="sidebar">
                <?php require("menu.php"); ?>
            </div>
            <div class="col-10 p-5" id="content-bg">
                <h1 class="display-4 text-white">Welcome back, <?php echo $_SESSION["username"]; ?></h1>
                <div class="card">
                    <h5 class="card-header font-weight-light">Tips</h5>
                    <div class="card-body">
                        <ul>
                            <li class="font-weight-bold">Order List</li>
                            <ul>
                                <li>View customers' order information.</li>
                            </ul>
                            <li class="font-weight-bold">Account List</li>
                            <ul>
                                <li>View or edit customers' account information.</li>
                            </ul>
                            <li class="font-weight-bold">Admin List</li>
                            <ul>
                                <li>View, delete, add or edit admins' information.</li>
                            </ul>
                            <li class="font-weight-bold">Product List</li>
                            <ul>
                                <li>View, delete, add or edit products' information.</li>
                            </ul>
                        </ul>
                        <h3>*Please protect the privacy of customers and be an ethical person.</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
        if ((isset($_SESSION["auth"]) && $_SESSION["auth"] == 1) || (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1)) {
            require_once("cart.php");
            echo '<script src="js/script.js"></script>';
        }
    ?>
</body>

</html>