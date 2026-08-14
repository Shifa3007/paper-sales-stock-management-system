<?php
session_start();

include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
if (isset($_POST['customer_id'])) {

    $invoice_no = $_POST['invoice_no'];

    $customer_id = $_POST['customer_id'];

    $invoice_date = $_POST['invoice_date'];

    $payment_method = $_POST['payment_method'];

    $discount = $_POST['discount'];

    $tax = $_POST['tax'];

    $subtotal = $_POST['subtotal'];

    $total = $_POST['total'];

    $status = "Paid";

    mysqli_query($conn, "
    INSERT INTO invoices
    (invoice_no, customer_id, invoice_date, payment_method, status, subtotal, discount, tax, total)
    VALUES
    ('$invoice_no','$customer_id','$invoice_date','$payment_method','$status','$subtotal','$discount','$tax','$total')
    ");

    $invoice_id = mysqli_insert_id($conn);

    mysqli_query($conn, "
    UPDATE invoice_items
    SET invoice_id = '$invoice_id'
    WHERE invoice_id = 0
    ");
}
