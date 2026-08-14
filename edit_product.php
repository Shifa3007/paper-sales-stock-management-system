<?php
session_start();
include "config.php";

if(!isset($_SESSION['username']))
{
    header("Location: login.html");
    exit();
}

$id = $_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");

$row = mysqli_fetch_assoc($result);

if(isset($_POST['update_product']))
{
    $paper_name = $_POST['paper_name'];
    $paper_type = $_POST['paper_type'];
    $gsm = $_POST['gsm'];
    $size = $_POST['size'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $sql = "UPDATE products SET
            paper_name='$paper_name',
            paper_type='$paper_type',
            gsm='$gsm',
            size='$size',
            quantity='$quantity',
            price='$price'
            WHERE id='$id'";

    if(mysqli_query($conn, $sql))
    {
        echo "<script>
                alert('Product Updated Successfully!');
                window.location='products.php';
              </script>";
    }
    else
    {
        echo mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Product</title>

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
    text-align:center;
    color:#0b1f4d;
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
    cursor:pointer;
    font-size:16px;
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

<h1>Edit Product</h1>

<form method="POST">

<input type="text" name="paper_name" value="<?php echo $row['paper_name']; ?>" required>

<input type="text" name="paper_type" value="<?php echo $row['paper_type']; ?>" required>

<input type="text" name="gsm" value="<?php echo $row['gsm']; ?>" required>

<input type="text" name="size" value="<?php echo $row['size']; ?>" required>

<input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" required>

<input type="number" name="price" value="<?php echo $row['price']; ?>" required>

<button type="submit" name="update_product"> Update Product </button>

</form>

<a href="products.php" class="back">← Back to Products</a>

</div>

</body>
</html>