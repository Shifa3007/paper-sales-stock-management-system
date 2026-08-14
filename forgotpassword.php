<?php
include("database.php");

$show_reset_form = false;
$email = "";

if (isset($_POST['check_email'])) {
    $email = $_POST['email'];

    $query = mysqli_query(
        $conn,
        "SELECT * FROM users WHERE email='$email'"
    );

    if (mysqli_num_rows($query) == 1) {
        $show_reset_form = true;
    } else {
        echo "<script>alert('Email not found!');</script>";
    }
}

if (isset($_POST['reset_password'])) {
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];

    mysqli_query(
        $conn,
        "UPDATE users
         SET password='$new_password'
         WHERE email='$email'"
    );

    echo "<script>
    alert('Password Updated Successfully!');
    window.location='login.html';
    </script>";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Forgot Password</title>

    <link rel="stylesheet" href="forgotpassword.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

    <div class="container">

        <!-- LEFT PANEL -->

        <div class="left">

            <div class="logo">
                <i class="fas fa-layer-group"></i>
            </div>

            <h1>Paper Sales and Stock Management System</h1>

            <p>
                Manage your paper stock,<br>
                customers, orders and invoices<br>
                in one place.
            </p>

            <div class="bottom-icons">

                <i class="fas fa-box"></i>

                <i class="fas fa-scroll"></i>

                <i class="fas fa-copy"></i>

            </div>

        </div>



        <!-- RIGHT PANEL -->

        <div class="right">

            <div class="lock">

                <i class="fas fa-lock"></i>

            </div>

            <h2>
                <?php echo $show_reset_form ? "Reset Password" : "Forgot Password?"; ?>
            </h2>

            <p class="subtitle">
                <?php
                echo $show_reset_form
                    ? "Enter your new password below."
                    : "No worries! Enter your registered email and we'll help you reset your password.";
                ?>
            </p>

            <?php if (!$show_reset_form) { ?>

                <form action="" method="POST">

                    <label>Email Address</label>

                    <div class="input-box">

                        <i class="fas fa-envelope"></i>

                        <input
                            type="email"
                            name="email"
                            placeholder="Enter your email address"
                            required>

                    </div>

                    <button type="submit" name="check_email">

                        <i class="fas fa-paper-plane"></i>

                        Send Reset Link

                    </button>

                </form>

            <?php } else { ?>

                <form action="" method="POST">

                    <input
                        type="hidden"
                        name="email"
                        value="<?php echo $email; ?>">

                    <label>New Password</label>

                    <div class="input-box">

                        <i class="fas fa-lock"></i>

                        <input
                            type="password"
                            name="new_password"
                            placeholder="Enter new password"
                            required>

                    </div>

                    <button type="submit" name="reset_password">

                        <i class="fas fa-key"></i>

                        Reset Password

                    </button>

                </form>

            <?php } ?>

            <div class="or">

                <span></span>

                or

                <span></span>

            </div>

            <button
                class="back"
                onclick="window.location.href='login.html'">

                <i class="fas fa-arrow-left"></i>

                Back to Login

            </button>

            <footer>

                © 2025 Paper Sales and Stock Management System.<br>

                All rights reserved.

            </footer>

        </div>

    </div>

</body>

</html>