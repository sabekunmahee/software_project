<?php
session_start();
include "dbconnection.php";

if(!isset($_SESSION['student_name'])){
    header("Location: student_login.php");
    exit();
}

$student = $_SESSION['student_name'];
$message = "";

// Handle Bikash payment demo
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['bikash_payment'])){
    $payment_amount = floatval($_POST['payment_amount']);
    
    // Get current payment data
    $fee_payments = $conn->query("SELECT * FROM fee_payments WHERE student_name='$student'");
    $total_paid = 0;
    if($fee_payments && $fee_payments->num_rows > 0){
        while($row = $fee_payments->fetch_assoc()){
            $total_paid += $row['payment_amount'];
        }
    }
    
    $balance_fee = 6000 - $total_paid;
    
    if($payment_amount <= 0 || $payment_amount > $balance_fee){
        $message = "<div style='color:red; padding:10px; background:#ffe6e6; border-radius:5px;'>Invalid payment amount! Maximum you can pay: ৳" . number_format($balance_fee) . "</div>";
    } else {
        // Generate random transaction ID
        $transaction_id = "BKS" . rand(100000, 999999);
        
        // Insert payment into database
        $insert_sql = "INSERT INTO fee_payments (student_name, payment_amount, payment_method, transaction_id) 
                      VALUES ('$student', $payment_amount, 'Bikash', '$transaction_id')";
        
        if($conn->query($insert_sql)){
            $message = "<div style='color:green; padding:10px; background:#e6ffe6; border-radius:5px;'>
                        ✅ Payment Successful!<br>
                        Amount: ৳" . number_format($payment_amount) . "<br>
                        Transaction ID: $transaction_id<br>
                        Method: Bikash
                        </div>";
        } else {
            $message = "<div style='color:red; padding:10px; background:#ffe6e6; border-radius:5px;'>Payment failed: " . $conn->error . "</div>";
        }
    }
}

// Get fee payments data
$fee_payments = $conn->query("SELECT * FROM fee_payments WHERE student_name='$student' ORDER BY payment_date DESC");

// Calculate totals
$total_paid = 0;
$payment_history = [];
if($fee_payments && $fee_payments->num_rows > 0){
    while($row = $fee_payments->fetch_assoc()){
        $total_paid += $row['payment_amount'];
        $payment_history[] = $row;
    }
}

// Fixed values for display
$total_fee = 6000; // Monthly fee
$balance_fee = $total_fee - $total_paid;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Fees</title>
<style>
body { 
    font-family: Arial, sans-serif; 
    background: #f8f9fa; 
    margin: 0;
    padding: 0;
}
.container { 
    width: 70%; 
    margin: 40px auto; 
    background: white; 
    padding: 30px; 
    border-radius: 15px; 
    box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
}
h2 { 
    text-align: center; 
    color: #0066cc; 
    margin-bottom: 30px; 
}
.student-info {
    background: #e6f2ff;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: center;
}
.student-info p {
    margin: 5px 0;
    font-size: 1.1em;
}
.fee-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}
.fee-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    border-top: 4px solid;
}
.total-fee { border-color: #0066cc; }
.paid-fee { border-color: #28a745; }
.balance-fee { border-color: #ff6b6b; }
.fee-card h3 {
    margin: 0 0 10px 0;
    font-size: 1em;
    color: #666;
}
.fee-amount {
    font-size: 2em;
    font-weight: bold;
    margin: 0;
}
.total-fee .fee-amount { color: #0066cc; }
.paid-fee .fee-amount { color: #28a745; }
.balance-fee .fee-amount { color: #ff6b6b; }

.payment-methods {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 30px;
}
.payment-methods h3 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}
.methods-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}
.method-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    cursor: pointer;
}
.method-card:hover {
    border-color: #0066cc;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.method-icon {
    font-size: 2.5em;
    margin-bottom: 10px;
}
.method-name {
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}
.method-details {
    font-size: 0.9em;
    color: #666;
}
.bikash-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.modal-content {
    background: white;
    padding: 30px;
    border-radius: 10px;
    width: 400px;
    text-align: center;
}
.modal-content h3 {
    color: #e2136e;
    margin-bottom: 20px;
}
.modal-content input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}
.modal-content button {
    background: #e2136e;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    margin: 5px;
}
.modal-content button:hover {
    background: #c1105f;
}
.close-btn {
    background: #6c757d !important;
}
.close-btn:hover {
    background: #545b62 !important;
}

.payment-history {
    margin-top: 30px;
}
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
.no-data {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 20px;
}
.status-paid {
    color: #28a745;
    font-weight: bold;
}
</style>
</head>
<body>
<div class="container">
    <h2>💰 My Fees</h2>

    <!-- Student Information -->
    <div class="student-info">
        <p><strong>Student:</strong> <?php echo $student; ?></p>
    </div>

    <?php echo $message; ?>

    <!-- Fee Summary -->
    <div class="fee-summary">
        <div class="fee-card total-fee">
            <h3>Total Fee</h3>
            <p class="fee-amount"><?php echo number_format($total_fee); ?> ৳</p>
        </div>
        <div class="fee-card paid-fee">
            <h3>Paid Amount</h3>
            <p class="fee-amount"><?php echo number_format($total_paid); ?> ৳</p>
        </div>
        <div class="fee-card balance-fee">
            <h3>Balance Due</h3>
            <p class="fee-amount"><?php echo number_format($balance_fee); ?> ৳</p>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="payment-methods">
        <h3>💳 Payment Methods</h3>
        <div class="methods-grid">
            <div class="method-card" onclick="openBikashModal()">
                <div class="method-icon" style="color: #e2136e;">📱</div>
                <div class="method-name">Bikash</div>
                <div class="method-details">Click to pay via Bikash</div>
            </div>
            <div class="method-card">
                <div class="method-icon">💸</div>
                <div class="method-name">Nagad</div>
                <div class="method-details">Coming Soon</div>
            </div>
            <div class="method-card">
                <div class="method-icon">🏦</div>
                <div class="method-name">Bank Transfer</div>
                <div class="method-details">Coming Soon</div>
            </div>
        </div>
    </div>

    <!-- Bikash Payment Modal -->
    <div id="bikashModal" class="bikash-modal">
        <div class="modal-content">
            <h3>📱 Bikash Payment</h3>
            <p>Enter payment amount (Maximum: ৳<?php echo number_format($balance_fee); ?>)</p>
            <form method="POST">
                <input type="number" name="payment_amount" placeholder="Enter amount" min="1" max="<?php echo $balance_fee; ?>" required>
                <div>
                    <button type="submit" name="bikash_payment">Pay Now</button>
                    <button type="button" class="close-btn" onclick="closeBikashModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment History -->
    <div class="payment-history">
        <h3>📊 Payment History</h3>
        <table>
            <tr>
                <th>Date</th>
                <th>Amount (৳)</th>
                <th>Method</th>
                <th>Transaction ID</th>
                <th>Status</th>
            </tr>
            <?php if(count($payment_history) > 0): ?>
                <?php foreach($payment_history as $payment): ?>
                    <tr>
                        <td><?php echo date('M j, Y g:i A', strtotime($payment['payment_date'])); ?></td>
                        <td><?php echo number_format($payment['payment_amount']); ?></td>
                        <td><?php echo $payment['payment_method']; ?></td>
                        <td><?php echo $payment['transaction_id']; ?></td>
                        <td class="status-paid">✅ Paid</td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="no-data">No payment history found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>
<a href="StudentDashboard.html" class="back-btn">⬅️ Go to Student Panel</a>
<script>
function openBikashModal() {
    document.getElementById('bikashModal').style.display = 'flex';
}

function closeBikashModal() {
    document.getElementById('bikashModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    var modal = document.getElementById('bikashModal');
    if (event.target == modal) {
        closeBikashModal();
    }
}

</script>
</body>
</html>