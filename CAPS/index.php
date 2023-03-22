<?php
    require_once("session.php");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Homepage</title>
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
    <style>
        #demo {
            z-index: 0;
        }
        #laptop {
            width: 50%;
        }

        #keyboard {
            width: 50%;
        }

        #content2 {
            display: flex;
        }

        #mouse {
            width: 50%;
        }

        #headset {
            width: 50%;
        }

        input {
            border: 0;
            border-bottom: 2px solid #c5ecfd;
            border-radius: 4px;
            background: transparent;
            color: #c5ecfd;
        }
    </style>
</head>

<body id="content">
    <div class="container-fluid p-0 bg-dark">
        <div id="header">
            <?php require("header.php"); ?>
        </div>
        <div id="demo" class="carousel slide" data-ride="carousel">
            <ul class="carousel-indicators">
                <li data-target="#demo" data-slide-to="0" class="active"></li>
                <li data-target="#demo" data-slide-to="1"></li>
                <li data-target="#demo" data-slide-to="2"></li>
            </ul>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="image/slide/slide1.jpg" alt="slide1" height="600px" width="100%">
                </div>
                <div class="carousel-item">
                    <img src="image/slide/slide2.jpg" alt="slide2" height="600px" width="100%">
                </div>
                <div class="carousel-item">
                    <img src="image/slide/slide3.jpg" alt="slide3" height="600px" width="100%">
                </div>
            </div>
            <a class="carousel-control-prev" href="#demo" data-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </a>
            <a class="carousel-control-next" href="#demo" data-slide="next">
                <span class="carousel-control-next-icon"></span>
            </a>
        </div>
        <div class="d-flex">
            <div id="laptop">
                <a href="laptop.php">
                    <img src="image/index/laptop.jpg" alt="laptop" height="400px" width="100%">
                </a>
            </div>
            <div id="keyboard">
                <a href="keyboard.php">
                    <img src="image/index/keyboard.jpg" alt="keyboard" height="400px" width="100%">
                </a>
            </div>
        </div>
        <div id="content2">
            <div id="mouse">
                <a href="mouse.php">
                    <img src="image/index/mouse.jpg" alt="mouse" height="400px" width="100%">
                </a>
            </div>
            <div id="headset">
                <a href="headset.php">
                    <img src="image/index/headset.jpg" alt="headset" height="400px" width="100%">
                </a>
            </div>
        </div>
        <div id="footer" class="p-5 ml-5">
            <?php require("footer.php"); ?>
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