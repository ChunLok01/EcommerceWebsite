<?php
    require_once("auth.php");
    require_once("cart_available_check.php");
    if (empty($_SESSION["cart"]) || empty($_SESSION["checkout"]) || empty($_SESSION["checkout"]["info"]) || empty($_SESSION["checkout"]["delivery"]) || $check_available_cart == false) {
        header("location: shipping.php");
        exit();
    }
    else {
        $error = "";
        if (isset($_POST["submit"]) && $_POST["submit"] == "payment") {
            if (!(isset($_POST["method"]) && !(empty($_POST["method"])))) {
                $errmethod = "Please select delivery method.";
                $error .= $errmethod;
            }

            if (empty($error)) {
                if (!($_POST["method"] == "cash" || $_POST["method"] == "card" || $_POST["method"] == "online")) {
                    $errmethod = "Delivery method is incorrect.";
                    $error .= $errmethod;
                }
            }
            
            if (empty($error)) {
                $checkout = array("payment"=>array("method"=>$_POST["method"]));

                $arrkey = array_keys($_SESSION["checkout"]);
                if (in_array("payment",$arrkey)) {
                    $_SESSION["checkout"]["payment"] = $checkout["payment"];
                }
                else {
                    $_SESSION["checkout"] = array_merge($_SESSION["checkout"],$checkout);
                }

                header("location: process_payment.php");
                exit();
            }
        }
    }
?>
<!DOCTYPE html>
<html>

<head>
    <title>Payment</title>
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
        <div class="d-flex">
            <div id="left-content">
                <div id="first">
                    <div id="flow">
                        <p>CART - INFORMATION - SHIPPING - <strong>PAYMENT</strong></p>
                    </div>
                    <h6 id="title" class="my-5">PAYMENT METHOD</h6>
                    <div id="method">
                        <p>Please select your payment method:</p>
                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                            <?php
                                if (isset($_POST["method"]) && !(empty($_POST["method"]))) {
                            ?>
                            <input type="radio" id="cash" name="method" value="cash" onchange="this.form.submit()"<?php echo ($_POST["method"] == "cash") ? " checked" : ""; ?>>
                            <label for="cash">Cash On Delivery</label><br>
                            <input type="radio" id="card" name="method" value="card" onchange="this.form.submit()"<?php echo ($_POST["method"] == "card") ? " checked" : ""; ?>>
                            <label for="card">Debit Card /Credit Card</label><br>
                            <input type="radio" id="online" name="method" value="online" onchange="this.form.submit()"<?php echo ($_POST["method"] == "online") ? " checked" : ""; ?>>
                            <label for="online">Online Banking</label>
                            <?php
                                }
                                else if (!(empty($_SESSION["checkout"]["payment"]))) {
                            ?>
                            <input type="radio" id="cash" name="method" value="cash" onchange="this.form.submit()"<?php echo ($_SESSION["checkout"]["payment"]["method"] == "cash") ? " checked" : ""; ?>>
                            <label for="cash">Cash On Delivery</label><br>
                            <input type="radio" id="card" name="method" value="card" onchange="this.form.submit()"<?php echo ($_SESSION["checkout"]["payment"]["method"] == "card") ? " checked" : ""; ?>>
                            <label for="card">Debit Card /Credit Card</label><br>
                            <input type="radio" id="online" name="method" value="online" onchange="this.form.submit()"<?php echo ($_SESSION["checkout"]["payment"]["method"] == "online") ? " checked" : ""; ?>>
                            <label for="online">Online Banking</label>
                            <?php
                                }
                                else {
                            ?>
                            <input type="radio" id="cash" name="method" value="cash" onchange="this.form.submit()">
                            <label for="cash">Cash On Delivery</label><br>
                            <input type="radio" id="card" name="method" value="card" onchange="this.form.submit()">
                            <label for="card">Debit Card /Credit Card</label><br>
                            <input type="radio" id="online" name="method" value="online" onchange="this.form.submit()">
                            <label for="online">Online Banking</label>
                            <?php
                                }
                            ?>
                        </form>
                    </div>
                    <p class="mb-2 text-danger"><?php echo ((isset($errmethod) && !(empty($errmethod))) ? $errmethod : ""); ?></p>
                    <div id="submit-button" class="mt-3">
                        <div id="return">
                            <button class="btn btn-info rounded btn btn-outline-dark" onclick="document.location='shipping.php'">Return to Shipping</button>
                        </div>
                        <div id="continue">
                            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                                <?php
                                    if (isset($_POST["method"]) && !(empty($_POST["method"]))) {
                                        echo '<input type="hidden" name="method" value="'.$_POST["method"].'"/>';
                                    }
                                    else if (!(empty($_SESSION["checkout"]["payment"]))) {
                                        echo '<input type="hidden" name="method" value="'.$_SESSION["checkout"]["payment"]["method"].'"/>';
                                    }
                                ?>
                                <button id="right" class="btn btn-info rounded btn btn-outline-dark" type="submit" name="submit" value="payment">Complete Order</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="line">
                    <div class="vl"></div>             
                </div>
            </div>
            <div id="right-content">
                <div id="prod_Cart" class="p-0">
                    <div id="prod_Cart_flow" class="border prod_Cart_height">
                        <?php
                            $total = 0.00;
                            foreach ($_SESSION["cart"] as $product) {
                                $total += ($product["price"] * $product["quantity"]);
                        ?>
                        <div class="product_item">
                            <img src="<?php echo $product["image"]; ?>" height="70%" class="img-position-center"/>
                            <span class="prod_name"><?php echo $product["name"]; ?></span>
                            <span class="qty-topright"><?php echo "Qty: ".$product["quantity"]; ?></span>
                            <span class="unitprice"><?php echo "Unit Price : RM ".number_format($product["price"],2); ?></span>
                            <div class="remove">
                                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                                    <input type="hidden" name="productID" value="<?php echo $product["id"]; ?>"/>
                                    <button class="rem_css btn-danger" type="submit" name="action" value="remove">Remove</button>
                                </form>
                            </div>
                        </div>
                        <?php
                            }
                        ?>
                    </div>
                </div>
                <hr>
                <div id="checkout">
                    <div class="d-flex justify-content-between">
                        <span id="subtotal-title">SUBTOTAL</span>
                        <div>
                            <span class="RM">RM</span>
                            <span class="subtotal"><?php echo number_format($total,2); ?></span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span id="shipping-title">SHIPPING</span>
                        <div>
                            <span class="RM">RM</span>
                            <span class="subtotal">10.00</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div id="last-total">
                    <div class="d-flex justify-content-between">
                        <span id="total-title">TOTAL</span>
                        <div>
                            <span class="RM">RM</span>
                            <span class="subtotal">
                                <?php
                                    echo number_format(($total + intval($_SESSION["checkout"]["delivery"]["shipfee"])),2);
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
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