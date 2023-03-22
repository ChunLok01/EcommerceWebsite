<?php
    require_once("authentication.php");
    if (!($_SESSION["userlevel"] == 1)) {
        header("location:login.php");
        exit();
    }
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

    if (isset($_POST["action"]) && ($_POST["action"] == "View" || $_POST["action"] == "Edit" || $_POST["action"] == "Delete") && isset($_POST["adminId"]) && !(empty($_POST["adminId"]))) {
        $adminId = $_POST["adminId"];
        if (isset($_POST["Edit"]) && $_POST["Edit"] == "Done") {
            require("user_validation.php");
            if (empty($error)) {
                $EditAdmin_query = "update adminAccount set username='".$_POST["username"]."', adminFname='".$_POST["Fname"]."', adminLname='".$_POST["Lname"]."', adminEmail='".$_POST["email"]."', adminPhone='".$_POST["phone"]."', userlevel=".$_POST["level"];
                if (isset($_POST["password"]) && !(empty($_POST["password"]))) {
                    $EditAdmin_query .= ", password=MD5('".$_POST["password"]."')";
                }
                $EditAdmin_query .= " where adminID=".$adminId;
                $Edit_result;
                if (($Edit_result = $mysqli->query($EditAdmin_query)) == false) {
                    echo 'Invalid query: '.$mysqli->error;
                    exit();
                }
            }
            else {
                $_POST["action"] = "Edit";
            }
        }
        else if (isset($_POST["Delete"]) && $_POST["Delete"] == "Yes") {
            $DeleteAdmin_query = "delete from adminAccount where adminID=".$adminId;
            $Delte_result;
            if (($Delte_result = $mysqli->query($DeleteAdmin_query)) == false) {
                echo 'Invalid query: '.$mysqli->error;
                exit();
            }
            else {
                header("location:admin.php");
                exit();
            }
        }
    }
    else if (isset($_POST["action"]) && $_POST["action"] == "Add") {
        if (isset($_POST["Add"]) && $_POST["Add"] == "Add") {
            require("user_validation.php");
            if (!(empty($error))) {
                unset($_POST["Add"]);
            }
        }
        else if (isset($_POST["Add"]) && $_POST["Add"] == "Yes") {
            date_default_timezone_set("Asia/Kuala_Lumpur");
            $register_time = date('Y-m-d H:i:s');
            $AddAdmin_query = "insert into adminAccount values (null,'".$_POST["username"]."','".$_POST["Fname"]."','".$_POST["Lname"]."',MD5('".$_POST["password"]."'),'".$_POST["email"]."','".$_POST["phone"]."',null,null,null,null,null,null,'".$register_time."',".$_POST["level"].")";
            $Add_result;
            $add_flag = false;
            if (($Add_result = $mysqli->query($AddAdmin_query)) == false) {
                echo 'Invalid query: '.$mysqli->error." (Add Admin unsuccessfully)";
                exit();
            }
            else {
                $add_flag = true;
                header("Refresh:5; URL=admin.php");
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
                <?php
                    if (isset($_POST["action"]) && ($_POST["action"] == "View" || $_POST["action"] == "Edit" || $_POST["action"] == "Delete") && isset($_POST["adminId"]) && !(empty($_POST["adminId"]))) {
                ?>
                <h1 class="display-4 text-white">Admin Information</h1>
                <?php
                    $admin_query = 'select * from adminAccount where adminID='.$adminId;

                    $admin_result;

                    if (($admin_result = $mysqli->query($admin_query)) == false) {
                        echo 'Invalid query: '.$mysqli->error;
                        exit();
                    }
                    if ($admin_result->num_rows == 1) {
                        $admin_row = $admin_result->fetch_assoc();
                ?>
                <div class="card">
                    <h5 class="card-header font-weight-light d-flex justify-content-between">Admin ID : <?php echo $adminId; ?></h5>
                    <div class="card-body">
                        <?php
                            if ($_POST["action"] == "Edit") {
                                echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                            }
                            echo '<table class="table">';
                            echo '<tr>';
                            echo '<th>User ID : </th>';
                            echo '<td>'.'<input type="text" class="form-control" name="id" value="'.$admin_row["adminID"].'" disabled/>'.'</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<th>User Name : </th>';
                            echo '<td>'.'<input type="text" class="form-control" name="username" value="'.(($_POST["action"]=="Edit" && isset($_POST["username"])) ? $_POST["username"] : $admin_row["username"]).'"'.($_POST["action"]=="Edit" ? '' : ' disabled').'/>'.(isset($errusername) && !(empty($errusername)) ? $errusername : "").'</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<th>First Name : </th>';
                            echo '<td>'.'<input type="text" class="form-control" name="Fname" value="'.(($_POST["action"]=="Edit" && isset($_POST["Fname"])) ? $_POST["Fname"] : $admin_row["adminFname"]).'"'.($_POST["action"]=="Edit" ? '' : ' disabled').'/>'.(isset($errFname) && !(empty($errFname)) ? $errFname : "").'</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<th>Last Name : </th>';
                            echo '<td>'.'<input type="text" class="form-control" name="Lname" value="'.(($_POST["action"]=="Edit" && isset($_POST["Lname"])) ? $_POST["Lname"] : $admin_row["adminLname"]).'"'.($_POST["action"]=="Edit" ? '' : ' disabled').'/>'.(isset($errLname) && !(empty($errLname)) ? $errLname : "").'</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<th>Phone Number : </th>';
                            echo '<td>'.'<input type="text" class="form-control" name="phone" value="'.(($_POST["action"]=="Edit" && isset($_POST["phone"])) ? $_POST["phone"] : $admin_row["adminPhone"]).'"'.($_POST["action"]=="Edit" ? '' : ' disabled').' placeholder="Exp: 012-3456789, Format: XXX-XXXXXX"/>'.(isset($errphone) && !(empty($errphone)) ? $errphone : "").'</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<th>Email Address : </th>';
                            echo '<td>'.'<input type="text" class="form-control" name="email" value="'.(($_POST["action"]=="Edit" && isset($_POST["email"])) ? $_POST["email"] : $admin_row["adminEmail"]).'"'.($_POST["action"]=="Edit" ? '' : ' disabled').'/>'.(isset($erremail) && !(empty($erremail)) ? $erremail : "").'</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<th>Password : </th>';
                            echo '<td>'.'<input type="password" class="form-control" name="password" value="'.($_POST["action"]=="Edit" ? (isset($_POST["password"]) && !(empty($_POST["password"])) ? $_POST["password"] : '') : $admin_row["password"]).'"'.($_POST["action"]=="Edit" ? '' : ' disabled').' placeholder="Empty this field to not change the password."/>'.(isset($errpassword) && !(empty($errpassword)) ? $errpassword : "").'</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<th>User Level : </th>';
                            echo '<td>'.'<input type="text" class="form-control" name="level" value="'.(($_POST["action"]=="Edit" && isset($_POST["level"])) ? $_POST["level"] : $admin_row["userlevel"]).'"'.($_POST["action"]=="Edit" ? '' : ' disabled').'/>'.(isset($errlevel) && !(empty($errlevel)) ? $errlevel : "").'</td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<th>Address : </th>';
                            echo '<td><input type="text" class="form-control" name="address" value="'.(!($admin_row["addressStreet"] === NULL) ? $admin_row["addressStreet"] : '').'" disabled/></td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<th>City : </th>';
                            echo '<td><input type="text" class="form-control" name="city" value="'.(!($admin_row["city"] === NULL) ? $admin_row["city"] : '').'" disabled/></td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<th>State : </th>';
                            echo '<td><input type="text" class="form-control" name="state" value="'.(!($admin_row["states"] === NULL) ? $admin_row["states"] : '').'" disabled/></td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<th>Postcode : </th>';
                            echo '<td><input type="text" class="form-control" name="postcode" value="'.(!($admin_row["postcode"] === NULL) ? $admin_row["postcode"] : '').'" disabled/></td>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<th>Registered On : </th>';
                            echo '<td>'.'<input type="text" class="form-control" name="registertime" value="'.$admin_row["registerTime"].'" disabled/>'.'</td>';
                            echo '</tr>';
                            echo '</table>';

                            if ($_POST["action"] == "Delete") {
                                echo '<div class="d-flex align-items-center justify-content-between">';
                            }
                            else {
                                echo '<div class="d-flex align-items-center justify-content-end">';
                            }

                            if ($_POST["action"] == "View") {
                                echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                echo '<input type="hidden" name="adminId" value="'.$adminId.'"/>';
                                echo '<input type="hidden" name="page" value="'.$page.'"/>';
                                if (isset($_POST["searchAdmin"]) && !(empty($_POST["searchAdmin"]))) {
                                    echo '<input type="hidden" name="searchAdmin" value="'.$_POST["searchAdmin"].'"/>';
                                }
                                echo '<input type="submit" class="btn btn-danger mx-3" name="action" value="Delete"/>';
                                echo '</form>';
                                
                                echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                echo '<input type="hidden" name="adminId" value="'.$adminId.'"/>';
                                echo '<input type="hidden" name="page" value="'.$page.'"/>';
                                if (isset($_POST["searchAdmin"]) && !(empty($_POST["searchAdmin"]))) {
                                    echo '<input type="hidden" name="searchAdmin" value="'.$_POST["searchAdmin"].'"/>';
                                }
                                echo '<input type="submit" class="btn btn-primary mx-3" name="action" value="Edit"/>';
                                echo '</form>';
                            }
                            else if ($_POST["action"] == "Edit") {
                                echo '<div class="mx-3">';
                                echo '<input type="hidden" name="adminId" value="'.$adminId.'"/>';
                                echo '<input type="hidden" name="page" value="'.$page.'"/>';
                                if (isset($_POST["searchAdmin"]) && !(empty($_POST["searchAdmin"]))) {
                                    echo '<input type="hidden" name="searchAdmin" value="'.$_POST["searchAdmin"].'"/>';
                                }
                                echo '<input type="hidden" name="action" value="View"/>';
                                echo '<input type="submit" class="btn btn-primary" name="Edit" value="Done"/>';
                                echo '</div>';
                            }
                            else if ($_POST["action"] == "Delete") {
                                echo '<span class="text-danger">Do you want to Delete this Admin?</span>';
                                echo '<div class="d-flex">';
                                echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                echo '<input type="hidden" name="adminId" value="'.$adminId.'"/>';
                                echo '<input type="hidden" name="page" value="'.$page.'"/>';
                                if (isset($_POST["searchAdmin"]) && !(empty($_POST["searchAdmin"]))) {
                                    echo '<input type="hidden" name="searchAdmin" value="'.$_POST["searchAdmin"].'"/>';
                                }
                                echo '<input type="hidden" name="action" value="Delete"/>';
                                echo '<input type="submit" class="btn btn-danger mx-3" name="Delete" value="Yes"/>';
                                echo '</form>';
                                echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                echo '<input type="hidden" name="adminId" value="'.$adminId.'"/>';
                                echo '<input type="hidden" name="page" value="'.$page.'"/>';
                                if (isset($_POST["searchAdmin"]) && !(empty($_POST["searchAdmin"]))) {
                                    echo '<input type="hidden" name="searchAdmin" value="'.$_POST["searchAdmin"].'"/>';
                                }
                                echo '<input type="hidden" name="action" value="View"/>';
                                echo '<input type="submit" class="btn btn-dark mx-3" name="Delete" value="No"/>';
                                echo '</form>';
                                echo '</div>';
                            }
                            echo '</div>';

                            if ($_POST["action"] == "Edit") {
                                echo '</form>';
                            }

                            echo '<div class="d-flex align-items-center justify-content-start">';
                            echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                            echo '<input type="hidden" name="page" value="'.$page.'"/>';
                            if (isset($_POST["searchAdmin"]) && !(empty($_POST["searchAdmin"]))) {
                                echo '<input type="hidden" name="searchAdmin" value="'.$_POST["searchAdmin"].'"/>';
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
                    else if (isset($_POST["action"]) && $_POST["action"] == "Add") {
                ?>
                <h1 class="display-4 text-white">Add Admin Account</h1>
                <div class="card">
                    <h5 class="card-header font-weight-light d-flex justify-content-between">Add Account</h5>
                    <div class="card-body">
                        <?php
                            if (isset($_POST["Add"]) && $_POST["Add"] == "Yes" && $add_flag === true) {
                                echo "Add Admin Successfully! The page will redirect to the admin page in 5 seconds.";
                                exit();
                            }
                            if (isset($_POST["Add"]) && ($_POST["Add"] == "No")) {
                                unset($_POST["Add"]);
                            }

                            echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                            require("add_user_table.php");

                            if (isset($_POST["Add"]) && $_POST["Add"] == "Add") {
                                echo '<div class="d-flex align-items-center justify-content-between">';
                                echo '<span class="text-primary">Are you sure you want to Add this Admin?</span>';
                                echo '<div class="d-flex">';
                                echo '<input type="hidden" name="action" value="Add"/>';
                                echo '<input type="submit" class="btn btn-success mx-3" name="Add" value="Yes"/>';
                                echo '<input type="submit" class="btn btn-dark mx-3" name="Add" value="No"/>';
                                echo '</div>';
                            }
                            else {
                                echo '<div class="d-flex align-items-center justify-content-end">';
                                echo '<input type="hidden" name="action" value="Add"/>';
                                echo '<input type="submit" class="btn btn-dark mx-3" name="Add" value="Add"/>';
                            }
                            
                            echo '</div>';
                            echo '</form>';
                            echo '<div class="d-flex">';
                            echo '<a class="btn btn-dark" href="'.$_SERVER["PHP_SELF"].'">Back</a>';
                            echo '</div>';
                        ?>
                    </div>
                </div>
                <?php
                    }
                    else {
                ?>
                <h1 class="display-4 text-white">Admin List</h1>
                <div class="card">
                    <h5 class="card-header font-weight-light d-flex justify-content-between">
                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                            <input type="text" name="searchAdmin" value="<?php echo ((isset($_POST["searchAdmin"]) && !(empty($_POST["searchAdmin"]))) ? $_POST["searchAdmin"] : ""); ?>"/>
                            <input type="submit" name="action" value="Search"/>
                        </form>
                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                            <input type="hidden" name="action" value="Add"/>
                            <input type="submit" name="submit" value="Add Account"/>
                        </form>
                    </h5>
                    <div class="card-body">
                        <?php
                            $admin_query;
                            if (isset($_POST["searchAdmin"]) && $_POST["searchAdmin"] != NULL) {
                                $searchAdmin = trim($_POST["searchAdmin"]);
                                $array_search = explode(" ", $searchAdmin);

                                $admin_query = "select * from adminAccount where username like '%".$array_search[0]."%' or adminFname like '%".$array_search[0]."%' or adminLname like '%".$array_search[0]."%'";
                                foreach ($array_search as $key=>$value) {
                                    if ($key > 0) {
                                        $admin_query .= " or username like '%".$value."%' or adminFname like '%".$value."%' or adminLname like '%".$value."%'";
                                    }
                                }
                            }
                            else {
                                $admin_query = "select * from adminAccount";
                            }
                            
                            $admin_result;

                            if (($admin_result = $mysqli->query($admin_query)) == false) {
                                echo 'Invalid query: '.$mysqli->error;
                            }
                            
                            if ($admin_result->num_rows > 0) {
                                $numrows = $admin_result->num_rows;
                                $limit = 5;
                                $start = ($page-1) * $limit;

                                echo '<table class="table">';
                                echo '<tr>';
                                echo '<th>Admin ID</th>';
                                echo '<th>Name</th>';
                                echo '<th>Phone Number</th>';
                                echo '<th>Email Address</th>';
                                echo '<th>Action</th>';
                                echo '</tr>';

                                mysqli_data_seek($admin_result, $start);
                                $count = 0;

                                while ($admin_row = $admin_result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>'.$admin_row["adminID"].'</td>';
                                    echo '<td>';
                                    echo $admin_row["username"];
                                    if (!($admin_row["adminFname"] === NULL) || !($admin_row["adminLname"] === NULL)) {
                                        $fname_lname = " (";
                                        if (!($admin_row["adminFname"] === NULL)) {
                                            $fname_lname .= $admin_row["adminFname"];
                                        }
                                        if (!($admin_row["adminLname"] === NULL)) {
                                            if (!($fname_lname === " (")) {
                                                $fname_lname .= " ";
                                            }
                                            $fname_lname .= $admin_row["adminLname"];
                                        }
                                        $fname_lname .= ")";
                                        echo $fname_lname;
                                    }
                                    echo '</td>';
                                    echo '<td>'.$admin_row["adminPhone"].'</td>';
                                    echo '<td>'.$admin_row["adminEmail"].'</td>';
                                    echo '<td>';
                                    echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                    echo '<input type="hidden" name="adminId" value="'.$admin_row["adminID"].'"/>';
                                    if (isset($_POST["searchAdmin"]) && !(empty($_POST["searchAdmin"]))) {
                                        echo '<input type="hidden" name="searchAdmin" value="'.$_POST["searchAdmin"].'"/>';
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
                                    if (isset($_POST["searchAdmin"])) {
                                        echo '<input type="hidden" name="searchAdmin" value="'.$_POST["searchAdmin"].'"/>';
                                    }
                                    echo '<input type="hidden" name="page" value="'.($page-1).'"/>';
                                    echo '<input type="submit" value="◁"/>';
                                    echo '</form>';
                                }
                                echo '<span class="mx-3">'.$page.' / '.$j.'</span>';
                                if ($page != $j) {
                                    echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                    if (isset($_POST["searchAdmin"])) {
                                        echo '<input type="hidden" name="searchAdmin" value="'.$_POST["searchAdmin"].'"/>';
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