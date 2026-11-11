<?php
$host = "localhost";
$username = "root";
$password = ""; // Leave blank for default XAMPP/WAMP setups
$database = "student_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>