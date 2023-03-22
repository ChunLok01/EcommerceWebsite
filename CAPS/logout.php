<?php
    require_once("auth.php");
    session_unset();
    session_destroy();
    header("Refresh:5; URL=index.php");
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

</head>
<style>
    body {
        max-width: 1920px;
        margin: auto;
    }

    #content {
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

<body>
    <div>
        <?php require("header.php"); ?>
        <div class="container-fluid">
            <div class="row" id="row-main">
                <div class="col-2 text-center text-white" id="sidebar">
                </div>
                <div class="col-10 p-5" id="content">
                    <h1 class="display-4 text-white">Logout Account</h1>
                    <div class="card">
                        <h5 class="card-header font-weight-light">Logout Page</h5>
                        <div class="card-body">
                            Logout Successfully! Page will redirect to Home Page in 5 seconds.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>