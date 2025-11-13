<!DOCTYPE html>
<html>
<head>
    <title>Student Fee Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .search-form {
            background: #fff;
            padding: 20px;
            width: 350px;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label, input {
            display: block;
            width: 100%;
        }
        input {
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 10px;
            width: 100%;
            border: none;
            background-color: #007BFF;
            color: white;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .tabs {
            display: flex;
            justify-content: center;
            margin: 30px 0;
        }
        .tab-button {
            padding: 12px 30px;
            margin: 0 10px;
            border: none;
            background: #6c757d;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: background 0.3s;
        }
        .tab-button.active {
            background: #007BFF;
        }
        .tab-button:hover {
            background: #0056b3;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        table {
            width: 100%;
            margin: 30px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        th {
            background: #007BFF;
            color: white;
        }
        .paid { color: #28a745; font-weight: bold; }
        .due { color: #dc3545; font-weight: bold; }
        .message {
            text-align: center;
            margin-top: 15px;
            font-weight: bold;
            padding: 10px;
            border-radius: 5px;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .student-summary {
            background: #e7f3ff;
            padding: 15px;
            margin: 10px auto;
            border-radius: 8px;
            text-align: center;
        }
        .all-students-header {
            text-align: center;
            margin: 20px 0;
            color: #333;
        }
        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        .summary-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .total-students { color: #007BFF; }
        .fully-paid { color: #28a745; }
        .has-due { color: #dc3545; }
        .total-collected { color: #17a2b8; }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Fee Management System</h2>
        <a href="AdminDashboard.html" class="back-btn">⬅️ Go to Admin Panel</a>
        <!-- Tabs for navigation -->
        <div class="tabs">
            <button class="tab-button active" onclick="switchTab('search')">Search Student</button>
            <button class="tab-button" onclick="switchTab('all')">All Students</button>
        </div>
        
        <!-- Search Tab -->
        <div id="search-tab" class="tab-content active">
            <form method="post" action="" class="search-form">
                <label><strong>Mobile Number:</strong></label>
                <input type="text" name="phone" placeholder="Enter mobile number" required>
                <button type="submit" name="search">Search Student</button>
            </form>
            

            <?php
            include 'dbconnection.php';

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
                $phone = $_POST['phone'];

                // Search for student fees
                $stmt = $conn->prepare("SELECT * FROM studentfees WHERE mobile_number=? ORDER BY 
                                       FIELD(month, 'November', 'October', 'September', 'August', 'July')");
                $stmt->bind_param("s", $phone);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $first_row = $result->fetch_assoc();
                    $student_name = $first_row['student_name'];
                    $room_number = $first_row['room_number'];
                    
                    // Display student summary
                    echo "<div class='student-summary'>";
                    echo "<h3>Student: $student_name | Room: $room_number | Mobile: $phone</h3>";
                    echo "</div>";
                    
                    // Reset pointer to beginning
                    $result->data_seek(0);
                    
                    echo "<table>
                            <tr>
                                <th>Month</th>
                                <th>Total Amount (৳)</th>
                                <th>Paid Amount (৳)</th>
                                <th>Due Amount (৳)</th>
                                <th>Status</th>
                            </tr>";
                    
                    while($row = $result->fetch_assoc()) {
                        $status = $row['due_amount'] == 0 ? 'Fully Paid' : 'Due';
                        $status_class = $row['due_amount'] == 0 ? 'paid' : 'due';
                        
                        echo "<tr>
                                <td><strong>{$row['month']}</strong></td>
                                <td>৳ " . number_format($row['total_amount']) . "</td>
                                <td class='paid'>৳ " . number_format($row['amount_paid']) . "</td>
                                <td class='due'>৳ " . number_format($row['due_amount']) . "</td>
                                <td class='$status_class'><strong>$status</strong></td>
                              </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<div class='message error'>No records found for mobile number: $phone</div>";
                }

                $stmt->close();
            }

            $conn->close();
            ?>
        </div>
        
        <!-- All Students Tab -->
        <div id="all-tab" class="tab-content">
            <div class="all-students-header">
                <h3>All Students Fee Information</h3>
            </div>
            
            <?php
            include 'dbconnection.php';
            
            // Get summary statistics
            $total_students = $conn->query("SELECT COUNT(DISTINCT mobile_number) as total FROM studentfees")->fetch_assoc()['total'];
            $fully_paid = $conn->query("SELECT COUNT(DISTINCT mobile_number) as paid FROM studentfees GROUP BY mobile_number HAVING SUM(due_amount) = 0")->num_rows;
            $has_due = $total_students - $fully_paid;
            $total_collected = $conn->query("SELECT SUM(amount_paid) as total FROM studentfees")->fetch_assoc()['total'];
            
            
            // Get all students with their fee summary
            $students_result = $conn->query("
                SELECT 
                    mobile_number,
                    student_name,
                    room_number,
                    SUM(total_amount) as total_fee,
                    SUM(amount_paid) as total_paid,
                    SUM(due_amount) as total_due,
                    COUNT(*) as month_count
                FROM studentfees 
                GROUP BY mobile_number, student_name, room_number
                ORDER BY student_name
            ");
            
            if ($students_result->num_rows > 0) {
                echo "<table>
                        <tr>
                            <th>Student Name</th>
                            <th>Mobile Number</th>
                            <th>Room No</th>
                            <th>Total Fee (৳)</th>
                            <th>Total Paid (৳)</th>
                            <th>Total Due (৳)</th>
                            <th>Status</th>
                        </tr>";
                
                while($student = $students_result->fetch_assoc()) {
                    $status = $student['total_due'] == 0 ? 'Fully Paid' : 'Has Due';
                    $status_class = $student['total_due'] == 0 ? 'paid' : 'due';
                    
                    echo "<tr>
                            <td><strong>{$student['student_name']}</strong></td>
                            <td>{$student['mobile_number']}</td>
                            <td>{$student['room_number']}</td>
                            <td>৳ " . number_format($student['total_fee']) . "</td>
                            <td class='paid'>৳ " . number_format($student['total_paid']) . "</td>
                            <td class='due'>৳ " . number_format($student['total_due']) . "</td>
                            <td class='$status_class'><strong>$status</strong></td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<div class='message error'>No student records found</div>";
            }
            
            $conn->close();
            ?>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.getElementById('search-tab').classList.remove('active');
            document.getElementById('all-tab').classList.remove('active');
            
            // Remove active class from all buttons
            var buttons = document.getElementsByClassName('tab-button');
            for (var i = 0; i < buttons.length; i++) {
                buttons[i].classList.remove('active');
            }
            
            // Show selected tab and activate button
            document.getElementById(tabName + '-tab').classList.add('active');
            event.currentTarget.classList.add('active');
        }
    </script>
    
</body>
</html>