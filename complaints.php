<?php
include "dbconnection.php";

// Set Bangladesh timezone
date_default_timezone_set('Asia/Dhaka');

$message = "";
$is_admin = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Admin Response Submit
    if (isset($_POST['submit_response']) && $is_admin) {
        $complaint_id = $conn->real_escape_string($_POST['complaint_id']);
        $progress_update = $conn->real_escape_string($_POST['progress_update']);
        $status = $conn->real_escape_string($_POST['status']);

        // Fetch previous progress
        $old_data = $conn->query("SELECT admin_comment FROM complaints WHERE id='$complaint_id'")->fetch_assoc();
        $old_comment = $old_data ? $conn->real_escape_string($old_data['admin_comment']) : '';

        // Current timestamp with Bangladesh time
        $timestamp = date("d M, Y h:i A");
        $new_entry = "🕓 [$timestamp] Progress: $progress_update";

        // Combine old and new progress
        $final_comment = trim($old_comment . "\n\n" . $new_entry);

        // Escape the final comment for SQL
        $final_comment_escaped = $conn->real_escape_string($final_comment);

        $update_sql = "UPDATE complaints SET 
                        admin_comment = '$final_comment_escaped',
                        responded_by = 'Admin',
                        status = '$status',
                        response_time = NOW()
                        WHERE id = '$complaint_id'";

        $message = $conn->query($update_sql)
            ? "<div style='color:green; padding:10px; background:#e6ffe6; border-radius:5px;'>Progress updated successfully!</div>"
            : "<div style='color:red; padding:10px; background:#ffe6e6; border-radius:5px;'>Error: " . $conn->error . "</div>";
    }
}

$complaints_result = $conn->query("SELECT * FROM complaints ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hostel Complaints</title>
<style>
    body { font-family: Arial; background: #f5f5f5; margin: 0; padding: 20px; }
    .container { max-width: 850px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
    h2 { text-align: center; color: #333; margin-bottom: 20px; }
    .complaint-item { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 8px; background: #fafafa; }
    .student-info { color: #555; font-size: 14px; margin-bottom: 10px; }
    .complaint-text { background: #f9f9f9; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
    .admin-response { background: #e8f4ff; padding: 10px; border-radius: 5px; margin-top: 10px; border-left: 3px solid #007bff; }
    .status { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 12px; }
    .pending { background: #fff3cd; color: #856404; }
    .in-progress { background: #cce5ff; color: #004085; }
    .resolved { background: #d4edda; color: #155724; }
    textarea, select, button { width: 100%; padding: 8px; margin-top: 6px; border-radius: 5px; border: 1px solid #ccc; }
    button { background: #007bff; color: white; border: none; margin-top: 10px; cursor: pointer; font-size: 15px; }
    button:hover { background: #0056b3; }
    .comment-box { background: #f1faff; padding: 8px; border-radius: 5px; white-space: pre-line; font-size: 14px; margin-bottom: 5px; }
    .progress-form { margin-top: 15px; border-top: 1px dashed #ddd; padding-top: 15px; }
</style>
</head>
<body>
<div class="container">
    <h2>Hostel Complaints</h2>
    <?php echo $message; ?>

    <?php
    if ($complaints_result->num_rows > 0) {
        while ($complaint = $complaints_result->fetch_assoc()) {
            echo "<div class='complaint-item'>";
            
            // Student info and status
            echo "<div class='student-info'>";
            echo "<strong>{$complaint['student_name']}</strong> - Room {$complaint['room_number']} - {$complaint['mobile_number']}";
            echo " <span class='status " . strtolower(str_replace(' ', '-', $complaint['status'])) . "'>{$complaint['status']}</span>";
            
            // Convert database time to Bangladesh time
            $created_date = new DateTime($complaint['created_at'], new DateTimeZone('UTC'));
            $created_date->setTimezone(new DateTimeZone('Asia/Dhaka'));
            echo "<span style='float:right; color:#999; font-size:12px;'>" . $created_date->format('d M, Y h:i A') . "</span>";
            
            echo "</div>";

            // Complaint text
            echo "<div class='complaint-text'>" . nl2br(htmlspecialchars($complaint['complaint_text'])) . "</div>";

            // Previous progress reports
            if (!empty($complaint['admin_comment'])) {
                echo "<div class='admin-response'>";
                echo "<strong>📊 Progress Reports:</strong><br><br>";
                $comments = explode("\n\n", trim($complaint['admin_comment']));
                foreach ($comments as $c) {
                    echo "<div class='comment-box'>" . nl2br(htmlspecialchars($c)) . "</div>";
                }
                echo "</div>";
            }

            // Admin progress update form
            if ($is_admin) {
                if ($complaint['status'] != 'Resolved') {
                    echo "<div class='progress-form'>";
                    echo "<form method='POST'>";
                    echo "<input type='hidden' name='complaint_id' value='{$complaint['id']}'>";
                    
                    echo "<label><strong>Update Status:</strong></label>";
                    echo "<select name='status'>";
                    echo "<option value='Pending'" . ($complaint['status'] == 'Pending' ? ' selected' : '') . ">Pending</option>";
                    echo "<option value='In Progress'" . ($complaint['status'] == 'In Progress' ? ' selected' : '') . ">In Progress</option>";
                    echo "<option value='Resolved'" . ($complaint['status'] == 'Resolved' ? ' selected' : '') . ">Resolved</option>";
                    echo "</select>";
                    
                    echo "<label><strong>Progress Report:</strong></label>";
                    echo "<textarea name='progress_update' placeholder='Update the progress of this complaint...' required rows='4'></textarea>";
                    
                    echo "<button type='submit' name='submit_response'>Update Progress</button>";
                    echo "</form>";
                    echo "</div>";
                } else {
                    echo "<p style='color:green; text-align:center;'><strong>✅ Complaint has been resolved.</strong></p>";
                }
            }

            echo "</div>";
        }
    } else {
        echo "<p style='text-align:center; color:#666;'>No complaints found.</p>";
    }
    ?>
    <a href="AdminDashboard.html" class="back-btn">⬅️ Go to Admin Panel</a>
</div>
</body>
</html>
<?php $conn->close(); ?>