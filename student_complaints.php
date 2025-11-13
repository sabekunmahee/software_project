<?php
include "dbconnection.php";
session_start();
if(!isset($_SESSION['student_name'])){
  header("Location: StudentLogin.php");
  exit();
}

$message = "";
if($_SERVER["REQUEST_METHOD"]=="POST"){
  $student = $_SESSION['student_name'];
  $complaint = $conn->real_escape_string($_POST['complaint']);
  $sql = "INSERT INTO complaintstudent (student_name, complaint_text) VALUES ('$student', '$complaint')";
  if($conn->query($sql)){
    $message = "<div style='color:green; padding:10px; background:#e6ffe6; border-radius:5px;'>Complaint submitted successfully!</div>";
  } else {
    $message = "<div style='color:red; padding:10px; background:#ffe6e6; border-radius:5px;'>Error: ".$conn->error."</div>";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>My Complaints</title>
<style>
body { 
    font-family:Arial; 
    background:#f2f2f2; 
    margin:0;
    padding:0;
}
.container { 
    width:80%; 
    margin:40px auto; 
    background:white; 
    padding:30px; 
    border-radius:10px; 
    box-shadow:0 0 10px rgba(0,0,0,0.1); 
}
h2 { 
    color:#0066cc; 
    text-align:center; 
    margin-bottom:20px; 
}
textarea { 
    width:100%; 
    height:120px; 
    padding:12px; 
    border:1px solid #ddd; 
    border-radius:5px; 
    font-size:14px;
    resize:vertical;
}
button { 
    padding:12px 25px; 
    background:#0066cc; 
    border:none; 
    color:white; 
    border-radius:5px; 
    cursor:pointer; 
    font-size:16px;
}
button:hover {
    background:#0055aa;
}
table { 
    width:100%; 
    margin-top:20px; 
    border-collapse:collapse; 
    background:white;
}
th,td { 
    border:1px solid #ddd; 
    padding:12px; 
    text-align:left; 
}
th { 
    background:#e6f2ff; 
    font-weight:bold;
}
.status-pending { 
    color:#ff9900; 
    background:#fff3cd; 
    padding:4px 8px; 
    border-radius:3px; 
    font-size:12px;
}
.status-resolved { 
    color:#28a745; 
    background:#d4edda; 
    padding:4px 8px; 
    border-radius:3px; 
    font-size:12px;
}
.reply-box {
    background:#f8f9fa;
    padding:10px;
    margin-top:5px;
    border-left:3px solid #0066cc;
    border-radius:3px;
}
.no-reply {
    color:#999;
    font-style:italic;
}
.complaint-form {
    background:#f8f9fa;
    padding:20px;
    border-radius:8px;
    margin-bottom:30px;
}
</style>
</head>
<body>
<div class="container">
    <h2>📩 My Complaints</h2>

    <!-- Complaint Form -->
    <div class="complaint-form">
        <form method="POST">
            <textarea name="complaint" required placeholder="Describe your problem in detail..."></textarea><br><br>
            <button type="submit">Submit Complaint</button>
        </form>
        <?php echo $message; ?>
    </div>

    <!-- Previous Complaints -->
    <h3>📋 Complaint History</h3>
    <table>
        <tr>
            <th>Date</th>
            <th>Complaint</th>
            <th>Status</th>
            <th>Admin Reply</th>
        </tr>
        <?php
        $student = $_SESSION['student_name'];
        $result = $conn->query("SELECT * FROM complaintstudent WHERE student_name='$student' ORDER BY date_submitted DESC");
        
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $status_class = $row['status'] == 'Resolved' ? 'status-resolved' : 'status-pending';
                $status_text = $row['status'] == 'Resolved' ? 'Resolved' : 'Pending';
                
                echo "<tr>";
                echo "<td>" . date('M j, Y g:i A', strtotime($row['date_submitted'])) . "</td>";
                echo "<td>{$row['complaint_text']}</td>";
                echo "<td><span class='$status_class'>$status_text</span></td>";
                echo "<td>";
                if(!empty($row['admin_reply'])){
                    echo "<div class='reply-box'>";
                    echo "<strong>Admin:</strong> {$row['admin_reply']}";
                    if($row['date_replied']){
                        echo "<br><small>Replied on: " . date('M j, Y g:i A', strtotime($row['date_replied'])) . "</small>";
                    }
                    echo "</div>";
                } else {
                    echo "<span class='no-reply'>Waiting for admin response...</span>";
                }
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4' style='text-align:center;'>No complaints submitted yet.</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>