
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Accounts Login - Hostel Management</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to right, #00b09b, #96c93d);
    margin:0;
    padding:0;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.login-container {
    background: #fff;
    padding: 40px 50px;
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    width: 350px;
    text-align:center;
}

.login-container img {
    width: 80px;
    margin-bottom: 20px;
}

.login-container h2 {
    margin-bottom: 25px;
    color: #333;
}

.login-container input[type="text"],
.login-container input[type="password"] {
    width: 100%;
    padding: 12px 15px;
    margin: 10px 0;
    border-radius: 5px;
    border: 1px solid #ccc;
    outline:none;
    font-size: 16px;
}

.login-container input[type="submit"] {
    width: 100%;
    padding: 12px;
    border:none;
    border-radius:5px;
    background: #00b09b;
    color:#fff;
    font-size: 18px;
    cursor:pointer;
    transition: 0.3s;
}

.login-container input[type="submit"]:hover {
    background: #028a77;
}

.error {
    color: red;
    margin-bottom: 10px;
    font-size: 14px;
}

.demo-credentials {
    margin-top: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
    font-size: 14px;
    text-align: left;
}

.demo-credentials h4 {
    margin: 0 0 10px 0;
    color: #333;
}
</style>
</head>
<body>

<div class="login-container">
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Accounts Logo">
    <h2>Accounts Login</h2>
    
    <?php if (isset($_GET['error'])): ?>
        <p class="error"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>
    
    <form action="accounts_authenticate.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="login" value="Login">
    </form>

   
</div>

</body>
</html>