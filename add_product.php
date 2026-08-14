<?php
session_start();

include "config.php";

if(!isset($_SESSION['username']))
{
    header("Location: login.html");
    exit();
}

if(isset($_POST['add_product']))
{
    $paper_name = $_POST['paper_name'];
    $paper_type = $_POST['paper_type'];
    $gsm = $_POST['gsm'];
    $size = $_POST['size'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $sql = "INSERT INTO products (paper_name, paper_type, gsm, size, quantity, price)
            VALUES ('$paper_name', '$paper_type', '$gsm', '$size', '$quantity', '$price')";

    if(mysqli_query($conn, $sql))
    {
        echo "<script>
                alert('Product Added Successfully!');
                window.location='products.php';
              </script>";
    }
    else
    {
        echo "Error: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Add Product</title>

<style>

body{
    font-family:'Segoe UI',sans-serif;
    background:#f4f6fb;
}

.container{
    width:500px;
    background:white;
    padding:30px;
    margin:50px auto;
    border-radius:15px;
    box-shadow:0 8px 18px rgba(0,0,0,.08);
}

h1{
    color:#0b1f4d;
    text-align:center;
}

input{
    width:100%;
    padding:12px;
    margin:10px 0;
    border:1px solid #ddd;
    border-radius:8px;
}

button{
    width:100%;
    padding:12px;
    background:#2563eb;
    color:white;
    border:none;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
}

button:hover{
    background:#1d4ed8;
}

.back{
    display:block;
    text-align:center;
    margin-top:15px;
    text-decoration:none;
}

</style>

</head>

<body>


<div class="container">

<h1>Add Product</h1>


<form method="POST">

<input type="text" name="paper_name" placeholder="Paper Name" required>

<input type="text" name="paper_type" placeholder="Paper Type" required>

<input type="text" name="gsm" placeholder="GSM" required>

<input type="text" name="size" placeholder="Size" required>

<input type="number" name="quantity" placeholder="Quantity" required>

<input type="number" name="price" placeholder="Price" required>


<button type="submit" name="add_product"> Add Product </button>

</form>


<a class="back" href="products.php">  ← Back to Products </a>

</div>


</body>

</html>