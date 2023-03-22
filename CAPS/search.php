<?php
    require_once("session.php");
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
?>
<!DOCTYPE html>
<html>

<head>
    <title>Search Product</title>
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
        .product {
            display: flex;
            margin-top: 20px 0px;
        }
        .item {
            border: 1px solid black;
        }
        .descrHeight {
            height: 200px;
            overflow-y: auto;
        }
        .add {
            width: 50%;
        }
        #email {
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
            <h5 class="text-center">Search Product</h5>
            <div class="w-100 text-center">
                <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                    <input type="text" name="searchProduct" value="<?php echo ((isset($_POST["searchProduct"]) && $_POST["searchProduct"] != "") ? $_POST["searchProduct"] : '' ); ?>" class="border text-dark"/>
                    <input type="submit" name="action" value="Search" class="btn btn-success"/>
                </form>
            </div>
        </div>
        <hr>
        <?php
            $product_query;
            if (isset($_POST["searchProduct"]) && $_POST["searchProduct"] != "" && isset($_POST["action"]) && $_POST["action"] == "Search") {
                $searchProduct = trim($_POST["searchProduct"]);
                $array_search = explode(" ", $searchProduct);

                $product_query = "select * from product p inner join category c on c.categoryID = p.prod_category where p.active=1";
                
                $product_name_array = array();
                $cal = 0;
                foreach ($array_search as $key=>$value) {
                    $category_query = "select * from category where category like '%".$value."%'";
                    if (($category_result = $mysqli->query($category_query)) == false) {
                        echo 'Invalid query: '.$mysqli->error;
                        exit();
                    }
                    if ($category_result->num_rows == 0) {
                        $checkduplicate = strtolower("p.prod_name like '%".$value."%'");
                        if (!(in_array($checkduplicate,$product_name_array))) {
                            $product_name_array[$cal] = $checkduplicate;
                            $cal++;
                        }
                    }
                }

                $product_descr_array = array();
                $cal = 0;
                foreach ($array_search as $key=>$value) {
                    $category_query = "select * from category where category like '%".$value."%'";
                    if (($category_result = $mysqli->query($category_query)) == false) {
                        echo 'Invalid query: '.$mysqli->error;
                        exit();
                    }
                    if ($category_result->num_rows == 0) {
                        $checkduplicate = strtolower("p.prod_descr like '%".$value."%'");
                        if (!(in_array($checkduplicate,$product_descr_array))) {
                            $product_descr_array[$cal] = $checkduplicate;
                            $cal++;
                        }
                    }
                }

                if (count($product_name_array) > 0 || count($product_descr_array) > 0) {
                    $product_query .= " and (";
                    if (count($product_name_array) > 0) {
                        $product_name = "(".join(" and ",$product_name_array).")";
                    }
                    if (count($product_descr_array) > 0) {
                        $product_descr = "(".join(" and ",$product_descr_array).")";
                    }
                    if (count($product_name_array) > 0 && !(count($product_descr_array) > 0)) {
                        $product_query .= $product_name;
                    }
                    else if (!(count($product_name_array) > 0) && count($product_descr_array) > 0) {
                        $product_query .= $product_descr;
                    }
                    else {
                        $product_query .= $product_name." or ".$product_descr;
                    }
                    $product_query .= ")";
                }

                $product_category_array = array();
                $cal = 0;
                foreach ($array_search as $key=>$value) {
                    $category_query = "select * from category where category like '%".$value."%'";
                    if (($category_result = $mysqli->query($category_query)) == false) {
                        echo 'Invalid query: '.$mysqli->error;
                        exit();
                    }
                    if ($category_result->num_rows > 0) {
                        $checkduplicate = strtolower("c.category like '%".$value."%'");
                        if (!(in_array($checkduplicate,$product_category_array))) {
                            $product_category_array[$cal] = $checkduplicate;
                            $cal++;
                        }
                    }
                }
                if (count($product_category_array) > 0) {
                    $product_query .= " and (".join(" or ",$product_category_array).")";
                }
            }
            else {
                $product_query = 'select * from product where active=1';
            }

            $product_result;
            if (($product_result = $mysqli->query($product_query)) == false) {
                echo 'Something went wrong! Server is unavailable!';
                echo '<br>'.$product_query;
                exit();
            }

            if ($product_result->num_rows > 0) {
                $numrows = $product_result->num_rows;
                $limit = 8;
                $start = ($page-1) * $limit;

                mysqli_data_seek($product_result, $start);
                $count = 0;

                if ($product_query == "select * from product where active=1") {
                    echo '<div class="w-100">';
                    echo '<strong>All Product : </strong>'.$numrows.' products.';
                    echo '</div>';
                }
                else {
                    echo '<div class="w-100">';
                    echo '<strong>Search Results : </strong>'.$numrows.' records.';
                    echo '</div>';
                }
                echo '<div class="row mx-0 mt-2">';
                while ($product_row = $product_result->fetch_assoc()) {
                    echo '<div class="col-6 col-md-4 col-lg-3 px-0">';
                    echo '<div class="item m-2 py-2">';
                    echo '<div class="text-center">';
                    echo '<img src="'.$product_row["prod_image"].'" width="100%" height="300">';
                    echo '</div>';
                    echo '<p class="text-center font-weight-bold">'.$product_row["prod_name"].'</p>';
                    echo '<div class="descrHeight my-3">';
                    echo '<p class="p-1">';
                    echo $product_row["prod_descr"];
                    echo '</p>';
                    echo '</div>';
                    echo '<div class="text-center">';
                    echo '<p class="font-weight-bold">Price: RM'.number_format($product_row["prod_price"],2).'</p>';
                    echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                    echo '<input type="hidden" name="productID" value="'.$product_row["prod_ID"].'"/>';
                    echo '<input type="hidden" name="page" value="'.$page.'"/>';
                    if (isset($_POST["searchProduct"]) && !(empty($_POST["searchProduct"]))) {
                        echo '<input type="hidden" name="searchProduct" value="'.$_POST["searchProduct"].'"/>';
                    }
                    if (isset($_POST["action"]) && $_POST["action"] == "Search") {
                        echo '<input type="hidden" name="action" value="'.$_POST["action"].'"/>';
                    }
                    echo '<button class="add btn btn-danger p-2 m-2" type="submit" name="product" value="Add">Add to Cart</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    $count++;
                    if ($count == $limit) {
                        break;
                    }
                }
                echo '</div>';
                $j = ceil($numrows/$limit);

                echo '<div class="d-flex justify-content-center align-items-center">';
                if ($page != 1) {
                    echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                    if (isset($_POST["searchProduct"]) && !(empty($_POST["searchProduct"]))) {
                        echo '<input type="hidden" name="searchProduct" value="'.$_POST["searchProduct"].'"/>';
                    }
                    if (isset($_POST["action"]) && $_POST["action"] == "Search") {
                        echo '<input type="hidden" name="action" value="'.$_POST["action"].'"/>';
                    }
                    echo '<input type="hidden" name="page" value="'.($page-1).'"/>';
                    echo '<input class="text-dark" type="submit" value="◁"/>';
                    echo '</form>';
                }
                echo '<span class="mx-3">'.$page.' / '.$j.'</span>';
                if ($page != $j) {
                    echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                    if (isset($_POST["searchProduct"]) && !(empty($_POST["searchProduct"]))) {
                        echo '<input type="hidden" name="searchProduct" value="'.$_POST["searchProduct"].'"/>';
                    }
                    if (isset($_POST["action"]) && $_POST["action"] == "Search") {
                        echo '<input type="hidden" name="action" value="'.$_POST["action"].'"/>';
                    }
                    echo '<input type="hidden" name="page" value="'.($page+1).'"/>';
                    echo '<input class="text-dark" type="submit" value="▷"/>';
                    echo '</form>';
                }
                echo '</div>';
            }
            else {
                echo '<strong>Search Results : </strong>No record found!';
            }
        ?>
    </div>
    <div id="footer" class="p-5 mt-3 bg-dark">
        <?php require("footer.php"); ?>
    </div>
    <?php
        if ((isset($_SESSION["auth"]) && $_SESSION["auth"] == 1) || (isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1)) {
            require_once("cart.php");
            echo '<script src="js/script.js"></script>';
            if (isset($_SESSION["remove"]) && !(empty($_SESSION["remove"])) && $_SESSION["remove"] == "item") {
                unset($_SESSION["remove"]);
                echo '<script>alert("Please remove unavailable item from cart to checkout.");opencart();</script>';
            }
        }
    ?>
</body>

</html>