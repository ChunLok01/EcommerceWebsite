<?php
    require_once("authentication.php");
    require_once("WWW/conn_db.php");

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

    function select_function($mysqli, $column, $table, $query) {
        $selectQuery = "select ".$column." from ".$table." ".$query;
        if (($selectResult = $mysqli->query($selectQuery)) == false) {
            echo "Invalid query: ".$mysqli->error;
        }
        return $selectResult;
    }
    function update_function($mysqli, $table, $query) {
        $updateQuery = "update ".$table." set ".$query;
        if (($updateResult = $mysqli->query($updateQuery)) == false) {
            echo "Invalid query: ".$mysqli->error;
        }
    }
    function array_checkandmake($mysqli,$array_required,$column) {
        $arraycheckmake = array();
        $cal = 0;
        foreach ($array_required as $key=>$value) {
            $checkduplicate = strtolower($column." like '%".$mysqli->real_escape_string($value)."%'");
            if (!(in_array($checkduplicate,$arraycheckmake))) {
                $arraycheckmake[$cal] = $checkduplicate;
                $cal++;
            }
        }
        return $arraycheckmake;
    }

    if (isset($_SESSION["orderID"]) && !(empty($_SESSION["orderID"]))) {
        $_POST["view"] = $_SESSION["orderID"];
        unset($_SESSION["orderID"]);
    }

    if (isset($_POST["view"]) && !(empty($_POST["view"]))) {
        if (preg_match('/^\d{1,}$/',$_POST["view"])) {
            $checkorder = select_function($mysqli, "ol.*,os.statusdetails", "orderlist ol inner join orderstatuslist os on ol.statusID=os.statusID", "where ol.orderID=".$_POST["view"]);
            if ($checkorder->num_rows > 0) {
                $orderlist = $checkorder->fetch_assoc();
                $selectStatus = select_function($mysqli, "MAX(statusID) as sID", "orderstatuslist", "");
                $status_row = $selectStatus->fetch_assoc();
                if (isset($_POST["action"]) && preg_match('/^\d{1,}$/',$_POST["action"]) && $_POST["action"] >= 0 && $_POST["action"] <= $status_row["sID"]) {
                    $table = "orderlist";
                    $query = "statusID=".$_POST["action"]." where orderID=".$orderlist["orderID"];
                    update_function($mysqli, $table, $query);
                }
            }
            else {
                unset($_POST["view"]);
            }
        }
        else {
            unset($_POST["view"]);
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
                <h1 class="display-4 text-white">
                    <?php
                        if (isset($_POST["view"]) && !(empty($_POST["view"]))) {
                            echo "VIEW ORDER";
                        }
                        else {
                            echo "ORDER LIST";
                        }
                    ?>
                </h1>
                <div class="card">
                    <h5 class="card-header font-weight-light d-flex justify-content-between">
                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                            <input type="text" name="search" value="<?php echo (isset($_POST["search"]) && $_POST["search"] != "") ? $_POST["search"] : ""; ?>"/>
                            <?php
                                $minStatus = select_function($mysqli, "MIN(statusID) as sID", "orderstatuslist", "");
                                $min_row = $minStatus->fetch_assoc();
                                $maxStatus = select_function($mysqli, "MAX(statusID) as sID", "orderstatuslist", "");
                                $max_row = $maxStatus->fetch_assoc();
                                if (isset($_POST["searchstatus"]) && $_POST["searchstatus"] == "set" && isset($_POST["status"]) && $_POST["status"] != "" && preg_match('/^\d{1,}$/',$_POST["status"]) && $_POST["status"] >= $min_row["sID"] && $_POST["status"] <= $max_row["sID"]) {
                                    echo '<input type="hidden" name="status" value="'.$_POST["status"].'"/>';
                                    echo '<input type="hidden" name="searchstatus" value="set"/>';
                                }
                            ?>
                            <select name="searchby">
                                <?php
                                    if (isset($_POST["searchby"]) && $_POST["searchby"] != "" && preg_match('/^\d{1}$/',$_POST["searchby"]) && $_POST["searchby"] >= 1 && $_POST["searchby"] <= 5) {
                                        echo '<option value="">--Search By--</option>';
                                        echo '<option value="1"'.(($_POST["searchby"]=="1") ? " selected" : "").'>Order ID</option>';
                                        echo '<option value="2"'.(($_POST["searchby"]=="2") ? " selected" : "").'>Username</option>';
                                        echo '<option value="3"'.(($_POST["searchby"]=="3") ? " selected" : "").'>Address</option>';
                                        echo '<option value="4"'.(($_POST["searchby"]=="4") ? " selected" : "").'>Order Date</option>';
                                        echo '<option value="5"'.(($_POST["searchby"]=="5") ? " selected" : "").'>Ship Date</option>';
                                    }
                                    else {
                                ?>
                                <option value="">--Search By--</option>
                                <option value="1">Order ID</option>
                                <option value="2">Username</option>
                                <option value="3">Address</option>
                                <option value="4">Order Date</option>
                                <option value="5">Ship Date</option>
                                <?php
                                    }
                                ?>
                            </select>
                            <button type="submit" name="submit" value="search">Search</button>
                        </form>
                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                            <input type="hidden" name="searchstatus" value="set"/>
                            <?php
                                if (isset($_POST["search"]) && $_POST["search"] != "") {
                                    echo '<input type="hidden" name="search" value="'.$_POST["search"].'"/>';
                                }
                                if (isset($_POST["searchby"]) && $_POST["searchby"] != "" && preg_match('/^\d{1}$/',$_POST["searchby"]) && $_POST["searchby"] >= 1 && $_POST["searchby"] <= 5) {
                                    echo '<input type="hidden" name="searchby" value="'.$_POST["searchby"].'"/>';
                                }
                            ?>
                            <select name="status" onchange="this.form.submit()">
                                <option value="">--Search by Status--</option>
                                <?php
                                    $status = select_function($mysqli, "*", "orderstatuslist", "");
                                    while ($statusrow = $status->fetch_assoc()) {
                                        echo '<option value="'.$statusrow["statusID"].'"';
                                        if (isset($_POST["status"]) && $_POST["status"] != "" && preg_match('/^\d{1,}$/',$_POST["status"]) && $_POST["status"] >= $min_row["sID"] && $_POST["status"] <= $max_row["sID"]) {
                                            if ($_POST["status"] == $statusrow["statusID"]) {
                                                echo ' selected';
                                            }
                                        }
                                        echo '>'.$statusrow["statusID"]." - ".$statusrow["statusdetails"].'</option>';
                                    }
                                ?>
                            </select>
                        </form>
                    </h5>
                    <div class="card-body">
                        <?php
                            if (isset($_POST["view"]) && !(empty($_POST["view"]))) {
                                $order_list = select_function($mysqli, "ol.*,os.statusdetails", "orderlist ol inner join orderstatuslist os on ol.statusID=os.statusID", "where ol.orderID=".$_POST["view"]);
                                $orderlist = $order_list->fetch_assoc();
                        ?>
                        <div class="row mx-0 my-3">
                            <div class="row col-12 px-0 mx-0 border" style="width:900px;">
                                <div class="row mx-0 border col-12 px-0">
                                    <div class="col-6 px-0">
                                        <p><?php echo nl2br($orderlist["shipaddress"]); ?></p>
                                    </div>
                                    <div class="col-6 px-0 d-flex justify-content-end">
                                        <div>
                                            <p>Order ID : <?php echo $orderlist["orderID"]." #".$orderlist["username"]; ?></p>
                                            <p>Order/Payment Time : <?php echo $orderlist["orderdate"]; ?></p>
                                            <p>Delivery method : <?php echo $orderlist["shipmethod"]; ?></p>
                                            <p>Payment method : <?php echo ($orderlist["paymentmethod"]=="cash") ? "COD" : $orderlist["paymentmethod"]; ?></p>
                                            <p>
                                                <span>Status :</span>
                                                <?php
                                                    $status = select_function($mysqli, "*", "orderstatuslist", "");
                                                    echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                                    echo '<input type="hidden" name="view" value="'.$_POST["view"].'"/>';
                                                    if (isset($_POST["search"]) && $_POST["search"] != "") {
                                                        echo '<input type="hidden" name="search" value="'.$_POST["search"].'"/>';
                                                    }
                                                    if (isset($_POST["searchby"]) && $_POST["searchby"] != "" && preg_match('/^\d{1}$/',$_POST["searchby"]) && $_POST["searchby"] >= 1 && $_POST["searchby"] <= 5) {
                                                        echo '<input type="hidden" name="searchby" value="'.$_POST["searchby"].'"/>';
                                                    }
                                                    if (isset($_POST["searchstatus"]) && $_POST["searchstatus"] == "set" && isset($_POST["status"]) && $_POST["status"] != "" && preg_match('/^\d{1,}$/',$_POST["status"]) && $_POST["status"] >= $min_row["sID"] && $_POST["status"] <= $max_row["sID"]) {
                                                        echo '<input type="hidden" name="status" value="'.$_POST["status"].'"/>';
                                                        echo '<input type="hidden" name="searchstatus" value="set"/>';
                                                    }
                                                    echo '<input type="hidden" name="page" value="'.$page.'"/>';
                                                    echo '<select name="action" onchange="this.form.submit()">';
                                                    while ($statusrow = $status->fetch_assoc()) {
                                                        echo '<option value="'.$statusrow["statusID"].'"';
                                                        if ($statusrow["statusID"] == $orderlist["statusID"]) {
                                                            echo ' selected';
                                                        }
                                                        echo '>'.$statusrow["statusID"]." - ".$statusrow["statusdetails"].'</option>';
                                                    }
                                                    echo '</select>';
                                                    echo '</form>';
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 px-0 row mx-0">
                                    <div class="row px-0 mx-0 col-12 border-bottom">
                                        <div class="col-1 px-0 font-weight-bold">NO</div>
                                        <div class="col-6 px-0 font-weight-bold">NAME</div>
                                        <div class="col-2 px-0 font-weight-bold">UNIT PRICE (RM)</div>
                                        <div class="col-1 px-0 font-weight-bold">QUANTITY</div>
                                        <div class="col-2 px-0 font-weight-bold d-flex justify-content-end">SUBTOTAL PRICE (RM)</div>
                                    </div>
                                    <?php
                                        $num = 1;
                                        $total = 0.00;
                                        $orderitem = select_function($mysqli, "*", "orderitem", "where orderID=".$orderlist["orderID"]);
                                        while ($orderitem_row = $orderitem->fetch_assoc()) {
                                            $subtotal = ($orderitem_row["listprice"] * $orderitem_row["qty"]);
                                            $total += $subtotal;
                                    ?>
                                    <div class="row px-0 mx-0 col-12 border-bottom">
                                        <div class="col-1 px-0"><?php echo $num; ?></div>
                                        <div class="col-6 px-0"><?php echo $orderitem_row["prod_name"]; ?></div>
                                        <div class="col-2 px-0">RM <?php echo number_format($orderitem_row["listprice"],2); ?></div>
                                        <div class="col-1 px-0 text-center">X <?php echo $orderitem_row["qty"]; ?></div>
                                        <div class="col-2 px-0 d-flex justify-content-end"><?php echo number_format($subtotal,2); ?></div>
                                    </div>
                                    <?php
                                            $num++;
                                        }
                                        $total += $orderlist["shipfee"];
                                    ?>
                                    <div class="row px-0 mx-0 col-12 border-bottom">
                                        <div class="col-1 px-0"><?php echo $num++; ?></div>
                                        <div class="col-6 px-0">Shipping Fee</div>
                                        <div class="col-2 px-0">RM <?php echo number_format($orderlist["shipfee"],2); ?></div>
                                        <div class="col-1 px-0 text-center">X 1</div>
                                        <div class="col-2 px-0 d-flex justify-content-end"><?php echo number_format($orderlist["shipfee"],2); ?></div>
                                    </div>
                                    <div class="row px-0 mx-0 col-12 border-bottom">
                                        <div class="col-10 px-0 font-weight-bold">TOTAL</div>
                                        <div class="col-2 px-0 font-weight-bold d-flex justify-content-end"><?php echo number_format($total,2); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 px-0 d-flex justify-content-between align-items-center">
                                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                                    <?php
                                        if (isset($_POST["search"]) && $_POST["search"] != "") {
                                            echo '<input type="hidden" name="search" value="'.$_POST["search"].'"/>';
                                        }
                                        if (isset($_POST["searchby"]) && $_POST["searchby"] != "" && preg_match('/^\d{1}$/',$_POST["searchby"]) && $_POST["searchby"] >= 1 && $_POST["searchby"] <= 5) {
                                            echo '<input type="hidden" name="searchby" value="'.$_POST["searchby"].'"/>';
                                        }
                                        if (isset($_POST["searchstatus"]) && $_POST["searchstatus"] == "set" && isset($_POST["status"]) && $_POST["status"] != "" && preg_match('/^\d{1,}$/',$_POST["status"]) && $_POST["status"] >= $min_row["sID"] && $_POST["status"] <= $max_row["sID"]) {
                                            echo '<input type="hidden" name="status" value="'.$_POST["status"].'"/>';
                                            echo '<input type="hidden" name="searchstatus" value="set"/>';
                                        }
                                        echo '<input type="hidden" name="page" value="'.$page.'"/>';
                                    ?>
                                    <button class="btn btn-dark" type="submit" name="page" value="<?php echo $page; ?>">Back</button>
                                </form>
                            </div>
                        </div>
                        <?php
                            }
                            else {
                                $querysearch = "";
                                
                                $search_string = "";
                                if (isset($_POST["search"]) && $_POST["search"] != "") {
                                    $searching = trim($_POST["search"]);
                                    $array_search = explode(" ", $searching);

                                    if (isset($_POST["searchby"]) && $_POST["searchby"] != "" && preg_match('/^\d{1}$/',$_POST["searchby"]) && $_POST["searchby"] >= 1 && $_POST["searchby"] <= 5) {
                                        $textsearchby = "";
                                        if ($_POST["searchby"] == "1") {
                                            $textsearchby = "orderID";
                                        }
                                        else if ($_POST["searchby"] == "2") {
                                            $textsearchby = "username";
                                        }
                                        else if ($_POST["searchby"] == "3") {
                                            $textsearchby = "shipaddress";
                                        }
                                        else if ($_POST["searchby"] == "4") {
                                            $textsearchby = "orderdate";
                                        }
                                        else if ($_POST["searchby"] == "5") {
                                            $textsearchby = "shipdate";
                                        }
                                        
                                        if ($textsearchby == "orderID") {
                                            if (preg_match('/^\d{1,}$/',$_POST["search"])) {
                                                $search_string = "ol.orderID=".$_POST["search"];
                                            }
                                            else {
                                                $error = '<p class="text-danger">Search Failed! Order ID must be number!</p>';
                                            }
                                        }
                                        else {
                                            $search_string_array = array_checkandmake($mysqli,$array_search,"ol.".$textsearchby);

                                            if (count($search_string_array) > 0) {
                                                $search_string = join(" and ",$search_string_array);
                                            }
                                        }
                                    }
                                    else {
                                        $search_name_array = array_checkandmake($mysqli,$array_search,"ol.username");
                                        
                                        $search_shipaddress_array = array_checkandmake($mysqli,$array_search,"ol.shipaddress");
                                        
                                        $search_shipmethod_array = array_checkandmake($mysqli,$array_search,"ol.shipmethod");
                                        
                                        $search_paymentmethod_array = array_checkandmake($mysqli,$array_search,"ol.paymentmethod");
                                        
                                        $search_odate_array = array_checkandmake($mysqli,$array_search,"ol.orderdate");
                                        
                                        $search_sdate_array = array_checkandmake($mysqli,$array_search,"ol.shipdate");

                                        $newarray = array(
                                            "(".(join(" and ",$search_name_array)).")",
                                            "(".(join(" and ",$search_shipaddress_array)).")",
                                            "(".(join(" and ",$search_shipmethod_array)).")",
                                            "(".(join(" and ",$search_paymentmethod_array)).")",
                                            "(".(join(" and ",$search_odate_array)).")",
                                            "(".(join(" and ",$search_sdate_array)).")",
                                        );
                                        $search_string = join(" or ",$newarray);
                                    }
                                }
                                $status_string = "";

                                if (isset($_POST["searchstatus"]) && $_POST["searchstatus"] == "set" && isset($_POST["status"]) && $_POST["status"] != "" && preg_match('/^\d{1,}$/',$_POST["status"]) && $_POST["status"] >= $min_row["sID"] && $_POST["status"] <= $max_row["sID"]) {
                                    $status_string = "ol.statusID=".$_POST["status"];
                                }

                                if (!(empty($search_string)) || !(empty($status_string))) {
                                    $querysearch .= "where ";
                                    if (!(empty($search_string)) && !(empty($status_string))) {
                                        $querysearch .= "(".$search_string.")";
                                        $querysearch .= " and ".$status_string;
                                    }
                                    else if (!(empty($search_string)) && empty($status_string)) {
                                        $querysearch .= "(".$search_string.")";
                                    }
                                    else {
                                        $querysearch .= $status_string;
                                    }
                                    $querysearch .= " ";
                                }

                                $purchased = select_function($mysqli, "ol.*,os.statusdetails", "orderlist ol inner join orderstatuslist os on ol.statusID=os.statusID", $querysearch."order by ol.orderdate desc");
                                $numrows = $purchased->num_rows;
                                if ($numrows > 0) {
                                    $limit = 5;
                                    $start = ($page-1) * $limit;
                                    mysqli_data_seek($purchased,$start);
                                    $count = 0;

                                    $num = ($start+1);
                        ?>
                        <div class="border my-3">
                            <?php echo (isset($error) && !(empty($error)))? $error : ""; ?>
                            <table class="table">
                                <tr>
                                    <th class="text-center">NO</th>
                                    <th class="text-center">Order ID</th>
                                    <th class="text-center">Order Date</th>
                                    <th class="text-center">Estimate Ship Date</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Billing</th>
                                    <th class="text-right">Total Price (RM)</th>
                                    <th class="text-center">Action</th>
                                </tr>
                                <?php
                                    while ($purchased_row = $purchased->fetch_assoc()) {
                                        $orderitem = select_function($mysqli, "*", "orderitem", "where orderID=".$purchased_row["orderID"]);
                                        $total = 0.00;
                                        if ($orderitem->num_rows > 0) {
                                            while ($orderitem_row = $orderitem->fetch_assoc()) {
                                                $total += ($orderitem_row["listprice"] * $orderitem_row["qty"]);
                                            }
                                        }
                                        $total += $purchased_row["shipfee"];
                                ?>
                                <tr>
                                    <td class="text-center align-middle"><?php echo $num++; ?></td>
                                    <td class="text-center align-middle">
                                        <?php
                                            echo $purchased_row["orderID"]." #".$purchased_row["username"];
                                            //echo '<form action="" method="POST">';
                                        ?>
                                    </td>
                                    <td class="text-center align-middle"><?php echo $purchased_row["orderdate"]; ?></td>
                                    <td class="text-center align-middle"><?php echo $purchased_row["shipdate"]; ?></td>
                                    <td class="text-center align-middle">
                                        <?php
                                            if ($purchased_row["statusID"] == 0) {
                                                echo '<div class="bg-danger text-white rounded">';
                                            }
                                            else if ($purchased_row["statusID"] == 1) {
                                                echo '<div class="bg-primary text-white rounded">';
                                            }
                                            else if ($purchased_row["statusID"] == 2) {
                                                echo '<div class="bg-warning text-white rounded">';
                                            }
                                            else {
                                                echo '<div class="bg-success text-white rounded">';
                                            }
                                            echo $purchased_row["statusdetails"];
                                            echo '</div>';
                                        ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="text-left" style="height:100px;overflow-y:auto;"><?php echo nl2br($purchased_row["shipaddress"]); ?></div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="row mx-0">
                                            <span class="col-6 px-0 text-right">RM</span>
                                            <span class="col-6 px-0 text-right"><?php echo number_format($total,2); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php
                                            echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                            if (isset($_POST["search"]) && $_POST["search"] != "") {
                                                echo '<input type="hidden" name="search" value="'.$_POST["search"].'"/>';
                                            }
                                            if (isset($_POST["searchby"]) && $_POST["searchby"] != "" && preg_match('/^\d{1}$/',$_POST["searchby"]) && $_POST["searchby"] >= 1 && $_POST["searchby"] <= 5) {
                                                echo '<input type="hidden" name="searchby" value="'.$_POST["searchby"].'"/>';
                                            }
                                            if (isset($_POST["searchstatus"]) && $_POST["searchstatus"] == "set" && isset($_POST["status"]) && $_POST["status"] != "" && preg_match('/^\d{1,}$/',$_POST["status"]) && $_POST["status"] >= $min_row["sID"] && $_POST["status"] <= $max_row["sID"]) {
                                                echo '<input type="hidden" name="status" value="'.$_POST["status"].'"/>';
                                                echo '<input type="hidden" name="searchstatus" value="set"/>';
                                            }
                                            echo '<input type="hidden" name="page" value="'.$page.'"/>';
                                            echo '<button class="btn btn-primary" type="submit" name="view" value="'.$purchased_row["orderID"].'">View</button>';
                                            echo '</form>';
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                        $count++;
                                        if ($count == $limit) {
                                            break;
                                        }
                                    }
                                ?>
                            </table>
                        </div>
                        <?php
                                    $j = ceil($numrows/$limit);
                                    echo '<div class="d-flex justify-content-center align-items-center">';
                                    if ($page != 1) {
                                        echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                        if (isset($_POST["search"]) && $_POST["search"] != "") {
                                            echo '<input type="hidden" name="search" value="'.$_POST["search"].'"/>';
                                        }
                                        if (isset($_POST["searchby"]) && $_POST["searchby"] != "" && preg_match('/^\d{1}$/',$_POST["searchby"]) && $_POST["searchby"] >= 1 && $_POST["searchby"] <= 5) {
                                            echo '<input type="hidden" name="searchby" value="'.$_POST["searchby"].'"/>';
                                        }
                                        if (isset($_POST["searchstatus"]) && $_POST["searchstatus"] == "set" && isset($_POST["status"]) && $_POST["status"] != "" && preg_match('/^\d{1,}$/',$_POST["status"]) && $_POST["status"] >= $min_row["sID"] && $_POST["status"] <= $max_row["sID"]) {
                                            echo '<input type="hidden" name="status" value="'.$_POST["status"].'"/>';
                                            echo '<input type="hidden" name="searchstatus" value="set"/>';
                                        }
                                        echo '<input type="hidden" name="page" value="'.($page-1).'"/>';
                                        echo '<input type="submit" value="◁"/>';
                                        echo '</form>';
                                    }
                                    echo '<span class="mx-3">'.$page.' / '.$j.'</span>';
                                    if ($page != $j) {
                                        echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                        if (isset($_POST["search"]) && $_POST["search"] != "") {
                                            echo '<input type="hidden" name="search" value="'.$_POST["search"].'"/>';
                                        }
                                        if (isset($_POST["searchby"]) && $_POST["searchby"] != "" && preg_match('/^\d{1}$/',$_POST["searchby"]) && $_POST["searchby"] >= 1 && $_POST["searchby"] <= 5) {
                                            echo '<input type="hidden" name="searchby" value="'.$_POST["searchby"].'"/>';
                                        }
                                        if (isset($_POST["searchstatus"]) && $_POST["searchstatus"] == "set" && isset($_POST["status"]) && $_POST["status"] != "" && preg_match('/^\d{1,}$/',$_POST["status"]) && $_POST["status"] >= $min_row["sID"] && $_POST["status"] <= $max_row["sID"]) {
                                            echo '<input type="hidden" name="status" value="'.$_POST["status"].'"/>';
                                            echo '<input type="hidden" name="searchstatus" value="set"/>';
                                        }
                                        echo '<input type="hidden" name="page" value="'.($page+1).'"/>';
                                        echo '<input type="submit" value="▷"/>';
                                        echo '</form>';
                                    }
                                    echo '</div>';
                                }
                                else {
                                    echo "No Record.";
                                }
                            }
                        ?>
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