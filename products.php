<?php
session_start();

include "config.php";

if(!isset($_SESSION['username']))
{
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Products & Stock</title>

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

/* Sidebar */

.sidebar{
    width:260px;
    height:100vh;
    background:#0b1f4d;
    color:white;
    position:fixed;
    left:0;
    top:0;
    padding:20px;
    overflow-y:auto;
}

.sidebar h2{
    text-align:center;
    margin-bottom:40px;
    font-size:24px;
}

.sidebar ul{
    list-style:none;
}

.sidebar ul li{
    margin:4px 0;
}

.sidebar ul li a{
    color:white;
    text-decoration:none;
    display:flex;
    align-items:center;
    padding:11px 16px;
    border-radius:10px;
    transition:.3s;
    font-size:17px;
}

.sidebar ul li a:hover,
.sidebar ul li a.active{
    background:#2563eb;
    border-radius:10px;
}

.sidebar ul li a i{
    margin-right:12px;
    width:22px;
}

/* Main Content */

.main{
    margin-left:260px;
    width:calc(100% - 260px);
    min-height:100vh;
}

/* Topbar */

.topbar{
    background:white;
    height:70px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 30px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.profile{
    font-weight:bold;
    color:#333;
}

/* Dashboard */

.content{
    padding:25px;
    width:100%;
}

.content h1{
    font-size:30px;
    font-weight:700;
    color:#1f2937;
    margin-bottom:25px;
}

.card h3{
    color:#666;
    font-size:16px;
}

.card h2{
    margin-top:10px;
    color:#0b1f4d;
}

.logo-section{
    text-align:center;
    margin-bottom:20px;
}

.logo-section img{
    width:70px;
    height:70px;
    background:white;
    border-radius:50%;
    padding:5px;
    object-fit:contain;
    margin-bottom:15px;
}

.logo-section h2{
    color:white;
    font-size:20px;
    margin-bottom:8px;
}

.logo-section p{
    color:#d0d8ff;
    font-size:13px;
    line-height:1.5;
}

.logo-section{
    border-bottom:1px solid rgba(255,255,255,.15);
    padding-bottom:20px;
}

/* Main Area */

.main{
    margin-left:260px;
    width:calc(100% - 260px);
}

.content{
    padding:30px;
}

.content h1{
    color:#222;
    margin-bottom:25px;
}

/* Topbar */

.topbar{
    background:white;
    height:70px;
    display:flex;
    align-items:center;
    padding:0 30px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.search-box{
    position:relative;
}

.search-box i{
    position:absolute;
    left:15px;
    top:13px;
    color:#888;
}

.search-box input{
    width:340px;
    padding:12px 15px 12px 42px;
    border:1px solid #ddd;
    border-radius:10px;
    font-size:15px;
}


/* Product Box */

.product-box{

    background:white;
    padding:30px;
    border-radius:15px;
    box-shadow:0 8px 18px rgba(0,0,0,.08);

}

.product-box h2{
    color:#0b1f4d;
    margin-bottom:10px;
}

.product-box p{
    color:#666;
}

.add-btn{
    background:#2563eb;
    color:white;
    padding:10px 18px;
    text-decoration:none;
    border-radius:8px;
    display:inline-block;
    font-weight:600;
}

.add-btn:hover{
    background:#1d4ed8;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
    background:white;
}

table th{
    background:#0b1f4d;
    color:white;
    padding:14px;
    text-align:left;
}

table td{
    padding:14px;
    border-bottom:1px solid #e5e7eb;
}

table tr:hover{
    background:#f8fafc;
}

table a{
    text-decoration:none;
    font-weight:600;
}

table a:first-child{
    color:#2563eb;
}

table a:last-child{
    color:#ef4444;
}

.product-header{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.product-header h2{
    margin: 0;
}

.add-btn{
    background: #2563eb;
    color: white;
    padding: 10px 18px;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
}

</style>

</head>


<body>


<?php include "sidebar.php"; ?>


<div class="main">


    <div class="topbar">

        <div class="search-box">

            <i class="fas fa-search"></i>

            <form method="GET">

<div class="search-box">
    <i class="fas fa-search"></i>

    <input
        type="text"
        name="search"
        placeholder="Search Product..."
        value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
</div>

</form>

        </div>

    </div>



    <div class="content">


        <h1>Products & Stock</h1>


        <div class="product-box">

<div class="product-header">

    <h2>Manage Paper Products</h2>

    <a href="add_product.php" class="add-btn">
        <i class="fas fa-plus"></i> Add Product
    </a>
</div>

<table>

<thead>
<tr>
    <th>ID</th>
    <th>Paper Name</th>
    <th>Type</th>
    <th>GSM</th>
    <th>Size</th>
    <th>Stock</th>
    <th>Price</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php

if(isset($_GET['search']) && $_GET['search'] != "")
{
    $search = mysqli_real_escape_string($conn, $_GET['search']);

    $result = mysqli_query($conn,
        "SELECT * FROM products
         WHERE paper_name LIKE '%$search%'
         OR paper_type LIKE '%$search%'
         OR size LIKE '%$search%'");
}
else
{
    $result = mysqli_query($conn,"SELECT * FROM products");
}

if(mysqli_num_rows($result)>0)
{
    while($row=mysqli_fetch_assoc($result))
    {
?>

<tr>

<td><?php echo $row['id']; ?></td>
<td><?php echo $row['paper_name']; ?></td>
<td><?php echo $row['paper_type']; ?></td>
<td><?php echo $row['gsm']; ?></td>
<td><?php echo $row['size']; ?></td>
<td><?php echo $row['quantity']; ?></td>
<td>₹ <?php echo $row['price']; ?></td>
<td>

<?php

if($row['quantity'] == 0)
{
    echo "<span style='color:red;font-weight:bold;'>Out of Stock</span>";
}
elseif($row['quantity'] <= 50)
{
    echo "<span style='color:orange;font-weight:bold;'>Low Stock</span>";
}
else
{
    echo "<span style='color:green;font-weight:bold;'>In Stock</span>";
}

?>

</td>
<td>

<a href="edit_product.php?id=<?php echo $row['id']; ?>">Edit</a> | 
<a href="delete_product.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Are you sure you want to delete this product?')"> Delete  </a>

</td>

</tr>

<?php
    }
}
else
{
?>

<tr>
<td colspan="8" style="text-align:center;">
No Products Found
</td>
</tr>

<?php
}
?>

</tbody>

</table>

        </div>


    </div>


</div>


</body>

</html>