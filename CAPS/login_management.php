<?php
    require_once("auth.php");
    include("WWW/conn_db.php");
    $database = "project";
    $mysqli = new mysqli($host,$user,$password,$database);

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: ".mysqli_connect_error();
        exit();
    }
    $user_query1 = "select * from userAccount where username='".$_SESSION["username"]."'";
    $user_query2 = "select * from adminAccount where username='".$_SESSION["username"]."'";
    if (($user_result1 = $mysqli->query($user_query1)) == false) {
        echo 'Something went wrong! Server is unavailable!';
        exit();
    }
    if (($user_result2 = $mysqli->query($user_query2)) == false) {
        echo 'Something went wrong! Server is unavailable!';
        exit();
    }

    $user_email;
    $user_phone;

    $user_row;
    if ($user_result2->num_rows == 1) {
        $user_row = $user_result2->fetch_assoc();
        $user_email = $user_row["adminEmail"];
        $user_phone = $user_row["adminPhone"];
    }
    else {
        $user_row = $user_result1->fetch_assoc();
        $user_email = $user_row["userEmail"];
        $user_phone = $user_row["userPhone"];
    }
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login Management</title>
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
        #login1{
            margin-top: 80px;
            display: flex;
            margin-bottom: 30px;
        }
        #login2{
            margin-top: 30px;
            display: flex;
            margin-bottom: 30px;
        }
        #login3{
            margin-top: 30px;
            display: flex;
            margin-bottom: 30px;
        }
        #login4{
            margin-top: 30px;
            display: flex;
            margin-bottom: 30px;
        }
        #email{
            width: 75%;
        }
        #phone{
            width: 75%;
        }
        #facebook{
            width: 75%;
        }
        #gmail{
            width: 75%;
        }
        .edit{
            float: right;
            width: 25%;
        }
        #media{
            margin-top: 30px;
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
                    <div class="text-center">
                        <img src="image/header/avatar.png" width="250px" height="250px">
                        <p class="font-weight-bold"><?php echo $_SESSION["username"]; ?></p>
                    </div>
                    <hr>
                    <div id="detail" class="m-2 text-center">
                        <a href="personal_profile.php"><p class="choose">Personal Profile</p></a>
                        <a href="login_management.php"><p class="choose  font-weight-bold">Login Management</p></a>
                        <a href="change_password.php"><p class="choose">Change Password</p></a>
                    </div>
                </div>
                <div id="line">
                    <div class="vl"></div>
                </div>
            </div>
            <div id="right-content">
                <div id="header1">
                    <h5 class="my-4">Login Management</h5>
                </div>
                <div id="login1">
                    <div id="email">
                        Email Address: <input type="text" name="email" value="<?php echo $user_email; ?>" placeholder="Email Address" size="80" disabled>
                    </div>
                </div>
                <hr>
                <div id="login2">
                    <div id="phone">
                        Phone Number: <input type="text" name="phone" value="<?php echo $user_phone; ?>" placeholder="Phone Number" size="80" disabled>
                    </div>
                </div>
                <hr>
                <?php
                    if (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1) {
                ?>
                <p>You are loging in an admin account.</p>
                <a href="admin_index.php">Click this link to access admin side.</a>
                <?php
                    }
                ?>
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