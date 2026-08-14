<?php
session_start();

include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$product_query = mysqli_query($conn, "SELECT * FROM products");

if (isset($_POST['add_item'])) {

    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $rate = $_POST['price'];

    $amount = $quantity * $rate;

    mysqli_query(
        $conn,
        "INSERT INTO invoice_items (product_id, quantity, rate, amount)
        VALUES ('$product_id','$quantity','$rate','$amount')"
    );

    header("Location: billing.php");

    exit();
}

?>

<!DOCTYPE html>
<html>

<head>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: #f4f6fb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 500px;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .08);
        }

        .container h1 {
            text-align: center;
            color: #0b1f4d;
            margin-bottom: 25px;
            font-size: 28px;
        }

        .field {
            margin-bottom: 15px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .field input,
        .field select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
        }

        .add-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: white;
            font-size: 18px;
            cursor: pointer;
            margin-top: 10px;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 25px;
            text-decoration: none;
            color: #5b21b6;
            font-size: 18px;
        }
    </style>

    <title>Add Invoice Item</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>
    <div class="container">

        <h1>Add Invoice Item</h1>

        <form action="" method="POST">

            <div class="field">
                <label>Product</label>

                <select name="product_id" id="product_id" onchange="getProductDetails()" required>

                    <option value="">Select Product</option>

                    <?php
                    while ($product = mysqli_fetch_assoc($product_query)) {
                    ?>

                        <option value="<?php echo $product['id']; ?>">
                            <?php echo $product['paper_name']; ?>
                        </option>

                    <?php
                    }
                    ?>

                </select>

            </div>

            <div class="field">
                <label>GSM</label>
                <input type="text" name="gsm" id="gsm" readonly>
            </div>

            <div class="field">
                <label>Size</label>
                <input type="text" name="size" id="size" readonly>
            </div>

            <div class="field">
                <label>Quantity</label>
                <input type="number" name="quantity" min="1">
            </div>

            <div class="field">
                <label>Rate (₹)</label>
                <input type="text" name="price" id="price" readonly>
            </div>

            <button type="submit" name="add_item" class="add-btn">
                Add Item
            </button>

        </form>

        <a href="billing.php" class="back">
            ← Back to Billing
        </a>

    </div>

    <script>
        function getProductDetails() {

            let product_id = document.getElementById("product_id").value;


            if (product_id == "") {
                return;
            }


            fetch("get_product.php?id=" + product_id)

                .then(response => response.json())

                .then(data => {

                    document.getElementById("gsm").value = data.gsm;

                    document.getElementById("size").value = data.size;

                    document.getElementById("price").value = data.price;

                });

        }
    </script>

</body>

</html>