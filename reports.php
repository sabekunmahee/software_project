<?php
include "dbconnection.php";
$message = "";

// Handle search request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $phone = trim($_POST['phone']);
    $monthFrom = $_POST['monthFrom'];
    $monthTo = $_POST['monthTo'];
    
    // Search for student by phone number
    $stmt = $conn->prepare("SELECT DISTINCT student_name, student_id, phone, class FROM reports WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        
        // Get monthly fee data for the selected period
        $months = ['August', 'September', 'October', 'November'];
        $fromIndex = array_search($monthFrom, $months);
        $toIndex = array_search($monthTo, $months);
        $periodMonths = array_slice($months, $fromIndex, $toIndex - $fromIndex + 1);
        
        if (count($periodMonths) > 0) {
            $placeholders = str_repeat('?,', count($periodMonths) - 1) . '?';
            $stmt2 = $conn->prepare("SELECT month, monthly_fee, amount_paid, due_amount, payment_status 
                                    FROM reports 
                                    WHERE phone = ? AND month IN ($placeholders)
                                    ORDER BY FIELD(month, 'August', 'September', 'October', 'November')");
            
            $params = array_merge([$phone], $periodMonths);
            $stmt2->bind_param(str_repeat('s', count($params)), ...$params);
            $stmt2->execute();
            $monthlyResult = $stmt2->get_result();
            
            $monthlyData = [];
            $totalFee = 0;
            $totalPaid = 0;
            $totalDue = 0;
            
            while($row = $monthlyResult->fetch_assoc()) {
                $monthlyData[] = $row;
                $totalFee += $row['monthly_fee'];
                $totalPaid += $row['amount_paid'];
                $totalDue += $row['due_amount'];
            }
            
            // Determine overall status
            if ($totalDue == 0) {
                $overallStatus = 'Paid';
            } elseif ($totalPaid == 0) {
                $overallStatus = 'Due';
            } else {
                $overallStatus = 'Partial';
            }
            
            $response = [
                'success' => true,
                'student_name' => $student['student_name'],
                'student_id' => $student['student_id'],
                'phone' => $student['phone'],
                'class' => $student['class'],
                'monthFrom' => $monthFrom,
                'monthTo' => $monthTo,
                'monthlyData' => $monthlyData,
                'totalFee' => $totalFee,
                'totalPaid' => $totalPaid,
                'totalDue' => $totalDue,
                'overallStatus' => $overallStatus
            ];
            
            echo json_encode($response);
            exit;
        }
    }
    
    echo json_encode(['success' => false, 'message' => 'Student not found']);
    exit;
}

// Handle report sending
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_report'])) {
    $admin_email = $_POST['admin_email'];
    $student_id = $_POST['student_id'];
    
    $allowed_emails = ['mahee@gmail.com', 'accounts@school.edu.bd', 'principal@school.edu.bd'];
    
    if (!in_array(strtolower($admin_email), array_map('strtolower', $allowed_emails))) {
        echo json_encode(['success' => false, 'message' => 'Invalid admin email']);
        exit;
    }
    
    // Here you would typically send the email
    // For now, we'll just return success
    echo json_encode(['success' => true, 'message' => 'Report sent successfully']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Reports</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .search-box {
            background: #e9f7fe;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .search-form input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .search-form button {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .search-form button:hover {
            background: #0056b3;
        }

        .period-selector {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .period-selector select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .student-details {
            display: none;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #007bff;
        }

        .student-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            padding: 12px;
            background: white;
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }

        .info-item label {
            font-weight: bold;
            color: #495057;
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .info-item span {
            color: #333;
            font-size: 16px;
        }

        .paid { color: #28a745; font-weight: bold; }
        .due { color: #dc3545; font-weight: bold; }
        .partial { color: #fd7e14; font-weight: bold; }

        .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .fee-table th, .fee-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        .fee-table th {
            background: #007bff;
            color: white;
            font-weight: 600;
        }

        .fee-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .fee-table tr:hover {
            background-color: #e9ecef;
        }

        .send-report {
            display: none;
            background: #e9f7fe;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .send-report-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .send-report-form input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .send-report-form button {
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            white-space: nowrap;
        }

        .send-report-form button:hover {
            background: #218838;
        }

        .send-report-form button:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-top: 10px;
            display: none;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-top: 10px;
            display: none;
            border: 1px solid #f5c6cb;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid { 
            background: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb;
        }
        .status-partial { 
            background: #fff3cd; 
            color: #856404; 
            border: 1px solid #ffeaa7;
        }
        .status-due { 
            background: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb;
        }

        .summary-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
        }

        .summary-card h4 {
            color: #495057;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .student-info {
                grid-template-columns: 1fr;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .send-report-form {
                flex-direction: column;
            }
            
            .period-selector {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Student Fee Reports</h1>
        
        <div class="search-box">
            <div class="search-form">
                <input type="text" id="searchInput" placeholder="Enter student phone number" maxlength="11">
                <button onclick="searchStudent()">🔍 Search Student</button>
            </div>
            <div class="period-selector">
                <span><strong>Period:</strong></span>
                <select id="monthFrom">
                    <option value="August">August</option>
                    <option value="September">September</option>
                    <option value="October">October</option>
                    <option value="November">November</option>
                </select>
                <span>to</span>
                <select id="monthTo">
                    <option value="August">August</option>
                    <option value="September">September</option>
                    <option value="October">October</option>
                    <option value="November" selected>November</option>
                </select>
            </div>
        </div>

        <div class="student-details" id="studentDetails">
            <!-- Student details will appear here -->
        </div>

        <div class="send-report" id="sendReport">
            <div class="send-report-form">
                <input type="email" id="adminEmail" placeholder="Enter admin email address" >
                <button onclick="sendReport()">📧 Send Report to Admin</button>
            </div>
            <div class="success" id="successMessage">
                ✅ Report sent successfully to admin!
            </div>
            <div class="error" id="errorMessage">
                ❌ Only admin emails are allowed! 
            </div>
        </div>
    </div>

    <script>
        // Allowed admin emails
        const allowedAdminEmails = [
            'mahee@gmail.com',
            'accounts@school.edu.bd', 
            'principal@school.edu.bd'
        ];

        function searchStudent() {
            const searchInput = document.getElementById('searchInput').value.trim();
            const studentDetails = document.getElementById('studentDetails');
            const sendReport = document.getElementById('sendReport');
            
            if (!searchInput) {
                alert('Please enter student phone number');
                return;
            }

            // Clean phone number (remove spaces, dashes, etc.)
            const cleanPhone = searchInput.replace(/\D/g, '');
            
            if (cleanPhone.length !== 11) {
                alert('Please enter a valid 11-digit phone number');
                return;
            }

            // Create form data for AJAX request
            const formData = new FormData();
            formData.append('search', 'true');
            formData.append('phone', cleanPhone);
            formData.append('monthFrom', document.getElementById('monthFrom').value);
            formData.append('monthTo', document.getElementById('monthTo').value);
            
            fetch('reports.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStudentDetails(data);
                    studentDetails.style.display = 'block';
                    sendReport.style.display = 'block';
                } else {
                    alert(data.message || 'Student not found with this phone number');
                    studentDetails.style.display = 'none';
                    sendReport.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error searching student');
            });
        }

        function showStudentDetails(data) {
            const studentDetails = document.getElementById('studentDetails');
            
            let monthlyRows = '';
            data.monthlyData.forEach(month => {
                const statusClass = `status-${month.payment_status.toLowerCase()}`;
                monthlyRows += `
                    <tr>
                        <td><strong>${month.month}</strong></td>
                        <td>৳${month.monthly_fee.toLocaleString()}</td>
                        <td class="paid">৳${month.amount_paid.toLocaleString()}</td>
                        <td class="due">৳${month.due_amount.toLocaleString()}</td>
                        <td><span class="status-badge ${statusClass}">${month.payment_status}</span></td>
                    </tr>
                `;
            });
            
            studentDetails.innerHTML = `
                <div class="summary-card">
                    <h3>👤 Student Information</h3>
                    <div class="student-info">
                        <div class="info-item">
                            <label>Full Name:</label>
                            <span>${data.student_name}</span>
                        </div>
                        <div class="info-item">
                            <label>Student ID:</label>
                            <span>${data.student_id}</span>
                        </div>
                        <div class="info-item">
                            <label>Phone Number:</label>
                            <span>${data.phone}</span>
                        </div>
                        <div class="info-item">
                            <label>Class:</label>
                            <span>${data.class}</span>
                        </div>
                    </div>
                </div>
                
                <div class="summary-card">
                    <h4>💰 Fee Summary (${data.monthFrom} - ${data.monthTo})</h4>
                    <div class="student-info">
                        <div class="info-item">
                            <label>Total Fee:</label>
                            <span>৳${data.totalFee.toLocaleString()}</span>
                        </div>
                        <div class="info-item">
                            <label>Total Paid:</label>
                            <span class="paid">৳${data.totalPaid.toLocaleString()}</span>
                        </div>
                        <div class="info-item">
                            <label>Total Due:</label>
                            <span class="due">৳${data.totalDue.toLocaleString()}</span>
                        </div>
                        <div class="info-item">
                            <label>Overall Status:</label>
                            <span class="${data.overallStatus === 'Paid' ? 'paid' : data.overallStatus === 'Partial' ? 'partial' : 'due'}">
                                ${data.overallStatus}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="summary-card">
                    <h4>📅 Monthly Breakdown</h4>
                    <table class="fee-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Monthly Fee</th>
                                <th>Paid Amount</th>
                                <th>Due Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>${monthlyRows}</tbody>
                    </table>
                </div>
            `;
        }

        function sendReport() {
            const email = document.getElementById('adminEmail').value.trim();
            const success = document.getElementById('successMessage');
            const error = document.getElementById('errorMessage');
            const studentId = document.querySelector('.student-info .info-item:nth-child(2) span')?.textContent;
            
            if (!email) {
                alert('Please enter admin email');
                return;
            }
            
            if (!studentId) {
                alert('Please search for a student first');
                return;
            }
            
            // Check if email is in allowed list
            if (!allowedAdminEmails.includes(email.toLowerCase())) {
                error.style.display = 'block';
                success.style.display = 'none';
                setTimeout(() => {
                    error.style.display = 'none';
                }, 3000);
                return;
            }
            
            // Send report to server
            const formData = new FormData();
            formData.append('send_report', 'true');
            formData.append('admin_email', email);
            formData.append('student_id', studentId);
            
            // Show sending animation
            const button = document.querySelector('.send-report-form button');
            const originalText = button.textContent;
            button.textContent = '⏳ Sending...';
            button.disabled = true;
            
            fetch('reports.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    success.style.display = 'block';
                    error.style.display = 'none';
                } else {
                    error.textContent = data.message;
                    error.style.display = 'block';
                    success.style.display = 'none';
                }
                
                button.textContent = originalText;
                button.disabled = false;
                
                setTimeout(() => {
                    success.style.display = 'none';
                    error.style.display = 'none';
                }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending report');
                button.textContent = originalText;
                button.disabled = false;
            });
        }

        // Event listeners
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchStudent();
        });

        // Format phone number input
        document.getElementById('searchInput').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) {
                value = value.slice(0, 11);
            }
            e.target.value = value;
        });

        // Auto-focus on search input
        document.getElementById('searchInput').focus();
    </script>
    <a href="AccountDashboard.html" class="back-btn">⬅️ Go to Account Panel</a>
</body>
</html>