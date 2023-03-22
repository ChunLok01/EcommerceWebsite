<?php
    require_once("auth.php");
    $error = "";
    $success = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["action"]) && $_POST["action"] === "changePW" && isset($_POST["oPW"]) && isset($_POST["nPW"]) && isset($_POST["rPW"]) && !(empty($_POST["oPW"])) && !(empty($_POST["nPW"])) && !(empty($_POST["rPW"]))) {
            include("WWW/conn_db.php");
            $database = "project";
            $mysqli = new mysqli($host,$user,$password,$database);
        
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: ".mysqli_connect_error();
                exit();
            }
            $pw_query = "";
            if (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1) {
                $pw_query = "select * from adminAccount where username='".$_SESSION["username"]."'";
            }
            else {
                $pw_query = "select * from userAccount where username='".$_SESSION["username"]."'";
            }
            $pw_query .= " and password=MD5('".$_POST["oPW"]."')";
            
            $pw_result = $mysqli->query($pw_query);
    
            if ($_POST["nPW"] !== $_POST["rPW"]) {
                $error .= "New Password Not Match!";
            }
            else if ($pw_result->num_rows == 0) {
                $error .= "Old Password Incorrect!";
            }
            else if (strlen($_POST["nPW"]) < 6) {
                $error .= "New Password must at least 6 characters!";
            }
            else if ($_POST["nPW"] === $_POST["rPW"] && $pw_result->num_rows == 1 && strlen($_POST["nPW"]) >= 6) {
                $chgpw_query = 'update ';
                if (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1) {
                    $chgpw_query .= 'adminAccount ';
                }
                else {
                    $chgpw_query .= 'userAccount ';
                }
                $chgpw_query .= "set password=MD5('".$_POST["nPW"]."') where username ='".$_SESSION["username"]."'";
                if (($chgpw_result = $mysqli->query($chgpw_query))==false) {
                    $error .= "Server unavailable to change password!";
                }
                else {
                    $success .= "Change Password Successfully!";
                }
            }
        }
        else {
            $error .= "Please key in password to change password!";
        }
    }
?>
<!DOCTYPE html>
<html>

<head>
    <title>Change Password</title>
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
        #left-content{
            width: 25%;
            display: flex;
            padding: 50px;
        }
        #right-content{
            width: 75%;
            padding: 50px;
        }
        .vl {
            border-left: 1px solid black;
            height: 100%;
            float: right;
            margin-left: 10px;
        }
        #message{
            margin-top: 30px;
            margin-bottom: 30px;
        }
        #brand {
            color: red;
        }
        .in {
            border: 0;
            border-bottom: 2px solid #c5ecfd;
            border-radius: 4px;
            background: transparent;
            color: #c5ecfd;
        }
    </style>
</head>

<body id="content">
    <div id="header">
        <?php require("header.php"); ?>
    </div>
    <div class="container-fluid">
        <div class="d-flex">
            <div id="left-content">
                <div id="first">
                    <div id="imgsize" class="text-center">
                        <img src="image/header/avatar.png" width="250px" height="250px">
                        <p class="font-weight-bold"><?php echo $_SESSION["username"]; ?></p>
                    </div>
                    <hr>
                    <div id="detail" class="m-2 text-center">
                        <a href="personal_profile.php"><p class="choose">Personal Profile</p></a>
                        <a href="login_management.php"><p class="choose">Login Management</p></a>
                        <a href="change_password.php"><p class="choose  font-weight-bold">Change Password</p></a>
                    </div>
                </div>
                <div id="line">
                    <div class="vl"></div>
                </div>
            </div>
            <div id="right-content">
                <div id="header1">
                    <h5 class="my-4">Change Password</h5>
                </div>
                <p id="message">To reset your passworrd, enter the old password and new password.</p>
                <hr>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <div id="password">
                        Old Password: <input type="password" name="oPW" value="" placeholder="Old Password" size="80"><br/><p class="mb-4"></p>
                        New Password: <input type="password" name="nPW" value="" placeholder="New Password" size="80"><br/><p class="mb-4"></p>
                        Re-type Password: <input type="password" name="rPW" value="" placeholder="Re-type Password" size="80">
                    </div>
                    <p class="text-danger"><?php echo (!(empty($error)) ? $error : ""); ?></p>
                    <p class="text-success"><?php echo (!(empty($success)) ? $success : ""); ?></p>
                    <div id="save" class="mt-4">
                        <button class="btn btn-success rounded" width="50%" type="submit" name="action" value="changePW">Change Password</button> 
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="footer" class="p-5 mt-3 bg-dark">
        <?php require("footer.php"); ?>
    </div>
    <?php
        if ((isset($_SESSION["auth"]) && $_SESSION["auth"] == 1) || (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1)) {
            require_once("cart.php");
            echo '<script src="js/script.js"></script>';
        }
    ?>
</body>

</html>