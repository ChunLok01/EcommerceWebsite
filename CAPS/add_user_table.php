<?php
echo '<table class="table">';
echo '<tr>';
echo '<th>User ID : </th>';
echo '<td>'.'<input type="text" class="form-control" name="id" value="" disabled/>'.'</td>';
echo '</tr>';
echo '<tr>';
echo '<th>User Name : </th>';
echo '<td>'.'<input type="text" class="form-control" name="username" value="'.(isset($_POST["username"]) && !(empty($_POST["username"])) ? $_POST["username"] : '').'"'.((isset($_POST["Add"]) && $_POST["Add"] == "Add") ? ' readonly' : '' ).'/>'.(isset($errusername) && !(empty($errusername)) ? $errusername : "").'</td>';
echo '</tr>';
echo '<tr>';
echo '<th>Password : </th>';
echo '<td>'.'<input type="password" class="form-control" name="password" value="'.(isset($_POST["password"]) && !(empty($_POST["password"])) ? $_POST["password"] : '').'"'.((isset($_POST["Add"]) && $_POST["Add"] == "Add") ? ' readonly' : '' ).'/>'.(isset($errpassword) && !(empty($errpassword)) ? $errpassword : "").'</td>';
echo '</tr>';
echo '<tr>';
echo '<th>User Level : </th>';
echo '<td>'.'<input type="text" class="form-control" name="level" value="'.(isset($_POST["level"]) && !(empty($_POST["level"])) ? $_POST["level"] : '').'"'.((isset($_POST["Add"]) && $_POST["Add"] == "Add") ? ' readonly' : '' ).'/>'.(isset($errlevel) && !(empty($errlevel)) ? $errlevel : "").'</td>';
echo '</tr>';
echo '<tr>';
echo '<th>First Name : </th>';
echo '<td>'.'<input type="text" class="form-control" name="Fname" value="'.(isset($_POST["Fname"]) && !(empty($_POST["Fname"])) ? $_POST["Fname"] : '').'"'.((isset($_POST["Add"]) && $_POST["Add"] == "Add") ? ' readonly' : '' ).'/>'.(isset($errFname) && !(empty($errFname)) ? $errFname : "").'</td>';
echo '</tr>';
echo '<tr>';
echo '<th>Last Name : </th>';
echo '<td>'.'<input type="text" class="form-control" name="Lname" value="'.(isset($_POST["Lname"]) && !(empty($_POST["Lname"])) ? $_POST["Lname"] : '').'"'.((isset($_POST["Add"]) && $_POST["Add"] == "Add") ? ' readonly' : '' ).'/>'.(isset($errLname) && !(empty($errLname)) ? $errLname : "").'</td>';
echo '</tr>';
echo '<tr>';
echo '<th>Phone Number : </th>';
echo '<td>'.'<input type="text" class="form-control" name="phone" value="'.(isset($_POST["phone"]) && !(empty($_POST["phone"])) ? $_POST["phone"] : '').'"'.((isset($_POST["Add"]) && $_POST["Add"] == "Add") ? ' readonly' : '' ).' placeholder="Exp: 012-3456789, Format: XXX-XXXXXX"/>'.(isset($errphone) && !(empty($errphone)) ? $errphone : "").'</td>';
echo '</tr>';
echo '<tr>';
echo '<th>Email Address : </th>';
echo '<td>'.'<input type="text" class="form-control" name="email" value="'.(isset($_POST["email"]) && !(empty($_POST["email"])) ? $_POST["email"] : '').'"'.((isset($_POST["Add"]) && $_POST["Add"] == "Add") ? ' readonly' : '' ).'/>'.(isset($erremail) && !(empty($erremail)) ? $erremail : "").'</td>';
echo '</tr>';
echo '</table>';
?>