<?php

$setting_query = mysqli_query($conn, "SELECT * FROM settings WHERE id=1");
$setting = mysqli_fetch_assoc($setting_query);

?>

<div class="sidebar">

    <div class="logo-section">

        <img src="images/<?php echo $setting['company_logo']; ?>" alt="Logo">

        <h2><?php echo $setting['company_name']; ?></h2>

        <p>Paper Sales &<br>Stock Management System</p>

    </div>

    <ul>

        <li>
            <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
        </li>

        <li>
            <a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                <i class="fas fa-box"></i>
                Products & Stock
            </a>
        </li>

        <li>
            <a href="customers.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                Customers & Orders
            </a>
        </li>

        <li>
            <a href="billing.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'billing.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-invoice"></i>
                Billing / Invoice
            </a>
        </li>

        <li>
            <a href="settings.php" class="<?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
                <i class="fas fa-cog"></i>
                Settings
            </a>
        </li>

        <li>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </li>


    </ul>

</div>