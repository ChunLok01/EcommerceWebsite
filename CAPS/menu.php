<?php 
if (isset($_SESSION["authentication"]) && $_SESSION["authentication"] === 1)
{
?>
<div class="pt-4 list-group">
    <div class="text-left pt-1">
        <p>CUSTOMER</p>
    </div>
    <a href="admin_order.php" class="list-group-item list-group-item-action bg-dark text-white">Order List </a>
    <a href="account.php" class="list-group-item list-group-item-action bg-dark text-white">Account List </a>
</div>
<div class="pt-4 list-group">
    <div class="text-left pt-1">
        <p>ADMIN</p>
    </div>
    <?php if ($_SESSION["userlevel"] == 1) { ?>
    <a href="admin.php" class="list-group-item list-group-item-action bg-dark text-white">Admin List </a>
    <?php } ?>
    <a href="admin_product.php" class="list-group-item list-group-item-action bg-dark text-white">Product List </a>
</div>
<?php
}
?>