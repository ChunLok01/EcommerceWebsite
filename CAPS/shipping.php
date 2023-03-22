<?php
    require_once("auth.php");
    require_once("cart_available_check.php");
    if (empty($_SESSION["cart"]) || empty($_SESSION["checkout"]) || empty($_SESSION["checkout"]["info"]) || $check_available_cart == false) {
        header("location: information.php");
        exit();
    }
    else {
        $error = "";
        if (isset($_POST["submit"]) && $_POST["submit"] == "delivery") {
            if (!(isset($_POST["delivery"]) && !(empty($_POST["delivery"])) && isset($_POST["shipfee"]) && !(empty($_POST["shipfee"])))) {
                $errdelivery = "Please select delivery method.";
                $error .= $errdelivery;
            }
            if (empty($error)) {
                if (isset($_POST["delivery"]) && !(empty($_POST["delivery"])) && isset($_POST["shipfee"]) && !(empty($_POST["shipfee"]))) {
                    //here is validation, if there is other methods in database, here will validate the method in query

                    // ↓ This is in case of now condition, if got database, will change to mysql query to validate ↓
                    if (!($_POST["delivery"] == "standard" && $_POST["shipfee"] == "10")) {
                        $errdelivery = "Delivery method is incorrect.";
                        $error .= $errdelivery;
                    }
                    // ↑ This is in case of now condition, if got database, will change to mysql query to validate ↑
                }
            }
            if (empty($error)) {
                $checkout = array("delivery"=>array("method"=>$_POST["delivery"],"shipfee"=>$_POST["shipfee"]));

                $arrkey = array_keys($_SESSION["checkout"]);
                if (in_array("delivery",$arrkey)) {
                    $_SESSION["checkout"]["delivery"] = $checkout["delivery"];
                }
                else {
                    $_SESSION["checkout"] = array_merge($_SESSION["checkout"],$checkout);
                }
                
                header("location: payment.php");
                exit();
            }
        }
    }
?>
<!DOCTYPE html>
<html>

<head>
    <title>Shipping</title>
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

        #deli {
            width: 50%;
        }

        #total {
            width: 50%;
            float: right;
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

        .width-size {
            width: 392px;
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
                <div id="first" class="width-size">
                    <div id="flow">
                        <p>CART - INFORMATION - <strong>SHIPPING</strong> - PAYMENT </p>
                    </div>

                    <h6 id="title" class="my-5">SHIPPING METHOD</h6>
                    <div class="d-flex justify-content-between">
                        <div id="deli">
                            <?php
                                if (isset($_POST["delivery"]) && !(empty($_POST["delivery"])) && isset($_POST["shipfee"]) && !(empty($_POST["shipfee"]))) {
                                    echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                    echo '<input type="radio" id="delivery1" name="delivery" value="'.$_POST["delivery"].'" onchange="this.form.submit()"';
                                    if (isset($_POST["shipfee"]) && $_POST["shipfee"] == "10") {
                                        echo ' checked';
                                    }
                                    echo '/>';
                                    echo '<input type="hidden" name="shipfee" value="'.$_POST["shipfee"].'"/>';
                                    echo '<label for="delivery1">Standart Delivery</label>';
                                    echo '</form>';
                                }
                                else if (!(empty($_SESSION["checkout"]["delivery"]))) {
                                    echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                    echo '<input type="radio" id="delivery1" name="delivery" value="'.$_SESSION["checkout"]["delivery"]["method"].'" onchange="this.form.submit()"';
                                    if (!(empty($_SESSION["checkout"]["delivery"])) && $_SESSION["checkout"]["delivery"]["shipfee"] == "10") {
                                        echo ' checked';
                                    }
                                    echo '/>';
                                    echo '<input type="hidden" name="shipfee" value="'.$_SESSION["checkout"]["delivery"]["shipfee"].'"/>';
                                    echo '<label for="delivery1">Standart Delivery</label>';
                                    echo '</form>';
                                }
                                else {
                                    echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                    echo '<input type="radio" id="delivery1" name="delivery" value="standard" onchange="this.form.submit()"/>';
                                    echo '<input type="hidden" name="shipfee" value="10"/>';
                                    echo '<label for="delivery1">Standart Delivery</label>';
                                    echo '</form>';
                                }
                            ?>
                        </div>
                        <div>
                            <span class="RM">RM</span>
                            <span class="subtotal">10.00</span>
                        </div>
                    </div>
                    <p class="mb-2 text-danger"><?php echo ((isset($errdelivery) && !(empty($errdelivery))) ? $errdelivery : ""); ?></p>
                    <div id="submit-button" class="mt-5">
                        <div id="return">
                            <button class="btn btn-info rounded btn btn-outline-dark" onclick="document.location='information.php'" type="button">Return to Information</button>
                        </div>
                        <div id="continue">
                            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                                <?php
                                    if (isset($_POST["delivery"]) && !(empty($_POST["delivery"])) && isset($_POST["shipfee"]) && !(empty($_POST["shipfee"]))) {
                                        echo '<input type="hidden" name="delivery" value="'.$_POST["delivery"].'"/>';
                                        echo '<input type="hidden" name="shipfee" value="'.$_POST["shipfee"].'"/>';
                                    }
                                    else if (!(empty($_SESSION["checkout"]["delivery"]))) {
                                        echo '<input type="hidden" name="delivery" value="'.$_SESSION["checkout"]["delivery"]["method"].'"/>';
                                        echo '<input type="hidden" name="shipfee" value="'.$_SESSION["checkout"]["delivery"]["shipfee"].'"/>';
                                    }
                                ?>
                                <button id="right" class="btn btn-info rounded btn btn-outline-dark" type="submit" name="submit" value="delivery">Continue to Payment</button>
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
                            <span class="subtotal">
                                <?php
                                    if (isset($_POST["delivery"]) && !(empty($_POST["delivery"])) && isset($_POST["shipfee"]) && !(empty($_POST["shipfee"]))) {
                                        echo number_format(intval($_POST["shipfee"]),2);
                                    }
                                    else if (!(empty($_SESSION["checkout"]["delivery"]["method"])) && !(empty($_SESSION["checkout"]["delivery"]["shipfee"]))) {
                                        echo number_format(intval($_SESSION["checkout"]["delivery"]["shipfee"]),2);
                                    }
                                    else {
                                        echo "--.--";
                                    }
                                ?>
                            </span>
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
                                    if (isset($_POST["delivery"]) && !(empty($_POST["delivery"])) && isset($_POST["shipfee"]) && !(empty($_POST["shipfee"]))) {
                                        $total += intval($_POST["shipfee"]);
                                        echo number_format($total,2);
                                    }
                                    else if (!(empty($_SESSION["checkout"]["delivery"]["method"])) && !(empty($_SESSION["checkout"]["delivery"]["shipfee"]))) {
                                        $total += intval($_SESSION["checkout"]["delivery"]["shipfee"]);
                                        echo number_format($total,2);
                                    }
                                    else {
                                        echo "--.--";
                                    }
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