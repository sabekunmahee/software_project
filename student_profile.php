<?php
session_start();
include "dbconnection.php";

if (!isset($_SESSION['student_name'])) {
    header("Location: StudentLogin.php");
    exit();
}

$student = $_SESSION['student_name'];
$query = "SELECT * FROM studentprofile WHERE student_name='$student'";
$result = $conn->query($query);
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile</title>
<style>
body { font-family: Arial, sans-serif; background:#f2f2f2; }
.container { width: 60%; margin: 40px auto; background: white; padding: 25px; border-radius: 10px; box-shadow:0 0 10px gray; }
h2 { text-align:center; color:#0077cc; }
table { width:100%; margin-top:20px; border-collapse: collapse; }
td { padding:10px; border-bottom:1px solid #ddd; }
.logout { text-align:center; margin-top:20px; }
.logout a { text-decoration:none; background:#ff6b6b; padding:10px 20px; border-radius:10px; color:white; transition:0.3s; }
.logout a:hover { background:#ff4757; }
</style>
</head>
<body>
<div class="container">
<h2>👤 My Profile</h2>
<table>
<tr><td><b>Name</b></td><td><?php echo $data['student_name']; ?></td></tr>
<tr><td><b>Mobile</b></td><td><?php echo $data['mobile_number']; ?></td></tr>
<tr><td><b>Emergency Contact</b></td><td><?php echo $data['emergency_contact']; ?></td></tr>
<tr><td><b>Room Number</b></td><td><?php echo $data['room_number']; ?></td></tr>
</table>

</div>
</body>
</html>