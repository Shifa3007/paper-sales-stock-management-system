<?php

include "config.php";


if(isset($_GET['id']))
{

    $id = $_GET['id'];


    $query = mysqli_query($conn,
    "SELECT * FROM products WHERE id='$id'");


    $product = mysqli_fetch_assoc($query);


    echo json_encode($product);

}

?>