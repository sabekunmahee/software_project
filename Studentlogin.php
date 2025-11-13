<?php
session_start();
include "dbconnection.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $student_name = trim($_POST['student_name']);
  $password = trim($_POST['password']);

  $sql = "SELECT * FROM student_login WHERE student_name='$student_name' AND password='$password'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $_SESSION['student_name'] = $student_name;
    header("Location: StudentDashboard.html");
    exit();
  } else {
    $message = "<div class='error'>Invalid Name or Password!</div>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Login</title>
<style>
* { 
    margin:0; 
    padding:0; 
    box-sizing:border-box; 
    font-family: Arial, sans-serif; 
}

body {
    background: #f5f5f5;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    color: #333;
}

.login-container {
    width: 400px;
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    text-align: center;
}

.login-img {
    width: 80px;
    height: 80px;
    margin-bottom: 20px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #0066cc;
}

h2 {
    margin-bottom: 25px;
    font-size: 1.8em;
    color: #0066cc;
}

.input-box { 
    margin-bottom: 20px; 
    text-align: left;
}

.input-box label {
    display: block;
    margin-bottom: 5px;
    color: #555;
    font-weight: bold;
}

.input-box input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    outline: none;
    font-size: 1em;
    background: white;
    color: #333;
}

.input-box input:focus {
    border-color: #0066cc;
    box-shadow: 0 0 5px rgba(0,102,204,0.3);
}

button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 5px;
    background: #0066cc;
    color: white;
    font-size: 1.1em;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #0055aa;
}

.error {
    background: #ffe6e6;
    color: #cc0000;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    border: 1px solid #ffcccc;
}

footer {
    position: absolute;
    bottom: 15px;
    width: 100%;
    text-align: center;
    color: #666;
    font-size: 0.9em;
}
</style>
</head>
<body>

<div class="login-container">
    <!-- Student Image -->
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Student" class="login-img">
    
    <h2>Student Login</h2>

    <?php echo $message; ?>

    <form method="POST">
        <div class="input-box">
            <label for="student_name">Student Name</label>
            <input type="text" name="student_name" placeholder="Enter your name" required>
        </div>
        <div class="input-box">
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Enter password" required>
        </div>
        <button type="submit">Login</button>
    </form>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> Hostel Management System
</footer>

</body>
</html>