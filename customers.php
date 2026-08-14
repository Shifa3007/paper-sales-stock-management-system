<?php
session_start();

include "config.php";

if(isset($_POST['create_order']))
{
    $customer_id = $_POST['customer_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $payment_type = $_POST['payment_type'];

    // Get product details
    $product = mysqli_query($conn, "SELECT * FROM products WHERE id='$product_id'");
    $row = mysqli_fetch_assoc($product);

    $price = $row['price'];
    $stock = $row['quantity'];

    // Check stock
    if($quantity > $stock)
    {
        echo "<script>alert('Not enough stock available!');</script>";
    }
    else
    {
        $total_amount = $price * $quantity;

        // Save order
        mysqli_query($conn, "INSERT INTO orders
        (customer_id, product_id, quantity, total_amount, payment_type)
        VALUES
        ('$customer_id','$product_id','$quantity','$total_amount','$payment_type')");

        // Update stock
        $new_stock = $stock - $quantity;

        mysqli_query($conn,
        "UPDATE products
        SET quantity='$new_stock'
        WHERE id='$product_id'");

        echo "<script>
                alert('Order Created Successfully');
                window.location='customers.php';
              </script>";
    }
}

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

<title>Customers & Orders</title>

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


/* Customer  Box */

.Customer-box{

    background:white;
    padding:30px;
    border-radius:15px;
    box-shadow:0 8px 18px rgba(0,0,0,.08);

}

.Customer-box h2{
    color:#0b1f4d;
    margin-bottom:10px;
}

.Customer-box p{
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

.layout{
    display:flex;
    gap:20px;
    align-items:flex-start;
}

.left-card{
    width:60%;
    height:500px;
}

.right-card{
    width:40%;
    height:500px;
}

.Customer-box,
.order-box{
    height:100%;
}

.order-box{
    background:white;
    padding:30px;
    border-radius:15px;
    box-shadow:0 8px 18px rgba(0,0,0,.08);
}

.order-box h2{
    color:#0b1f4d;
    margin-bottom:20px;
}

.order-box label{
    display:block;
    margin-top:15px;
    margin-bottom:6px;
    font-weight:600;
}

.order-box input,
.order-box select{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:8px;
    font-size:15px;
}

.order-box button{
    margin-top:20px;
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    background:#2563eb;
    color:white;
    cursor:pointer;
    font-size:16px;
}

.order-box button:hover{
    background:#1d4ed8;
}

.order-history{
    background:white;
    padding:30px;
    border-radius:15px;
    box-shadow:0 8px 18px rgba(0,0,0,.08);
    margin-top:25px;
}

.order-history h2{
    color:#0b1f4d;
    margin-bottom:20px;
}

.customer-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
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
        placeholder="Search Customers..."
        value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
</div>

</form>

        </div>

    </div>

    <div class="content">

    <h1>Customers & Orders</h1>

    <div class="layout">

        <div class="left-card">

            <div class="Customer-box">
           
        <div class="customer-header">

        <h2>Customers</h2>

        <a href="add_customer.php" class="add-btn">
            <i class="fas fa-user-plus"></i> Add Customer
        </a>

    </div>

    <br>

        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>Action</th>
                </tr>

            </thead>

<tbody>

<?php

if(isset($_GET['search']) && $_GET['search'] != "")
{
    $search = mysqli_real_escape_string($conn, $_GET['search']);

    $result = mysqli_query($conn,
        "SELECT * FROM customers
         WHERE customer_name LIKE '%$search%'
         OR phone LIKE '%$search%'
         OR city LIKE '%$search%'");
}
else
{
    $result = mysqli_query($conn,"SELECT * FROM customers");
}

if(mysqli_num_rows($result) > 0)
{
    while($row = mysqli_fetch_assoc($result))
    {
?>

<tr>

    <td><?php echo $row['customer_id']; ?></td>
    <td><?php echo $row['customer_name']; ?></td>
    <td><?php echo $row['phone']; ?></td>
    <td><?php echo $row['city']; ?></td>

    <td>
        <a href="edit_customer.php?id=<?php echo $row['customer_id']; ?>">Edit</a> |
        <a href="delete_customer.php?id=<?php echo $row['customer_id']; ?>"
onclick="return confirm('Are you sure you want to delete this customer?')">
Delete
</a>
    </td>

</tr>

<?php
    }
}
else
{
?>

<tr>
    <td colspan="5" style="text-align:center;"> No Customers Found </td>
</tr>

<?php
}
?>

</tbody>

</table>

        </div>

    </div>

    <div class="right-card">

    <div class="order-box">

        <h2>Create New Order</h2>

<form method="POST">

<label>Customer</label>

<select name="customer_id" required>

<option value="">Select Customer</option>

<?php
$result = mysqli_query($conn, "SELECT * FROM customers");

while($customer = mysqli_fetch_assoc($result))
{
?>
<option value="<?php echo $customer['customer_id']; ?>">
    <?php echo $customer['customer_name']; ?>
</option>
<?php
}
?>

</select>

<label style="margin-top:15px;">Product</label>

<select name="product_id" required>

<option value="">Select Product</option>

<?php
$result = mysqli_query($conn, "SELECT * FROM products");

while($product = mysqli_fetch_assoc($result))
{
?>
<option value="<?php echo $product['id']; ?>">
    <?php echo $product['paper_name']; ?>
</option>
<?php
}
?>

</select>

<label style="margin-top:15px;">Quantity</label>

<input type="number" name="quantity" min="1" required>

<label style="margin-top:15px;">Payment Type</label>

<select name="payment_type">

<option>Cash</option>
<option>UPI</option>
<option>Online</option>

</select>

<button type="submit" name="create_order">Create Order</button>

</form>

</div>

</div>

</div>

<div class="order-history">

<h2>Order History</h2>

<table>

<tr>
<th>Order ID</th>
<th>Customer</th>
<th>Paper</th>
<th>Quantity</th>
<th>Payment</th>
</tr>

<?php

$order_history = mysqli_query($conn,

"SELECT 
orders.order_id,
customers.customer_name,
products.paper_name,
orders.quantity,
orders.payment_type

FROM orders

JOIN customers 
ON orders.customer_id = customers.customer_id

JOIN products
ON orders.product_id = products.id"

);

while($row = mysqli_fetch_assoc($order_history))
{
?>

<tr>
<td><?php echo $row['order_id']; ?></td>
<td><?php echo $row['customer_name']; ?></td>
<td><?php echo $row['paper_name']; ?></td>
<td><?php echo $row['quantity']; ?></td>
<td><?php echo $row['payment_type']; ?></td>
</tr>

<?php
}

?>

</table>

</div>

</body>

</html>