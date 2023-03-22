<?php
    require_once("WWW/conn_db.php");
    $database = "project";
    $mysqli = new mysqli($host,$user,$password,$database);

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: ".mysqli_connect_error();
        exit();
    }
    
    $reloadcart_query = "select * from cart where username='".$_SESSION["username"]."'";

    if (($reloadcart_result = $mysqli->query($reloadcart_query)) == false) {
        echo 'Invalid query: '.$mysqli->error;
    }
    else {
        if ($reloadcart_result->num_rows > 0) {
            $cart_row = $reloadcart_result->fetch_assoc();
            $cart = unserialize($cart_row["cart"]);
            $_SESSION["cart"] = $cart;
        }
        else {
            unset($_SESSION["cart"]);
        }
    }
    
    if (isset($_POST["productID"]) && preg_match('/^\d{1,}$/',$_POST["productID"]) && isset($_POST["product"]) && $_POST["product"] == "Add") {
        $p_id = $_POST["productID"];
        $add_query = 'select * from product where active=1 and prod_ID='.$p_id;

        if (($add_result = $mysqli->query($add_query)) == false) {
            echo 'Invalid query: '.$mysqli->error;
            exit();
        }

        if ($add_result->num_rows > 0) {
            $add_row = $add_result->fetch_assoc();
            $name = $add_row['prod_name'];
            $product_id = "P".$add_row['prod_ID'];
            $price = $add_row['prod_price'];
            $image = $add_row['prod_image'];
            
            $cartArray = array(
                $product_id=>array(
                'name'=>$name,
                'id'=>substr($product_id,1),
                'price'=>$price,
                'quantity'=>1,
                'image'=>$image)
                );
            if(empty($_SESSION["cart"])) {
                $_SESSION["cart"] = $cartArray;
            }
            else {
                $array_keys = array_keys($_SESSION["cart"]);
                
                if(in_array($product_id,$array_keys)) {
                    $_SESSION["cart"][$product_id]["quantity"] += 1;
                }
                else {
                    $_SESSION["cart"] = array_merge($_SESSION["cart"],$cartArray);
                }
            }
            $checkcart_query = "select * from cart where username='".$_SESSION["username"]."'";
            if (($checkcart_result = $mysqli->query($checkcart_query)) == false) {
                echo 'Invalid query: '.$mysqli->error;
            }

            $cart = serialize($_SESSION["cart"]);
            if ($checkcart_result->num_rows > 0) {
                $updatecart_query = "update cart set cart='".$cart."' where username='".$_SESSION["username"]."'";
                if (($updatecart_result = $mysqli->query($updatecart_query)) == false) {
                    echo 'Invalid query: '.$mysqli->error;
                }
            }
            else {
                $insertcart_query = "insert into cart values (null,'".$_SESSION["username"]."','".$cart."')";
                if (($insetcart_result = $mysqli->query($insertcart_query)) == false) {
                    echo 'Invalid query: '.$mysqli->error;
                }
            }
        }
    }

    if ((isset($_POST["action"]) && $_POST["action"] == "remove") && isset($_POST["productID"]) && preg_match('/^\d{1,}$/',$_POST["productID"])) {
        if (!(empty($_SESSION["cart"]))) {
            $product_id = "P".$_POST["productID"];
            $array_keys = array_keys($_SESSION["cart"]);
            if (in_array($product_id,$array_keys)) {
                unset($_SESSION["cart"][$product_id]);
            }
            if (empty($_SESSION["cart"])) {
                $deletecart_query = "delete from cart where username='".$_SESSION["username"]."'";

                if (($deletecart_result = $mysqli->query($deletecart_query)) == false) {
                    echo 'Invalid query: '.$mysqli->error;
                }
                unset($_SESSION["cart"]);
            }
            else {
                $checkcart_query = "select * from cart where username='".$_SESSION["username"]."'";
                if (($checkcart_result = $mysqli->query($checkcart_query)) == false) {
                    echo 'Invalid query: '.$mysqli->error;
                }
    
                $cart = serialize($_SESSION["cart"]);
                if ($checkcart_result->num_rows > 0) {
                    $updatecart_query = "update cart set cart='".$cart."' where username='".$_SESSION["username"]."'";
                    if (($updatecart_result = $mysqli->query($updatecart_query)) == false) {
                        echo 'Invalid query: '.$mysqli->error;
                    }
                }
                else {
                    $insertcart_query = "insert into cart values (null,'".$_SESSION["username"]."','".$cart."')";
                    if (($insetcart_result = $mysqli->query($insertcart_query)) == false) {
                        echo 'Invalid query: '.$mysqli->error;
                    }
                }
            }
        }
    }
    
    $reloadcart_query = "select * from cart where username='".$_SESSION["username"]."'";

    if (($reloadcart_result = $mysqli->query($reloadcart_query)) == false) {
        echo 'Invalid query: '.$mysqli->error;
    }
    else {
        if ($reloadcart_result->num_rows > 0) {
            $cart_row = $reloadcart_result->fetch_assoc();
            $cart = unserialize($cart_row["cart"]);
            $_SESSION["cart"] = $cart;
        }
        else {
            unset($_SESSION["cart"]);
        }
    }
?>