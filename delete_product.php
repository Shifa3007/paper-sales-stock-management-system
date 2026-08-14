<?php
session_start();
include "config.php";

if(!isset($_SESSION['username']))
{
    header("Location: login.html");
    exit();
}

if(isset($_GET['id']))
{
    $id = $_GET['id'];

    $sql = "DELETE FROM products WHERE id='$id'";

    if(mysqli_query($conn, $sql))
    {
        echo "<script>
                alert('Product Deleted Successfully!');
                window.location='products.php';
              </script>";
    }
    else
    {
        echo "Error: " . mysqli_error($conn);
    }
}
?>