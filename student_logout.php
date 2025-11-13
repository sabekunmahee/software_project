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
            padding: 40px; 
            border-radius: 10px; 
            text-align: center; 
            box-shadow: 0 0 10px #ccc; 
        }
        .login-btn {
            display: inline-block;
            background: #0066cc;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 1.1em;
            transition: 0.3s;
        }
        .login-btn:hover {
            background: #0055aa;
            transform: scale(1.05);
        }
        h2 {
            color: #28a745;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="message">
        <h2>✅ Successfully Logged Out</h2>
        <p>You have been logged out of Student Panel.</p>
        <a href="Studentlogin.php" class="login-btn">Login Again</a>
         <a href="Homepage.html" class="back-btn">⬅️ Go to Homepage</a>
    </div>
</body>
</html>