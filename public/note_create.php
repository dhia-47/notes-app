<?php
session_start();
require_once dirname(__DIR__) . "/config/db.php";
include "header.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title    = mysqli_real_escape_string($conn, $_POST["title"]);
    $category = mysqli_real_escape_string($conn, $_POST["category"]);
    $content  = mysqli_real_escape_string($conn, $_POST["content"]);
    $user_id  = $_SESSION["user_id"];

    $sql = "INSERT INTO notes (user_id, title, category, content)
            VALUES ($user_id, '$title', '$category', '$content')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit;
    } else {
        $message = "<div class='alert alert-danger'>Error creating note.</div>";
    }
}
?>

<h3>Create New Note</h3>

<?= $message ?>

<form method="POST">
    <input type="text" name="title" class="form-control mb-3" placeholder="Title" required>

    <input type="text" name="category" class="form-control mb-3" placeholder="Category">

    <textarea name="content" class="form-control mb-3" rows="6" placeholder="Note content..."></textarea>

    <button class="btn btn-success w-100">Save Note</button>
</form>

<?php include "footer.php"; ?>
