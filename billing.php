<?php
session_start();

include "config.php";

$setting_query = mysqli_query($conn, "SELECT * FROM settings WHERE id=1");
$setting = mysqli_fetch_assoc($setting_query);

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$selected_customer = $_SESSION['customer_id'] ?? "";
$selected_date = $_SESSION['invoice_date'] ?? "";
$selected_payment = $_SESSION['payment_method'] ?? "";

$result = mysqli_query($conn, "SELECT MAX(invoice_id) AS last_id FROM invoices");
$row = mysqli_fetch_assoc($result);

if ($row['last_id'] == NULL) {
    $next_invoice = "INV0001";
} else {
    $next_invoice = "INV" . str_pad($row['last_id'] + 1, 4, "0", STR_PAD_LEFT);
}

$summary = mysqli_query($conn, "
SELECT SUM(amount) AS subtotal
FROM invoice_items
WHERE invoice_id = 0
");

$total = mysqli_fetch_assoc($summary);

$subtotal = $total['subtotal'];

if ($subtotal == "") {
    $subtotal = 0;
}

$discount = 0;
$tax = 0;

$grand_total = $subtotal - $discount + $tax;

?>

<!DOCTYPE html>
<html>

<head>

    <title>Billing / Invoice</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: #f4f6fb;
        }

        /* ================= Sidebar ================= */

        .sidebar {
            width: 260px;
            height: 100vh;
            background: #0b1f4d;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px;
            overflow-y: auto;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 24px;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin: 4px 0;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 11px 16px;
            border-radius: 10px;
            transition: .3s;
            font-size: 17px;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: #2563eb;
        }

        .sidebar ul li a i {
            margin-right: 12px;
            width: 22px;
        }

        /* ================= Logo ================= */

        .logo-section {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, .15);
            padding-bottom: 20px;
        }

        .logo-section img {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 50%;
            padding: 5px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .logo-section h2 {
            color: white;
            font-size: 20px;
            margin-bottom: 8px;
        }

        .logo-section p {
            color: #d0d8ff;
            font-size: 13px;
            line-height: 1.5;
        }

        /* ================= Topbar ================= */

        .topbar {
            background: white;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
        }

        .profile {
            font-weight: bold;
            color: #333;
        }

        /* ================= Layout ================= */

        .dashboard-container {
            display: flex;
        }

        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
            padding: 30px;
        }

        /* ================= Header ================= */

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .new-btn {
            background: #2563eb;
            color: white;
            padding: 12px 22px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            display: inline-block;
        }

        /* ================= Invoice Details ================= */

        .invoice-details {
            background: white;
            padding: 25px;
            border-radius: 12px;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            margin-bottom: 25px;
        }

        .field label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .field input,
        .field select {
            width: 100%;
            padding: 11px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
        }

        .readonly-field {
            background: #f3f4f6;
            color: #666;
            cursor: not-allowed;
        }

        .status-box {
            width: 100%;
            padding: 12px;
            background: #e8f7ee;
            color: #198754;
            border: 1px solid #b7e4c7;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
        }

        /* ================= Invoice Section ================= */

        .invoice-section {
            display: flex;
            gap: 25px;
            align-items: flex-start;
            margin-top: 25px;
        }

        .invoice-items {
            flex: 3;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            margin-bottom: 25px;
        }

        .invoice-summary {
            flex: 1;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .invoice-items,
        .invoice-summary {
            min-height: 350px;
        }

        .invoice-items h2,
        .invoice-summary h2 {
            margin-bottom: 20px;
            color: #0b1f4d;
            font-size: 22px;
        }

        .invoice-items table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-items th {
            background: #0b1f4d;
            color: white;
            padding: 14px;
            text-align: left;
        }

        .invoice-items td {
            padding: 14px;
            border-bottom: 1px solid #eee;
        }

        .invoice-items input {
            width: 70px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .add-item {
            margin-top: 20px;
            padding: 10px 18px;
            background: white;
            border: 1px solid #2563eb;
            color: #2563eb;
            border-radius: 8px;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
        }

        /* ================= Summary ================= */

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 15px 0;
            font-size: 16px;
        }

        .summary-total {
            font-size: 20px;
            color: #0b1f4d;
            font-weight: bold;
        }

        /* ================= Preview ================= */

        .preview-box {
            background: white;
            padding: 25px;
            margin-top: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .preview-box h2 {
            color: #0b1f4d;
            margin-bottom: 20px;
        }

        .invoice-preview {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .invoice-title {
            text-align: center;
            margin-left: -40px;
        }

        .invoice-title h3 {
            color: #2563eb;
            font-size: 30px;
            margin-bottom: 8px;
        }

        .invoice-title p {
            font-size: 15px;
            margin: 3px 0;
            color: #333;
        }

        .invoice-title strong {
            font-size: 15px;
        }

        .preview-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .preview-box table th {
            background: #0b1f4d;
            color: white;
            padding: 12px;
        }

        .preview-box table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .preview-total {
            text-align: right;
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
            color: #0b1f4d;
        }

        .preview-content {
            display: flex;
            gap: 25px;
            align-items: flex-end;
        }

        .preview-page {
            flex: 3;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 25px;
        }

        .preview-side {
            flex: 1;
        }

        .preview-side .new-btn {
            width: 100%;
            margin-bottom: 10px;
            background: #0b1f4d;
            color: #f3f4f6;
        }

        .company {
            width: 260px;
        }

        .company-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .company-logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #0b1f4d;
        }

        .company-name h2 {
            margin: 0;
            color: #0b1f4d;
            font-size: 20px;
            font-weight: 700;
        }

        .company p {
            margin: 3px 0;
            font-size: 13px;
            color: #555;
            line-height: 1.4;
        }

        .customer {
            text-align: left;
        }

        .customer h3 {
            font-size: 24px;
            color: #0b1f4d;
            margin-bottom: 8px;
        }

        .customer p {
            font-size: 15px;
            margin: 0;
        }

        @media print {

            body {
                background: white;
            }

            .sidebar,
            .invoice-header,
            .invoice-details,
            .invoice-section,
            .preview-side,
            .preview-box h2 {
                display: none;
            }

            .main-content {
                margin: 0;
                width: 100%;
                padding: 0;
            }

            .preview-box {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }

            .invoice-preview {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }

            .company {
                width: 32%;
                flex-shrink: 0;
            }

            .invoice-title {
                width: 36%;
                text-align: center;
                margin-left: -40px;
            }

            .customer {
                width: 32%;
                text-align: right;
                flex-shrink: 0;
            }

            div[style*="text-align:right"] {
                display: none !important;
            }

            .company-name {
                display: block !important;
                visibility: visible !important;
            }

            .company-name h2 {
                display: block !important;
                visibility: visible !important;
                color: #000 !important;
                font-size: 20px !important;
                font-weight: bold !important;
                margin: 0 !important;
            }

        }
    </style>

</head>

<body>

    <div class="dashboard-container">

        <?php include "sidebar.php"; ?>

        <div class="main-content">

            <div class="invoice-header">

                <h1>Billing / Invoice</h1>

                <a href="new_invoice.php" class="new-btn">
                    <i class="fa-solid fa-plus"></i>
                    New Invoice
                </a>

            </div>

            <form action="save_invoice.php" method="POST">

                <div class="invoice-details">

                    <div class="field">
                        <label>Invoice No.</label>
                        <input type="text" value="<?php echo $next_invoice; ?>" readonly class="readonly-field">
                    </div>

                    <div class="field">
                        <label>Customer Name</label>

                        <select name="customer_id" required onchange="saveSession()">

                            <option>Select Customer</option>

                            <?php

                            $customer_query = mysqli_query($conn, "SELECT * FROM customers");

                            while ($customer = mysqli_fetch_assoc($customer_query)) {

                            ?>

                                <option
                                    value="<?php echo $customer['customer_id']; ?>"
                                    <?php if ($selected_customer == $customer['customer_id']) echo "selected"; ?>>
                                    <?php echo $customer['customer_name']; ?>
                                </option>
                            <?php } ?>

                        </select>

                    </div>

                    <div class="field">
                        <label>Invoice Date</label>
                        <input type="date" name="invoice_date" value="<?php echo $selected_date; ?>" required onchange="saveSession()">
                    </div>

                    <div class="field">
                        <label>Payment Method</label>

                        <select name="payment_method" onchange="saveSession()">

                            <option value="Cash" <?php if ($selected_payment == "Cash") echo "selected"; ?>>
                                Cash
                            </option>

                            <option value="Online" <?php if ($selected_payment == "Online") echo "selected"; ?>>
                                Online
                            </option>

                            <option value="Credit" <?php if ($selected_payment == "Credit") echo "selected"; ?>>
                                Credit
                            </option>

                        </select>

                    </div>

                    <div class="field">
                        <label>Status</label>

                        <div class="status-box">
                            Paid
                        </div>

                    </div>

                </div>

                <?php

                $item_query = mysqli_query($conn, "
            SELECT invoice_items.*, products.paper_name, products.gsm, products.size
            FROM invoice_items
            INNER JOIN products
            ON invoice_items.product_id = products.id
            WHERE invoice_items.invoice_id = 0
            ");

                ?>

                <div class="invoice-section">

                    <div class="invoice-items">

                        <h2>Invoice Items</h2>

                        <table>

                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>GSM</th>
                                <th>Size</th>
                                <th>Qty</th>
                                <th>Rate (₹)</th>
                                <th>Amount (₹)</th>
                            </tr>

                            <?php
                            $i = 1;

                            while ($item = mysqli_fetch_assoc($item_query)) {
                            ?>

                                <tr>

                                    <td><?php echo $i++; ?></td>

                                    <td><?php echo $item['paper_name']; ?></td>

                                    <td><?php echo $item['gsm']; ?></td>

                                    <td><?php echo $item['size']; ?></td>

                                    <td><?php echo $item['quantity']; ?></td>

                                    <td>₹<?php echo $item['rate']; ?></td>

                                    <td>₹<?php echo $item['amount']; ?></td>

                                </tr>

                            <?php
                            }
                            ?>

                        </table>

                        <a href="add_item.php" class="add-item">
                            <i class="fa-solid fa-plus"></i>
                            Add Item
                        </a>

                    </div>

                    <div class="invoice-summary">

                        <h2>Invoice Summary</h2>

                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="subtotal">
                                ₹<?php echo number_format($subtotal, 2); ?>
                            </span>
                        </div>

                        <div class="summary-row">
                            <span>Discount</span>
                            <span>₹<?php echo number_format($discount, 2); ?></span>
                        </div>

                        <div class="summary-row">
                            <span>GST (%)</span>

                            <input
                                type="number"
                                name="tax"
                                id="gst"
                                value="0"
                                min="0"
                                max="100"
                                step="0.01"
                                style="width:80px;padding:6px;text-align:center;"
                                oninput="calculateTotal()">
                        </div>

                        <div class="summary-row">
                            <span>GST Amount</span>
                            <span id="gstAmount">₹0.00</span>
                        </div>

                        <hr>

                        <div class="summary-row total">
                            <strong>Total</strong>
                            <strong id="grandTotal">₹<?php echo number_format($subtotal, 2); ?></strong>
                        </div>

                    </div>

                </div>

                <div style="margin-top:10px;text-align:right;">

                    <button type="submit" class="new-btn">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Save Invoice
                    </button>

                </div>

                <div class="preview-box">

                    <h2>Invoice Preview</h2>

                    <div class="preview-content">

                        <div class="preview-page">

                            <div class="invoice-preview">

                                <!-- Company Details -->

                                <div class="company">

                                    <div class="company-header">

                                        <img src="images/<?php echo $setting['company_logo']; ?>" class="company-logo">

                                        <div class="company-name">
                                            <h2><?php echo $setting['company_name']; ?></h2>
                                        </div>

                                    </div>
                                    <p><?php echo $setting['company_address']; ?></p>

                                    <p>Phone : <?php echo $setting['company_phone']; ?></p>

                                    <p>GSTIN : 27ABCDE1234F1Z5</p>

                                </div>

                                <!-- Invoice Details -->

                                <div class="invoice-title">

                                    <h3>INVOICE</h3>

                                    <p><strong>Invoice No :</strong> <?php echo $next_invoice; ?></p>

                                    <p><strong>Date :</strong> <?php echo $selected_date; ?></p>

                                </div>

                                <!-- Customer Details -->

                                <div class="customer">

                                    <h4>Bill To:</h4>

                                    <?php

                                    $customer_name = "";

                                    if ($selected_customer != "") {

                                        $customer = mysqli_query(
                                            $conn,
                                            "SELECT customer_name
                     FROM customers
                     WHERE customer_id='$selected_customer'"
                                        );

                                        if (mysqli_num_rows($customer) > 0) {

                                            $row = mysqli_fetch_assoc($customer);

                                            $customer_name = $row['customer_name'];
                                        }
                                    }

                                    ?>

                                    <p><strong><?php echo $customer_name; ?></strong></p>

                                </div>

                            </div>

                            <table>

                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>GSM</th>
                                    <th>Size</th>
                                    <th>Qty</th>
                                    <th>Rate</th>
                                    <th>Amount</th>
                                </tr>

                                <?php

                                $preview_items = mysqli_query($conn, "
        SELECT invoice_items.*, products.paper_name, products.gsm, products.size
        FROM invoice_items
        INNER JOIN products
        ON invoice_items.product_id = products.id
        WHERE invoice_items.invoice_id = 0
    ");

                                $i = 1;

                                while ($row = mysqli_fetch_assoc($preview_items)) {
                                ?>

                                    <tr>

                                        <td><?php echo $i++; ?></td>

                                        <td><?php echo $row['paper_name']; ?></td>

                                        <td><?php echo $row['gsm']; ?></td>

                                        <td><?php echo $row['size']; ?></td>

                                        <td><?php echo $row['quantity']; ?></td>

                                        <td>₹<?php echo number_format($row['rate'], 2); ?></td>

                                        <td>₹<?php echo number_format($row['amount'], 2); ?></td>

                                    </tr>

                                <?php
                                }
                                ?>

                            </table>

                            <div class="preview-total">

                                Grand Total :
                                <span id="previewGrandTotal">
                                    <?php echo number_format($subtotal, 2); ?>
                                </span>

                            </div>
                        </div>

                        <div class="preview-side">

                            <button type="button" class="new-btn" onclick="printInvoice()">Print Invoice</button>

                        </div>

                    </div>

                    <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">

                    <input type="hidden" name="discount" value="<?php echo $discount; ?>">

                    <input type="hidden" id="totalInput" name="total" value="<?php echo $subtotal; ?>">

                    <input type="hidden" name="invoice_no" value="<?php echo $next_invoice; ?>">

            </form>

        </div> <!-- main-content -->
    </div> <!-- dashboard-container -->

    <script>
        function saveSession() {

            let customer = document.querySelector("[name='customer_id']").value;
            let date = document.querySelector("[name='invoice_date']").value;
            let payment = document.querySelector("[name='payment_method']").value;

            fetch("save_session.php", {

                method: "POST",

                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },

                body: "customer_id=" + encodeURIComponent(customer) +
                    "&invoice_date=" + encodeURIComponent(date) +
                    "&payment_method=" + encodeURIComponent(payment)

            });

        }
    </script>

    <script>
        function calculateTotal() {
            let subtotal = <?php echo $subtotal; ?>;

            let gst = parseFloat(document.getElementById("gst").value) || 0;

            let gstAmount = subtotal * gst / 100;

            let total = subtotal + gstAmount;

            document.getElementById("gstAmount").innerHTML =
                "₹" + gstAmount.toFixed(2);

            document.getElementById("grandTotal").innerHTML =
                "₹" + total.toFixed(2);

            document.getElementById("previewGrandTotal").innerHTML =
                "₹" + total.toFixed(2);

            document.getElementById("totalInput").value = total.toFixed(2);
        }

        calculateTotal();

        function printInvoice() {
            window.print();
        }
    </script>

</body>

</html>