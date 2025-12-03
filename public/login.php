<?php
session_start();

// Load DB
require_once dirname(__DIR__) . "/config/db.php";
include "header.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["password"];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);

        if (password_verify($password, $user['password'])) {

            // Store session data
            $_SESSION["user_id"]   = $user["id"];
            $_SESSION["username"]  = $user["username"];
            $_SESSION["email"]     = $user["email"];

            header("Location: index.php");
            exit;

        } else {
            $message = "<div class='alert alert-danger'>Incorrect password.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Account not found.</div>";
    }
}
?>

<h3 class="mb-3">Login</h3>

<?= $message ?>

<form method="POST">
    <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
    <button class="btn btn-primary w-100">Login</button>
</form>

<p class="mt-3">
    Don't have an account? <a href="register.php">Register</a>
</p>

<?php include "footer.php"; ?>
