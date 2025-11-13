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
        .login-btn, .back-btn {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 1.1em;
            margin-top: 20px;
            transition: 0.3s;
        }
        .login-btn {
            background: #0066cc;
        }
        .login-btn:hover {
            background: #0055aa;
            transform: scale(1.05);
        }
        .back-btn {
            background: #28a745;
            margin-left: 10px;
        }
        .back-btn:hover {
            background: #218838;
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
        <p>You have been logged out of Accounts Panel.</p>
        <a href="Accountslogin.php" class="login-btn">Login Again</a>
        <a href="Homepage.html" class="back-btn">⬅️ Go to Homepage</a>
    </div>
</body>
</html>
