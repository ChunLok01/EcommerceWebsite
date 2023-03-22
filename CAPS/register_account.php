<?php
    $error = "";
    require_once("session.php");
    if (isset($_SESSION["username"]) && $_SESSION["username"] !== "" && isset($_SESSION["userlevel"]) && $_SESSION["userlevel"] !== "" && ((isset($_SESSION["auth"]) && $_SESSION["auth"] === 1) || (isset($_SESSION["authentication"]) && $_SESSION["authentication"] === 1))) {
        header("location:index.php");
        exit();
    }
    else {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            include("WWW/conn_db.php");
            $database = "project";
            $mysqli = new mysqli($host,$user,$password,$database);
    
            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: ".mysqli_connect_error();
                exit();
            }
            $username = (isset($_POST["username"]) ? $_POST["username"] : "");
            $email = (isset($_POST["email"]) ? $_POST["email"] : "");
            $phone = (isset($_POST["phone"]) ? $_POST["phone"] : "");
            $password = (isset($_POST["password"]) ? $_POST["password"] : "");
            $retype_password = (isset($_POST["r-password"]) ? $_POST["r-password"] : "");

            if (!(empty($username)) && !(empty($email)) && !(empty($phone)) && !(empty($password)) && !(empty($retype_password))) {
                $check_username_query1 = "select * from userAccount where username='".$username."'";
                $check_username_query2 = "select * from adminAccount where username='".$username."'";
                if (($check_username_result1 = $mysqli->query($check_username_query1)) == false) {
                    echo 'Invalid query: '.$mysqli->error;
                    exit();
                }
                if (($check_username_result2 = $mysqli->query($check_username_query2)) == false) {
                    echo 'Invalid query: '.$mysqli->error;
                    exit();
                }
                if ($check_username_result1->num_rows > 0) {
                    $errusername = '<p class="text-danger">This username has been used! Please Try another one.</p>';
                    $error .= $errusername;
                }
                else if ($check_username_result2->num_rows > 0) {
                    $errusername = '<p class="text-danger">This username has been used! Please Try another one.</p>';
                    $error .= $errusername;
                }
                else if (!(preg_match('/^[a-zA-Z0-9]{4,}$/', $username))) {
                    $errusername = '<p class="text-danger">Username must be at least 4 characters (alphabet and number & not spacing).</p>';
                    $error .= $errusername;
                }

                $check_email_query1 = "select * from userAccount where userEmail='".$email."'";
                $check_email_query2 = "select * from adminAccount where adminEmail='".$email."'";
                if (($check_email_result1 = $mysqli->query($check_email_query1)) == false) {
                    echo 'Invalid query: '.$mysqli->error;
                    exit();
                }
                if (($check_email_result2 = $mysqli->query($check_email_query2)) == false) {
                    echo 'Invalid query: '.$mysqli->error;
                    exit();
                }
                if ($check_email_result1->num_rows > 0) {
                    $erremail = '<p class="text-danger">This email has been used! Please Try another one.</p>';
                    $error .= $erremail;
                }
                else if ($check_email_result2->num_rows > 0) {
                    $erremail = '<p class="text-danger">This email has been used! Please Try another one.</p>';
                    $error .= $erremail;
                }
                else if (!(preg_match('/^([a-zA-Z0-9]{1,}[.]{0,}){1,}[a-zA-Z0-9]@([a-zA-Z]{1,}[.]{1}){1,}[a-zA-Z]{2,}$/', $email))) {
                    $erremail = '<p class="text-danger">Invalid Email!</p>';
                    $error .= $erremail;
                }

                $check_phone_query1 = "select * from userAccount where userPhone='".$phone."'";
                $check_phone_query2 = "select * from adminAccount where adminPhone='".$phone."'";
                if (($check_phone_result1 = $mysqli->query($check_phone_query1)) == false) {
                    echo 'Invalid query: '.$mysqli->error;
                    exit();
                }
                if (($check_phone_result2 = $mysqli->query($check_phone_query2)) == false) {
                    echo 'Invalid query: '.$mysqli->error;
                    exit();
                }
                if ($check_phone_result1->num_rows > 0) {
                    $errphone = '<p class="text-danger">This phone has been used! Please Try another one.</p>';
                    $error .= $errphone;
                }
                else if ($check_phone_result2->num_rows > 0) {
                    $errphone = '<p class="text-danger">This phone has been used! Please Try another one.</p>';
                    $error .= $errphone;
                }
                else if (!(preg_match('/^01[0-9]-[1-9][0-9]{6,7}$/', $phone))) {
                    $errphone = '<p class="text-danger">Invalid Phone Number. Phone must be mobile number.</p>';
                    $error .= $errphone;
                }

                if (!($password === $retype_password)) {
                    $errpassword = '<p id="password-msg" class="text-danger">Password does not match!</p>';
                    $error .= $errpassword;
                }
                else if ($password === $retype_password && strlen($password) < 6) {
                    $errpassword = '<p id="password-msg" class="text-danger">Password must be at least 6 characters.</p>';
                    $error .= $errpassword;
                }
            }
            else {
                $errempty = '<p class="text-danger">Please key in all to register an account</p>';
                $error .= $errempty;
            }

            if (empty($error)) {
                date_default_timezone_set("Asia/Kuala_Lumpur");
                $register_time = date('Y-m-d H:i:s');
                $register_query = "insert into userAccount values (null,'".$username."',null,null,MD5('".$password."'),'".$email."','".$phone."',null,null,null,null,null,null,'".$register_time."',1)";
                if (($register_result = $mysqli->query($register_query)) == false) {
                    echo "Invalid mysqli ".$mysqli->error;
                }
                else {
                    $register_flag = true;
                    header("Refresh: 5; URL=login.php");
                }
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
    <?php
        if (isset($errpassword) && !(empty($errpassword))) {
    ?>
    <script>
        $(document).ready(function () {
            $('input[type="password"]').click(function () {
                clearpw();
            });
            function clearpw() {
                $('input[type="password"]').val("");
                $("#password-msg").text("");
                $('input[type="password"]').unbind();
            }
        });
    </script>
    <?php
        }
    ?>
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
                    <h1 class="display-4 text-white">Register Account</h1>
                    <div class="card">
                        <h5 class="card-header font-weight-light">Register Page</h5>
                        <div class="card-body">
                            <?php
                                if (isset($register_flag) && $register_flag === true) {
                                    echo 'Register Successfully! Page will redirect to login page in 5 seconds or <a href="login.php">Click Here</a>';
                                }
                                else {
                            ?>
                            <form action="<?php $_SERVER["PHP_SELF"] ?>" method="POST">
                                <div class="form-group">
                                    <label>Username : </label>
                                    <input type="text" class="form-control" name="username" value="<?php echo (isset($_POST["username"]) && !(empty($_POST["username"]))) ? $username : ""; ?>"/>
                                    <?php
                                        if (isset($errusername) && !(empty($errusername))) {
                                            echo $errusername;
                                        }
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Email Address : </label>
                                    <input type="text" class="form-control" name="email" value="<?php echo (isset($_POST["email"]) && !(empty($_POST["email"]))) ? $email : ""; ?>"/>
                                    <?php
                                        if (isset($erremail) && !(empty($erremail))) {
                                            echo $erremail;
                                        }
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Phone Number : </label>
                                    <input type="text" class="form-control" name="phone" value="<?php echo (isset($_POST["phone"]) && !(empty($_POST["phone"]))) ? $phone : ""; ?>" placeholder="Example: 012-3456789, XXX-XXXXXX (with '-')"/>
                                    <?php
                                        if (isset($errphone) && !(empty($errphone))) {
                                            echo $errphone;
                                        }
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Password : </label>
                                    <input type="password" class="form-control" name="password" value="<?php echo (isset($_POST["password"]) && !(empty($_POST["password"]))) ? $password : ""; ?>"/>
                                    <?php
                                        if (isset($errpassword) && !(empty($errpassword))) {
                                            echo $errpassword;
                                        }
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label>Retype Password : </label>
                                    <input type="password" class="form-control" name="r-password" value="<?php echo (isset($_POST["r-password"]) && !(empty($_POST["r-password"]))) ? $retype_password : ""; ?>"/>
                                </div>
                                <input type="submit" class="btn btn-primary" name="action" value="Register"/>
                                <?php
                                    if (isset($errempty) && !(empty($errempty))) {
                                        echo $errempty;
                                    }
                                ?>
                            </form>
                            <?php
                                }
                            ?>
                            <?php
                                if (!(isset($register_flag))) {
                            ?>
                            <br/>
                            <a href="login.php" class="btn btn-dark">Back to Login</a>
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>