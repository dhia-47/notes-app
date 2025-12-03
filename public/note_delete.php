<?php
session_start();
require_once dirname(__DIR__) . "/config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["id"])) {
    die("Invalid request.");
}

$id = (int)$_GET["id"];
$user_id = $_SESSION["user_id"];

// Delete only if the note belongs to the user
mysqli_query($conn, "DELETE FROM notes WHERE id=$id AND user_id=$user_id");

header("Location: index.php");
exit;
