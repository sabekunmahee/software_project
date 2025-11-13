<?php
include 'dbconnection.php';
// Get input
$username = $_POST['username'];
$password = $_POST['password'];

// Validate credentials
$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // User found
    session_start();
    $_SESSION['username'] = $username;
    header("Location: admindashboard.html");
} else {
    // Invalid credentials
    header("Location: adminlogin.php?error=Invalid username or password");
}

$conn->close();
?>