<?php
session_start();

include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// Total Products
$product_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM products");
$product = mysqli_fetch_assoc($product_query);
$total_products = $product['total'];

// Total Stock
$stock_query = mysqli_query($conn, "SELECT SUM(quantity) AS total_stock FROM products");
$stock = mysqli_fetch_assoc($stock_query);
$total_stock = $stock['total_stock'];

// Total Customers
$customer_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM customers");
$customer = mysqli_fetch_assoc($customer_query);
$total_customers = $customer['total'];

// Total Orders
$order_query = mysqli_query($conn, "SELECT COUNT(*) AS total_orders FROM orders");
$order = mysqli_fetch_assoc($order_query);
$total_orders = $order['total_orders'];

$sales_data = [];

$sales_query = mysqli_query(
    $conn,
    "SELECT
        WEEK(order_date) AS week_no,
        SUM(quantity) AS total_sales
     FROM orders
     WHERE MONTH(order_date) = MONTH(CURDATE())
     AND YEAR(order_date) = YEAR(CURDATE())
     GROUP BY WEEK(order_date)
     ORDER BY WEEK(order_date)"
);

while ($row = mysqli_fetch_assoc($sales_query)) {
    $sales_data[] = $row['total_sales'];
}

while (count($sales_data) < 4) {
    $sales_data[] = 0;
}

// Stock Status
$in_stock = mysqli_num_rows(mysqli_query(
    $conn,
    "SELECT * FROM products WHERE quantity > 50"
));

$low_stock = mysqli_num_rows(mysqli_query(
    $conn,
    "SELECT * FROM products WHERE quantity > 0 AND quantity <= 50"
));

$out_stock = mysqli_num_rows(mysqli_query(
    $conn,
    "SELECT * FROM products WHERE quantity = 0"
));

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

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

        .wrapper {
            display: flex;
        }

        /* Sidebar */

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
            border-radius: 10px;
        }

        .sidebar ul li a i {
            margin-right: 12px;
            width: 22px;
        }

        /* Main Content */

        .main {
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
        }

        /* Topbar */

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

        /* Dashboard */

        .content {
            padding: 25px;
            width: 100%;
        }

        .content h1 {
            font-size: 30px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 25px;
        }

        .card h3 {
            color: #666;
            font-size: 16px;
        }

        .card h2 {
            margin-top: 10px;
            color: #0b1f4d;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 20px;
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

        .logo-section {
            border-bottom: 1px solid rgba(255, 255, 255, .15);
            padding-bottom: 20px;
        }

        /* Search Box */

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 13px;
            color: #888;
        }

        .search-box input {
            width: 340px;
            padding: 12px 15px 12px 42px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
        }

        /* Right Side */

        .top-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        /* Admin */

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-icon {
            width: 45px;
            height: 45px;
            background: #2563eb;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
        }

        .admin-info h4 {
            margin: 0;
            font-size: 16px;
        }

        .admin-info p {
            margin: 0;
            color: #777;
            font-size: 13px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .card {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 25px;
            border-radius: 15px;
            background: white;
            box-shadow: 0 8px 18px rgba(0, 0, 0, .08);
        }

        .icon {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 26px;
            color: white;
        }

        .blue .icon {
            background: #3b82f6;
        }

        .green .icon {
            background: #22c55e;
        }

        .orange .icon {
            background: #f59e0b;
        }

        .purple .icon {
            background: #8b5cf6;
        }

        .card h3 {
            margin: 0;
            font-size: 17px;
            color: #555;
        }

        .card h2 {
            margin: 6px 0;
            font-size: 34px;
            color: #111827;
        }

        .card p {
            margin: 0;
            font-size: 14px;
            color: #888;
        }

        .dashboard-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-top: 25px;
        }

        .chart-box {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            min-height: 360px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, .08);
        }

        .chart-box h3 {
            margin-bottom: 20px;
            color: #1f2937;
        }

        .chart-placeholder {
            height: 280px;
            border: 2px dashed #d6d6d6;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #777;
            font-size: 18px;
        }

        .recent-orders {
            background: #fff;
            margin-top: 25px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, .08);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-header h3 {
            font-size: 20px;
            color: #1f2937;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: #0b1f4d;
            padding: 15px;
            text-align: left;
            color: white;
            font-weight: 600;
        }

        table td {
            padding: 15px;
            border-top: 1px solid #eee;
        }

        .empty-data {
            text-align: center;
            color: #888;
            padding: 40px;
        }

        #salesChart {
            width: 100% !important;
            height: 300px !important;
        }

        #stockChart {
            width: 220px !important;
            height: 220px !important;
        }

        .stock-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 25px;
            height: 300px;
        }

        .chart-container {
            width: 220px;
            height: 220px;
            position: relative;
        }

        .chart-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .chart-center h2 {
            margin: 0;
            font-size: 30px;
            color: #1f2937;
        }

        .chart-center p {
            margin-top: 5px;
            color: #777;
            font-size: 14px;
        }

        .stock-legend {
            flex: 1;
        }

        .legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .green-dot {
            background: #22c55e;
        }

        .orange-dot {
            background: #f59e0b;
        }

        .red-dot {
            background: #ef4444;
        }
    </style>

</head>

<body>

    <div class="wrapper">
        <?php include 'sidebar.php'; ?>

        <!-- Main -->
        <div class="main">

            <div class="topbar">

                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search anything...">
                </div>

                <div class="top-right">

                    <div class="admin-profile">

                        <div class="admin-icon">
                            <i class="fas fa-user"></i>
                        </div>

                        <div class="admin-info">
                            <h4><?php echo ucfirst($_SESSION['username']); ?></h4>
                            <p>Administrator</p>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Content -->
            <div class="content">

                <h1>Dashboard</h1>

                <div class="cards">

                    <div class="card blue">
                        <div class="icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <h3>Total Products</h3>
                            <h2><?php echo $total_products; ?></h2>
                            <p>All Paper Products</p>
                        </div>
                    </div>

                    <div class="card green">
                        <div class="icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <h3>Total Stock</h3>
                            <h2><?php echo $total_stock; ?></h2>
                            <p>In Stock</p>
                        </div>
                    </div>

                    <div class="card orange">
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h3>Total Customers</h3>
                            <h2><?php echo $total_customers; ?></h2>
                            <p>Registered Customers</p>
                        </div>
                    </div>

                    <div class="card purple">
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <h3>Total Orders</h3>
                            <h2><?php echo $total_orders; ?></h2>
                            <p>Total Orders</p>
                        </div>
                    </div>

                </div>

                <div class="dashboard-row">

                    <div class="chart-box">
                        <h3>Sales Overview (This Month)</h3>

                        <canvas id="salesChart"></canvas>

                    </div>

                    <div class="chart-box">
                        <h3>Stock Status</h3>

                        <div class="stock-wrapper">

                            <div class="chart-container">
                                <canvas id="stockChart"></canvas>

                                <div class="chart-center">
                                    <h2><?php echo $total_stock; ?></h2>
                                    <p>Total Stock</p>
                                </div>
                            </div>

                            <div class="stock-legend">
                                <div class="legend-item">
                                    <span class="dot green-dot"></span>
                                    <span>In Stock</span>
                                    <strong><?php echo $in_stock; ?></strong>
                                </div>

                                <div class="legend-item">
                                    <span class="dot orange-dot"></span>
                                    <span>Low Stock</span>
                                    <strong><?php echo $low_stock; ?></strong>
                                </div>

                                <div class="legend-item">
                                    <span class="dot red-dot"></span>
                                    <span>Out of Stock</span>
                                    <strong><?php echo $out_stock; ?></strong>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="recent-orders">

                    <div class="section-header">
                        <h3>Recent Orders</h3>
                    </div>

                    <table>

                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php

                            $order_result = mysqli_query(
                                $conn,
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
    ON orders.product_id = products.id

    ORDER BY orders.order_date DESC

    LIMIT 5"
                            );


                            while ($row = mysqli_fetch_assoc($order_result)) {

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

                        </tbody>

                    </table>

                </div>

            </div> <!-- content -->

        </div> <!-- main -->

    </div> <!-- wrapper -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const salesChart = new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Sales',
                    data: [<?php echo implode(',', $sales_data); ?>],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,0.15)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#2563eb'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const stockChart = new Chart(document.getElementById('stockChart'), {
            type: 'doughnut',
            data: {
                labels: ['In Stock', 'Low Stock', 'Out of Stock'],
                datasets: [{
                    data: [
                        <?php echo $in_stock; ?>,
                        <?php echo $low_stock; ?>,
                        <?php echo $out_stock; ?>
                    ],
                    backgroundColor: [
                        '#22c55e',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>

</body>

</html>