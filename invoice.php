<?php

session_start();

include "config.php";

if(!isset($_SESSION['username']))
{
    header("Location: login.html");
    exit();
}

if(isset($_GET['order_id']))
{

$order_id=$_GET['order_id'];

$query=mysqli_query($conn,

"SELECT o.order_id, o.quantity, o.payment_type, o.order_date, c.customer_name, p.paper_name, p.price FROM orders o
JOIN customers c ON o.customer_id = c.customer_id
JOIN products p ON o.product_id = p.id
WHERE o.order_id='$order_id'
");
$data=mysqli_fetch_assoc($query);

}

?>

<!DOCTYPE html>
<html>
<head>
<title>Invoice</title>
</head>

<body>

<h1>Ayan Enterprises</h1>

<h2>Invoice</h2>

<p>
Invoice ID:
<?php echo $data['order_id']; ?>
</p>

<p>
Customer:
<?php echo $data['customer_name']; ?>
</p>

<p>
Product:
<?php echo $data['paper_name']; ?>
</p>

<p>
Quantity:
<?php echo $data['quantity']; ?>
</p>

<p>
Price:
₹<?php echo $data['price']; ?>
</p>

<p>
Payment:
<?php echo $data['payment_type']; ?>
</p>

</body>

</html>