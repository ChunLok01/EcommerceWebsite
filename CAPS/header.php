<style>
    #brand {
        color: red;
    }
</style>
<nav class="navbar navbar-expand bg-dark navbar-dark justify-content-between text-white">
    <ul class="navbar-nav">
        <a id="brand" class="navbar-brand text-center" href="index.php">REPUBLIC OF GAMER</a>
        <li class="nav-item">
            <a class="nav-link text-white" href="laptop.php">Laptops</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="keyboard.php">Keyboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="mouse.php">Mouse</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white" href="headset.php">Headset</a>
        </li>
    </ul>
    <ul class="navbar-nav text-right">
        <?php
            if (isset($_SESSION["auth"]) && $_SESSION["auth"] == 1 || isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1) {
        ?>
        <li class="nav-item text-right">
            <a class="nav-link text-white" href="login_management.php"><img class="text-white" src="image/header/avatar.png" height="30px" width="30px"/></a>
        </li>
        <li class="nav-item text-right">
            <a class="nav-link text-white" href="logout.php">Logout</a>
        </li>
        <?php
            }
            else {
        ?>
        <li class="nav-item text-right">
            <a class="nav-link text-white" href="login.php">Login</a>
        </li>
        <?php
            }
        ?>
        <li class="nav-item text-right">
            <a class="nav-link text-white" href="search.php">Search</a>
        </li>
        <?php
            if (isset($_SESSION["auth"]) && $_SESSION["auth"] == 1 || isset($_SESSION["authentication"]) && $_SESSION["authentication"] == 1) {
        ?>
        <li style="cursor:pointer;" id="cartcount" class="nav-item text-right position-relative" onclick="opencart()">
            <span class="nav-link text-white"><img src="image/header/cart.png" height="30px" width="30px"/></span>
        </li>
        <?php
            }
        ?>
    </ul>
</nav>