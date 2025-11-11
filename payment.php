<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "back_stage_pass database";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// -------- Handle Payment Status Update --------
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $payment_id = intval($_GET['id']);
    if ($action == "complete") {
        $conn->query("UPDATE payments SET status='completed' WHERE payment_id=$payment_id");
    } elseif ($action == "pending") {
        $conn->query("UPDATE payments SET status='pending' WHERE payment_id=$payment_id");
    }
    header("Location: payment.php");
    exit;
}

// -------- Fetch Payments --------
$sql = "
SELECT 
    p.payment_id,
    p.user_id,
    p.user_name,
    p.user_email,
    p.amount,
    p.payment_method,
    p.status,
    p.created_at
FROM payments p
ORDER BY p.payment_id DESC";
$payments = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payments</title>
<link rel="stylesheet" href="./assets/css/style.css">
<style>
h1 {
    text-align: center;
    color: #e0123f;
    margin-top: 30px;
    font-weight: 600;
}
table {
    width: 90%;
    border-collapse: collapse;
    margin: 30px auto;
    background: #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-radius: 10px;
    overflow: hidden;
}
th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}
th {
    background: #e0123f;
    color: white;
    font-weight: 600;
}
tr:hover {
    background: #f9f9f9;
}
.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: 0.2s;
}
.btn:hover {
    opacity: 0.85;
}
.confirm { background: #28a745; color: #fff; }
.cancel { background: #dc3545; color: #fff; }
.pending { background: #ffcc00; color: #000; padding: 4px 10px; border-radius: 6px; }
.confirmed { color: #28a745; font-weight: 600; }
.cancelled { color: #dc3545; font-weight: 600; }
.status { padding:5px 10px; border-radius:6px; font-weight:600; text-transform:capitalize; }
.status.pending { background:#ffcc00; color:#000; }
.status.completed { background:#28a745; color:#fff; }
.complete { background:#28a745; color:#fff; }
.pending { background:#ffc107; color:#000; }
</style>
</head>
<body>

<?php include("./includes/sidebar.php"); ?>
<?php include("./includes/topbar.php"); ?>

<h1>Payments</h1>

<table>
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Email</th>
        <th>Amount</th>
        <th>Method</th>
        <th>Status</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    <?php if ($payments && $payments->num_rows > 0): ?>
        <?php while ($row = $payments->fetch_assoc()): ?>
            <tr>
                <td><?= $row['payment_id'] ?></td>
                <td><?= htmlspecialchars($row['user_name']) ?></td>
                <td><?= htmlspecialchars($row['user_email']) ?></td>
                <td>$<?= number_format($row['amount'], 2) ?></td>
                <td><?= htmlspecialchars($row['payment_method']) ?></td>
                <td><span class="status <?= strtolower($row['status']) ?>"><?= $row['status'] ?></span></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <?php if ($row['status'] == 'pending'): ?>
                        <a href="payment.php?action=complete&id=<?= $row['payment_id'] ?>" class="btn complete">Mark Completed</a>
                    <?php else: ?>
                        <a href="payment.php?action=pending&id=<?= $row['payment_id'] ?>" class="btn pending">Mark Pending</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8" style="text-align:center;">No payments found.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
