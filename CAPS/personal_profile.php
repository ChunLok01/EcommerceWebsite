<?php
    require_once("auth.php");
    include("WWW/conn_db.php");
    $database = "project";
    $mysqli = new mysqli($host,$user,$password,$database);
    $username = $_SESSION["username"];
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: ".mysqli_connect_error();
        exit();
    }
    /*
    Edit personal info below code
    
    */
    function updateProfile($table, $query, $username, $mysqli) {
        $uQuery = "update ".$table." set ".$query." where username='".$username."'";
        if (($update_result = $mysqli->query($uQuery)) == false) {
            echo "Server Unavailable!";
        }
    }
    $err_postcode = "";
    $err_dob = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["action"]) && $_POST["action"] == "changeProfile") {
            $update_table = "";
            if (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1) {
                $update_table = "adminAccount";
            }
            else {
                $update_table = "userAccount";
            }
            $update_query = "";
            if (isset($_POST["fname"]) && !(empty($_POST["fname"]))) {
                if (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1) {
                    $update_query = "adminFname='".$_POST["fname"]."'";
                }
                else {
                    $update_query = "userFname='".$_POST["fname"]."'";
                }
                updateProfile($update_table, $update_query, $username, $mysqli);
            }
            if (isset($_POST["lname"]) && !(empty($_POST["lname"]))) {
                if (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1) {
                    $update_query = "adminLname='".$_POST["lname"]."'";
                }
                else {
                    $update_query = "userLname='".$_POST["lname"]."'";
                }
                updateProfile($update_table, $update_query, $username, $mysqli);
            }
            if (isset($_POST["address"]) && !(empty($_POST["address"]))) {
                $update_query = "addressStreet='".$_POST["address"]."'";
                updateProfile($update_table, $update_query, $username, $mysqli);
            }
            if (isset($_POST["city"]) && !(empty($_POST["city"]))) {
                $update_query = "city='".$_POST["city"]."'";
                updateProfile($update_table, $update_query, $username, $mysqli);
            }
            if (((isset($_POST["day"]) && !(empty($_POST["day"]))) && (isset($_POST["month"]) && !(empty($_POST["month"]))) && (isset($_POST["year"]) && !(empty($_POST["year"]))))) {
                $day = $_POST["day"];
                $month = $_POST["month"];
                $year = $_POST["year"];

                if (checkdate($month, $day, $year)) {
                    $update_query = "DOB='".$year."-".$month."-".$day."'";
                    updateProfile($update_table, $update_query, $username, $mysqli);
                }
                else {
                    $err_dob = "Your Date of Birth is not valid!";
                }
            }
            if (isset($_POST["gender"]) && !(empty($_POST["gender"]))) {
                $update_query = "gender='".$_POST["gender"]."'";
                updateProfile($update_table, $update_query, $username, $mysqli);
            }
            if (isset($_POST["postcode"]) && !(empty($_POST["postcode"]))) {
                if (preg_match('/\d{5}/', $_POST["postcode"])) {
                    $update_query = "postcode='".$_POST["postcode"]."'";
                    updateProfile($update_table, $update_query, $username, $mysqli);
                }
                else {
                    $err_postcode = "Post Code must be in number (5 digits number). Example : 43000";
                }
            }
            if (isset($_POST["states"]) && !(empty($_POST["states"]))) {
                $update_query = "states='".$_POST["states"]."'";
                updateProfile($update_table, $update_query, $username, $mysqli);
            }
        }
    }
    /*
    Edit personal info above code
    
    */
    $user_query = "select * from";
    if (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1) {
        $user_query .= " adminAccount";
    }
    else {
        $user_query .= " userAccount";
    }
    $user_query .= " where username='".$_SESSION["username"]."'";
    
    if (($user_result = $mysqli->query($user_query)) == false) {
        echo 'Something went wrong! Server is unavailable!';
        exit();
    }

    $user_fname = "";
    $user_lname = "";
    $address = "";
    $city = "";
    $dob = "";
    $y = ((isset($_POST["year"]) && !(empty($_POST["year"]))) ? intval($_POST["year"]) : "");
    $m = ((isset($_POST["month"]) && !(empty($_POST["month"]))) ? intval($_POST["month"]) : "");
    $d = ((isset($_POST["day"]) && !(empty($_POST["day"]))) ? intval($_POST["day"]) : "");
    $user_phone = "";
    $gender = "";
    $postcode = ((isset($_POST["postcode"]) && !(empty($_POST["postcode"]))) ? $_POST["postcode"] : "");
    $states = "";

    $user_row = $user_result->fetch_assoc();
    if (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1) {
        if (!($user_row["adminFname"] === NULL)) {
            $user_fname = $user_row["adminFname"];
        }
        if (!($user_row["adminLname"] === NULL)) {
            $user_lname = $user_row["adminLname"];
        }
        $user_phone = $user_row["adminPhone"];
    }
    else {
        if (!($user_row["userFname"] === NULL)) {
            $user_fname = $user_row["userFname"];
        }
        if (!($user_row["userLname"] === NULL)) {
            $user_lname = $user_row["userLname"];
        }
        $user_phone = $user_row["userPhone"];
    }
    if (!($user_row["gender"] === NULL)) {
        $gender = $user_row["gender"];
    }
    if (!($user_row["DOB"] === NULL)) {
        $dob = $user_row["DOB"];
        $y = intval(substr($dob,0,4));
        $m = intval(substr($dob,5,2));
        $d = intval(substr($dob,8,2));
    }
    if (!($user_row["addressStreet"] === NULL)) {
        $address = $user_row["addressStreet"];
    }
    if (!($user_row["city"] === NULL)) {
        $city = $user_row["city"];
    }
    if (!($user_row["postcode"] === NULL)) {
        $postcode = $user_row["postcode"];
    }
    if (!($user_row["states"] === NULL)) {
        $states = $user_row["states"];
    }
?>
<!DOCTYPE html>
<html>

<head>
    <title>Personal Profile</title>
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
        #left-content {
            width: 25%;
            display: flex;
            padding: 50px;
        }

        #right-content {
            width: 75%;
            padding: 50px;
            display: flex;
        }

        .vl {
            border-left: 1px solid black;
            height: 100%;
            float: right;
            margin-left: 10px;
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

        #first-content {
            display: flex;
            width: 50%;
        }

        #second-content {
            width: 50%;
            padding-top: 50px;
            padding-left: 20px;
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
                        <a href="personal_profile.php"><p class="choose font-weight-bold">Personal Profile</p></a>
                        <a href="login_management.php"><p class="choose">Login Management</p></a>
                        <a href="change_password.php"><p class="choose">Change Password</p></a>
                    </div>
                </div>
                <div id="line">
                    <div class="vl"></div>
                </div>
            </div>
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" id="right-content">
                <div id="first-content">
                    <div id="header1">
                        <h5 class="my-4">My Profile</h5>
                        <input type="text" name="fname" placeholder="First Name" size="50" value="<?php echo $user_fname; ?>">
                        <p class="mb-4 text-danger"></p>
                        <input type="text" name="lname" placeholder="Last Name" size="50" value="<?php echo $user_lname; ?>">
                        <p class="mb-4 text-danger"></p>
                        <input type="text" name="address" placeholder="Address" size="50" value="<?php echo $address; ?>">
                        <p class="mb-4 text-danger"></p>
                        <input type="text" name="city" placeholder="City" size="50" value="<?php echo $city; ?>">
                        <p class="mb-4 text-danger"></p>
                        <input type="text" name="phone" placeholder="Mobile" size="50" value="<?php echo $user_phone; ?>" disabled>
                        <p class="mb-4 text-danger"></p>
                    </div>

                </div>
                <div id="second-content">
                    <label for="birthday">Date of Birth:</label><br>
                    <select name="day" id="day">
                        <option value="">--Day--</option>
                        <?php
                            for ($i = 1; $i <= 31; $i++) {
                                echo '<option value="'.$i.'"'.((isset($d) && !(empty($d))) ? (($i === $d) ? ' selected' : '') : '').'>'.$i.'</option>';
                            }
                        ?>
                    </select>
                    <select name="month" id="month">
                        <option value="">--Month--</option>
                        <?php
                            $arr_month = array("January","February","March","April","May","June","July","August","September","October","November","December");
                            for ($i = 1; $i <= 12; $i++) {
                                echo '<option value="'.$i.'"'.((isset($m) && !(empty($m))) ? (($i === $m) ? ' selected' : '') : '').'>'.$arr_month[($i-1)].'</option>';
                            }
                        ?>
                    </select>
                    <select name="year" id="year">
                        <option value="">--Year--</option>
                        <?php
                            $year = intval(date('Y'));
                            for ($x = 0; $x <= 120; $x++) {
                                echo '<option value="'.($year-$x).'"'.((isset($y) && !(empty($y))) ? ((($year-$x) === $y) ? ' selected' : '') : '').'>'.($year-$x).'</option>';
                            }
                        ?>
                    </select>
                    <p class="mb-2 text-danger"><?php echo (!(empty($err_dob)) ? $err_dob : ""); ?></p><br>
                    Gender:<br>
                    <input type="radio" id="male" name="gender" value="M"<?php echo (($gender === "M") ? " checked" : ""); ?>>
                    <label for="male">Male</label>
                    <input type="radio" id="female" name="gender" value="F"<?php echo (($gender === "F") ? " checked" : ""); ?>>
                    <label for="female">Female</label><br>
                    <p class="mb-4"></p>
                    <input type="text" name="postcode" placeholder="Post Code" size="50" value="<?php echo $postcode; ?>">
                    <p class="mb-4 text-danger"><?php echo (!(empty($err_postcode)) ? $err_postcode : ""); ?></p>
                    <input type="text" name="states" placeholder="State" size="50" value="<?php echo $states; ?>">
                    <p class="mb-4"></p>
                    <button class="btn btn-success rounded" width="50%" type="submit" name="action" value="changeProfile">Submit</button> 
                </div>
            </form>
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