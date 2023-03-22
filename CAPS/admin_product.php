<?php
    require_once("authentication.php");
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

    $product_query;
    if (isset($_POST["searchProduct"]) && $_POST["searchProduct"] != NULL) {
        $searchProduct = trim($_POST["searchProduct"]);
        $array_search = explode(" ", $searchProduct);

        $product_query = "select * from product p inner join category c on c.categoryID = p.prod_category where";
        
        $product_name_array = array();
        $cal = 0;
        foreach ($array_search as $key=>$value) {
            $category_query = "select * from category where category like '%".$mysqli->real_escape_string($value)."%'";
            if (($category_result = $mysqli->query($category_query)) == false) {
                echo 'Invalid query: '.$mysqli->error;
                exit();
            }
            if ($category_result->num_rows == 0) {
                $checkduplicate = strtolower("p.prod_name like '%".$mysqli->real_escape_string($value)."%'");
                if (!(in_array($checkduplicate,$product_name_array))) {
                    $product_name_array[$cal] = $checkduplicate;
                    $cal++;
                }
            }
        }

        $product_descr_array = array();
        $cal = 0;
        foreach ($array_search as $key=>$value) {
            $category_query = "select * from category where category like '%".$mysqli->real_escape_string($value)."%'";
            if (($category_result = $mysqli->query($category_query)) == false) {
                echo 'Invalid query: '.$mysqli->error;
                exit();
            }
            if ($category_result->num_rows == 0) {
                $checkduplicate = strtolower("p.prod_descr like '%".$mysqli->real_escape_string($value)."%'");
                if (!(in_array($checkduplicate,$product_descr_array))) {
                    $product_descr_array[$cal] = $checkduplicate;
                    $cal++;
                }
            }
        }

        if (count($product_name_array) > 0 || count($product_descr_array) > 0) {
            $product_query .= " (";
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
            $category_query = "select * from category where category like '%".$mysqli->real_escape_string($value)."%'";
            if (($category_result = $mysqli->query($category_query)) == false) {
                echo 'Invalid query: '.$mysqli->error;
                exit();
            }
            if ($category_result->num_rows > 0) {
                $checkduplicate = strtolower("c.category like '%".$mysqli->real_escape_string($value)."%'");
                if (!(in_array($checkduplicate,$product_category_array))) {
                    $product_category_array[$cal] = $checkduplicate;
                    $cal++;
                }
            }
        }
        if (count($product_category_array) > 0 && (count($product_name_array) > 0 || count($product_descr_array) > 0)) {
            $product_query .= " and (".join(" or ",$product_category_array).")";
        }
        else if (count($product_category_array) > 0 && !(count($product_name_array)) > 0 && !(count($product_descr_array) > 0)) {
            $product_query .= " (".join(" or ",$product_category_array).")";
        }
        
        $id_array = array();
        $cal = 0;
        foreach ($array_search as $key=>$value) {
            if (preg_match('/^\d{1,}$/',$value)) {
                $checkduplicate = "p.prod_ID=".$mysqli->real_escape_string($value);
                if (!(in_array($checkduplicate,$id_array))) {
                    $id_array[$cal] = $checkduplicate;
                    $cal++;
                }
            }
        }
        if (count($id_array) > 0) {
            if (count($product_name_array) > 0 || count($product_descr_array) > 0 || count($product_category_array) > 0) {
                $product_query .= " or ";
            }
            $product_query .= join(" or ",$id_array);
        }
    }
    else {
        $product_query = "select * from product inner join category on product.prod_category=category.categoryID order by prod_ID asc";
    }
    
    $product_result;

    if (($product_result = $mysqli->query($product_query)) == false) {
        echo 'Invalid query: '.$mysqli->error;
        exit();
    }
    $numrows = $product_result->num_rows;
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
                        if (isset($_POST["searchProduct"]) && !(empty($_POST["searchProduct"]))) {
                            echo "SEARCH PRODUCTS";
                        }
                        else {
                            echo "ALL PRODUCTS";
                        }
                    ?>
                </h1>
                <div class="card">
                    <h5 class="card-header font-weight-light d-flex justify-content-between">
                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
                            <input type="text" name="searchProduct" value="<?php echo ((isset($_POST["searchProduct"]) && !(empty($_POST["searchProduct"]))) ? $_POST["searchProduct"] : ""); ?>"/>
                            <input type="submit" name="action" value="Search"/>
                            <?php
                                if (isset($_POST["searchProduct"]) && !(empty($_POST["searchProduct"]))) {
                                    echo "Search Results: ".$numrows." record(s)";
                                }
                                else {
                                    echo $numrows." product(s)";
                                }
                            ?>
                        </form>
                        <a href="add_product_table.php"><input type="button" value="Add Product"/></a>
                    </h5>
                    <div class="card-body">
                        <?php
                            if ($numrows > 0) {
                                $limit = 5;
                                $start = ($page-1) * $limit;

                                echo '<table class="table">';
                                echo '<tr>';
                                echo '<th>Product ID</th>';
                                echo '<th>Category</th>';
                                echo '<th>Image</th>';
                                echo '<th>Name</th>';
                                echo '<th>Price</th>';
                                echo '<th>Action</th>';
                                echo '</tr>';

                                mysqli_data_seek($product_result, $start);
                                $count = 0;

                                while ($product_row = $product_result->fetch_assoc()) {
                                    echo '<tr>';

                                    echo '<td>'.$product_row["prod_ID"].'</td>';
                                    echo '<td>'.$product_row["category"].'</td>';

                                    echo '<td>'.'<img src="'.$product_row["prod_image"].'" width="100px"/>'.'</td>';
                                    echo '<td>'.$product_row["prod_name"].'</td>';
                                    echo '<td>RM '.number_format($product_row["prod_price"],2).'</td>';
                                    echo '<td>';
                                    echo '<div class="d-flex">';
                                    echo '<form action="edit_product_table.php" method="POST">';
                                    if (isset($_POST["searchProduct"])) {
                                        echo '<input type="hidden" name="searchProduct" value="'.$_POST["searchProduct"].'"/>';
                                    }
                                    echo '<input type="hidden" name="page" value="'.$page.'"/>';
                                    echo '<input type="hidden" name="productID" value="'.$product_row["prod_ID"].'"/>';
                                    echo '<input class="btn btn-primary" type="submit" name="action" value="View"/>';
                                    echo '</form>';
                                    echo '</div>';
                                    echo '</td>';

                                    echo '</tr>';

                                    $count++;
                                    if ($count == $limit) {
                                        break;
                                    }
                                }
                                echo '</table>';
                                
                                $j = ceil($numrows/$limit);

                                echo '<div class="d-flex justify-content-center align-items-center">';
                                if ($page != 1) {
                                    echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                    if (isset($_POST["searchProduct"])) {
                                        echo '<input type="hidden" name="searchProduct" value="'.$_POST["searchProduct"].'"/>';
                                    }
                                    echo '<input type="hidden" name="page" value="'.($page-1).'"/>';
                                    echo '<input type="submit" value="◁"/>';
                                    echo '</form>';
                                }
                                echo '<span class="mx-3">'.$page.' / '.$j.'</span>';
                                if ($page != $j) {
                                    echo '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
                                    if (isset($_POST["searchProduct"])) {
                                        echo '<input type="hidden" name="searchProduct" value="'.$_POST["searchProduct"].'"/>';
                                    }
                                    echo '<input type="hidden" name="page" value="'.($page+1).'"/>';
                                    echo '<input type="submit" value="▷"/>';
                                    echo '</form>';
                                }
                                echo '</div>';
                            }
                            else {
                                echo "No records!";
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