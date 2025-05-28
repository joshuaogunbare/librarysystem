<?php
$host = "localhost";
$dbname = "bookdb";
$username = "root";
$password = "";

// Create  new database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>