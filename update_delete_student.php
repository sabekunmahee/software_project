<!DOCTYPE html>
<html>
<head>
    <title>Update/Delete Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        form {
            background: #fff;
            padding: 20px;
            width: 350px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        button {
            margin-top: 15px;
            padding: 10px;
            width: 48%;
            border: none;
            color: white;
            cursor: pointer;
        }
        .update-btn {
            background-color: #007BFF;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .message {
            text-align: center;
            margin-top: 15px;
            font-weight: bold;
        }
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        th {
            background: #007BFF;
            color: white;
        }
    </style>
</head>
<body>

<h2>Update or Delete Student</h2>

<form method="post" action="">
    <label>Student ID (required):</label>
    <input type="number" name="id" required>

    <label>Name:</label>
    <input type="text" name="name">

    <label>Email:</label>
    <input type="email" name="email">

    <label>Room Number:</label>
    <input type="text" name="room_number">

    <label>Phone:</label>
    <input type="text" name="phone">

    <div style="display: flex; justify-content: space-between;">
        <button type="submit" name="update" class="update-btn">Update</button>
        <button type="submit" name="delete" class="delete-btn">Delete</button>
    </div>
    
</form>
<a href="AdminDashboard.html" class="back-btn">⬅️ Go to Admin Panel</a>
<?php
include 'dbconnection.php'; // Make sure this defines $conn

if (!$conn) {
    die("<div class='message' style='color:red;'>Database connection failed.</div>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    // UPDATE STUDENT
    if (isset($_POST['update'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $room = $_POST['room_number'];
        $phone = $_POST['phone'];

        $stmt = $conn->prepare("UPDATE up_students SET name=?, email=?, room_number=?, phone=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $room, $phone, $id);

        if ($stmt->execute()) {
            echo "<div class='message' style='color:green;'>Student updated successfully.</div>";
        } else {
            echo "<div class='message' style='color:red;'>Error updating student: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }

    // DELETE STUDENT
    if (isset($_POST['delete'])) {
        $stmt = $conn->prepare("DELETE FROM up_students WHERE id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<div class='message' style='color:green;'>Student deleted successfully.</div>";
        } else {
            echo "<div class='message' style='color:red;'>Error deleting student: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}

// SHOW STUDENT LIST (from students table)
$result = $conn->query("SELECT * FROM up_students ORDER BY id ASC");
if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Room</th>
                <th>Phone</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['room_number']}</td>
                <td>{$row['phone']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<div class='message'>No students found.</div>";
}

$conn->close();
?>

</body>
</html>
