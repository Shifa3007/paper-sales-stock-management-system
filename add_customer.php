<?php
session_start();

include "config.php";

if(!isset($_SESSION['username']))
{
    header("Location: login.html");
    exit();
}

if(isset($_POST['add_customer']))
{
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);

    $sql = "INSERT INTO customers(customer_name, phone, city)
            VALUES('$customer_name', '$phone', '$city')";

    if(mysqli_query($conn, $sql))
    {
        header("Location: customers.php");
        exit();
    }
    else
    {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Customer</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    background:#f4f6fb;
}

.container{
    width:500px;
    margin:50px auto;
    background:white;
    padding:30px;
    border-radius:15px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}

h2{
    color:#0b1f4d;
    margin-bottom:25px;
}

label{
    display:block;
    margin-top:15px;
    margin-bottom:6px;
    font-weight:600;
}

input{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:8px;
    font-size:15px;
}

button{
    margin-top:25px;
    width:100%;
    padding:12px;
    background:#2563eb;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-size:16px;
}

button:hover{
    background:#1d4ed8;
}

</style>

</head>

<body>

<div class="container">

<h2>Add Customer</h2>

<form method="POST">

<label>Customer Name</label>
<input type="text" name="customer_name" required>

<label>Phone</label>
<input type="text" name="phone" required>

<label>City</label>
<input type="text" name="city" required>

<button type="submit" name="add_customer"> Add Customer </button>

</form>

</div>

</body>

</html>