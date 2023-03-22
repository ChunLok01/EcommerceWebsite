<?php
require_once("authentication.php");
include("WWW/conn_db.php");
$database = "project";
$mysqli = new mysqli($host,$user,$password,$database);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: ".mysqli_connect_error();
    exit();
}
$page;
if (isset($_POST["page"])) {
    $page = $_POST["page"];
}
else {
    $page = 1;
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
        if ((isset($_SESSION["auth"]) && $_SESSION["auth"] == 1) || (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1)) {
            echo '<link rel="stylesheet" href="css/style.css">';
        }
    ?>
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
                <?php
                if (isset($_POST["action"]) && $_POST["action"] == "View" && isset($_POST["userId"]) && !(empty($_POST["userId"]))) {
                    $userId = $_POST["userId"];
                    if (isset($_POST["changeLevel"]) && $_POST["changeLevel"] == "Yes") {
                        $changeLevelQuery = 'update userAccount set userlevel='.$_POST["level"].' where userID='.$userId;
                        $changeLevelResult;
                        if (($changeLevelResult = $mysqli->query($changeLevelQuery)) == false) {
                            echo 'Invalid query: '.$mysqli->error.' while changing level.';
                            exit();
                        }
                    }
                ?>
                <h1 class="display-4 text-white">Customer Information</h1>
                <?php
                    $user_query = 'select * from userAccount where userID='.$userId;
                    $user_result;
                    if (($user_result = $mysqli->query($user_query)) == false) {
                        echo 'Invalid query: '.$mysqli->error;
                        exit();
                    }
                    if ($user_result->num_rows == 1) {
                        $user_row = $user_result->fetch_assoc();
                ?>
                <div class="card">
                    <h5 class="card-header font-weight-light d-flex justify-content-between">Customer ID : <?php echo $userId; ?></h5>
                    <div class="card-body">
                        <?php
                        echo '<table class="table">';
                        echo '<tr>';
                        echo '<th>User ID : </th>';
                        echo '<td><input type="text" class="form-control" name="userId" value="'.$userId.'" disabled/></td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<th>User Name : </th>';
                        echo '<td><input type="text" class="form-control" name="username" value="'.$user_row["username"].'" disabled/></td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<tr>';
                        echo '<th>First Name : </th>';
                        echo '<td><input type="text" class="form-control" name="fname" value="'.$user_row["userFname"].'" disabled/></td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<th>Last Name : </th>';
                        echo '<td><input type="text" class="form-control" name="lname" value="'.$user_row["userLname"].'" disabled/></td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<tr>';
                        echo '<th>Phone Number : </th>';
                        echo '<td><input type="text" class="form-control" name="phone" value="'.$user_row["userPhone"].'" disabled/></td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<th>Email Address : </th>';
                        echo '<td><input type="text" class="form-control" name="email" value="'.$user_row["userEmail"].'" disabled/></td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<th>User Level : </th>';
                        echo '<td>';
                        echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                        echo '<input type="hidden" name="page" value="'.$page.'"/>';
                        if (isset($_POST["searchCustomer"]) && !(empty($_POST["searchCustomer"]))) {
                            echo '<input type="hidden" name="searchCustomer" value="'.$_POST["searchCustomer"].'"/>';
                        }
                        echo '<input type="hidden" name="userId" value="'.$userId.'"/>';
                        echo '<input type="hidden" name="action" value="View"/>';
                        echo '<input type="hidden" name="changeLevel" value="Yes"/>';
                        echo '<select name="level" class="form-control" onchange="this.form.submit()">';
                        echo '<option value="0"'.(($user_row["userlevel"] == 0) ? ' selected' : '').'>0</option>';
                        echo '<option value="1"'.(($user_row["userlevel"] == 1) ? ' selected' : '').'>1</option>';
                        echo '</select>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<th>Address : </th>';
                        echo '<td><input type="text" class="form-control" name="address" value="'.(!($user_row["addressStreet"] === NULL) ? $user_row["addressStreet"] : '').'" disabled/></td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<th>City : </th>';
                        echo '<td><input type="text" class="form-control" name="city" value="'.(!($user_row["city"] === NULL) ? $user_row["city"] : '').'" disabled/></td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<th>State : </th>';
                        echo '<td><input type="text" class="form-control" name="state" value="'.(!($user_row["states"] === NULL) ? $user_row["states"] : '').'" disabled/></td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<th>Postcode : </th>';
                        echo '<td><input type="text" class="form-control" name="postcode" value="'.(!($user_row["postcode"] === NULL) ? $user_row["postcode"] : '').'" disabled/></td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<th>Registered On : </th>';
                        echo '<td><input type="text" class="form-control" name="registertime" value="'.$user_row["registerTime"].'" disabled/></td>';
                        echo '</tr>';
                        echo '</table>';

                        echo '<div class="d-flex align-items-center justify-content-start">';
                        echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                        echo '<input type="hidden" name="page" value="'.$page.'"/>';
                        if (isset($_POST["searchCustomer"]) && !(empty($_POST["searchCustomer"]))) {
                            echo '<input type="hidden" name="searchCustomer" value="'.$_POST["searchCustomer"].'"/>';
                        }
                        echo '<input type="submit" class="btn btn-dark" name="action" value="Back"/>';
                        echo '</form>';
                        echo '</div>';
                        ?>
                    </div>
                </div>
                <?php
                    }
                    else {
                        echo "<p>No record / something went wrong!</p>";
                    }
                }
                else {
                ?>
                <h1 class="display-4 text-white">Customer List</h1>
                <div class="card">
                    <h5 class="card-header font-weight-light">
                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                            <input type="text" name="searchCustomer" value="<?php echo ((isset($_POST["action"]) && $_POST["action"]=="Search") ? $_POST["searchCustomer"] : ""); ?>"/>
                            <input type="submit" name="action" value="Search"/>
                        </form>
                    </h5>
                    <div class="card-body">
                        <?php
                        $customer_query;
                        if (isset($_POST["searchCustomer"]) && $_POST["searchCustomer"] != NULL) {
                            $searchCustomer = trim($_POST["searchCustomer"]);
                            $array_search = explode(" ", $searchCustomer);

                            $customer_query = "select * from userAccount where username like '%".$array_search[0]."%' or userFname like '%".$array_search[0]."%' or userLname like '%".$array_search[0]."%'";
                            foreach ($array_search as $key=>$value) {
                                if ($key > 0) {
                                    $customer_query .= " or username like '%".$value."%' or userFname like '%".$value."%' or userLname like '%".$value."%'";
                                }
                            }
                        }
                        else {
                            $customer_query = "select * from userAccount";
                        }
                        
                        $customer_result;

                        if (($customer_result = $mysqli->query($customer_query)) == false) {
                            echo 'Invalid query: '.$mysqli->error;
                        }
                        
                        if ($customer_result->num_rows > 0) {
                            $numrows = $customer_result->num_rows;
                            $limit = 5;
                            $start = ($page-1) * $limit;

                            echo '<table class="table">';
                            echo '<tr>';
                            echo '<th>User ID</th>';
                            echo '<th>Name</th>';
                            echo '<th>Phone Number</th>';
                            echo '<th>Email Address</th>';
                            echo '<th>Action</th>';
                            echo '</tr>';

                            mysqli_data_seek($customer_result, $start);
                            $count = 0;

                            while ($customer_row = $customer_result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>'.$customer_row["userID"].'</td>';
                                echo '<td>';
                                echo $customer_row["username"];
                                if (!($customer_row["userFname"] === NULL) || !($customer_row["userLname"] === NULL)) {
                                    $fname_lname = " (";
                                    if (!($customer_row["userFname"] === NULL)) {
                                        $fname_lname .= $customer_row["userFname"];
                                    }
                                    if (!($customer_row["userLname"] === NULL)) {
                                        if (!($fname_lname === " (")) {
                                            $fname_lname .= " ";
                                        }
                                        $fname_lname .= $customer_row["userLname"];
                                    }
                                    $fname_lname .= ")";
                                    echo $fname_lname;
                                }
                                echo '</td>';
                                echo '<td>'.$customer_row["userPhone"].'</td>';
                                echo '<td>'.$customer_row["userEmail"].'</td>';
                                echo '<td>';
                                echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                echo '<input type="hidden" name="userId" value="'.$customer_row["userID"].'"/>';
                                if (isset($_POST["searchCustomer"])) {
                                    echo '<input type="hidden" name="searchCustomer" value="'.$_POST["searchCustomer"].'"/>';
                                }
                                echo '<input type="hidden" name="page" value="'.$page.'"/>';
                                echo '<input type="submit" name="action" value="View"/>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';

                                $count++;
                                if ($count == $limit) {
                                    break;
                                }
                            }
                            echo '</table>';

                            $j = ceil($numrows/$limit);

                            echo '<div class="d-flex justify-content-center align-items-center">';
                            if ($page != 1) {
                                echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                if (isset($_POST["searchCustomer"])) {
                                    echo '<input type="hidden" name="searchCustomer" value="'.$_POST["searchCustomer"].'"/>';
                                }
                                echo '<input type="hidden" name="page" value="'.($page-1).'"/>';
                                echo '<input type="submit" value="◁"/>';
                                echo '</form>';
                            }
                            echo '<span class="mx-3">'.$page.' / '.$j.'</span>';
                            if ($page != $j) {
                                echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                if (isset($_POST["searchCustomer"])) {
                                    echo '<input type="hidden" name="searchCustomer" value="'.$_POST["searchCustomer"].'"/>';
                                }
                                echo '<input type="hidden" name="page" value="'.($page+1).'"/>';
                                echo '<input type="submit" value="▷"/>';
                                echo '</form>';
                            }
                            echo '</div>';
                        }
                        else {
                            echo "No records!";
                        }
                        ?>
                    </div>
                </div>
                <?php
                }
                ?>
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