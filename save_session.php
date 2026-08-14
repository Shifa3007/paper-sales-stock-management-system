<?php
session_start();

$_SESSION['customer_id'] = $_POST['customer_id'];
$_SESSION['invoice_date'] = $_POST['invoice_date'];
$_SESSION['payment_method'] = $_POST['payment_method'];