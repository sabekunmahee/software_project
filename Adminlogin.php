<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - Hostel Management</title>
<link rel="stylesheet" href="../Homecss.css">
<style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to right, #6a11cb, #2575fc);
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

.login-container input[type="email"],
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
    background: #2575fc;
    color:#fff;
    font-size: 18px;
    cursor:pointer;
    transition: 0.3s;
}

.login-container input[type="submit"]:hover {
    background: #6a11cb;
}

.error {
    color: red;
    margin-bottom: 10px;
    font-size: 14px;
}
</style>
</head>
<body>

<div class="login-container">
    <img src="password.jpeg" alt="Admin Logo">
    <h2>Admin Login</h2>
    <?php if (isset($_GET['error'])): ?>
            <p class="error"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>
    <form action="authenticate.php" method="POST">
        <input type="email" name="username" placeholder="username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="login" value="Login">
    </form>
</div>

</body>
</html>
