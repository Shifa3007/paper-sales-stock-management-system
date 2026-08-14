<?php

session_start();

include "config.php";

if(!isset($_SESSION['username']))
{
    header("Location: login.html");
    exit();
}

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM customers WHERE customer_id='$id'");

header("Location: customers.php");
exit();

?>