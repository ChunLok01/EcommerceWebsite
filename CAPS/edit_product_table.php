<?php
    require_once("authentication.php");
    include("WWW/conn_db.php");
    $database = "project";
    $mysqli = new mysqli($host,$user,$password,$database);

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: ".mysqli_connect_error();
        exit();
    }

    if (isset($_POST["productID"]) && !(empty($_POST["productID"])) && preg_match('/^\d{1,}$/',$_POST["productID"]) && isset($_POST["action"]) && ($_POST["action"] == "View" || $_POST["action"] == "Edit" || $_POST["action"] == "Done")) {
        $page;
        if (isset($_POST["page"])) {
            $page = $_POST["page"];
        }
        else {
            $page = 1;
        }
        $prod_ID = $_POST["productID"];
    }
    else {
        header("location: admin_product.php");
        exit();
    }
    $error = "";

    $name = (isset($_POST["name"]) && !(empty($_POST["name"]))) ? $_POST["name"] : "";
    $category = (isset($_POST["category"]) && !(empty($_POST["category"]))) ? $_POST["category"] : "";
    $price = (isset($_POST["price"]) && $_POST["price"] != "") ? $_POST["price"] : "";
    $image = (isset($_POST["ori-image"]) && !(empty($_POST["ori-image"]))) ? $_POST["ori-image"] : "";
    $descr = (isset($_POST["descr"]) && !(empty($_POST["descr"]))) ? $_POST["descr"] : "";
    $active = (isset($_POST["active"]) && $_POST["active"] != "") ? $_POST["active"] : "";
    
    if (isset($_POST["action"]) && $_POST["action"] == "Done") {
        if (empty($name)) {
            $errname = '<p class="text-danger">Name must be filled in!</p>';
            $error .= $errname;
        }
        $categorymaxid_query = "select MAX(categoryID) as cID from category";
        if (($categorymaxid_result = $mysqli->query($categorymaxid_query)) == false) {
            echo "Invalid query ".$mysqli->error;
            exit();
        }
        $categorymaxid_row = $categorymaxid_result->fetch_assoc();
        if (empty($category) || !($category > 0 && $category <= $categorymaxid_row["cID"])) {
            $errcategory = '<p class="text-danger">Category must be selected!</p>';
            $error .= $errcategory;
        }
        if ($price == "") {
            $errprice = '<p class="text-danger">Price must be filled in!</p>';
            $error .= $errprice;
        }
        else if (!(preg_match('/^\d{1,}([.]\d{1,2}){0,1}$/',$price) && $price != "")) {
            $errprice = '<p class="text-danger">Invalid Input! Price must be in number (Example: 10, 10.50)</p>';
            $error .= $errprice;
        }
        
        if (isset($_FILES["image"])) {
            if ($_FILES["image"]["error"] > 0 && $_FILES["image"]["error"] != 4) {
                $errimage = '<p class="text-danger">Error: '.$_FILES["image"]["error"].'</p>';
                $error .= $errimage;
            }
            else if ($_FILES["image"]["error"] == 0) {
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                $filename = basename($_FILES["image"]["name"]);
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $imageFileType = strtolower($extension);
                if ($check == false) {
                    $errimage = '<p class="text-danger">Your file is not an image! Please upload again</p>';
                    $error .= $errimage;
                }
                else if (!($_FILES["image"]["size"] < 1048576)) {
                    $errimage = '<p class="text-danger">Your file is too large! Please upload again (must be in 1 MB)</p>';
                    $error .= $errimage;
                }
                else if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                    $errimage = '<p class="text-danger">Your file type is invalid! Please upload again.</p>';
                    $error .= $errimage;
                }
            }
        }

        if (empty($descr)) {
            $errdescr = '<p class="text-danger">Product description must be filled in!</p>';
            $error .= $errdescr;
        }

        if (!($active != "" && ($active == "0" || $active == "1"))) {
            $erractive = '<p class="text-danger">'."Please select product's status</p>";
            $error .= $erractive;
        }

        if (!(empty($error))) {
            $_POST["action"] = "Edit";
        }
    }
    if (empty($error) && isset($_POST["action"]) && $_POST["action"] == "Done") {
        $update_query = "update product set prod_name='".$mysqli->real_escape_string($name)."',prod_descr='".$mysqli->real_escape_string($descr)."',";
        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
            if (!(file_exists("image"))) {
                mkdir("image");
            }
            if (!(file_exists("image/product"))) {
                mkdir("image/product");
            }

            $old_destination = "";
            $des_query = 'select prod_image from product where prod_ID='.$prod_ID;
            if (($des_result = $mysqli->query($des_query)) == false) {
                echo "Invalid query ".$mysqli->error;
                exit();
            }
            $des_row = $des_result->fetch_assoc();
            if (!($des_row["prod_image"] === NULL)) {
                $old_destination .= $des_row["prod_image"];
            }
            if ($old_destination != "" && file_exists($old_destination)) {
                if (!(unlink($old_destination))) {
                    echo 'Failed to delete file on the server!';
                    exit();
                }
            }

            $destination = 'image/product/';
            $destination .= $prod_ID.'.'.$extension;
            move_uploaded_file($_FILES["image"]["tmp_name"], $destination);

            $update_query .= "prod_image='".$mysqli->real_escape_string($destination)."',";
        }
        $update_query .= "prod_price=".$price.",prod_category=".$category.",active=".$active;
        $update_query .= " where prod_ID=".$prod_ID;

        if (($update_result = $mysqli->query($update_query)) == false) {
            echo 'Invalid query: '.$mysqli->error.'<br>';
            echo $update_query;
            exit();
        }

        $_POST["action"] = "View";
    }
    if (isset($_POST["action"]) && $_POST["action"] == "View") {
        $product_query = "select * from product where prod_ID=".$prod_ID;

        if (($product_result = $mysqli->query($product_query)) == false) {
            echo 'Invalid query: '.$mysqli->error;
            exit();
        }
        $product_row = $product_result->fetch_assoc();

        $name = $product_row["prod_name"];
        $category = $product_row["prod_category"];
        $price = $product_row["prod_price"];
        $image = $product_row["prod_image"];
        $descr = $product_row["prod_descr"];
        $active = $product_row["active"];
    }
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin - Edit Product</title>
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
                <h1 class="display-4 text-white">EDIT PRODUCT</h1>
                <div class="card">
                    <h5 class="card-header font-weight-light d-flex justify-content-between">Product ID: <?php echo $prod_ID; ?></h5>
                    <div class="card-body">
                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" enctype="multipart/form-data">
                            <table class="table">
                                <tr>
                                    <th><label for="id">Product ID : </label></th>
                                    <td>
                                        <input id="id" class="form-control" type="text" name="productID" value="<?php echo $prod_ID; ?>" readonly/>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="name">Name : </label></th>
                                    <td>
                                        <input id="name" class="form-control" type="text" name="name" value="<?php echo $name; ?>"<?php echo (isset($_POST["action"]) && $_POST["action"] == "View") ? " readonly" : ""; ?>/>
                                        <?php
                                            if (isset($errname) && !(empty($errname))) {
                                                echo $errname;
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="category">Category : </label></th>
                                    <td>
                                        <select id="category" class="form-control" name="category"<?php echo (isset($_POST["action"]) && $_POST["action"] == "View") ? " readonly" : ""; ?>>
                                            <?php
                                                $category_query = "select * from category";
                                                if (($category_result = $mysqli->query($category_query)) == false) {
                                                    echo 'Invalid query: '.$mysqli->error;
                                                }
                                                if ($category_result->num_rows > 0) {
                                                    while ($category_row = $category_result->fetch_assoc()) {
                                                        echo '<option value="'.$category_row["categoryID"].'"'.(($category == $category_row["categoryID"]) ? " selected" : "").'>'.$category_row["categoryID"].' - '.$category_row["category"].'</option>';
                                                    }
                                                }
                                                else{
                                                    echo '<option value="">--Server Unavailable!--</option>';
                                                }
                                            ?>
                                        </select>
                                        <?php
                                            if (isset($errcategory) && !(empty($errcategory))) {
                                                echo $errcategory;
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="price">Price : RM</label></th>
                                    <td>
                                        <input id="price" class="form-control" type="text" name="price" value="<?php echo $price; ?>"<?php echo (isset($_POST["action"]) && $_POST["action"] == "View") ? " readonly" : ""; ?>/>
                                        <?php
                                            if (isset($errprice) && !(empty($errprice))) {
                                                echo $errprice;
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="image">Image : </label></th>
                                    <td>
                                        <?php
                                            if (isset($_POST["action"]) && $_POST["action"] == "View") {
                                                echo '<img src="'.$image.'" width="300px"/>';
                                            }
                                            else {
                                        ?>
                                        <div class="w-100">If you want to change the image of the product, please upload new image!</div>
                                        <input id="image" type="file" name="image"/>
                                        <i>Acceptable image formats: JPEG / PNG, Size must below 1 MB</i>
                                        <?php
                                                if (isset($errimage) && !(empty($errimage))) {
                                                    echo $errimage;
                                                }
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="descr">Product Desciption : </label></th>
                                    <td>
                                        <textarea id="descr" class="form-control" name="descr" rows="7"<?php echo (isset($_POST["action"]) && $_POST["action"] == "View") ? " readonly" : ""; ?>><?php echo $descr; ?></textarea>
                                        <?php
                                            if (isset($errdescr) && !(empty($errdescr))) {
                                                echo $errdescr;
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="active">Product Status : </label></th>
                                    <td>
                                        <select id="active" class="form-control" name="active"<?php echo (isset($_POST["action"]) && $_POST["action"] == "View") ? " readonly" : ""; ?>>
                                            <option value="0"<?php echo ((isset($active) && $active != "" && $active === "0") ? " selected" : ""); ?>>0 - Unavailable</option>
                                            <option value="1"<?php echo ((isset($active) && $active != "" && $active === "1") ? " selected" : ""); ?>>1 - Active</option>
                                        </select>
                                        <?php
                                            if (isset($erractive) && !(empty($erractive))) {
                                                echo $erractive;
                                            }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                            <input type="hidden" name="page" value="<?php echo $page; ?>"/>
                            <?php
                                if (isset($_POST["searchProduct"]) && !(empty($_POST["searcProduct"]))) {
                                    echo '<input type="hidden" name="searcProduct" value="'.$_POST["searcProduct"].'"/>';
                                }
                            ?>
                            <div class="d-flex justify-content-between">
                                <div></div>
                                <div>
                                    <?php
                                        if (isset($_POST["action"]) && $_POST["action"] == "Edit") {
                                    ?>
                                    <button class="btn btn-primary mx-2" type="submit" name="action" value="Done">Done</button>
                                    <?php
                                        }
                                        else {
                                    ?>
                                    <button class="btn btn-primary mx-2" type="submit" name="action" value="Edit">Edit</button>
                                    <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </form>
                        <div>
                            <form action="admin_product.php" method="POST">
                                <input type="hidden" name="page" value="<?php echo $page; ?>"/>
                                <?php
                                    if (isset($_POST["searchProduct"]) && !(empty($_POST["searcProduct"]))) {
                                        echo '<input type="hidden" name="searcProduct" value="'.$_POST["searcProduct"].'"/>';
                                    }
                                ?>
                                <button class="btn btn-dark" type="submit" name="action" value="Back">Back</button>
                            </form>
                        </div>
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