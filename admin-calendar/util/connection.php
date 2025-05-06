<?php


$host = "localhost:3307"; // Replace with your database host
$username = "root"; // Replace with your database username
$password = "1234"; // Replace with your database password
$database = "calendar"; // Replace with your database name

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; //Remove this after testing.
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die(); // Stop script execution on connection failure.  Important for security.
}
?>