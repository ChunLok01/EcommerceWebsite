<?php
$error = "";
if (isset($_POST["Edit"]) && $_POST["Edit"] == "Done") {
    $check_mutiply_username_query = "select * from adminAccount where username='".$_POST["username"]."' and NOT adminID=".$adminId;
}
else {
    $check_mutiply_username_query = "select * from adminAccount where username='".$_POST["username"]."'";
}
$check_mutiply_username_query1 = "select * from userAccount where username='".$_POST["username"]."'";
$check_result;
$check_result1;
if (($check_result = $mysqli->query($check_mutiply_username_query)) == false) {
    echo 'Invalid query: '.$mysqli->error." (Checking multiply user error)";
    exit();
}
if (($check_result1 = $mysqli->query($check_mutiply_username_query1)) == false) {
    echo 'Invalid query: '.$mysqli->error." (Checking multiply user error)";
    exit();
}

if ($check_result->num_rows > 0 || $check_result1->num_rows > 0) {
    $errusername = '<span class="text-danger">This username has been used.</span>';
    $error .= $errusername;
}
else if (!(isset($_POST["username"]) && !(empty($_POST["username"])) && preg_match('/^[a-zA-Z0-9]{4,}$/', trim($_POST["username"])))) {
    $errusername = '<span class="text-danger">Username must be at least 4 characters (alphabet and number & not spacing).</span>';
    $error .= $errusername;
}
if (isset($_POST["Edit"]) && $_POST["Edit"] == "Done") {
    if (isset($_POST["password"]) && !(empty($_POST["password"]))) {
        if (!(strlen($_POST["password"]) >= 6)) {
            $errpassword = '<span class="text-danger">Password must be at least 6 characters.</span>';
            $error .= $errpassword;
        }
    }
}
else {
    if (!(isset($_POST["password"]) && !(empty($_POST["password"])) && strlen($_POST["password"]) >= 6)) {
        $errpassword = '<span class="text-danger">Password must be at least 6 characters.</span>';
        $error .= $errpassword;
    }
}

if (!(isset($_POST["level"]) && $_POST["level"] != "" && preg_match('/^\d{1}$/', trim($_POST["level"])))) {
    $errlevel = '<span class="text-danger">User Level must be one digit number.</span>';
    $error .= $errlevel;
}
if (!(isset($_POST["Fname"]) && !(empty($_POST["Fname"])) && preg_match('/^[a-zA-Z][a-zA-Z ]{0,}$/', trim($_POST["Fname"])))) {
    $errFname = '<span class="text-danger">First Name must be alphabet and at least one characters.</span>';
    $error .= $errFname;
}
if (!(isset($_POST["Lname"]) && !(empty($_POST["Lname"])) && preg_match('/^[a-zA-Z][a-zA-Z ]{0,}$/', trim($_POST["Lname"])))) {
    $errLname = '<span class="text-danger">Last Name must be alphabet and at least one characters.</span>';
    $error .= $errLname;
}

if (isset($_POST["Edit"]) && $_POST["Edit"] == "Done") {
    $check_mutiply_phone_query = "select * from adminAccount where adminPhone='".$_POST["phone"]."' and NOT adminID=".$adminId;
}
else {
    $check_mutiply_phone_query = "select * from adminAccount where adminPhone='".$_POST["phone"]."'";
}
$check_mutiply_phone_query1 = "select * from userAccount where userPhone='".$_POST["phone"]."'";
$check_result2;
$check_result2_1;
if (($check_result2 = $mysqli->query($check_mutiply_phone_query)) == false) {
    echo 'Invalid query: '.$mysqli->error." (Checking multiply phone error)";
    exit();
}
if (($check_result2_1 = $mysqli->query($check_mutiply_phone_query1)) == false) {
    echo 'Invalid query: '.$mysqli->error." (Checking multiply phone error)";
    exit();
}
if ($check_result2->num_rows > 0 || $check_result2_1->num_rows > 0) {
    $errphone = '<span class="text-danger">This Phone Number has been used.</span>';
    $error .= $errphone;
}
else if (!(isset($_POST["phone"]) && !(empty($_POST["phone"])) && preg_match('/^01[0-9]-[1-9][0-9]{6,7}$/', trim($_POST["phone"])))) {
    $errphone = '<span class="text-danger">Phone must be Mobile Number.</span>';
    $error .= $errphone;
}

if (isset($_POST["Edit"]) && $_POST["Edit"] == "Done") {
    $check_mutiply_email_query = "select * from adminAccount where adminEmail='".$_POST["email"]."' and NOT adminID=".$adminId;
}
else {
    $check_mutiply_email_query = "select * from adminAccount where adminEmail='".$_POST["email"]."'";
}
$check_mutiply_email_query1 = "select * from userAccount where userEmail='".$_POST["email"]."'";
$check_result3;
$check_result3_1;
if (($check_result3 = $mysqli->query($check_mutiply_email_query)) == false) {
    echo 'Invalid query: '.$mysqli->error." (Checking multiply email error)";
    exit();
}
if (($check_result3_1 = $mysqli->query($check_mutiply_email_query1)) == false) {
    echo 'Invalid query: '.$mysqli->error." (Checking multiply email error)";
    exit();
}
if ($check_result3->num_rows > 0 || $check_result3_1->num_rows > 0) {
    $erremail = '<span class="text-danger">This Email has been used.</span>';
    $error .= $erremail;
}
else if (!(isset($_POST["email"]) && !(empty($_POST["email"])) && preg_match('/^([a-zA-Z0-9]{1,}[.]{0,}){1,}[a-zA-Z0-9]@([a-zA-Z]{1,}[.]{1}){1,}[a-zA-Z]{2,}$/', trim($_POST["email"])))) {
    $erremail = '<span class="text-danger">Invalid Email.</span>';
    $error .= $erremail;
}
?>