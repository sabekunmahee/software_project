<?php
include "dbconnection.php";

$message = "";
$action_buttons = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_number = $conn->real_escape_string($_POST['room_number']);
    $action = $_POST['action'];

    // --- Search Room ---
    if ($action == "search") {
        $sql = "SELECT capacity, occupied, status FROM rooms WHERE room_number='$room_number'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $status = $row['status'];
            $occupied = $row['occupied'];
            $capacity = $row['capacity'];
            $message = "Room <b>$room_number</b> is <b>$status</b> ($occupied/$capacity occupied).";

            // Show action buttons
            if ($status != "Occupied") {
                $action_buttons = "
                    <form method='POST' style='display:inline-block; margin-top:10px;'>
                        <input type='hidden' name='room_number' value='$room_number'>
                        <input type='hidden' name='action' value='book'>
                        <button type='submit' class='action-btn book'>Book Room</button>
                    </form>";
            }
            if ($status != "Vacant") {
                $action_buttons .= "
                    <form method='POST' style='display:inline-block; margin-top:10px;'>
                        <input type='hidden' name='room_number' value='$room_number'>
                        <input type='hidden' name='action' value='free'>
                        <button type='submit' class='action-btn free'>Free Room</button>
                    </form>";
            }
        } else {
            $message = "Room <b>$room_number</b> not found!";
        }
    }

    // --- Book Room ---
    elseif ($action == "book") {
        $sql = "SELECT capacity, occupied, status FROM rooms WHERE room_number='$room_number'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $occupied = $row['occupied'];
            $capacity = $row['capacity'];

            if ($occupied >= $capacity) {
                $message = "Room <b>$room_number</b> is already fully occupied!";
            } else {
                $occupied++;
                $status = ($occupied == $capacity) ? 'Occupied' : 'Partially Occupied';
                $conn->query("UPDATE rooms SET occupied=$occupied, status='$status' WHERE room_number='$room_number'");
                $message = "Room <b>$room_number</b> booked! Status: <b>$status</b> ($occupied/$capacity occupied).";
            }
        } else {
            $message = "Room <b>$room_number</b> not found!";
        }
    }

    // --- Free Room ---
    elseif ($action == "free") {
        $sql = "SELECT capacity, occupied, status FROM rooms WHERE room_number='$room_number'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $occupied = $row['occupied'];
            $capacity = $row['capacity'];

            if ($occupied <= 0) {
                $message = "Room <b>$room_number</b> is already vacant!";
            } else {
                $occupied--;
                $status = ($occupied == 0) ? 'Vacant' : 'Partially Occupied';
                $conn->query("UPDATE rooms SET occupied=$occupied, status='$status' WHERE room_number='$room_number'");
                $message = "Room <b>$room_number</b> freed! Status: <b>$status</b> ($occupied/$capacity occupied).";
            }
        } else {
            $message = "Room <b>$room_number</b> not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Room - Hostel Management System</title>
<style>
body { font-family: Arial, sans-serif; background-color: #ffeaf1; }
.container { width: 70%; margin: 40px auto; background: white; padding: 25px; border-radius: 15px; box-shadow: 0px 0px 10px gray; }
h2 { text-align: center; color: #d63384; }
form { display: flex; flex-direction: column; gap: 10px; }
input, select, button { padding: 10px; font-size: 16px; border-radius: 8px; border: 1px solid #ccc; }
button { background-color: #d63384; color: white; border: none; cursor: pointer; }
button:hover { background-color: #b52d6b; }
.action-btn { display: inline-block; padding: 10px 20px; font-size: 16px; margin:5px; border-radius: 8px; cursor: pointer; border:none; }
.action-btn.book { background-color: green; color: white; }
.action-btn.free { background-color: red; color: white; }
.message { text-align:center; font-weight:bold; margin-top:15px; padding:10px; border-radius:8px; background:#f8f8f8; }
table { width: 100%; text-align: center; border-collapse: collapse; margin-top: 20px; }
th, td { padding: 8px; border: 1px solid #ddd; }
th { background-color: #f8c0d4; }
</style>
</head>
<body>
<div class="container">
    <h2>Manage Room</h2>

    <form method="POST">
        <label>Room Number:</label>
        <input type="text" name="room_number" placeholder="Enter Room Number" required>

        <label>Action:</label>
        <select name="action" required>
            <option value="search">Search Room</option>
        </select>

        <button type="submit">Search</button>
    </form>

    <?php if ($message): ?>
        <div class="message">
            <?php echo $message; ?>
            <div><?php echo $action_buttons; ?></div>
        </div>
    <?php endif; ?>

    <hr>
    <h3>Currently Available Rooms</h3>
    <table>
        <tr>
            <th>Room Number</th>
            <th>Status</th>
            <th>Occupied/Capacity</th>
        </tr>
        <?php
        $sql = "SELECT * FROM rooms ORDER BY room_number ASC";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $color = ($row['status'] == 'Vacant') ? 'green' : 'red';
                echo "<tr>
                        <td>{$row['room_number']}</td>
                        <td style='color:$color;font-weight:bold'>{$row['status']}</td>
                        <td>{$row['occupied']}/{$row['capacity']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No rooms found</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>

<?php $conn->close(); ?>
