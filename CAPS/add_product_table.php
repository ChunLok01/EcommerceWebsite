<?php
    require_once("authentication.php");
    include("WWW/conn_db.php");
    $database = "project";
    $mysqli = new mysqli($host,$user,$password,$database);

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: ".mysqli_connect_error();
        exit();
    }

    $error = "";
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = ((isset($_POST["name"])) && !(empty($_POST["name"]))) ? $_POST["name"] : "";
        $category = ((isset($_POST["category"])) && !(empty($_POST["category"]))) ? $_POST["category"] : "";
        $price = (isset($_POST["price"]) && $_POST["price"] != "") ? $_POST["price"] : "";
        $descr = ((isset($_POST["descr"])) && !(empty($_POST["descr"]))) ? $_POST["descr"] : "";
        $active = (isset($_POST["active"]) && $_POST["active"] != "") ? $_POST["active"] : "";

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
            else if ($_FILES["image"]["error"] == 4) {
                $errimage = '<p class="text-danger">'."Please upload product's image!</p>";
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

        if (empty($error)) {
            if (!(file_exists("image"))) {
                mkdir("image");
            }
            if (!(file_exists("image/product"))) {
                mkdir("image/product");
            }
            

            $checklatestID_query = "select MAX(prod_ID) as ID from product";
            if (($checklatestID_result = $mysqli->query($checklatestID_query)) == false) {
                echo "Invalid query: ".$mysqli->error;
                exit();
            }
            $checklatestID_row = $checklatestID_result->fetch_assoc();

            $newID = strval(intval($checklatestID_row["ID"]) + 1);

            $destination = "image/product/";
            $destination .= $newID.'.'.$extension;
            move_uploaded_file($_FILES["image"]["tmp_name"], $destination);

            $add_product_query = "insert into product values (".$newID.",'".$mysqli->real_escape_string($name)."','".$mysqli->real_escape_string($descr)."','".$mysqli->real_escape_string($destination)."',".$price.",".$category.",".$active.")";
            if (($add_product_result = $mysqli->query($add_product_query)) == false) {
                echo "Invalid query ".$mysqli->error;
                exit();
            }
            else {
                $add_product_flag = true;
                header("Refresh:5; URL=admin_product.php");
            }
        }
    }
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin - Add Product</title>
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
                <h1 class="display-4 text-white">ADD PRODUCT</h1>
                <div class="card">
                    <h5 class="card-header font-weight-light d-flex justify-content-between">New Product:</h5>
                    <div class="card-body">
                        <?php
                            if (isset($add_product_flag) && $add_product_flag == true) {
                                echo "Product is added successfully! Page will redirect to product list page in 5 seconds or ".'<a href="admin_product.php">Click Here</a>';
                            }
                            else {
                        ?>
                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" enctype="multipart/form-data">
                            <table class="table">
                                <tr>
                                    <th><label for="name">Name : </label></th>
                                    <td>
                                        <input id="name" class="form-control" type="text" name="name" value="<?php echo ((isset($name) && !(empty($name))) ? $name : ""); ?>"/>
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
                                        <select id="category" class="form-control" name="category">
                                            <option value="">--Please Select--</option>
                                            <?php
                                                $category_query = "select * from category";
                                                if (($category_result = $mysqli->query($category_query)) == false) {
                                                    echo 'Invalid query: '.$mysqli->error;
                                                }
                                                if ($category_result->num_rows > 0) {
                                                    while ($category_row = $category_result->fetch_assoc()) {
                                                        echo '<option value="'.$category_row["categoryID"].'"'.((isset($category) && !(empty($category)) && $category == $category_row["categoryID"]) ? " selected" : "").'>'.$category_row["categoryID"].' - '.$category_row["category"].'</option>';
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
                                        <input id="price" class="form-control" type="text" name="price" value="<?php echo ((isset($price) && $price != "") ? $price : ""); ?>"/>
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
                                        <input id="image" type="file" name="image"/>
                                        <i>Acceptable image formats: JPEG / PNG, Size must below 1 MB</i>
                                        <?php
                                            if (isset($errimage) && !(empty($errimage))) {
                                                echo $errimage;
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="descr">Product Desciption : </label></th>
                                    <td>
                                        <textarea id="descr" class="form-control" name="descr" rows="7"><?php echo ((isset($descr) && !(empty($descr))) ? $descr : ""); ?></textarea>
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
                                        <select id="active" class="form-control" name="active">
                                            <option value="">--Please Select--</option>
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
                            <div class="d-flex justify-content-between">
                                <div></div>
                                <div>
                                    <button class="btn btn-primary mx-2" type="submit" name="action" value="Add">Add</button>
                                </div>
                            </div>
                        </form>
                        <div>
                            <a href="admin_product.php" class="btn btn-dark">Back</a>
                        </div>
                        <?php
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