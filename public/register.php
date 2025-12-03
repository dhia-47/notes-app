<?php 
// Load DB connection (only once)
include "../config/db.php"; 

// Load header
include "header.php"; 

$message = "";

// Ensure DB loaded
if (!isset($conn)) {
    die("db.php NOT LOADED — include path wrong.");
}

// If form submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $email    = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Check if username or email already exists
    $check = mysqli_query($conn, 
        "SELECT * FROM users WHERE username='$username' OR email='$email'"
    );

    if (mysqli_num_rows($check) > 0) {
        $message = "<div class='alert alert-danger'>Username or Email already taken.</div>";
    } else {

        $sql = "INSERT INTO users (username, email, password) 
                VALUES ('$username', '$email', '$password')";

        if (mysqli_query($conn, $sql)) {
            $message = "<div class='alert alert-success'>Account created! You can login.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error creating account.</div>";
        }
    }
}
?>

<h3 class="mb-3">Register</h3>

<?= $message ?>

<form method="post">
    <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
    <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
    <button class="btn btn-primary w-100">Register</button>
</form>

<p class="mt-3">Already have an account?
    <a href="login.php">Login</a>
</p>

<?php include "footer.php"; ?>
