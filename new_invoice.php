<?php
session_start();

include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

unset($_SESSION['customer_id']);
unset($_SESSION['invoice_date']);
unset($_SESSION['payment_method']);

// Remove all temporary invoice items
mysqli_query($conn, "DELETE FROM invoice_items WHERE invoice_id = 0");

header("Location: billing.php");
exit();
?>