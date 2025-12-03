<?php
session_start();
require_once dirname(__DIR__) . "/config/db.php";
include "header.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["id"])) {
    die("Invalid note ID.");
}

$id = (int)$_GET["id"];
$user_id = $_SESSION["user_id"];

// Fetch note
$note = mysqli_query($conn, "SELECT * FROM notes WHERE id=$id AND user_id=$user_id");

if (mysqli_num_rows($note) === 0) {
    die("<div class='alert alert-danger'>Note not found.</div>");
}

$note = mysqli_fetch_assoc($note);

// Handle update
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title    = mysqli_real_escape_string($conn, $_POST["title"]);
    $category = mysqli_real_escape_string($conn, $_POST["category"]);
    $content  = mysqli_real_escape_string($conn, $_POST["content"]);

    $sql = "UPDATE notes 
            SET title='$title', category='$category', content='$content', updated_at=NOW()
            WHERE id=$id AND user_id=$user_id";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit;
    } else {
        $message = "<div class='alert alert-danger'>Error updating note.</div>";
    }
}
?>

<h3>Edit Note</h3>

<?= $message ?>

<form method="POST">

    <input type="text" name="title" class="form-control mb-3" 
           value="<?= $note['title'] ?>" required>

    <input type="text" name="category" class="form-control mb-3" 
           value="<?= $note['category'] ?>">

    <textarea name="content" class="form-control mb-3" rows="6"><?= $note['content'] ?></textarea>

    <button class="btn btn-primary w-100">Update Note</button>
</form>

<?php include "footer.php"; ?>
