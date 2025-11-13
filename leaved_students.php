<?php
include "dbconnection.php";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $conn->real_escape_string($_POST['student_name']);
    $room_number = $conn->real_escape_string($_POST['room_number']);
    $mobile_number = $conn->real_escape_string($_POST['mobile_number']);
    $leave_reason = $conn->real_escape_string($_POST['leave_reason']);
    $leave_date = $conn->real_escape_string($_POST['leave_date']);
    
    $insert_sql = "INSERT INTO leaved_students (student_name, room_number, mobile_number, leave_reason, leave_date) 
                   VALUES ('$student_name', '$room_number', '$mobile_number', '$leave_reason', '$leave_date')";
    
    if ($conn->query($insert_sql)) {
        $message = "<div style='color:green; text-align:center;'>Student added to leave list!</div>";
    } else {
        $message = "<div style='color:red; text-align:center;'>Error: " . $conn->error . "</div>";
    }
}

$leaved_query = "SELECT * FROM leaved_students ORDER BY leave_date DESC";
$leaved_result = $conn->query($leaved_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaved Students - Hostel Management</title>
    <style>
        body { font-family: Arial; background: #f0f8ff; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
        h2 { text-align: center; color: #0066cc; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 80px; }
        button { background: #0066cc; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0055aa; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #e6f2ff; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 3px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🚪 Leaved Students</h2>
        
        <?php echo $message; ?>

        <!-- Add Leaved Student Form -->
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <h3>Add Leaved Student</h3>
            <form method="POST">
                <div class="grid-2">
                    <div class="form-group">
                        <label>Student Name:</label>
                        <input type="text" name="student_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Room Number:</label>
                        <input type="text" name="room_number" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mobile Number:</label>
                    <input type="text" name="mobile_number" required>
                </div>
                
                <div class="form-group">
                    <label>Leave Reason:</label>
                    <textarea name="leave_reason" placeholder="Reason for leaving..." required></textarea>
                </div>

                <div class="form-group">
                    <label>Leave Date:</label>
                    <input type="date" name="leave_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <button type="submit">Add to Leave List</button>
            </form>
        </div>

        <!-- Leaved Students List -->
        <h3>Leaved Students History</h3>
        
        <table>
            <tr>
                <th>Student Name</th>
                <th>Room No.</th>
                <th>Mobile</th>
                <th>Leave Reason</th>
                <th>Leave Date</th>
            </tr>
            <?php
            if ($leaved_result->num_rows > 0) {
                while ($student = $leaved_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><strong>{$student['student_name']}</strong></td>";
                    echo "<td>{$student['room_number']}</td>";
                    echo "<td>{$student['mobile_number']}</td>";
                    echo "<td>{$student['leave_reason']}</td>";
                    echo "<td>" . date('M j, Y', strtotime($student['leave_date'])) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>No leaved students found</td></tr>";
            }
            ?>
        </table>
    </div>
    <a href="AdminDashboard.html" class="back-btn">⬅️ Go to Admin Panel</a>
</body>
</html>

<?php $conn->close(); ?>