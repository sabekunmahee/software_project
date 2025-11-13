<?php
session_start();
include "dbconnection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    
    $sql = "SELECT * FROM accounts WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['accounts_id'] = $user['id'];
        $_SESSION['accounts_name'] = $user['full_name'];
        $_SESSION['accounts_username'] = $user['username'];
        
        // FIXED: Redirect to correct dashboard
        header("Location: AccountDashboard.html");
        exit();
    } else {
        // FIXED: Redirect back to login with error
        header("Location: Accountslogin.php?error=Invalid username or password");
        exit();
    }
}

$conn->close();
?>