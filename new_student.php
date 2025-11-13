<?php
include "dbconnection.php";
$message = "";

// Fetch available rooms (Vacant or Partially Occupied)
$room_query = "SELECT room_number, status, capacity, occupied FROM rooms WHERE status IN ('Vacant', 'Partially Occupied')";
$room_result = $conn->query($room_query);

// When form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mobile_number = $conn->real_escape_string($_POST['mobile_number']);
    $student_name = $conn->real_escape_string($_POST['student_name']);
    $emergency_contact = $conn->real_escape_string($_POST['emergency_contact']);
    $room_number = isset($_POST['room_number']) ? $conn->real_escape_string($_POST['room_number']) : '';

    if ($room_number == "") {
        $message = "<span style='color:red;'>Please select a room!</span>";
    } else {
        // Check if selected room exists
        $room_check = $conn->query("SELECT capacity, occupied, status FROM rooms WHERE room_number='$room_number'");
        if ($room_check->num_rows > 0) {
            $room_data = $room_check->fetch_assoc();
            $capacity = $room_data['capacity'];
            $occupied = $room_data['occupied'];

            // Check if room is full
            if ($occupied >= $capacity) {
                $message = "<span style='color:red;'>Room <b>$room_number</b> is already full!</span>";
            } else {
                // Insert new student
                $insert_sql = "INSERT INTO new_student (mobile_number, student_name, emergency_contact, room_number)
                               VALUES ('$mobile_number', '$student_name', '$emergency_contact', '$room_number')";
                if ($conn->query($insert_sql)) {
                    // Update room occupied count and status
                    $new_occupied = $occupied + 1;
                    $new_status = ($new_occupied == $capacity) ? 'Occupied' : 'Partially Occupied';
                    $conn->query("UPDATE rooms SET occupied=$new_occupied, status='$new_status' WHERE room_number='$room_number'");

                    $message = "<span style='color:green;'>✅ Student <b>$student_name</b> added successfully to room <b>$room_number</b>.</span>";
                } else {
                    $message = "<span style='color:red;'>Error adding student: ".$conn->error."</span>";
                }
            }
        } else {
            $message = "<span style='color:red;'>Room not found!</span>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Student - Hostel Management System</title>
<style>
body { font-family: Arial, sans-serif; background-color: #eaf4ff; }
.container { width: 70%; margin: 40px auto; background: white; padding: 25px; border-radius: 15px; box-shadow: 0px 0px 10px gray; }
h2 { text-align: center; color: #0077cc; }
form { display: flex; flex-direction: column; gap: 12px; }
label { font-weight: bold; }
input, select, button { padding: 10px; font-size: 16px; border-radius: 8px; border: 1px solid #ccc; }
button { background-color: #0077cc; color: white; border: none; cursor: pointer; }
button:hover { background-color: #005fa3; }
.message { text-align:center; margin-top:15px; font-weight:bold; }
table { width:100%; border-collapse: collapse; margin-top:20px; }
th, td { padding:10px; border:1px solid #ddd; text-align:center; }
th { background:#cce6ff; }
</style>
</head>
<body>
<div class="container">
    <h2>🧍‍♂️ New Student Registration</h2>

    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Mobile Number:</label>
        <input type="text" name="mobile_number" required>

        <label>Student Name:</label>
        <input type="text" name="student_name" required>

        <label>Emergency Contact:</label>
        <input type="text" name="emergency_contact" required>

        <label>Assign Room:</label>
        <select name="room_number" required>
            <option value="">-- Select Available Room --</option>
            <?php
            $room_result->data_seek(0); // Reset pointer
            if ($room_result->num_rows > 0) {
                while ($room = $room_result->fetch_assoc()) {
                    echo "<option value='{$room['room_number']}'>{$room['room_number']} ({$room['status']})</option>";
                }
            } else {
                echo "<option value=''>No rooms available</option>";
            }
            ?>
        </select>

        <button type="submit">💾 Save Student</button>
    </form>

    <hr>
    <h3>🏠 Current Students</h3>

    <table>
        <tr>
            <th>Student Name</th>
            <th>Mobile Number</th>
            <th>Emergency Contact</th>
            <th>Room Number</th>
        </tr>
        <?php
        $students = $conn->query("SELECT student_name, mobile_number, emergency_contact, room_number FROM new_student ORDER BY room_number ASC");
        if ($students->num_rows > 0) {
            while ($row = $students->fetch_assoc()) {
                $room_display = !empty($row['room_number']) ? $row['room_number'] : 'N/A';
                echo "<tr>
                        <td>{$row['student_name']}</td>
                        <td>{$row['mobile_number']}</td>
                        <td>{$row['emergency_contact']}</td>
                        <td>{$room_display}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No students found</td></tr>";
        }
        ?>
    </table>
    <a href="AdminDashboard.html" class="back-btn">⬅️ Go to Admin Panel</a>

</div>
</body>
</html>

<?php $conn->close(); ?>
