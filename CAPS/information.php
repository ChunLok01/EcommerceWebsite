<?php
    require_once("auth.php");
    require_once("cart_available_check.php");
    if (!(isset($_SESSION["cart"]) && !(empty($_SESSION["cart"])))) {
        header("Refresh:1; URL=search.php");
        echo '<script>alert("Please add cart before check out.");</script>';
        exit();
    }
    else if ($check_available_cart == false) {
        $_SESSION["remove"] = "item";
        header("location: search.php");
        exit();
    }
    else {
        $error = "";
        if (!(empty($_SESSION["cart"]))) {
            if (isset($_POST["action"]) && $_POST["action"] == "getinfo") {
                require_once("WWW/conn_db.php");
                $database = "project";
                $mysqli = new mysqli($host,$user,$password,$database);
    
                if (mysqli_connect_errno()) {
                    echo "Failed to connect to MySQL: ".mysqli_connect_error();
                    exit();
                }
    
                $getinfo_query = "select * from ";
                if (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1) {
                    $getinfo_query .= "adminAccount";
                    $account = "admin";
                }
                else {
                    $getinfo_query .= "userAccount";
                    $account = "user";
                }
                $getinfo_query .= " where username='".$_SESSION["username"]."'";
                if (($getinfo_result = $mysqli->query($getinfo_query)) == false) {
                    echo 'Invalid query: '.$mysqli->error;
                }
    
                $getinfo_row = $getinfo_result->fetch_assoc();
    
                $fname = ($getinfo_row[($account."Fname")] != NULL) ? $getinfo_row[($account."Fname")] : "";
                $lname = ($getinfo_row[($account."Lname")] != NULL) ? $getinfo_row[($account."Lname")] : "";
                $address = ($getinfo_row["addressStreet"] != NULL) ? $getinfo_row["addressStreet"] : "";
                $postcode = ($getinfo_row["postcode"] != NULL) ? $getinfo_row["postcode"] : "";
                $city = ($getinfo_row["city"] != NULL) ? $getinfo_row["city"] : "";
                $state = ($getinfo_row["states"] != NULL) ? $getinfo_row["states"] : "";
                $phone = ($getinfo_row[($account."Phone")] != NULL) ? $getinfo_row[($account."Phone")] : "";
                $email = ($getinfo_row[($account."Email")] != NULL) ? $getinfo_row[($account."Email")] : "";
            }
            else {
                $fname = (isset($_POST["fname"]) && !(empty($_POST["fname"]))) ? $_POST["fname"] : ((!(empty($_SESSION["checkout"]))) ? $_SESSION["checkout"]["info"]["fname"] : "");
                $lname = (isset($_POST["lname"]) && !(empty($_POST["lname"]))) ? $_POST["lname"] : ((!(empty($_SESSION["checkout"]))) ? $_SESSION["checkout"]["info"]["lname"] : "");
                $address = (isset($_POST["address"]) && !(empty($_POST["address"]))) ? $_POST["address"] : ((!(empty($_SESSION["checkout"]))) ? $_SESSION["checkout"]["info"]["address"] : "");
                $postcode = (isset($_POST["postcode"]) && !(empty($_POST["postcode"]))) ? $_POST["postcode"] : ((!(empty($_SESSION["checkout"]))) ? $_SESSION["checkout"]["info"]["postcode"] : "");
                $city = (isset($_POST["city"]) && !(empty($_POST["city"]))) ? $_POST["city"] : ((!(empty($_SESSION["checkout"]))) ? $_SESSION["checkout"]["info"]["city"] : "");
                $state = (isset($_POST["state"]) && !(empty($_POST["state"]))) ? $_POST["state"] : ((!(empty($_SESSION["checkout"]))) ? $_SESSION["checkout"]["info"]["state"] : "");
                $phone = (isset($_POST["phone"]) && !(empty($_POST["phone"]))) ? $_POST["phone"] : ((!(empty($_SESSION["checkout"]))) ? $_SESSION["checkout"]["info"]["phone"] : "");
                $email = (isset($_POST["email"]) && !(empty($_POST["email"]))) ? $_POST["email"] : ((!(empty($_SESSION["checkout"]))) ? $_SESSION["checkout"]["info"]["email"] : "");
            }
            
            if (isset($_POST["submit"]) && $_POST["submit"] == "info") {
                if (empty($fname)) {
                    $errfname = "Please fill in first name.";
                    $error .= $errfname;
                }
                if (empty($lname)) {
                    $errlname = "Please fill in last name.";
                    $error .= $errlname;
                }
                if (empty($address)) {
                    $erraddress = "Please fill in address.";
                    $error .= $erraddress;
                }
                if (empty($postcode)) {
                    $errpc = "Please fill in post code.";
                    $error .= $errpc;
                }
                else if (!(preg_match('/\d{5}/',trim($postcode)))) {
                    $errpc = "Please fill in post code.";
                    $error .= $errpc;
                }
                if (empty($city)) {
                    $errcity = "Please fill in city.";
                    $error .= $errcity;
                }
                if (empty($state)) {
                    $errstate = "Please fill in state.";
                    $error .= $errstate;
                }
                if (empty($phone)) {
                    $errphone = "Please fill in phone number.";
                    $error .= $errphone;
                }
                else if (!(preg_match('/^01[0-9]-[1-9][0-9]{6,7}$/', trim($phone)) || preg_match('/^0[1-9]{1,2}-[1-9][0-9]{6,7}$/', trim($phone)))) {
                    $errphone = "Invalid phone number. Phone number example: 012-3456789, 03-90979097";
                    $error .= $errphone;
                }
                if (empty($email)) {
                    $erremail = "Please fill in email.";
                    $error .= $erremail;
                }
                else if (!(preg_match('/^([a-zA-Z0-9]{1,}[.]{0,}){1,}[a-zA-Z0-9]@([a-zA-Z]{1,}[.]{1}){1,}[a-zA-Z]{2,}$/', trim($email)))) {
                    $erremail = "Invalid email.";
                    $error .= $erremail;
                }
    
                if (empty($error)) {
                    $checkout = array("info"=>array("fname"=>$fname,"lname"=>$lname,"address"=>$address,"city"=>$city,"postcode"=>$postcode,"state"=>$state,"phone"=>$phone,"email"=>$email));
                    
                    if (empty($_SESSION["checkout"])) {
                        $_SESSION["checkout"] = $checkout;
                    }
                    else {
                        $arrkey = array_keys($_SESSION["checkout"]);
                        if (in_array("info",$arrkey)) {
                            $_SESSION["checkout"]["info"] = $checkout["info"];
                        }
                        else {
                            $_SESSION["checkout"] = array_merge($_SESSION["checkout"],$checkout);
                        }
                    }
                    
                    header("location: shipping.php");
                    exit();
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html>

<head>
    <title>Information</title>
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

        #address2 {
            display: flex;
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

        #method {
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
                        <p>CART - <strong>INFORMATION</strong> - SHIPPING - PAYMENT </p>
                    </div>
                    <h6 id="title" class="my-5">PERSONAL INFORMATION</h6>
                    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                        <button type="submit" name="action" value="getinfo">Auto fill info from my account</button>
                    </form>
                    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                        <input type="text" name="fname" placeholder="First Name" size="50" value="<?php echo $fname; ?>">
                        <p class="mb-2 text-danger"><?php echo ((isset($errfname) && !(empty($errfname))) ? $errfname : ""); ?></p>
                        <input type="text" name="lname" placeholder="Last Name" size="50" value="<?php echo $lname; ?>">
                        <p class="mb-2 text-danger"><?php echo ((isset($errlname) && !(empty($errlname))) ? $errlname : ""); ?></p>
                        <div id="address">
                            <input type="text" name="address" placeholder="Address" size="50" value="<?php echo $address; ?>">
                            <p class="mb-2 text-danger"><?php echo ((isset($erraddress) && !(empty($erraddress))) ? $erraddress : ""); ?></p>
                        </div>
                        <input type="text" name="city" placeholder="City" size="50" value="<?php echo $city; ?>">
                        <p class="mb-2 text-danger"><?php echo ((isset($errcity) && !(empty($errcity))) ? $errcity : ""); ?></p>
                        <input type="text" name="postcode" placeholder="Poscode" size="50" value="<?php echo $postcode; ?>">
                        <p class="mb-2 text-danger"><?php echo ((isset($errpc) && !(empty($errpc))) ? $errpc : ""); ?></p>
                        <div id="address2">
                            <div id="state">
                                <input type="text" name="state" placeholder="State" size="50" value="<?php echo $state; ?>">
                                <p class="mb-2 text-danger"><?php echo ((isset($errstate) && !(empty($errstate))) ? $errstate : ""); ?></p>
                            </div>
                        </div>
                        <input type="text" name="phone" placeholder="Phone Number" size="50" value="<?php echo $phone; ?>">
                        <p class="mb-2 text-danger"><?php echo ((isset($errphone) && !(empty($errphone))) ? $errphone : ""); ?></p>
                        <input type="text" name="email" placeholder="Email Address" size="50" value="<?php echo $email; ?>">
                        <p class="mb-2 text-danger"><?php echo ((isset($erremail) && !(empty($erremail))) ? $erremail : ""); ?></p>
                        <div id="submit-button" class="mt-3">
                            <button id="right" class="btn btn-info rounded btn btn-outline-dark" type="submit" name="submit" value="info">Continue to Shipping</button>
                        </div>
                    </form>
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
                            <span class="subtotal">--.--</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div id="last-total">
                    <div class="d-flex justify-content-between">
                        <span id="total-title">TOTAL</span>
                        <div>
                            <span class="RM">RM</span>
                            <span class="subtotal">--.--</span>
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