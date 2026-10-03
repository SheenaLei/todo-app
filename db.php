<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "todo_db";

// Connect to MySQL
$conn = new mysqli($host, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Auto-create database & table
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

$tableQuery = "CREATE TABLE IF NOT EXISTS tasks (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    task VARCHAR(255) NOT NULL,
    description TEXT,
    deadline_date DATE,
    deadline_time TIME,
    status VARCHAR(50) DEFAULT 'Pending'
)";
$conn->query($tableQuery);
?>