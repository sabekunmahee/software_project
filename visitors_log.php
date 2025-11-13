<?php
include "dbconnection.php";
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_visitor'])) {
        $student_name = $conn->real_escape_string($_POST['student_name']);
        $room_number = $conn->real_escape_string($_POST['room_number']);
        $visitor_name = $conn->real_escape_string($_POST['visitor_name']);
        $visitor_relation = $conn->real_escape_string($_POST['visitor_relation']);
        $visitor_mobile = $conn->real_escape_string($_POST['visitor_mobile']);
        $visit_purpose = $conn->real_escape_string($_POST['visit_purpose']);
        
        // Get custom date and time
        $visit_date = $conn->real_escape_string($_POST['visit_date']);
        $visit_time = $conn->real_escape_string($_POST['visit_time']);
        
        // Combine date and time
        $entry_datetime = $visit_date . ' ' . $visit_time . ':00';
        
        $insert_sql = "INSERT INTO visitors_log (student_name, room_number, visitor_name, visitor_relation, visitor_mobile, visit_purpose, time_in) 
                       VALUES ('$student_name', '$room_number', '$visitor_name', '$visitor_relation', '$visitor_mobile', '$visit_purpose', '$entry_datetime')";
        
        if ($conn->query($insert_sql)) {
            $message = "<div style='color:green; text-align:center;'>✅ Visitor registered successfully!</div>";
        } else {
            $message = "<div style='color:red; text-align:center;'>❌ Error: " . $conn->error . "</div>";
        }
    } elseif (isset($_POST['mark_exit'])) {
        $visitor_id = $conn->real_escape_string($_POST['visitor_id']);
        
        // Get custom exit date and time
        $exit_date = $conn->real_escape_string($_POST['exit_date']);
        $exit_time = $conn->real_escape_string($_POST['exit_time']);
        
        // Combine date and time
        $exit_datetime = $exit_date . ' ' . $exit_time . ':00';
        
        $update_sql = "UPDATE visitors_log SET time_out = '$exit_datetime' WHERE id = '$visitor_id'";
        
        if ($conn->query($update_sql)) {
            $message = "<div style='color:green; text-align:center;'>✅ Exit time recorded!</div>";
        } else {
            $message = "<div style='color:red; text-align:center;'>❌ Error: " . $conn->error . "</div>";
        }
    }
}

// Fixed query to avoid duplicates
$visitors_query = "SELECT * FROM visitors_log ORDER BY time_in DESC";
$visitors_result = $conn->query($visitors_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitors Log - Hostel Management</title>
    <style>
        body { font-family: Arial; background: #f0f8ff; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
        h2 { text-align: center; color: #0066cc; margin-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 80px; }
        button { background: #0066cc; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0055aa; }
        .status { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 12px; margin-left: 10px; }
        .in-campus { background: #d4edda; color: #155724; }
        .exited { background: #e2e3e5; color: #383d41; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 3px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #e6f2ff; }
        .time-input { background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .small-btn { padding: 5px 10px; font-size: 12px; }
        .datetime-group { display: flex; gap: 10px; }
        .datetime-group input { flex: 1; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🏢 Hostel Visitors Log</h2>
        
        <?php echo $message; ?>

        <!-- Add Visitor Form -->
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <h3>👥 Register New Visitor</h3>
            <form method="POST">
                <input type="hidden" name="add_visitor" value="1">
                
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

                <div class="grid-2">
                    <div class="form-group">
                        <label>Visitor Name:</label>
                        <input type="text" name="visitor_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Relation:</label>
                        <input type="text" name="visitor_relation" placeholder="e.g., Father, Mother, Guardian, Uncle, etc." required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Visitor Mobile:</label>
                    <input type="text" name="visitor_mobile" required>
                </div>
                
                <div class="form-group">
                    <label>Visit Purpose:</label>
                    <textarea name="visit_purpose" placeholder="Purpose of visit..." required></textarea>
                </div>

                <!-- Flexible Date & Time Section -->
                <div class="time-input">
                    <label><strong>🕒 Entry Date & Time:</strong></label>
                    <div class="datetime-group">
                        <input type="date" name="visit_date" value="<?php echo date('Y-m-d'); ?>" required>
                        <input type="time" name="visit_time" value="<?php echo date('H:i'); ?>" required>
                    </div>
                </div>
                
                <button type="submit">✅ Register Visitor</button>
            </form>
        </div>
<a href="AdminDashboard.html" class="back-btn">⬅️ Go to Admin Panel</a>
        <!-- Visitors List -->
        <h3>📋 Visitors History</h3>
        
        <table>
            <tr>
                <th>Visitor</th>
                <th>Relation</th>
                <th>Student</th>
                <th>Room</th>
                <th>Purpose</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            
            <?php
            if ($visitors_result->num_rows > 0) {
                $displayed_ids = []; // Track displayed IDs to avoid duplicates
                
                while ($visitor = $visitors_result->fetch_assoc()) {
                    // Skip if this ID already displayed (fix for duplicate issue)
                    if (in_array($visitor['id'], $displayed_ids)) {
                        continue;
                    }
                    $displayed_ids[] = $visitor['id'];
                    
                    $is_current = empty($visitor['time_out']);
                    $status_text = $is_current ? 'In Campus' : 'Exited';
                    $status_color = $is_current ? 'in-campus' : 'exited';
                    
                    // Calculate duration properly
                    $time_in = strtotime($visitor['time_in']);
                    if ($is_current) {
                        $duration_text = "Still here";
                    } else {
                        $time_out = strtotime($visitor['time_out']);
                        $duration = $time_out - $time_in;
                        $hours = floor($duration / 3600);
                        $minutes = floor(($duration % 3600) / 60);
                        $duration_text = $hours . "h " . $minutes . "m";
                    }
                    
                    echo "<tr>";
                    echo "<td><strong>{$visitor['visitor_name']}</strong><br><small>{$visitor['visitor_mobile']}</small></td>";
                    echo "<td>{$visitor['visitor_relation']}</td>";
                    echo "<td>{$visitor['student_name']}</td>";
                    echo "<td>{$visitor['room_number']}</td>";
                    echo "<td>{$visitor['visit_purpose']}</td>";
                    echo "<td>" . date('M j, g:i A', strtotime($visitor['time_in'])) . "</td>";
                    
                    if ($visitor['time_out']) {
                        echo "<td>" . date('M j, g:i A', strtotime($visitor['time_out'])) . "</td>";
                    } else {
                        echo "<td><em>Still here</em></td>";
                    }
                    
                    echo "<td>{$duration_text}</td>";
                    echo "<td><span class='status $status_color'>$status_text</span></td>";
                    
                    if ($is_current) {
                        echo "<td>
                                <form method='POST' style='margin:0;'>
                                    <input type='hidden' name='visitor_id' value='{$visitor['id']}'>
                                    <div style='margin-bottom:5px;'>
                                        <input type='date' name='exit_date' value='" . date('Y-m-d') . "' style='font-size:12px; padding:3px; margin-bottom:2px;' required>
                                    </div>
                                    <div style='margin-bottom:5px;'>
                                        <input type='time' name='exit_time' value='" . date('H:i') . "' style='font-size:12px; padding:3px;' required>
                                    </div>
                                    <button type='submit' name='mark_exit' class='small-btn'>Mark Exit</button>
                                </form>
                              </td>";
                    } else {
                        echo "<td>-</td>";
                    }
                    
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10' style='text-align:center;'>No visitors recorded</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>