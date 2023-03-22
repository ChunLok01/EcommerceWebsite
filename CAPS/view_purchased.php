<?php
    require_once("auth.php");
    require_once("cart_available_check.php");
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

    if (isset($_SESSION["orderID"]) && !(empty($_SESSION["orderID"]))) {
        $_POST["view"] = $_SESSION["orderID"];
        unset($_SESSION["orderID"]);
    }

    if (isset($_POST["view"]) && !(empty($_POST["view"]))) {
        if (preg_match('/^\d{1,}$/',$_POST["view"])) {
            $checkorder = select_function($mysqli, "ol.*,os.statusdetails", "orderlist ol inner join orderstatuslist os on ol.statusID=os.statusID", "where ol.username='".$_SESSION["username"]."' and ol.orderID=".$_POST["view"]);
            if ($checkorder->num_rows > 0) {
                $orderlist = $checkorder->fetch_assoc();
                if ($orderlist["statusID"] == 2 && isset($_POST["action"]) && $_POST["action"] == "3") {
                    $table = "orderlist";
                    $query = "statusID=3 where orderID=".$orderlist["orderID"];
                    update_function($mysqli, $table, $query);
                }
                else if ($orderlist["statusID"] == 1 && isset($_POST["action"]) && $_POST["action"] == "0") {
                    $table = "orderlist";
                    $query = "statusID=0 where orderID=".$orderlist["orderID"];
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
    <title>View Purchase Page</title>
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
        #header1 {
            margin-top: 30px;
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
        <div id="header1">
            <h5 class="text-center">Purchased History</h5>
        </div>
        <hr>
        <div class="d-flex justify-content-center align-items-center flex-column">
            <?php
                if (isset($_POST["view"]) && !(empty($_POST["view"]))) {
                    $order_list = select_function($mysqli, "ol.*,os.statusdetails", "orderlist ol inner join orderstatuslist os on ol.statusID=os.statusID", "where ol.username='".$_SESSION["username"]."' and ol.orderID=".$_POST["view"]);
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
                                <p>Order ID : <?php echo $orderlist["orderID"]; ?></p>
                                <p>Order/Payment Time : <?php echo $orderlist["orderdate"]; ?></p>
                                <p>Delivery method : <?php echo $orderlist["shipmethod"]; ?></p>
                                <p>Payment method : <?php echo ($orderlist["paymentmethod"]=="cash") ? "COD" : $orderlist["paymentmethod"]; ?></p>
                                <p>Status : <?php echo $orderlist["statusdetails"]; ?></p>
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
                            //foreach ($_SESSION["cart"] as $prodshow) {
                                //$total += ($prodshow["price"] * $prodshow["quantity"]);
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
                        <button class="btn btn-dark" type="submit" name="page" value="<?php echo $page; ?>">Back</button>
                    </form>
                    <?php
                        if ($orderlist["statusID"] == 1) {
                    ?>
                    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                        <input type="hidden" name="view" value="<?php echo $_POST["view"]; ?>"/>
                        <button class="btn btn-danger" type="submit" name="action" value="0">Cancel Order</button>
                    </form>
                    <?php
                        }
                    ?>
                    <?php
                        if ($orderlist["statusID"] == 2) {
                    ?>
                    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                        <input type="hidden" name="view" value="<?php echo $_POST["view"]; ?>"/>
                        <button class="btn btn-success" type="submit" name="action" value="3">Received Order</button>
                    </form>
                    <?php
                        }
                    ?>
                </div>
            </div>
            <?php
                }
                else {
                    $purchased = select_function($mysqli, "*", "orderlist ol inner join orderstatuslist os on ol.statusID=os.statusID", "where ol.username='".$_SESSION["username"]."' order by ol.orderID desc");
                    $numrows = $purchased->num_rows;
                    if ($numrows > 0) {
                        $limit = 10;
                        $start = ($page-1) * $limit;
                        mysqli_data_seek($purchased,$start);
                        $count = 0;

                        $num = ($start+1);
            ?>
            <div class="border my-3">
                <table class="table">
                    <tr>
                        <th class="text-center">NO</th>
                        <th class="text-center">Order ID</th>
                        <th class="text-center">Total Price (RM)</th>
                        <th class="text-center">Order Date</th>
                        <th class="text-center">Estimate Ship Date</th>
                        <th class="text-center">Status</th>
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
                        <td class="text-center"><?php echo $num++; ?></td>
                        <td class="text-center"><?php echo $purchased_row["orderID"]; ?></td>
                        <td class="text-center">RM <?php echo number_format($total,2); ?></td>
                        <td class="text-center"><?php echo $purchased_row["orderdate"]; ?></td>
                        <td class="text-center"><?php echo $purchased_row["shipdate"]; ?></td>
                        <td class="text-center"><?php echo $purchased_row["statusdetails"]; ?></td>
                        <td class="text-center">
                            <?php
                                echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
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
                            echo '<input type="hidden" name="page" value="'.($page-1).'"/>';
                            echo '<input type="submit" value="◁"/>';
                            echo '</form>';
                        }
                        echo '<span class="mx-3">'.$page.' / '.$j.'</span>';
                        if ($page != $j) {
                            echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                            echo '<input type="hidden" name="page" value="'.($page+1).'"/>';
                            echo '<input type="submit" value="▷"/>';
                            echo '</form>';
                        }
                        echo '</div>';
                    }
                    else {
                        echo "No Purchased History.";
                    }
                }
            ?>
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