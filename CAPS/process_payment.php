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

    function create_order_function($table, $query, $mysqli) {
        $addQuery = "insert into ".$table." values (".$query.")";
        if (($addResult = $mysqli->query($addQuery)) == false) {
            echo "Invalid query: ".$mysqli->error;
        }
    }
    if (empty($_SESSION["cart"]) || empty($_SESSION["checkout"]) || empty($_SESSION["checkout"]["info"]) || empty($_SESSION["checkout"]["delivery"]) || empty($_SESSION["checkout"]["payment"]) || $check_available_cart == false) {
        header("location: payment.php");
        exit();
    }
    else {
        if (isset($_POST["confirm"]) && $_POST["confirm"] == "payment") {
            $max_order_id = "select MAX(orderID) as maxID from orderlist";
            if (($max_order_id_result = $mysqli->query($max_order_id)) == false) {
                echo 'Invalid query: '.$mysqli->error;
                exit();
            }
            $maxID_row = $max_order_id_result->fetch_assoc();
            $maxID = intval($maxID_row["maxID"]);
            $newID = ($maxID + 1);

            date_default_timezone_set("Asia/Kuala_Lumpur");
            $datecreate = date_create();
            $timestamp = date_timestamp_get($datecreate);
            $orderdatetime = date('Y-m-d H:i:s',$timestamp);
            $timeestimate_timestamp = strtotime("+2 week",$timestamp);
            $estimateshipdate = date('Y-m-d',$timeestimate_timestamp);

            $ordertable = "orderlist";
            $shipaddress = "Name: ".$mysqli->real_escape_string($_SESSION["checkout"]["info"]["fname"])." ".$mysqli->real_escape_string($_SESSION["checkout"]["info"]["lname"])."\n".$mysqli->real_escape_string($_SESSION["checkout"]["info"]["address"])."\n".$mysqli->real_escape_string($_SESSION["checkout"]["info"]["city"])."\n".$mysqli->real_escape_string($_SESSION["checkout"]["info"]["postcode"])."\n".$mysqli->real_escape_string($_SESSION["checkout"]["info"]["state"])."\nPhone: ".$mysqli->real_escape_string($_SESSION["checkout"]["info"]["phone"])."\nEmail: ".$mysqli->real_escape_string($_SESSION["checkout"]["info"]["email"]);
            $postcode = $_SESSION["checkout"]["info"]["postcode"];
            $shipmethod = $_SESSION["checkout"]["delivery"]["method"];
            $shipfee = $_SESSION["checkout"]["delivery"]["shipfee"];
            $paymentmethod = $_SESSION["checkout"]["payment"]["method"];
            $order_query = $newID.",'".$_SESSION["username"]."','".$shipaddress."',".$postcode.",'".$shipmethod."',".$shipfee.",'".$paymentmethod."','".$orderdatetime."','".$estimateshipdate."',1";
            create_order_function($ordertable, $order_query, $mysqli);

            foreach ($_SESSION["cart"] as $product) {
                $prod_id = $product["id"];
                $prod_name = $product["name"];
                $prod_qty = $product["quantity"];
                $prod_listprice = $product["price"];
                $table = "orderitem";
                $query = $newID.",".$prod_id.",'".$prod_name."',".$prod_qty.",".$prod_listprice;
                create_order_function($table, $query, $mysqli);
            }
            $_SESSION["orderID"] = $newID;

            $deletecartquery = "delete from cart where username='".$_SESSION["username"]."'";
            if (($deletecartresult = $mysqli->query($deletecartquery)) == false) {
                echo 'Invalid query: '.$mysqli->error;
            }
            unset($_SESSION["cart"]);

            if (!($_SESSION["checkout"]["payment"]["method"] == "card" || $_SESSION["checkout"]["payment"]["method"] == "online")) {
                unset($_SESSION["checkout"]);
                header("location: view_purchased.php");
            }
            else {
                unset($_SESSION["checkout"]);
                header("Refresh: 5; URL=view_purchased.php");
            }
        }
    }
?>
<!DOCTYPE html>
<html>

<head>
    <title>Proccess Payment</title>
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
       #method{
            border: 1px solid black;
            padding: 15px;
        }
        #left-content {
            width: 50%;
            display: flex;
            padding: 50px;
        }
        
        .vl {
            border-left: 1px solid black;
            height: 100%;
            float: right;
            margin-left: 280px;
        }
        #right-content {
            width: 50%;
            padding: 50px;
        }

        #submit-button {
            display: flex;
        }

        #return {
            width: 50%;
        }

        #continue {
            width: 50%;
        }

        #right {
            float: right;
        }
        .product-details {
            border: 1px solid black;
            padding: 10px;
            height: 150px;
            position: relative;
            margin-bottom: 10px;
        }

        .remove {
            position: absolute;
            right: 10px;
            bottom: 10px;
        }

        #add {
            float: right;
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
        <div class="d-flex justify-content-center align-items-center">
        <?php
            if (isset($_POST["confirm"]) && $_POST["confirm"] == "payment") {
        ?>
        <img src="image/payment/loading.gif"/>
        <?php
            }
            else {
        ?>
        <div class="row mx-0 my-3">
            <div class="row col-12 px-0 mx-0 border" style="width:900px;">
                <div class="row mx-0 border col-12 px-0">
                    <div class="col-6 px-0">
                        <p>Name : <?php echo $_SESSION["checkout"]["info"]["fname"]." ".$_SESSION["checkout"]["info"]["fname"]; ?></p>
                        <div class="d-flex">
                            <p>Address : </p>
                            <p><?php echo $_SESSION["checkout"]["info"]["address"]."<br/>".$_SESSION["checkout"]["info"]["city"]."<br/>".$_SESSION["checkout"]["info"]["postcode"]."<br/>".$_SESSION["checkout"]["info"]["state"]; ?></p>
                        </div>
                        <p>Phone : <?php echo $_SESSION["checkout"]["info"]["phone"]; ?></p>
                        <p>Email : <?php echo $_SESSION["checkout"]["info"]["email"]; ?></p>
                    </div>
                    <div class="col-6 px-0 d-flex justify-content-end">
                        <div>
                            <p>Date : <?php echo date('Y-m-d'); ?></p>
                            <p>Delivery method : <?php echo $_SESSION["checkout"]["delivery"]["method"]; ?></p>
                            <p>Payment method : <?php echo ($_SESSION["checkout"]["payment"]["method"]=="cash") ? "COD" : $_SESSION["checkout"]["payment"]["method"]; ?></p>
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
                        foreach ($_SESSION["cart"] as $prodshow) {
                            $total += ($prodshow["price"] * $prodshow["quantity"]);
                    ?>
                    <div class="row px-0 mx-0 col-12 border-bottom">
                        <div class="col-1 px-0"><?php echo $num; ?></div>
                        <div class="col-6 px-0"><?php echo $prodshow["name"]; ?></div>
                        <div class="col-2 px-0">RM <?php echo number_format($prodshow["price"],2); ?></div>
                        <div class="col-1 px-0">X <?php echo $prodshow["quantity"]; ?></div>
                        <div class="col-2 px-0 d-flex justify-content-end"><?php echo number_format(($prodshow["price"] * $prodshow["quantity"]),2); ?></div>
                    </div>
                    <?php
                            $num++;
                        }
                        $total += intval($_SESSION["checkout"]["delivery"]["shipfee"]);
                    ?>
                    <div class="row px-0 mx-0 col-12 border-bottom">
                        <div class="col-1 px-0"><?php echo $num++; ?></div>
                        <div class="col-6 px-0">Shipping Fee</div>
                        <div class="col-2 px-0">RM <?php echo number_format((intval($_SESSION["checkout"]["delivery"]["shipfee"])),2); ?></div>
                        <div class="col-1 px-0">X 1</div>
                        <div class="col-2 px-0 d-flex justify-content-end"><?php echo number_format(((intval($_SESSION["checkout"]["delivery"]["shipfee"])) * 1),2); ?></div>
                    </div>
                    <div class="row px-0 mx-0 col-12 border-bottom">
                        <div class="col-10 px-0 font-weight-bold">TOTAL</div>
                        <div class="col-2 px-0 font-weight-bold d-flex justify-content-end"><?php echo number_format($total,2); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-12 px-0 d-flex justify-content-between align-items-center">
                <button class="btn btn-dark" onclick="document.location='payment.php'">Back to change details</button>
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <button class="btn btn-success" type="submit" name="confirm" value="payment">Confirm Payment</button>
                </form>
            </div>
        </div>
        <?php
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