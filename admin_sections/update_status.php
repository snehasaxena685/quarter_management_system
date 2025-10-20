<?php
include('../db_connect.php');
session_start();

header('Content-Type: application/json');

if (!isset($_POST['quarter_no'])) {
    echo json_encode(['success' => false, 'message' => 'Missing quarter number']);
    exit;
}

$quarter_no = $_POST['quarter_no'];
$admin = $_SESSION['username'] ?? 'system';

// Get current details
$q = $conn->prepare("SELECT * FROM quarters_admin WHERE quarter_no = ?");
$q->bind_param("s", $quarter_no);
$q->execute();
$res = $q->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Quarter not found']);
    exit;
}

$row = $res->fetch_assoc();
$new_status = ($row['status'] === 'occupied') ? 'vacant' : 'occupied';

// Update main table
$update = $conn->prepare("UPDATE quarters_admin SET status=?, updated_by=?, updated_at=NOW() WHERE quarter_no=?");
$update->bind_param("sss", $new_status, $admin, $quarter_no);
$update->execute();

// Insert into history table
$insert = $conn->prepare("
    INSERT INTO quarter_history (quarter_no, occupant_id, occupant_name, status, updated_by, remarks)
    VALUES (?, ?, ?, ?, ?, ?)
");
$remarks = "Status changed to $new_status";
$insert->bind_param(
    "ssssss",
    $row['quarter_no'],
    $row['emp_no'],
    $row['occupant_name'],
    $new_status,
    $admin,
    $remarks
);
$insert->execute();

echo json_encode(['success' => true, 'new_status' => $new_status]);
?>
