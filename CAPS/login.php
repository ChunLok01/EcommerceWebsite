<?php
$error = "";
require_once("session.php");
if (isset($_SESSION["username"]) && $_SESSION["username"] !== "" && isset($_SESSION["userlevel"]) && $_SESSION["userlevel"] !== "" && ((isset($_SESSION["auth"]) && $_SESSION["auth"] === 1) || (isset($_SESSION["authentication"]) && $_SESSION["authentication"] === 1))) {
    header("location:index.php");
    exit();
}
else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        if (isset($_POST["username"]) && $_POST["username"] != "" && isset($_POST["password"]) && $_POST["password"] != "") {
            include("WWW/conn_db.php");
            $database = "project";
            $mysqli = new mysqli($host,$user,$password,$database);
    
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: ".mysqli_connect_error();
                exit();
            }
            $login_query1 = "select * from userAccount where username='".$username."' and password=MD5('". $_POST["password"] ."')";
            $login_query2 = "select * from adminAccount where username='".$username."' and password=MD5('". $_POST["password"] ."')";
            $result1 = $mysqli->query($login_query1);
            $result2 = $mysqli->query($login_query2);
            if ($result1->num_rows == 1) {
                $row1 = $result1->fetch_assoc();
                if ($row1["userlevel"] > 0) {
                    $_SESSION["username"] = $row1["username"];
                    $_SESSION["userlevel"] = $row1["userlevel"];
                    $_SESSION["auth"] = 1;
                    header("location:index.php");
                }
                else {
                    $error = "Your account has been banned!";
                }
            }
            else if ($result2->num_rows == 1) {
                $row2 = $result2->fetch_assoc();
                if ($row2["userlevel"] > 0) {
                    $_SESSION["username"] = $row2["username"];
                    $_SESSION["userlevel"] = $row2["userlevel"];
                    $_SESSION["authentication"] = 1;
                    header("location:index.php");
                }
                else {
                    $error = "Your account has been banned!";
                }
            }
            else {
                $error = "Your username or password is wrong!";
            }
        }
        else {
            $error = "Please Key In your Username and Password to Login!";
        }
    }
}
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
                <?php require("menu.php"); ?>
                </div>
                <div class="col-10 p-5" id="content">
                    <h1 class="display-4 text-white">Login Account</h1>
                    <div class="card">
                        <h5 class="card-header font-weight-light">Login Page</h5>
                        <div class="card-body">
                            <form action="<?php $_SERVER["PHP_SELF"] ?>" method="POST">
                                <div class="form-group">
                                    <label>Username : </label>
                                    <input type="text" class="form-control" name="username" value="<?php echo isset($_POST["username"]) ? $username : ""; ?>"/>
                                </div>
                                <div class="form-group">
                                    <label>Password : </label>
                                    <input type="password" class="form-control" name="password" value=""/>
                                </div>
                                <?php echo empty($error) ? "" : '<p class="text-danger">'.$error."</p>"; ?>
                                <input type="submit" class="btn btn-primary" value="Login"/>
                            </form>
                            <div class="w-100">
                                Don't have an account? <a href="register_account.php">Click here to register!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>