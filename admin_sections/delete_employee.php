<?php
include('../db_connect.php');

if(isset($_POST['emp_no'])) {
    $emp_no = $_POST['emp_no'];

    $stmt = $conn->prepare("DELETE FROM employee WHERE emp_no = ?");
    $stmt->bind_param("s", $emp_no);

    if($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Employee number not provided']);
}
?>
