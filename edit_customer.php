<?php
session_start();

include "config.php";

if(!isset($_SESSION['username']))
{
    header("Location: login.html");
    exit();
}

$id = $_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM customers WHERE customer_id='$id'");
$row = mysqli_fetch_assoc($result);

if(isset($_POST['update_customer']))
{
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);

    mysqli_query($conn, "UPDATE customers
    SET
    customer_name='$customer_name',
    phone='$phone',
    city='$city'
    WHERE customer_id='$id'");

    header("Location: customers.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Customer</title>

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
    margin-bottom:20px;
    color:#0b1f4d;
}

label{
    display:block;
    margin-top:15px;
    margin-bottom:5px;
    font-weight:600;
}

input{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:8px;
}

button{
    width:100%;
    margin-top:25px;
    padding:12px;
    background:#2563eb;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

button:hover{
    background:#1d4ed8;
}

</style>

</head>

<body>

<div class="container">

<h2>Edit Customer</h2>

<form method="POST">

<label>Customer Name</label>
<input type="text" name="customer_name"
value="<?php echo $row['customer_name']; ?>" required>

<label>Phone</label>
<input type="text" name="phone"
value="<?php echo $row['phone']; ?>" required>

<label>City</label>
<input type="text" name="city"
value="<?php echo $row['city']; ?>" required>

<button type="submit" name="update_customer">Update Customer</button>

</form>

</div>

</body>
</html>