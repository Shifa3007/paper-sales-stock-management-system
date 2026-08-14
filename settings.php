<?php
session_start();
include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

if(isset($_POST['save_company']))
{
    $company_name = $_POST['company_name'];
    $company_address = $_POST['company_address'];
    $company_phone = $_POST['company_phone'];

    if($_FILES['company_logo']['name'] != "")
{
    $logo_name = $_FILES['company_logo']['name'];
    $temp_name = $_FILES['company_logo']['tmp_name'];

    move_uploaded_file(
        $temp_name,
        "images/" . $logo_name
    );

    mysqli_query($conn,"
        UPDATE settings
        SET company_logo='$logo_name'
        WHERE id=1
    ");
}

    mysqli_query($conn,"
        UPDATE settings
        SET
        company_name='$company_name',
        company_address='$company_address',
        company_phone='$company_phone'
        WHERE id=1
    ");

    echo "<script>alert('Company Information Updated Successfully');</script>";
}

if(isset($_POST['change_password']))
{
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $username = $_SESSION['username'];

    $user_query = mysqli_query(
        $conn,
        "SELECT * FROM users
         WHERE username='$username'"
    );

    $user = mysqli_fetch_assoc($user_query);

    if($user['password'] != $current_password)
    {
        echo "<script>alert('Current Password Incorrect');</script>";
    }
    elseif($new_password != $confirm_password)
    {
        echo "<script>alert('Passwords Do Not Match');</script>";
    }
    else
    {
        mysqli_query(
            $conn,
            "UPDATE users
             SET password='$new_password'
             WHERE username='$username'"
        );

        echo "<script>alert('Password Changed Successfully');</script>";
    }
}

$setting_query = mysqli_query($conn, "SELECT * FROM settings WHERE id=1");
$setting = mysqli_fetch_assoc($setting_query);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Settings</title>

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

        /* Logo */

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

        /* Layout */

        .dashboard-container {
            display: flex;
        }

        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
            padding: 30px;
        }

        .invoice-header {
            margin-bottom: 25px;
        }

        .invoice-header h1 {
            color: #0b1f4d;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .settings-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .settings-card h2 {
            color: #0b1f4d;
            margin-bottom: 20px;
        }

        .field {
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .field input,
        .field textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
        }

        .field textarea {
            height: 100px;
            resize: none;
        }

        .new-btn {
            background: #2563eb;
            color: white;
            padding: 12px 22px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
        }

        .new-btn:hover {
            background: #1d4ed8;
        }

        hr {
            margin: 30px 0;
            border: none;
            border-top: 1px solid #ddd;
        }
    </style>

</head>

<body>

    <div class="dashboard-container">

        <?php include "sidebar.php"; ?>

        <div class="main-content">

            <div class="invoice-header">
                <h1>Settings</h1>
            </div>

        <form method="POST" enctype="multipart/form-data">

            <div class="settings-grid">

                <!-- Company Card -->
                <div class="settings-card">

                    <h2>Company Information</h2>

                    <div class="field">
                        <label>Company Name</label>
                        <input type="text" name="company_name" value="<?php echo $setting['company_name']; ?>">
                    </div>

                    <div class="field">
                        <label>Company Address</label>
                        <textarea name="company_address"><?php echo $setting['company_address']; ?></textarea>
                    </div>

                    <div class="field">
                        <label>Phone Number</label>
                        <input type="text" name="company_phone" value="<?php echo $setting['company_phone']; ?>">
                    </div>

                    <div class="field">
                        <label>Company Logo</label>
                        <input type="file" name="company_logo">
                    </div>

                    <button type="submit" name="save_company" class="new-btn">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Save Info
                    </button>

                </div>

                <!-- Password Card -->
                <div class="settings-card">

                    <h2>Change Password</h2>

                    <div class="field">
                        <label>Current Password</label>
                        <input type="password" name="current_password">
                    </div>

                    <div class="field">
                        <label>New Password</label>
                        <input type="password" name="new_password">
                    </div>

                    <div class="field">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password">
                    </div>

                    <button type="submit" name="change_password" class="new-btn">
                        Change Password
                    </button>

                </div>

            </div>

        </form>

        </div>

    </div>

</body>

</html>