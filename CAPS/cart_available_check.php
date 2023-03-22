<?php
    if (!(empty($_SESSION["cart"]))) {
        require_once("WWW/conn_db.php");
        $database = "project";
        $mysqli = new mysqli($host,$user,$password,$database);

        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: ".mysqli_connect_error();
            exit();
        }
        $check_available_cart = true;
        foreach ($_SESSION["cart"] as $product) {
            $check_item_query = 'select * from product where prod_ID='.$product["id"];

            if (($check_item_result = $mysqli->query($check_item_query)) == false) {
                echo 'Invalid query: '.$mysqli->error;
                exit();
            }

            $check_item_row = $check_item_result->fetch_assoc();

            if (!($check_item_row["active"] > 0)) {
                $check_available_cart = false;
            }
        }
    }
?>