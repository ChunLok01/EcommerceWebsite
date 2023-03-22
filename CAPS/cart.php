<?php
    if ((isset($_POST["productID"]) && preg_match('/^\d{1,}$/',$_POST["productID"])) && ((isset($_POST["action"]) && $_POST["action"] == "remove") || (isset($_POST["product"]) && $_POST["product"] == "Add"))) {
        echo '<script>$(document).ready(function () {opencart();});</script>';
    }

    if (!(empty($_SESSION["cart"]))) {
        echo '<script>$(document).ready(function () {$("#cartcount").append('."'".'<span id="cartcountnumber">'.count($_SESSION["cart"]).'</span>'."'".');});</script>';
    }
?>
<div id="masking" class="hidediv">
    <div id="mask" class="hidediv" onclick="closecart()"></div>
    <div id="modelex" class="hidediv">
        <div id="cart">
            <div id="Times">
                <strong id="closeTimes" onclick="closecart()">X</strong>
            </div>
            <div id="cartContent">
                <div id="cartTitle">
                    <span id="headingCart">CART</span>
                    <a href="view_purchased.php" id="viewPurchased">VIEW HISTORY</a>
                </div>
                <div id="prod_Cart">
                    <div id="prod_Cart_flow" class="border">
                        <?php
                            if (isset($_SESSION["cart"]) && !(empty($_SESSION["cart"]))) {
                                $total = 0.00;
                                $checkout_flag = true;
                                foreach ($_SESSION["cart"] as $product) {
                                    $checkitem_query = 'select * from product where prod_ID='.$product["id"];

                                    if (($checkitem_result = $mysqli->query($checkitem_query)) == false) {
                                        echo 'Invalid query: '.$mysqli->error;
                                        exit();
                                    }

                                    $checkitem_row = $checkitem_result->fetch_assoc();

                                    if ($checkitem_row["active"] > 0) {
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
                                    else {
                                        $checkout_flag = false;
                        ?>
                        <div class="product_item">
                            <div class="w-100 position-absolute h-100 bg-dark" style="z-index: 1;opacity: 0.7;"></div>
                            <div class="d-flex justify-content-center align-items-center w-100 h-100 position-relative text-danger" style="z-index: 2;font-size: 35px;">Item Unavailable</div>
                            <img src="<?php echo $product["image"]; ?>" height="70%" class="img-position-center"/>
                            <span class="prod_name"><?php echo $product["name"]; ?></span>
                            <span class="qty-topright"><?php echo "Qty: ".$product["quantity"]; ?></span>
                            <span class="unitprice"><?php echo "Unit Price : RM ".number_format($product["price"],2); ?></span>
                            <div class="remove" style="z-index:2;">
                                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                                    <input type="hidden" name="productID" value="<?php echo $product["id"]; ?>"/>
                                    <button class="rem_css btn-danger" type="submit" name="action" value="remove">Remove</button>
                                </form>
                            </div>
                        </div>
                        <?php
                                    }
                                }
                            }
                            else {
                                echo '<div class="d-flex justify-content-center align-items-center h-100">';
                                echo "Your cart is empty!";
                                echo '</div>';
                            }
                        ?>
                    </div>
                </div>
                <?php
                    if (isset($_SESSION["cart"]) && !(empty($_SESSION["cart"]))) {
                        if ($checkout_flag == false) {
                ?>
                <div id="showcheckout">
                    <div id="showprice" class="d-flex justify-content-between my-3">
                        <span id="showsubtotal-title">SUBTOTAL</span>
                        <div class="d-flex">
                            <span class="price-font-size">RM</span>
                            <span class="price-font-size"><?php echo number_format($total,2); ?></span>
                        </div>
                    </div>
                    <div>
                        <button type="button" style="bottom: 10px; width: 100%; padding: 20px 5px;" class="btn btn-dark text-white" onclick="alert('Please remove unavailable item(s) to checkout!');" readonly>Check Out</button>
                    </div>
                </div>
                <?php
                        }
                        else {
                ?>
                <div id="showcheckout">
                    <div id="showprice" class="d-flex justify-content-between my-3">
                        <span id="showsubtotal-title">SUBTOTAL</span>
                        <div class="d-flex">
                            <span class="price-font-size">RM</span>
                            <span class="price-font-size"><?php echo number_format($total,2); ?></span>
                        </div>
                    </div>
                    <div>
                        <form action="information.php" method="POST">
                            <button type="submit" id="CheckOutButton" class="text-white" name="submit" value="checkout">Check Out</button>
                        </form>
                    </div>
                </div>
                <?php
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</div>