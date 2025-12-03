<?php
// Define the project root once
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__)); 
}

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "notes_app";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
