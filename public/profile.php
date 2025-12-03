<?php 
include "header.php"; 
include "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$user = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id")
);

$message = "";

// --- Update username & email ---
if (isset($_POST["update_profile"])) {
    $new_username = mysqli_real_escape_string($conn, $_POST["username"]);
    $new_email    = mysqli_real_escape_string($conn, $_POST["email"]);

    // Check if username/email already exists (but not this user)
    $check = mysqli_query($conn, 
        "SELECT * FROM users 
         WHERE (username='$new_username' OR email='$new_email') 
         AND id != $user_id"
    );

    if (mysqli_num_rows($check) > 0) {
        $message = "<div class='alert alert-danger'>Username or Email already taken.</div>";
    } else {
        mysqli_query($conn, 
            "UPDATE users 
             SET username='$new_username', email='$new_email' 
             WHERE id=$user_id"
        );

        $_SESSION['username'] = $new_username;
        $message = "<div class='alert alert-success'>Profile updated successfully!</div>";
    }
}

// --- Update password ---
if (isset($_POST["update_password"])) {
    $old_pass = $_POST["old_password"];
    $new_pass = $_POST["new_password"];
    $confirm  = $_POST["confirm_password"];

    if (!password_verify($old_pass, $user["password"])) {
        $message = "<div class='alert alert-danger'>Old password is incorrect.</div>";
    } elseif ($new_pass !== $confirm) {
        $message = "<div class='alert alert-warning'>New passwords do not match.</div>";
    } else {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);

        mysqli_query($conn, 
            "UPDATE users SET password='$hashed' WHERE id=$user_id"
        );

        $message = "<div class='alert alert-success'>Password changed successfully!</div>";
    }
}
?>

<h3>My Profile</h3>
<hr>

<?= $message ?>

<div class="row">
    <div class="col-md-6">
        <div class="card p-4 shadow-sm mb-4">
            <h4>Edit Profile</h4>
            <form method="post">
                <label>Username</label>
                <input type="text" name="username" value="<?= $user['username'] ?>" 
                       class="form-control mb-3" required>

                <label>Email</label>
                <input type="email" name="email" value="<?= $user['email'] ?>" 
                       class="form-control mb-3" required>

                <button name="update_profile" class="btn btn-primary w-100">Save Changes</button>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card p-4 shadow-sm mb-4">
            <h4>Change Password</h4>
            <form method="post">
                <label>Old Password</label>
                <input type="password" name="old_password" class="form-control mb-3" required>

                <label>New Password</label>
                <input type="password" name="new_password" class="form-control mb-3" required>

                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control mb-3" required>

                <button name="update_password" class="btn btn-warning w-100">
                    Change Password
                </button>
            </form>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
