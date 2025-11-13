<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out</title>
    <style>
        body { 
            font-family: Arial; 
            background: #f0f8ff; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .message { 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            text-align: center; 
            box-shadow: 0 0 10px #ccc; 
        }
    </style>
</head>
<body>
    <div class="message">
        <h2>✅ Successfully Logged Out</h2>
        <p>You have been logged out of the system.</p>
        <a href="Adminlogin.php" style="color: #0066cc;">Click here to login again</a>
         <a href="Homepage.html" class="back-btn">⬅️ Go to Homepage</a>
    </div>
</body>
</html>