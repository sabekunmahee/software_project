<?php
session_start();
include "dbconnection.php";

if(!isset($_SESSION['accounts_id'])){
    header("Location: Accountslogin.php");
    exit();
}

$message = "";

// Handle fee payment
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pay_fee'])){
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $payment_amount = floatval($_POST['payment_amount']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    
    // Get current student data
    $student_data = $conn->query("SELECT * FROM feescollection WHERE id='$student_id'");
    if($student_data->num_rows > 0){
        $student = $student_data->fetch_assoc();
        $current_paid = $student['paid_amount'];
        $current_due = $student['due_amount'];
        
        $new_paid = $current_paid + $payment_amount;
        $new_due = $current_due - $payment_amount;
        
        if($new_due < 0){
            $message = "<div style='color:red; padding:10px; background:#ffe6e6; border-radius:5px;'>Payment amount exceeds due amount!</div>";
        } else {
            // Update student fees in feescollection table
            $status = ($new_due == 0) ? 'Paid' : 'Partial';
            $update_sql = "UPDATE feescollection SET 
                          paid_amount = $new_paid, 
                          due_amount = $new_due, 
                          status = '$status',
                          last_payment_date = CURDATE() 
                          WHERE id = '$student_id'";
            
            if($conn->query($update_sql)){
                // Insert into payment history
                $history_sql = "INSERT INTO fee_payments (student_name, payment_amount, payment_method) 
                               VALUES ('{$student['student_name']}', $payment_amount, '$payment_method')";
                $conn->query($history_sql);
                
                $message = "<div style='color:green; padding:10px; background:#e6ffe6; border-radius:5px;'>Payment of ৳$payment_amount received from {$student['student_name']}!</div>";
            } else {
                $message = "<div style='color:red; padding:10px; background:#ffe6e6; border-radius:5px;'>Error: " . $conn->error . "</div>";
            }
        }
    }
}

// Get all students from feescollection table
$students_result = $conn->query("SELECT * FROM feescollection ORDER BY room_number");
$total_students = $students_result->num_rows;

// Calculate totals from feescollection table
$total_query = $conn->query("SELECT 
    SUM(monthly_fee) as total_fee,
    SUM(paid_amount) as total_paid,
    SUM(due_amount) as total_due 
    FROM feescollection");
$totals = $total_query->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees Collection</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #f8f9fa; 
            margin: 0;
            padding: 0;
        }
        .container { 
            width: 95%; 
            margin: 20px auto; 
            background: white; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
        }
        h2 { 
            text-align: center; 
            color: #0066cc; 
            margin-bottom: 20px; 
        }
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border-top: 4px solid;
        }
        .total-students { border-color: #0066cc; }
        .total-fee { border-color: #28a745; }
        .total-paid { border-color: #4ecdc4; }
        .total-due { border-color: #ff6b6b; }
        .card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
        }
        .card .amount {
            font-size: 24px;
            font-weight: bold;
        }
        .total-students .amount { color: #0066cc; }
        .total-fee .amount { color: #28a745; }
        .total-paid .amount { color: #4ecdc4; }
        .total-due .amount { color: #ff6b6b; }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            background: white;
        }
        th, td { 
            padding: 12px; 
            border: 1px solid #ddd; 
            text-align: center; 
        }
        th { 
            background: #e6f2ff; 
            font-weight: bold;
        }
        .status-paid { 
            color: #28a745; 
            background: #d4edda; 
            padding: 5px 10px; 
            border-radius: 15px; 
        }
        .status-partial { 
            color: #ffc107; 
            background: #fff3cd; 
            padding: 5px 10px; 
            border-radius: 15px; 
        }
        .status-due { 
            color: #dc3545; 
            background: #f8d7da; 
            padding: 5px 10px; 
            border-radius: 15px; 
        }
        .payment-form {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        input, select, button {
            padding: 8px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>💰 Fees Collection</h2>
    
    <?php echo $message; ?>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="card total-students">
            <h3>Total Students</h3>
            <div class="amount"><?php echo $total_students; ?></div>
        </div>
        <div class="card total-fee">
            <h3>Total Monthly Fee</h3>
            <div class="amount">৳ <?php echo number_format($totals['total_fee']); ?></div>
        </div>
        <div class="card total-paid">
            <h3>Total Collected</h3>
            <div class="amount">৳ <?php echo number_format($totals['total_paid']); ?></div>
        </div>
        <div class="card total-due">
            <h3>Total Due</h3>
            <div class="amount">৳ <?php echo number_format($totals['total_due']); ?></div>
        </div>
    </div>

    <!-- Students List -->
    <h3>Students Fee Status</h3>
    <table>
        <tr>
            <th>Student Name</th>
            <th>Room No</th>
            <th>Mobile</th>
            <th>Monthly Fee</th>
            <th>Paid</th>
            <th>Due</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php
        if($students_result->num_rows > 0){
            while($student = $students_result->fetch_assoc()){
                $status_class = "status-" . strtolower($student['status']);
                echo "<tr>";
                echo "<td>{$student['student_name']}</td>";
                echo "<td>{$student['room_number']}</td>";
                echo "<td>{$student['mobile_number']}</td>";
                echo "<td>৳ " . number_format($student['monthly_fee']) . "</td>";
                echo "<td>৳ " . number_format($student['paid_amount']) . "</td>";
                echo "<td>৳ " . number_format($student['due_amount']) . "</td>";
                echo "<td><span class='$status_class'>{$student['status']}</span></td>";
                echo "<td>";
                if($student['due_amount'] > 0){
                    echo "<form method='POST' class='payment-form'>";
                    echo "<input type='hidden' name='student_id' value='{$student['id']}'>";
                    echo "<input type='number' name='payment_amount' placeholder='Amount' min='1' max='{$student['due_amount']}' required style='width:80px;'>";
                    echo "<select name='payment_method' required style='width:100px;'>";
                    echo "<option value='Bikash'>Bikash</option>";
                    echo "<option value='Nagad'>Nagad</option>";
                    echo "<option value='Bank'>Bank</option>";
                    echo "<option value='Cash'>Cash</option>";
                    echo "</select>";
                    echo "<button type='submit' name='pay_fee'>Pay</button>";
                    echo "</form>";
                } else {
                    echo "<span style='color:#28a745;'>✅ Paid</span>";
                }
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8' style='text-align:center;'>No students found</td></tr>";
        }
        ?>
    </table>
</div>
<a href="AccountDashboard.html" class="back-btn">⬅️ Go to Account Panel</a>
</body>
</html>